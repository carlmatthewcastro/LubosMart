# LuboSmart MySQL query cookbook

These examples target `database/sql/lubosmart_mysql.sql`. Use bound parameters from Laravel or PDO; the `:name` values below are placeholders, not string interpolation points.

## Product discovery

```sql
SELECT p.id, p.name, p.slug, p.base_price, p.currency,
       p.average_rating, p.review_count, s.id AS shop_id, s.name AS shop_name
FROM products AS p
JOIN shops AS s ON s.id = p.shop_id AND s.status = 'active'
LEFT JOIN categories AS c ON c.id = p.category_id
WHERE p.status = 'active'
  AND (:category_id IS NULL OR p.category_id = :category_id)
  AND (:search IS NULL OR MATCH(p.name, p.description) AGAINST (:search IN NATURAL LANGUAGE MODE))
ORDER BY p.created_at DESC
LIMIT :limit OFFSET :offset;
```

## Product detail and variants

```sql
SELECT p.*, s.name AS shop_name, c.name AS category_name
FROM products AS p
JOIN shops AS s ON s.id = p.shop_id
LEFT JOIN categories AS c ON c.id = p.category_id
WHERE p.id = :product_id AND p.status = 'active';

SELECT v.id, v.sku, v.option_snapshot, v.price,
       (b.on_hand - b.reserved) AS available_quantity
FROM product_variants AS v
JOIN inventory_balances AS b ON b.variant_id = v.id
WHERE v.product_id = :product_id AND v.status = 'active'
ORDER BY v.sku;
```

## Cart read and upsert

```sql
SELECT ci.id, ci.product_id, ci.variant_id, ci.quantity, ci.unit_price,
       p.name, p.slug, s.name AS shop_name
FROM cart_items AS ci
JOIN products AS p ON p.id = ci.product_id
JOIN shops AS s ON s.id = p.shop_id
JOIN carts AS c ON c.id = ci.cart_id
WHERE c.buyer_id = :buyer_id AND c.status = 'active';

INSERT INTO cart_items (id, cart_id, product_id, variant_id, quantity, unit_price, created_at, updated_at)
SELECT UUID(), :cart_id, v.product_id, v.id, :quantity, v.price, UTC_TIMESTAMP(), UTC_TIMESTAMP()
FROM product_variants AS v
WHERE v.id = :variant_id AND v.status = 'active'
ON DUPLICATE KEY UPDATE
    quantity = quantity + VALUES(quantity),
    unit_price = VALUES(unit_price),
    updated_at = UTC_TIMESTAMP();

UPDATE cart_items AS ci
JOIN carts AS c ON c.id = ci.cart_id
SET ci.quantity = :quantity, ci.updated_at = UTC_TIMESTAMP()
WHERE ci.id = :cart_item_id AND c.buyer_id = :buyer_id AND c.status = 'active';
```

## Atomic COD checkout and inventory reservation

Run the following logic in one transaction. Lock rows in a deterministic variant order in application code, validate the address and prices server-side, and use the checkout idempotency key to make retries safe.

```sql
START TRANSACTION;

SELECT id FROM checkout_batches
WHERE buyer_id = :buyer_id AND idempotency_key = :idempotency_key
FOR UPDATE;

SELECT v.id, v.product_id, v.price, b.on_hand, b.reserved
FROM product_variants AS v
JOIN inventory_balances AS b ON b.variant_id = v.id
WHERE v.id IN (:variant_id_1, :variant_id_2)
ORDER BY v.id
FOR UPDATE;

-- Validate each requested quantity <= on_hand - reserved in the service layer.
-- Create checkout_batches, one order per shop, order_items, and order_addresses.

UPDATE inventory_balances
SET reserved = reserved + :quantity,
    updated_at = UTC_TIMESTAMP()
WHERE variant_id = :variant_id
  AND on_hand - reserved >= :quantity;

INSERT INTO inventory_movements
    (id, variant_id, order_id, movement_type, quantity, idempotency_key, created_at)
VALUES
    (UUID(), :variant_id, :order_id, 'reserve', :quantity, :movement_key, UTC_TIMESTAMP());

INSERT INTO order_status_events
    (id, order_id, from_status, to_status, actor_id, created_at)
VALUES
    (UUID(), :order_id, NULL, 'placed', :buyer_id, UTC_TIMESTAMP());

COMMIT;
```

If any inventory update affects zero rows, roll back the entire checkout. Do not trust cart prices or quantities from the browser.

## Order history and buyer tracking

