-- LuboSmart marketplace and first-party logistics schema
-- MySQL 8.0+ / InnoDB / utf8mb4
-- Target contract: UUID application keys, string-backed status values, immutable snapshots.

-- Use a separate database while validating this UUID-based target schema.
CREATE DATABASE IF NOT EXISTS `lubosmart_test`
  CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `lubosmart_test`;

-- Disable foreign key checks during schema creation
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE users (
    id CHAR(36) NOT NULL,
    email VARCHAR(255) NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(32) NOT NULL DEFAULT 'buyer',
    status VARCHAR(32) NOT NULL DEFAULT 'pending',
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_email_role (email, role),
    KEY ix_users_role_status (role, status)
) ENGINE=InnoDB;

CREATE TABLE addresses (
    id CHAR(36) NOT NULL,
    user_id CHAR(36) NOT NULL,
    type VARCHAR(16) NOT NULL DEFAULT 'shipping',
    label VARCHAR(100) NULL,
    recipient_name VARCHAR(255) NOT NULL,
    phone VARCHAR(32) NOT NULL,
    address_line_1 VARCHAR(255) NOT NULL,
    address_line_2 VARCHAR(255) NULL,
    region_code VARCHAR(32) NULL,
    region_name VARCHAR(150) NULL,
    province_code VARCHAR(32) NULL,
    province_name VARCHAR(150) NULL,
    city_code VARCHAR(32) NULL,
    city_name VARCHAR(150) NULL,
    barangay_code VARCHAR(32) NULL,
    barangay_name VARCHAR(150) NULL,
    postal_code CHAR(4) NULL,
    latitude DECIMAL(10,7) NULL,
    longitude DECIMAL(10,7) NULL,
    is_default BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_addresses_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    KEY ix_addresses_user_type (user_id, type),
    KEY ix_addresses_postal_code (postal_code)
) ENGINE=InnoDB;

CREATE TABLE shops (
    id CHAR(36) NOT NULL,
    seller_id CHAR(36) NOT NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    description TEXT NULL,
    status VARCHAR(32) NOT NULL DEFAULT 'pending',
    vacation_mode BOOLEAN NOT NULL DEFAULT FALSE,
    default_pickup_address_id CHAR(36) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_shops_seller (seller_id),
    UNIQUE KEY uq_shops_slug (slug),
    CONSTRAINT fk_shops_seller FOREIGN KEY (seller_id) REFERENCES users(id),
    CONSTRAINT fk_shops_pickup_address FOREIGN KEY (default_pickup_address_id) REFERENCES addresses(id) ON DELETE SET NULL,
    KEY ix_shops_status (status)
) ENGINE=InnoDB;

CREATE TABLE categories (
    id CHAR(36) NOT NULL,
    parent_id CHAR(36) NULL,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(180) NOT NULL,
    status VARCHAR(32) NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_categories_slug (slug),
    CONSTRAINT fk_categories_parent FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL,
    KEY ix_categories_parent_status (parent_id, status)
) ENGINE=InnoDB;

CREATE TABLE products (
    id CHAR(36) NOT NULL,
    shop_id CHAR(36) NOT NULL,
    category_id CHAR(36) NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    description TEXT NULL,
    status VARCHAR(32) NOT NULL DEFAULT 'draft',
    base_price DECIMAL(12,2) NOT NULL,
    currency CHAR(3) NOT NULL DEFAULT 'PHP',
    average_rating DECIMAL(3,2) NOT NULL DEFAULT 0,
    review_count INT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_products_shop_slug (shop_id, slug),
    CONSTRAINT fk_products_shop FOREIGN KEY (shop_id) REFERENCES shops(id),
    CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    KEY ix_products_status_category (status, category_id),
    FULLTEXT KEY ft_products_name_description (name, description)
) ENGINE=InnoDB;

CREATE TABLE product_variants (
    id CHAR(36) NOT NULL,
    product_id CHAR(36) NOT NULL,
    sku VARCHAR(100) NOT NULL,
    option_snapshot JSON NULL,
    price DECIMAL(12,2) NOT NULL,
    status VARCHAR(32) NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_variants_sku (sku),
    UNIQUE KEY uq_variants_product_id (product_id, id),
    CONSTRAINT fk_variants_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    KEY ix_variants_product_status (product_id, status)
) ENGINE=InnoDB;

CREATE TABLE inventory_balances (
    variant_id CHAR(36) NOT NULL,
    on_hand INT UNSIGNED NOT NULL DEFAULT 0,
    reserved INT UNSIGNED NOT NULL DEFAULT 0,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (variant_id),
    CONSTRAINT fk_inventory_variant FOREIGN KEY (variant_id) REFERENCES product_variants(id) ON DELETE CASCADE,
    CONSTRAINT chk_inventory_reserved CHECK (reserved <= on_hand)
) ENGINE=InnoDB;

CREATE TABLE inventory_movements (
    id CHAR(36) NOT NULL,
    variant_id CHAR(36) NOT NULL,
    order_id CHAR(36) NULL,
    movement_type VARCHAR(32) NOT NULL,
    quantity INT NOT NULL,
    idempotency_key VARCHAR(100) NOT NULL,
    notes VARCHAR(255) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_inventory_idempotency (idempotency_key),
    CONSTRAINT fk_inventory_movements_variant FOREIGN KEY (variant_id) REFERENCES product_variants(id),
    KEY ix_inventory_movements_variant_created (variant_id, created_at),
    KEY ix_inventory_movements_order (order_id)
) ENGINE=InnoDB;