```sql
SELECT o.id, o.order_number, o.status, o.payment_status, o.total_amount,
       o.created_at, s.name AS shop_name,
       sh.status AS shipment_status, w.tracking_id
FROM orders AS o
JOIN shops AS s ON s.id = o.shop_id
LEFT JOIN parcels AS p ON p.order_id = o.id
LEFT JOIN waybills AS w ON w.id = p.waybill_id
LEFT JOIN shipments AS sh ON sh.parcel_id = p.id
WHERE o.buyer_id = :buyer_id
ORDER BY o.created_at DESC
LIMIT :limit OFFSET :offset;

SELECT ose.from_status, ose.to_status, ose.created_at,
       se.event_type, se.from_status AS shipment_from, se.to_status AS shipment_to
FROM orders AS o
LEFT JOIN order_status_events AS ose ON ose.order_id = o.id
LEFT JOIN parcels AS p ON p.order_id = o.id
LEFT JOIN shipments AS sh ON sh.parcel_id = p.id
LEFT JOIN shipment_events AS se ON se.shipment_id = sh.id
WHERE o.id = :order_id AND o.buyer_id = :buyer_id
ORDER BY COALESCE(ose.created_at, se.created_at);
```

## Seller fulfillment queue

```sql
SELECT o.id, o.order_number, o.status, o.created_at,
       COUNT(oi.id) AS line_count, SUM(oi.quantity) AS item_count
FROM orders AS o
JOIN order_items AS oi ON oi.order_id = o.id
WHERE o.shop_id = :shop_id
  AND o.status IN ('placed', 'seller_processing', 'ready_for_pickup')
GROUP BY o.id
ORDER BY o.created_at;
```

When the seller marks an order ready, create the immutable waybill, parcel, and shipment in the same transaction, using a unique order key to make retries idempotent.

## Logistics ready-to-dispatch queue

```sql
SELECT sh.id AS shipment_id, w.tracking_id, o.order_number,
     sh.status, a.snapshot AS destination,
     dss.source_lane_snapshot AS source_lane
FROM shipments AS sh
JOIN parcels AS p ON p.id = sh.parcel_id
JOIN orders AS o ON o.id = p.order_id
JOIN waybills AS w ON w.id = p.waybill_id
JOIN order_addresses AS a ON a.order_id = o.id AND a.address_type = 'shipping'
LEFT JOIN dispatch_schedule_shipments dss ON dss.shipment_id = sh.id
WHERE sh.hub_id = :hub_id
  AND sh.status = 'sorted_at_hub'
  AND dss.shipment_id IS NULL
ORDER BY sh.sorted_at, sh.id
LIMIT 15;
```

The source lane is read from the immutable `dispatch_schedule_shipments.source_lane_snapshot`; the query intentionally does not infer a lane from mutable destination data.

## Courier task and delivery history

```sql
SELECT dt.id, dt.leg, dt.status, o.order_number, w.tracking_id,
       s.name AS shop_name, a.snapshot AS destination
FROM delivery_tasks AS dt
JOIN shipments AS sh ON sh.id = dt.shipment_id
JOIN parcels AS p ON p.id = sh.parcel_id
JOIN orders AS o ON o.id = p.order_id
JOIN shops AS s ON s.id = o.shop_id
JOIN waybills AS w ON w.id = p.waybill_id
JOIN order_addresses AS a ON a.order_id = o.id AND a.address_type = 'shipping'
WHERE dt.courier_id = :courier_id
  AND dt.status IN ('seller_pickup_assigned', 'delivery_assigned', 'delivery_accepted', 'in_transit', 'out_for_delivery')
ORDER BY dt.created_at;
```

## Transactional first-mile pickup confirmation

```sql
START TRANSACTION;

SELECT dt.id, dt.shipment_id, dt.status, sh.parcel_id
FROM delivery_tasks AS dt
JOIN shipments AS sh ON sh.id = dt.shipment_id
WHERE dt.id = :task_id AND dt.courier_id = :courier_id AND dt.leg = 'first_mile'
FOR UPDATE;

-- Validate the submitted waybill/reference, then transition both task and shipment.
UPDATE delivery_tasks
SET status = 'picked_up_from_seller', completed_at = UTC_TIMESTAMP(), updated_at = UTC_TIMESTAMP()
WHERE id = :task_id AND status = 'seller_pickup_accepted';

UPDATE shipments
SET status = 'picked_up_from_seller', updated_at = UTC_TIMESTAMP()
WHERE id = :shipment_id AND status = 'seller_pickup_accepted';

-- Convert reservation to fulfilled exactly once using a unique movement key.
UPDATE inventory_balances ib
JOIN order_items oi ON oi.variant_id = ib.variant_id
JOIN orders o ON o.id = oi.order_id
JOIN parcels p ON p.order_id = o.id
JOIN shipments sh ON sh.parcel_id = p.id
SET ib.on_hand = ib.on_hand - oi.quantity,
    ib.reserved = ib.reserved - oi.quantity,
    ib.updated_at = UTC_TIMESTAMP()
WHERE sh.id = :shipment_id;

COMMIT;
```

The service layer must guard the inventory update with an idempotency key and a lock. Returns, refunds, post-pickup cancellation, and partial fulfillment are intentionally outside this MVP contract.