CREATE TABLE carts (
    id CHAR(36) NOT NULL,
    buyer_id CHAR(36) NOT NULL,
    status VARCHAR(16) NOT NULL DEFAULT 'active',
    active_buyer_id CHAR(36) GENERATED ALWAYS AS (IF(status = 'active', buyer_id, NULL)) STORED,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_carts_active_buyer (active_buyer_id),
    CONSTRAINT fk_carts_buyer FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE cart_items (
    id CHAR(36) NOT NULL,
    cart_id CHAR(36) NOT NULL,
    product_id CHAR(36) NOT NULL,
    variant_id CHAR(36) NULL,
    quantity INT UNSIGNED NOT NULL,
    unit_price DECIMAL(12,2) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_cart_variant (cart_id, product_id, variant_id),
    CONSTRAINT fk_cart_items_cart FOREIGN KEY (cart_id) REFERENCES carts(id) ON DELETE CASCADE,
    CONSTRAINT fk_cart_items_product FOREIGN KEY (product_id) REFERENCES products(id),
    CONSTRAINT fk_cart_items_variant FOREIGN KEY (variant_id) REFERENCES product_variants(id),
    CONSTRAINT chk_cart_quantity CHECK (quantity > 0),
    KEY ix_cart_items_cart (cart_id)
) ENGINE=InnoDB;

CREATE TABLE checkout_batches (
    id CHAR(36) NOT NULL,
    buyer_id CHAR(36) NOT NULL,
    idempotency_key VARCHAR(100) NOT NULL,
    payment_method VARCHAR(32) NOT NULL DEFAULT 'cod',
    status VARCHAR(32) NOT NULL DEFAULT 'placed',
    total_amount DECIMAL(12,2) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_checkout_buyer_idempotency (buyer_id, idempotency_key),
    CONSTRAINT fk_checkout_buyer FOREIGN KEY (buyer_id) REFERENCES users(id),
    KEY ix_checkout_buyer_created (buyer_id, created_at)
) ENGINE=InnoDB;

CREATE TABLE orders (
    id CHAR(36) NOT NULL,
    checkout_batch_id CHAR(36) NULL,
    buyer_id CHAR(36) NOT NULL,
    shop_id CHAR(36) NOT NULL,
    order_number VARCHAR(40) NOT NULL,
    status VARCHAR(32) NOT NULL DEFAULT 'placed',
    payment_method VARCHAR(32) NOT NULL DEFAULT 'cod',
    payment_status VARCHAR(32) NOT NULL DEFAULT 'pending',
    subtotal DECIMAL(12,2) NOT NULL,
    shipping_fee DECIMAL(12,2) NOT NULL DEFAULT 0,
    discount_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    total_amount DECIMAL(12,2) NOT NULL,
    currency CHAR(3) NOT NULL DEFAULT 'PHP',
    placed_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_orders_number (order_number),
    CONSTRAINT fk_orders_checkout FOREIGN KEY (checkout_batch_id) REFERENCES checkout_batches(id) ON DELETE SET NULL,
    CONSTRAINT fk_orders_buyer FOREIGN KEY (buyer_id) REFERENCES users(id),
    CONSTRAINT fk_orders_shop FOREIGN KEY (shop_id) REFERENCES shops(id),
    KEY ix_orders_buyer_status_created (buyer_id, status, created_at),
    KEY ix_orders_shop_status_created (shop_id, status, created_at)
) ENGINE=InnoDB;

ALTER TABLE inventory_movements
    ADD CONSTRAINT fk_inventory_movements_order
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL;

CREATE TABLE order_addresses (
    id CHAR(36) NOT NULL,
    order_id CHAR(36) NOT NULL,
    address_type VARCHAR(16) NOT NULL DEFAULT 'shipping',
    snapshot JSON NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_order_address_type (order_id, address_type),
    CONSTRAINT fk_order_addresses_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE order_items (
    id CHAR(36) NOT NULL,
    order_id CHAR(36) NOT NULL,
    product_id CHAR(36) NULL,
    variant_id CHAR(36) NULL,
    sku VARCHAR(100) NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    option_snapshot JSON NULL,
    unit_price DECIMAL(12,2) NOT NULL,
    quantity INT UNSIGNED NOT NULL,
    line_total DECIMAL(12,2) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    CONSTRAINT fk_order_items_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL,
    CONSTRAINT fk_order_items_variant FOREIGN KEY (variant_id) REFERENCES product_variants(id) ON DELETE SET NULL,
    CONSTRAINT chk_order_item_quantity CHECK (quantity > 0),
    KEY ix_order_items_order (order_id)
) ENGINE=InnoDB;

CREATE TABLE order_status_events (
    id CHAR(36) NOT NULL,
    order_id CHAR(36) NOT NULL,
    from_status VARCHAR(32) NULL,
    to_status VARCHAR(32) NOT NULL,
    actor_id CHAR(36) NULL,
    metadata JSON NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_order_status_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    CONSTRAINT fk_order_status_actor FOREIGN KEY (actor_id) REFERENCES users(id) ON DELETE SET NULL,
    KEY ix_order_status_order_created (order_id, created_at)
) ENGINE=InnoDB;

CREATE TABLE vouchers (
    id CHAR(36) NOT NULL,
    code VARCHAR(80) NOT NULL,
    issuer_type VARCHAR(16) NOT NULL,
    shop_id CHAR(36) NULL,
    benefit_type VARCHAR(16) NOT NULL,
    value_type VARCHAR(16) NOT NULL,
    value DECIMAL(12,2) NOT NULL,
    minimum_order_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    usage_limit INT UNSIGNED NULL,
    used_count INT UNSIGNED NOT NULL DEFAULT 0,
    starts_at TIMESTAMP NULL,
    ends_at TIMESTAMP NULL,
    status VARCHAR(16) NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_vouchers_code (code),
    CONSTRAINT fk_vouchers_shop FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE,
    KEY ix_vouchers_status_dates (status, starts_at, ends_at)
) ENGINE=InnoDB;

CREATE TABLE order_vouchers (
    id CHAR(36) NOT NULL,
    order_id CHAR(36) NOT NULL,
    voucher_id CHAR(36) NOT NULL,
    code_snapshot VARCHAR(80) NOT NULL,
    discount_amount DECIMAL(12,2) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_order_voucher (order_id, voucher_id),
    CONSTRAINT fk_order_vouchers_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    CONSTRAINT fk_order_vouchers_voucher FOREIGN KEY (voucher_id) REFERENCES vouchers(id)
) ENGINE=InnoDB;

CREATE TABLE product_reviews (
    id CHAR(36) NOT NULL,
    product_id CHAR(36) NOT NULL,
    order_item_id CHAR(36) NOT NULL,
    buyer_id CHAR(36) NOT NULL,
    rating TINYINT UNSIGNED NOT NULL,
    body TEXT NULL,
    status VARCHAR(16) NOT NULL DEFAULT 'published',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_review_order_item_buyer (order_item_id, buyer_id),
    CONSTRAINT fk_reviews_product FOREIGN KEY (product_id) REFERENCES products(id),
    CONSTRAINT fk_reviews_order_item FOREIGN KEY (order_item_id) REFERENCES order_items(id),
    CONSTRAINT fk_reviews_buyer FOREIGN KEY (buyer_id) REFERENCES users(id),
    CONSTRAINT chk_review_rating CHECK (rating BETWEEN 1 AND 5),
    KEY ix_reviews_product_status (product_id, status)
) ENGINE=InnoDB;

CREATE TABLE logistics_organizations (
    id CHAR(36) NOT NULL,
    owner_id CHAR(36) NOT NULL,
    name VARCHAR(255) NOT NULL,
    status VARCHAR(32) NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_logistics_owner (owner_id),
    CONSTRAINT fk_logistics_owner FOREIGN KEY (owner_id) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE logistics_hubs (
    id CHAR(36) NOT NULL,
    organization_id CHAR(36) NOT NULL,
    address_id CHAR(36) NOT NULL,
    name VARCHAR(255) NOT NULL,
    status VARCHAR(16) NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_hub_organization (organization_id),
    CONSTRAINT fk_hubs_organization FOREIGN KEY (organization_id) REFERENCES logistics_organizations(id),
    CONSTRAINT fk_hubs_address FOREIGN KEY (address_id) REFERENCES addresses(id),
    KEY ix_hubs_status (status)
) ENGINE=InnoDB;

CREATE TABLE courier_affiliations (
    id CHAR(36) NOT NULL,
    courier_id CHAR(36) NOT NULL,
    organization_id CHAR(36) NOT NULL,
    hub_id CHAR(36) NOT NULL,
    status VARCHAR(16) NOT NULL DEFAULT 'pending',
    approved_by CHAR(36) NULL,
    approved_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_courier_affiliation (courier_id),
    CONSTRAINT fk_affiliation_courier FOREIGN KEY (courier_id) REFERENCES users(id),
    CONSTRAINT fk_affiliation_org FOREIGN KEY (organization_id) REFERENCES logistics_organizations(id),
    CONSTRAINT fk_affiliation_hub FOREIGN KEY (hub_id) REFERENCES logistics_hubs(id),
    CONSTRAINT fk_affiliation_approver FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    KEY ix_affiliation_org_status (organization_id, status)
) ENGINE=InnoDB;

CREATE TABLE vehicles (
    id CHAR(36) NOT NULL,
    courier_id CHAR(36) NOT NULL,
    vehicle_type VARCHAR(32) NOT NULL,
    plate_number VARCHAR(32) NOT NULL,
    make VARCHAR(100) NULL,
    model VARCHAR(100) NULL,
    or_document_path VARCHAR(500) NULL,
    cr_document_path VARCHAR(500) NULL,
    status VARCHAR(16) NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_vehicle_courier (courier_id),
    UNIQUE KEY uq_vehicle_plate (plate_number),
    CONSTRAINT fk_vehicles_courier FOREIGN KEY (courier_id) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE seller_pickup_requests (
    id CHAR(36) NOT NULL,
    shop_id CHAR(36) NOT NULL,
    organization_id CHAR(36) NOT NULL,
    pickup_address_snapshot JSON NOT NULL,
    status VARCHAR(32) NOT NULL DEFAULT 'requested',
    requested_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_pickup_requests_shop FOREIGN KEY (shop_id) REFERENCES shops(id),
    CONSTRAINT fk_pickup_requests_org FOREIGN KEY (organization_id) REFERENCES logistics_organizations(id),
    KEY ix_pickup_requests_org_status (organization_id, status, requested_at),
    KEY ix_pickup_requests_shop_status (shop_id, status)
) ENGINE=InnoDB;

CREATE TABLE seller_pickup_request_orders (
    pickup_request_id CHAR(36) NOT NULL,
    order_id CHAR(36) NOT NULL,
    position_no SMALLINT UNSIGNED NOT NULL,
    PRIMARY KEY (pickup_request_id, order_id),
    UNIQUE KEY uq_pickup_request_position (pickup_request_id, position_no),
    UNIQUE KEY uq_order_active_pickup_request (order_id),
    CONSTRAINT fk_pickup_request_orders_request FOREIGN KEY (pickup_request_id) REFERENCES seller_pickup_requests(id) ON DELETE CASCADE,
    CONSTRAINT fk_pickup_request_orders_order FOREIGN KEY (order_id) REFERENCES orders(id)
) ENGINE=InnoDB;

CREATE TABLE waybills (
    id CHAR(36) NOT NULL,
    order_id CHAR(36) NOT NULL,
    tracking_id VARCHAR(80) NOT NULL,
    qr_identifier VARCHAR(120) NOT NULL,
    snapshot JSON NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_waybill_order (order_id),
    UNIQUE KEY uq_waybill_tracking (tracking_id),
    UNIQUE KEY uq_waybill_qr (qr_identifier),
    CONSTRAINT fk_waybills_order FOREIGN KEY (order_id) REFERENCES orders(id)
) ENGINE=InnoDB;

CREATE TABLE parcels (
    id CHAR(36) NOT NULL,
    order_id CHAR(36) NOT NULL,
    waybill_id CHAR(36) NOT NULL,
    destination_snapshot JSON NOT NULL,
    item_snapshot JSON NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_parcel_order (order_id),
    UNIQUE KEY uq_parcel_waybill (waybill_id),
    CONSTRAINT fk_parcels_order FOREIGN KEY (order_id) REFERENCES orders(id),
    CONSTRAINT fk_parcels_waybill FOREIGN KEY (waybill_id) REFERENCES waybills(id)
) ENGINE=InnoDB;

CREATE TABLE shipments (
    id CHAR(36) NOT NULL,
    parcel_id CHAR(36) NOT NULL,
    organization_id CHAR(36) NOT NULL,
    hub_id CHAR(36) NOT NULL,
    status VARCHAR(40) NOT NULL DEFAULT 'awaiting_seller_pickup',
    received_at TIMESTAMP NULL,
    sorted_at TIMESTAMP NULL,
    dispatched_at TIMESTAMP NULL,
    delivered_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_shipment_parcel (parcel_id),
    CONSTRAINT fk_shipments_parcel FOREIGN KEY (parcel_id) REFERENCES parcels(id),
    CONSTRAINT fk_shipments_org FOREIGN KEY (organization_id) REFERENCES logistics_organizations(id),
    CONSTRAINT fk_shipments_hub FOREIGN KEY (hub_id) REFERENCES logistics_hubs(id),
    KEY ix_shipments_hub_status (hub_id, status),
    KEY ix_shipments_org_status (organization_id, status)
) ENGINE=InnoDB;

CREATE TABLE pickup_schedules (
    id CHAR(36) NOT NULL,
    organization_id CHAR(36) NOT NULL,
    hub_id CHAR(36) NOT NULL,
    courier_id CHAR(36) NOT NULL,
    scheduled_for DATETIME NOT NULL,
    status VARCHAR(24) NOT NULL DEFAULT 'scheduled',
    created_by CHAR(36) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_pickup_schedule_org FOREIGN KEY (organization_id) REFERENCES logistics_organizations(id),
    CONSTRAINT fk_pickup_schedule_hub FOREIGN KEY (hub_id) REFERENCES logistics_hubs(id),
    CONSTRAINT fk_pickup_schedule_courier FOREIGN KEY (courier_id) REFERENCES users(id),
    CONSTRAINT fk_pickup_schedule_creator FOREIGN KEY (created_by) REFERENCES users(id),
    KEY ix_pickup_schedule_org_date (organization_id, scheduled_for, status)
) ENGINE=InnoDB;

CREATE TABLE pickup_schedule_shipments (
    pickup_schedule_id CHAR(36) NOT NULL,
    shipment_id CHAR(36) NOT NULL,
    PRIMARY KEY (pickup_schedule_id, shipment_id),
    CONSTRAINT fk_pickup_schedule_ship_schedule FOREIGN KEY (pickup_schedule_id) REFERENCES pickup_schedules(id) ON DELETE CASCADE,
    CONSTRAINT fk_pickup_schedule_ship_shipment FOREIGN KEY (shipment_id) REFERENCES shipments(id),
    KEY ix_pickup_schedule_ship_shipment (shipment_id)
) ENGINE=InnoDB;

CREATE TABLE delivery_tasks (
    id CHAR(36) NOT NULL,
    shipment_id CHAR(36) NOT NULL,
    courier_id CHAR(36) NULL,
    leg VARCHAR(16) NOT NULL,
    status VARCHAR(40) NOT NULL DEFAULT 'awaiting_seller_pickup',
    accepted_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_task_shipment_leg (shipment_id, leg),
    CONSTRAINT fk_tasks_shipment FOREIGN KEY (shipment_id) REFERENCES shipments(id),
    CONSTRAINT fk_tasks_courier FOREIGN KEY (courier_id) REFERENCES users(id) ON DELETE SET NULL,
    KEY ix_tasks_courier_status (courier_id, status),
    KEY ix_tasks_leg_status (leg, status)
) ENGINE=InnoDB;

CREATE TABLE delivery_task_offers (
    id CHAR(36) NOT NULL,
    delivery_task_id CHAR(36) NOT NULL,
    courier_id CHAR(36) NOT NULL,
    status VARCHAR(16) NOT NULL DEFAULT 'offered',
    offered_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    responded_at TIMESTAMP NULL,
    rejection_reason VARCHAR(255) NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_task_offers_task FOREIGN KEY (delivery_task_id) REFERENCES delivery_tasks(id) ON DELETE CASCADE,
    CONSTRAINT fk_task_offers_courier FOREIGN KEY (courier_id) REFERENCES users(id),
    KEY ix_task_offers_courier_status (courier_id, status),
    KEY ix_task_offers_task_status (delivery_task_id, status)
) ENGINE=InnoDB;

CREATE TABLE dispatch_schedules (
    id CHAR(36) NOT NULL,
    organization_id CHAR(36) NOT NULL,
    hub_id CHAR(36) NOT NULL,
    courier_id CHAR(36) NOT NULL,
    scheduled_for DATETIME NOT NULL,
    status VARCHAR(24) NOT NULL DEFAULT 'scheduled',
    created_by CHAR(36) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_dispatch_org FOREIGN KEY (organization_id) REFERENCES logistics_organizations(id),
    CONSTRAINT fk_dispatch_hub FOREIGN KEY (hub_id) REFERENCES logistics_hubs(id),
    CONSTRAINT fk_dispatch_courier FOREIGN KEY (courier_id) REFERENCES users(id),
    CONSTRAINT fk_dispatch_creator FOREIGN KEY (created_by) REFERENCES users(id),
    KEY ix_dispatch_org_date (organization_id, scheduled_for, status)
) ENGINE=InnoDB;

CREATE TABLE dispatch_schedule_shipments (
    dispatch_schedule_id CHAR(36) NOT NULL,
    shipment_id CHAR(36) NOT NULL,
    delivery_task_id CHAR(36) NOT NULL,
    source_lane_snapshot VARCHAR(150) NULL,
    PRIMARY KEY (dispatch_schedule_id, shipment_id),
    UNIQUE KEY uq_dispatch_task (delivery_task_id),
    CONSTRAINT fk_dispatch_ship_schedule FOREIGN KEY (dispatch_schedule_id) REFERENCES dispatch_schedules(id) ON DELETE CASCADE,
    CONSTRAINT fk_dispatch_ship_shipment FOREIGN KEY (shipment_id) REFERENCES shipments(id),
    CONSTRAINT fk_dispatch_ship_task FOREIGN KEY (delivery_task_id) REFERENCES delivery_tasks(id)
) ENGINE=InnoDB;

CREATE TABLE shipment_evidence (
    id CHAR(36) NOT NULL,
    shipment_id CHAR(36) NOT NULL,
    delivery_task_id CHAR(36) NULL,
    submitted_by CHAR(36) NOT NULL,
    purpose VARCHAR(24) NOT NULL,
    evidence_type VARCHAR(24) NOT NULL,
    storage_disk VARCHAR(64) NULL,
    storage_path VARCHAR(500) NULL,
    reference_value VARCHAR(120) NULL,
    status VARCHAR(24) NOT NULL DEFAULT 'submitted',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_evidence_shipment FOREIGN KEY (shipment_id) REFERENCES shipments(id),
    CONSTRAINT fk_evidence_task FOREIGN KEY (delivery_task_id) REFERENCES delivery_tasks(id) ON DELETE SET NULL,
    CONSTRAINT fk_evidence_submitter FOREIGN KEY (submitted_by) REFERENCES users(id),
    KEY ix_evidence_shipment_purpose (shipment_id, purpose, created_at)
) ENGINE=InnoDB;

CREATE TABLE shipment_events (
    id CHAR(36) NOT NULL,
    shipment_id CHAR(36) NOT NULL,
    delivery_task_id CHAR(36) NULL,
    event_type VARCHAR(48) NOT NULL,
    from_status VARCHAR(40) NULL,
    to_status VARCHAR(40) NULL,
    actor_id CHAR(36) NULL,
    metadata JSON NULL,
    idempotency_key VARCHAR(120) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_shipment_event_idempotency (idempotency_key),
    CONSTRAINT fk_shipment_events_shipment FOREIGN KEY (shipment_id) REFERENCES shipments(id),
    CONSTRAINT fk_shipment_events_task FOREIGN KEY (delivery_task_id) REFERENCES delivery_tasks(id) ON DELETE SET NULL,
    CONSTRAINT fk_shipment_events_actor FOREIGN KEY (actor_id) REFERENCES users(id) ON DELETE SET NULL,
    KEY ix_shipment_events_shipment_created (shipment_id, created_at)
) ENGINE=InnoDB;

CREATE TABLE notifications (
    id CHAR(36) NOT NULL,
    user_id CHAR(36) NOT NULL,
    type VARCHAR(120) NOT NULL,
    data JSON NOT NULL,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_notifications_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    KEY ix_notifications_user_read_created (user_id, read_at, created_at)
) ENGINE=InnoDB;

CREATE TABLE audit_logs (
    id CHAR(36) NOT NULL,
    actor_id CHAR(36) NULL,
    action VARCHAR(120) NOT NULL,
    source_feature VARCHAR(80) NOT NULL,
    auditable_type VARCHAR(120) NULL,
    auditable_id CHAR(36) NULL,
    metadata JSON NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_audit_actor FOREIGN KEY (actor_id) REFERENCES users(id) ON DELETE SET NULL,
    KEY ix_audit_feature_created (source_feature, created_at),
    KEY ix_audit_actor_created (actor_id, created_at)
) ENGINE=InnoDB;

CREATE TABLE sorting_plans (
    id CHAR(36) NOT NULL,
    hub_id CHAR(36) NOT NULL,
    name VARCHAR(150) NOT NULL,
    status VARCHAR(16) NOT NULL DEFAULT 'active',
    created_by CHAR(36) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_sort_plans_hub FOREIGN KEY (hub_id) REFERENCES logistics_hubs(id),
    CONSTRAINT fk_sort_plans_creator FOREIGN KEY (created_by) REFERENCES users(id),
    KEY ix_sort_plans_hub_status (hub_id, status)
) ENGINE=InnoDB;

CREATE TABLE sorting_plan_lanes (
    id CHAR(36) NOT NULL,
    sorting_plan_id CHAR(36) NOT NULL,
    name VARCHAR(150) NOT NULL,
    lane_type VARCHAR(16) NOT NULL DEFAULT 'standard',
    postal_code CHAR(4) NULL,
    created_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_sort_lane_postal (sorting_plan_id, postal_code),
    CONSTRAINT fk_sort_lanes_plan FOREIGN KEY (sorting_plan_id) REFERENCES sorting_plans(id) ON DELETE CASCADE,
    KEY ix_sort_lanes_type (sorting_plan_id, lane_type)
) ENGINE=InnoDB;

CREATE TABLE sorting_scans (
    id CHAR(36) NOT NULL,
    sorting_plan_id CHAR(36) NULL,
    shipment_id CHAR(36) NULL,
    tracking_id VARCHAR(80) NOT NULL,
    lane_id CHAR(36) NULL,
    result VARCHAR(24) NOT NULL,
    exception_reason VARCHAR(255) NULL,
    idempotency_key VARCHAR(120) NOT NULL,
    scanned_by CHAR(36) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_sort_scan_idempotency (idempotency_key),
    CONSTRAINT fk_sort_scans_plan FOREIGN KEY (sorting_plan_id) REFERENCES sorting_plans(id) ON DELETE SET NULL,
    CONSTRAINT fk_sort_scans_shipment FOREIGN KEY (shipment_id) REFERENCES shipments(id) ON DELETE SET NULL,
    CONSTRAINT fk_sort_scans_lane FOREIGN KEY (lane_id) REFERENCES sorting_plan_lanes(id) ON DELETE SET NULL,
    CONSTRAINT fk_sort_scans_user FOREIGN KEY (scanned_by) REFERENCES users(id),
    KEY ix_sort_scans_tracking_created (tracking_id, created_at)
) ENGINE=InnoDB;

CREATE TABLE sorting_sessions (
    id CHAR(36) NOT NULL,
    hub_id CHAR(36) NOT NULL,
    sorting_plan_id CHAR(36) NULL,
    opened_by CHAR(36) NOT NULL,
    status VARCHAR(16) NOT NULL DEFAULT 'open',
    opened_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    closed_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_sort_sessions_hub FOREIGN KEY (hub_id) REFERENCES logistics_hubs(id),
    CONSTRAINT fk_sort_sessions_plan FOREIGN KEY (sorting_plan_id) REFERENCES sorting_plans(id) ON DELETE SET NULL,
    CONSTRAINT fk_sort_sessions_user FOREIGN KEY (opened_by) REFERENCES users(id),
    KEY ix_sort_sessions_hub_status (hub_id, status)
) ENGINE=InnoDB;

CREATE TABLE sorting_session_items (
    sorting_session_id CHAR(36) NOT NULL,
    shipment_id CHAR(36) NOT NULL,
    sorting_scan_id CHAR(36) NULL,
    result VARCHAR(24) NOT NULL DEFAULT 'pending',
    PRIMARY KEY (sorting_session_id, shipment_id),
    CONSTRAINT fk_sort_session_items_session FOREIGN KEY (sorting_session_id) REFERENCES sorting_sessions(id) ON DELETE CASCADE,
    CONSTRAINT fk_sort_session_items_shipment FOREIGN KEY (shipment_id) REFERENCES shipments(id),
    CONSTRAINT fk_sort_session_items_scan FOREIGN KEY (sorting_scan_id) REFERENCES sorting_scans(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE failed_delivery_attempts (
    id CHAR(36) NOT NULL,
    delivery_task_id CHAR(36) NOT NULL,
    reason VARCHAR(255) NOT NULL,
    notes TEXT NULL,
    attempted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_by CHAR(36) NOT NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_failed_attempt_task FOREIGN KEY (delivery_task_id) REFERENCES delivery_tasks(id),
    CONSTRAINT fk_failed_attempt_user FOREIGN KEY (created_by) REFERENCES users(id),
    KEY ix_failed_attempt_task_time (delivery_task_id, attempted_at)
) ENGINE=InnoDB;

-- Re-enable foreign key checks after schema completion
SET FOREIGN_KEY_CHECKS = 1;