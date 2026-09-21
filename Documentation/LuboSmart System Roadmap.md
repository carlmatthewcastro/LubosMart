# LuboSmart System Roadmap & Feature Blueprint

**Product:** LuboSmart
**Tagline:** *Lubos na Kaginhawaan, Matalinong Pamimili.*
**Document status:** Delivery blueprint
**Planning baseline:** 2026-09-22
**Primary reference:** Adapted LuboSmart roadmap and feature specifications in this folder

## 1. Product Direction

LuboSmart is a multi-tenant, hyper-local e-commerce marketplace for communities in the Philippines. It connects Buyers, MSME Sellers, Couriers, and Administrators through one transaction and fulfillment system.

The platform must make local commerce convenient without hiding operational responsibility:

- Buyers discover nearby products, checkout using the MVP payment method, message vendors, and follow delivery progress.
- Sellers manage a shop, catalog, inventory, vouchers, buyer orders, and fulfillment readiness.
- Couriers accept dispatch work, follow pickup and delivery instructions, confirm custody, and track earnings.
- Administrators approve participants, enforce compliance, resolve disputes, and audit commissions and operational activity.

### Brand system

| Token | Value | Product use |
| --- | --- | --- |
| Primary Purple | `#582A86` | Primary actions, links, active navigation |
| Deep Purple | `#35145A` | Headers, high-contrast surfaces, authenticated shell |
| Smart Orange | `#F59E0B` | Calls to action, status emphasis, delivery alerts |
| Soft Canvas | `#F7F4FB` | Page background and calm workspace surfaces |

The visual system should feel trustworthy, local, and efficient. Purple carries the LuboSmart identity; orange signals progress and action. Interfaces must remain accessible, keyboard-usable, responsive, and legible on low-cost mobile devices.

## 2. Architecture Contract

### 2.1 Target stack

| Layer | LuboSmart standard |
| --- | --- |
| Buyer and dashboard clients | React, JavaScript ES6+, HTML5 |
| Styling | Tailwind CSS with LuboSmart design tokens |
| API | PHP 8.3+, Laravel |
| Authentication | Laravel Sanctum personal access tokens for API clients |
| Authorization | Laravel middleware, policies, gates, and role/permission checks |
| Database | MySQL 8.0+ with InnoDB and foreign-key constraints |
| Local and production packaging | Docker and Docker Compose |
| File storage | Laravel filesystem abstraction; private evidence must not be public by URL |
| Async work | Laravel queues for notifications, audit events, and retryable integrations |

The API is the system of record. React clients must not encode business authority in the browser. Every seller, courier, tenant, inventory, commission, and status transition must be revalidated by Laravel inside a transaction.

### 2.2 Application boundaries

The recommended delivery shape is a Laravel API with role-oriented React surfaces in one repository:

```text
LuboSmart/
├── app/                 # Laravel domain logic, policies, services, API resources
├── database/            # MySQL migrations, factories, seeders
├── resources/js/        # Shared React shell and role-oriented screens
├── resources/css/       # Tailwind and LuboSmart tokens
├── routes/api.php       # Versioned JSON API: /api/v1/*
├── routes/web.php       # Server-rendered entry points and fallback routes
├── tests/               # Feature, unit, authorization, and workflow tests
├── docker/              # PHP, Nginx, and worker configuration
└── docker-compose.yml   # app, web, mysql, queue, and frontend services
```

The current repository contains working web routes and role views. Phase 1 should add or reconcile a versioned API surface rather than allowing new business features to depend on ad hoc web POST routes.

### 2.3 Tenant and authorization rules

- `users` is the shared identity table; persisted roles use lowercase values: `buyer`, `seller`, `courier`, `admin`.
- A Seller owns one Shop in the MVP. Every catalog, inventory, order, voucher, and report query is scoped through the authenticated Seller's Shop.
- A Courier is affiliated with an approved Logistics operator or dispatch pool. A Courier cannot self-assign work.
- Admin actions require explicit policy authorization and create an audit record.
- Buyers can only read or mutate their own profile, addresses, cart, orders, messages, and reviews.
- Authorization is enforced server-side even when the UI hides a control.

The current code uses some legacy role names and separate Logistics routes. During Phase 1, map existing values deliberately; do not silently rename persisted data in a feature branch.

## 3. End-to-End Operating Flow

```text
Buyer browses catalog
  -> adds product/variant to cart
  -> applies voucher and confirms address
  -> places COD order

Seller receives order
  -> accepts/processes order
  -> reserves and fulfills inventory
  -> packs parcel
  -> marks ready_for_pickup
  -> selects eligible Logistics provider
  -> system creates immutable shared waybill

Admin dispatch operations create first-mile task
  -> eligible Courier accepts
  -> Courier picks up from Seller
  -> custody event is validated and recorded

Admin dispatch operations receive at hub
  -> scans and sorts parcel
  -> creates delivery dispatch batch (maximum 15 parcels)
  -> assigns final-mile Courier

Courier accepts delivery task
  -> picks up at hub
  -> travels to Buyer
  -> records delivery proof and completion

Buyer sees delivered order
  -> rates product and Seller
  -> support/dispute window remains available
```

### Status separation

`orders.status` is the Buyer/Seller-facing commercial status. Physical custody belongs to shipment and delivery-task records. They must not be collapsed into one overloaded enum.

| Commercial state | Buyer-facing label | Physical milestone |
| --- | --- | --- |
| `placed` | To prepare | Order created; inventory reserved |
| `seller_processing` | Preparing | Seller is processing |
| `ready_for_pickup` | Ready for pickup | Parcel packed; awaiting first mile |
| `assigned` | In transit to hub | First-mile task accepted or hub receipt projected |
| `picked_up` | On the way | Final-mile Courier has collected from hub |
| `out_for_delivery` | Out for delivery | Courier is delivering |
| `delivered` | Completed | Delivery confirmed |
| `cancelled` / `rejected` | Cancelled | Commercial flow ended |

Required physical milestones are `awaiting_seller_pickup`, `seller_pickup_assigned`, `seller_pickup_accepted`, `picked_up_from_seller`, `received_at_hub`, `sorted_at_hub`, `dispatched_from_hub`, `delivery_assigned`, `delivery_accepted`, `picked_up_from_hub`, `in_transit`, `out_for_delivery`, and `delivered`.

The shared waybill/tracking ID is generated once when the Seller commits the pickup request. Printing, opening, or scanning the waybill never changes custody by itself. Only an authorized transition service can advance physical status.

## 4. Role-Based Feature Matrix

| Role | Core features | Primary flow | Success measure |
| --- | --- | --- | --- |
| Buyer | Registration/login, catalog search, category browsing, product variants, cart, vouchers, address book, COD checkout, order tracking, vendor messaging, ratings/reviews, cancellation before pickup | Discover -> compare -> checkout -> track -> receive -> review | Successful order with clear status and no duplicate charge/order |
| Seller (MSME) | Registration and approval, shop profile, catalog, variants, stock, pricing, vouchers, order queue, pack/ready workflow, pickup request, waybill, reports, buyer messaging | Onboard -> publish -> receive order -> prepare -> hand off -> measure | Accurate stock and on-time handoff |
| Courier | Registration/affiliation, profile and vehicle, task inbox, accept/reject, route details, pickup scan, custody confirmation, delivery proof, earnings history | Receive offer -> accept -> collect -> deliver -> close task | Validated custody events and completed deliveries |
| Admin | Registration approval, user/shop compliance, category management, disputes, commission audit, reports, announcements, permissions, audit logs | Review -> approve/enforce -> monitor -> resolve | Traceable decisions and reconciled commissions |

### Buyer acceptance path

1. Guest can browse active Shops, Categories, Products, and available variants.
2. Authenticated Buyer adds only purchasable quantities to a cart.
3. Checkout validates stock, address ownership, voucher eligibility, and total server-side.
4. COD placement creates one checkout batch and one Shop Order per Seller atomically.
5. Buyer can see commercial status, physical timeline, Seller contact channel, courier status when available, and cancellation eligibility.
6. Delivered orders unlock one review per eligible order item.

### Seller acceptance path

1. Seller registration creates one pending Shop and application record.
2. Admin approval activates the existing Seller and Shop; no duplicate Shop is created.
3. Seller can publish only valid products with price, stock, category, and required media.
4. Processing an order does not itself create a waybill.
5. Marking the parcel ready for pickup commits the selected dispatch operation provider and creates the immutable waybill.
6. Inventory reservation is converted to fulfilled stock at successful first-mile pickup; pre-pickup cancellation releases the reservation idempotently.

### Courier acceptance path

1. Courier can act only after account and Logistics affiliation approval.
2. The task projection exposes the minimum order, parcel, pickup, destination, and delivery-instruction data needed for execution.
3. Accept/reject is atomic and prevents two Couriers from accepting the same offer.
4. A scan or photo is evidence; the server transition validates task ownership, parcel identity, and current state.
5. Delivery completion is idempotent and records proof metadata privately.

### Admin acceptance path

1. Approval queues show application evidence and current account status.
2. Compliance actions are policy-authorized, reasoned, timestamped, and auditable.
3. Commission reports reconcile gross order value, discounts, shipping, platform commission, seller net, and courier/logistics amounts.
4. Disputes link to the order, messages, status history, evidence, and resolution actor.

## 5. Phase-by-Phase Team Roadmap

### Phase 1: Environment, API Foundation, and Auth

**Goal:** Establish a repeatable Docker/MySQL development baseline, shared identity model, Sanctum authentication, and role-aware navigation.

**Carl Matthew Castro - Backend/DB**

- Confirm PHP, Composer, Node, Docker, and Docker Compose versions.
- Reconcile legacy `buyer`/`rider`/`superadmin` values to the LuboSmart API contract (`buyer`/`courier`/`admin`) with a documented migration strategy.
- Configure MySQL connection, strict mode, timezone, UUID strategy, factories, and seed data.
- Add Sanctum token issuance/revocation and `/api/v1/auth/*` endpoints.
- Implement role middleware, policies, account status checks, and registration approval rules.
- Add `users`, role profiles, registration applications, addresses, documents, personal access tokens, audit logs, and notifications relations as needed.
- Establish API resources, validation requests, error envelope, pagination, and API versioning conventions.

**Jayward Villanueva - Frontend binding/database consolidation**

- Consolidate API client configuration, token persistence, CSRF/token refresh behavior, and typed response mapping in JavaScript.
- Bind login, registration, logout, current-user, and approval-pending states to real endpoints.
- Inventory current migrations and identify duplicate or conflicting tables/columns for consolidation with Carl.
- Build shared form data handling for profile, address, and document uploads.

**Allianah Pauline Palconan - UI/UX and visual ergonomics**

- Create the LuboSmart Tailwind token layer using the four brand colors, accessible contrast, spacing, focus, and status conventions.
- Design responsive auth, approval-pending, account, and role-shell layouts.
- Define navigation patterns for Buyer, Seller, Courier, and Admin without duplicating interaction rules.
- Document empty, loading, validation, unauthorized, and offline-friendly states.

**Key API touchpoints**

- `POST /api/v1/auth/register`
- `POST /api/v1/auth/login`
- `POST /api/v1/auth/logout`
- `GET /api/v1/auth/me`
- `PATCH /api/v1/me`
- `GET|POST|PATCH|DELETE /api/v1/me/addresses`
- `POST /api/v1/me/documents`
- `GET /api/v1/admin/registration-applications`
- `POST /api/v1/admin/registration-applications/{application}/approve`
- `POST /api/v1/admin/registration-applications/{application}/reject`

**Exit criteria**

- Fresh `docker compose up` starts app, MySQL, worker, and frontend services.
- A seeded user for each role can authenticate through Sanctum.
- Unauthorized cross-role and cross-tenant requests return consistent 401/403 responses.
- Migrations run cleanly on an empty MySQL database and a representative existing database.

### Phase 2: Core Admin and Seller Engine

**Goal:** Make the marketplace supply side operational: approved Shops, categories, catalog, inventory, vouchers, and seller order preparation.

**Carl Matthew Castro - Backend/DB**

- Implement Shop, Category, Product, ProductVariant, ProductMedia, inventory, and voucher migrations/models.
- Add tenant-scoped Seller policies and catalog services.
- Implement atomic stock reservation, release, and fulfillment rules.
- Build Seller order queue and `seller_processing` -> `ready_for_pickup` transition service.
- Implement Admin compliance, Shop/category moderation, and commission configuration endpoints.
- Add audit events for product changes, approval, stock adjustments, and compliance actions.

**Jayward Villanueva - Frontend binding/database consolidation**

- Bind Seller dashboard forms and tables to catalog, inventory, voucher, and order APIs.
- Consolidate catalog/product/variant payloads so the UI and database use the same IDs and status values.
- Add optimistic refresh only for non-authoritative UI state; refetch after inventory or order mutations.
- Build Admin approval, category, seller, and compliance data grids.

**Allianah Pauline Palconan - UI/UX and visual ergonomics**

- Design dense, scan-friendly Seller catalog and fulfillment workspaces.
- Establish reusable product editor, variant matrix, stock badge, voucher builder, and order status components.
- Create Admin review panels that make evidence, decision, reason, and audit history visible.
- Verify responsive behavior for laptop dashboards and tablet/mobile seller use.

**Key MySQL relations**

```text
users 1--1 seller_profiles
users 1--1 shops
shops 1--many products
categories 1--many products
products 1--many product_variants
products 1--many product_media
product_variants 1--1 inventory
shops 1--many vouchers
shops 1--many orders
orders 1--many order_items
order_items many--1 product_variants
```

**Key API touchpoints**

- `GET|POST|PATCH /api/v1/seller/shop`
- `GET|POST|PATCH|DELETE /api/v1/seller/products`
- `GET|POST|PATCH|DELETE /api/v1/seller/products/{product}/variants`
- `GET /api/v1/seller/inventory`
- `POST /api/v1/seller/inventory/{variant}/adjust`
- `GET|POST|PATCH /api/v1/seller/vouchers`
- `GET /api/v1/seller/orders`
- `POST /api/v1/seller/orders/{order}/process`
- `POST /api/v1/seller/orders/{order}/ready-for-pickup`
- `GET|POST /api/v1/admin/categories`
- `GET /api/v1/admin/reports/commissions`

**Exit criteria**

- An approved Seller can publish a product, maintain stock, receive an order, and mark it ready for pickup.
- Inventory changes are transaction-safe and idempotent.
- Admin can approve or restrict a Seller/product and see the audit record.

### Phase 3: Buyer Catalog, Cart, Checkout, and Messaging

**Goal:** Deliver the complete marketplace purchase path with reliable server-side totals and buyer-visible order tracking.

**Carl Matthew Castro - Backend/DB**

- Implement catalog search/filter/sort with pagination and Shop/category scope.
- Implement cart ownership, variant quantity validation, checkout quote, voucher application, and COD order creation.
- Create checkout batch, Shop Orders, order items, order addresses, status events, and cancellation rules.
- Add Buyer/Seller message threads with authorization and abuse/audit controls.
- Add order timeline resource and review/rating eligibility rules.

**Jayward Villanueva - Frontend binding/database consolidation**

- Bind catalog, product detail, variant selection, cart, checkout, confirmation, and order-history screens.
- Connect address selection and voucher application to server recalculation rather than client-only totals.
- Bind Buyer-Seller messaging and order timeline polling/refresh behavior.
- Merge checkout payload requirements with the final MySQL order schema and seed realistic fixture data.

**Allianah Pauline Palconan - UI/UX and visual ergonomics**

- Design a fast, local-marketplace storefront with clear product media, price, stock, shop identity, and delivery expectations.
- Create accessible cart and checkout forms with clear error recovery and COD confirmation.
- Design order timeline, cancellation states, messages, ratings, and review composition.
- Ensure Soft Canvas does not reduce contrast and that Smart Orange is reserved for action/progress emphasis.

**Key MySQL relations**

```text
users 1--many carts
carts 1--many cart_items
cart_items many--1 product_variants
users 1--many checkout_batches
checkout_batches 1--many orders
orders 1--many order_items
orders 1--many order_addresses
orders 1--many order_status_events
users 1--many messages
orders 1--many reviews
order_items 1--0..1 reviews
```

**Key API touchpoints**

- `GET /api/v1/catalog/products`
- `GET /api/v1/catalog/products/{product}`
- `GET|POST|PATCH|DELETE /api/v1/buyer/cart/items`
- `POST /api/v1/buyer/checkout/quote`
- `POST /api/v1/buyer/orders`
- `GET /api/v1/buyer/orders`
- `GET /api/v1/buyer/orders/{order}`
- `POST /api/v1/buyer/orders/{order}/cancel`
- `GET|POST /api/v1/messages/threads`
- `GET|POST /api/v1/buyer/orders/{order}/reviews`

**Exit criteria**

- A Buyer can complete a COD purchase from catalog through confirmation.
- Checkout totals and stock are calculated and committed server-side in one transaction.
- Duplicate submission does not create duplicate orders; an idempotency key is required for order creation.
- Seller and Buyer see the same order item snapshots and commercial status.

### Phase 4: Courier Dispatch and Hyper-Local Fulfillment

**Goal:** Connect Seller-ready parcels to Logistics-controlled first-mile and final-mile delivery.

**Carl Matthew Castro - Backend/DB**

- Add immutable Waybill, Parcel, Shipment, DeliveryTask, DeliveryTaskOffer, ShipmentEvent, DispatchBatch, custody evidence, and proof-of-delivery tables.
- Implement Logistics provider selection and one-hub MVP rules.
- Create first-mile tasks only after Seller readiness; enforce Courier affiliation and offer acceptance.
- Add atomic hub receipt, sorting, dispatch-batch creation (maximum 15 parcels), final-mile assignment, and delivery transitions.
- Keep photo evidence private and store disk/path/metadata, never public file authority.
- Add courier earnings ledger/reporting inputs and notification jobs.

**Jayward Villanueva - Frontend binding/database consolidation**

- Bind Seller pickup request and shared waybill print/reprint screens.
- Build Logistics queues for pickup schedules, hub receipt, sorting, ready-to-dispatch, and dispatch batches.
- Bind Courier task inbox, accept/reject, pickup confirmation, delivery completion, and earnings views.
- Consolidate physical status payloads with commercial order timeline mapping.
- Support retry/reconnect for non-authoritative scan capture where operationally necessary.

**Allianah Pauline Palconan - UI/UX and visual ergonomics**

- Design compact scan-first Logistics screens usable on tablets and handheld devices.
- Design Courier mobile-responsive task cards with prominent pickup/destination, status, action, and failure recovery.
- Establish clear custody milestones, exception states, rejected-offer states, and proof upload feedback.
- Make the shared waybill printable and legible, with human-readable reference and barcode/QR compatibility.

**Key MySQL relations**

```text
orders 1--1 parcels
parcels 1--1 waybills
parcels 1--1 shipments
shipments 1--many delivery_tasks
delivery_tasks 1--many delivery_task_offers
shipments 1--many shipment_events
delivery_tasks 1--many custody_evidence
logistics_organizations 1--1 logistics_hubs
logistics_organizations 1--many courier_affiliations
courier_affiliations many--1 users
```

**Key API touchpoints**

- `POST /api/v1/seller/orders/{order}/pickup-request`
- `GET /api/v1/seller/orders/{order}/waybill`
- `GET /api/v1/logistics/pickup-queue`
- `POST /api/v1/logistics/pickup-schedules`
- `POST /api/v1/logistics/tasks/{task}/offer`
- `POST /api/v1/courier/tasks/{task}/accept`
- `POST /api/v1/courier/tasks/{task}/reject`
- `POST /api/v1/courier/tasks/{task}/pickup-confirmation`
- `POST /api/v1/logistics/shipments/{shipment}/receive`
- `POST /api/v1/logistics/shipments/{shipment}/sort`
- `POST /api/v1/logistics/dispatch-batches`
- `POST /api/v1/courier/tasks/{task}/hub-pickup`
- `POST /api/v1/courier/tasks/{task}/delivery-proof`
- `POST /api/v1/courier/tasks/{task}/complete`
- `GET /api/v1/courier/earnings`

**Exit criteria**

- A single seeded order completes Seller -> first-mile Courier -> hub -> final-mile Courier -> Buyer.
- Every custody transition is authorized, transactional, append-only in history, and idempotent.
- A Courier rejection leaves the order unchanged and permits a Logistics re-offer.
- A shared waybill remains immutable across reprints and both delivery legs.

### Phase 5: End-to-End Integration, Docker Consolidation, and Release Readiness

**Goal:** Prove the complete product in an environment that resembles production and make operational risks visible.

**Carl Matthew Castro - Backend/DB**

- Consolidate migrations, foreign keys, indexes, unique constraints, status transitions, and seed data for MySQL.
- Add queue retry policy, notification deduplication, scheduled jobs, audit retention, and operational health checks.
- Complete commission reconciliation, disputes, admin reporting, and authorization review.
- Add database backup/restore procedure, migration rollback guidance, secret handling, and production environment checklist.
- Run API security review: mass assignment, IDOR, upload validation, token scope, rate limits, and tenant isolation.

**Jayward Villanueva - Frontend binding/database consolidation**

- Remove mock data and dead bindings; verify every screen against the versioned API.
- Consolidate shared loading, error, pagination, empty, offline, and retry behavior.
- Build end-to-end fixtures and smoke flows across all four role clients.
- Verify Docker frontend build, asset caching, API base URL configuration, and production environment variables.

**Allianah Pauline Palconan - UI/UX and visual ergonomics**

- Perform responsive review at mobile, tablet, and desktop widths for all role surfaces.
- Conduct keyboard, focus, contrast, form-label, error-message, and screen-reader-oriented checks.
- Refine empty/error/loading/permission-denied states so operational users always understand the next action.
- Prepare a visual regression checklist for the LuboSmart brand system.

**Integration test journey**

```text
Admin approves Seller and Courier
  -> Seller publishes product and stock
  -> Buyer checks out with COD
  -> Seller prepares and requests pickup
  -> Admin dispatch operations schedule first mile
  -> Courier accepts and confirms pickup
  -> Admin dispatch operations receive and sorts
  -> Admin dispatch operations dispatch up to 15 parcels
  -> Courier accepts, picks up, uploads proof, completes delivery
  -> Buyer sees delivered state and submits review
  -> Admin reconciles commission and audit trail
```

**Exit criteria**

- `docker compose up --build` starts the agreed services from a clean checkout.
- CI runs migration, unit, feature, authorization, API contract, and frontend build checks.
- The integration journey passes against MySQL with no manual database edits.
- Logs and health endpoints identify failed workers, database connectivity, and queue backlog.
- Production deployment has documented secrets, backups, monitoring, and rollback steps.

## 6. API and Data Contract

### Common API conventions

- Prefix all new JSON routes with `/api/v1`.
- Use JSON resource representations; never expose Eloquent models directly.
- Return `422` for validation, `401` for missing authentication, `403` for authorization failure, `404` for hidden/not-found resources, `409` for state or idempotency conflicts, and `429` for throttling.
- Use cursor pagination for operational queues and normal pagination for catalog grids.
- Require `Idempotency-Key` on checkout placement, pickup-request commit, dispatch-batch commit, and delivery completion.
- Return a correlation/request ID for support and logs.
- Keep all timestamps UTC in storage and return ISO 8601 values; render Philippine local time in clients.

### Core MySQL table groups

**Identity and governance:** `users`, `buyer_profiles`, `seller_profiles`, `courier_profiles`, `admin_profiles`, `registration_applications`, `documents`, `addresses`, `personal_access_tokens`, `notifications`, `audit_logs`.

**Marketplace:** `shops`, `categories`, `products`, `product_variants`, `product_media`, `inventory`, `carts`, `cart_items`, `vouchers`, `voucher_redemptions`.

**Commerce:** `checkout_batches`, `orders`, `order_items`, `order_addresses`, `order_status_events`, `order_cancellations`, `reviews`, `message_threads`, `messages`.

**Fulfillment:** `logistics_organizations`, `logistics_hubs`, `courier_affiliations`, `vehicles`, `waybills`, `parcels`, `shipments`, `shipment_events`, `delivery_tasks`, `delivery_task_offers`, `dispatch_batches`, `custody_evidence`, `proof_of_delivery`.

**Reporting:** `commission_rules`, `commission_ledger_entries`, `courier_earnings`, `seller_payout_summaries`, `disputes`, `dispute_events`.

### MySQL integrity requirements

- Use InnoDB for all domain tables.
- Use UUID or ULID identifiers consistently; do not mix client-generated business IDs with database identity IDs.
- Add foreign keys and indexes for every tenant key, status queue, lookup key, and timestamp used for sorting.
- Add unique constraints for one Shop per Seller, one active cart per Buyer, one waybill per Parcel, one active first-mile task per Parcel, and one review per eligible Order Item.
- Use decimal columns for money, never floating point. Store currency explicitly as `PHP` at the order/ledger boundary.
- Snapshot product name, variant, price, Seller, address, and selected dispatch operation data into order/waybill records so history does not change when catalog data changes.
- Lock the relevant inventory rows during checkout and use a transaction for reservation changes.
- Use soft deletion only where the business requires history; archive products and Shops rather than deleting referenced commerce records.

## 7. Cross-Cutting Quality and Security

### Functional quality gates

- Unit tests for money, voucher, inventory, status-transition, commission, and authorization rules.
- Feature tests for every role endpoint and every tenant boundary.
- Contract tests for React API bindings and error envelopes.
- End-to-end test for the full order-to-delivery journey.
- Migration test from clean MySQL and from a representative current database.
- Idempotency tests for every externally retried mutation.

### Security controls

- Hash passwords with Laravel defaults; never log passwords or tokens.
- Use Sanctum tokens with revocation and role-aware middleware.
- Validate file MIME, size, dimensions, and storage destination; serve private evidence through authorized endpoints.
- Throttle login, registration, password reset, messaging, uploads, scans, and delivery completion.
- Prevent IDOR with policies on every route model binding.
- Audit Admin approvals, compliance, status overrides, commission adjustments, and dispute resolutions.
- Redact personal data from logs and courier payloads to the minimum operational need.

### Non-functional targets for MVP

- API p95 under 500 ms for ordinary catalog and dashboard reads in the target deployment profile.
- Checkout and custody transitions are atomic and retry-safe, even if notification delivery is delayed.
- Operational queues remain usable on mobile connections and support pagination.
- A failed optional map/geocoding integration never blocks manual Philippine address entry.
- The system can recover queued notifications and failed jobs without duplicating business transitions.

## 8. Delivery Governance

### Definition of done for a feature

1. Business rule and role ownership are documented.
2. MySQL migration, model relationship, policy, request validation, resource, and route are present where applicable.
3. React screen is connected to the real API and includes loading, empty, error, and unauthorized states.
4. Unit/feature tests cover the happy path, authorization boundary, and retry/idempotency behavior.
5. Audit and notification behavior is defined for consequential actions.
6. Docker-based verification passes from a clean environment.

### Change control

- Carl owns the final decision on schema relationships, transaction boundaries, business rules, API contracts, and RBAC enforcement.
- Jayward owns binding correctness, client data flow, and database consolidation implementation in coordination with Carl.
- Allianah owns interaction consistency, accessibility, responsive behavior, and the LuboSmart visual system.
- A schema or status change requires updating the API resource, affected React bindings, tests, and this roadmap’s contract.
- Never introduce a role-specific shortcut that bypasses the shared transition service.

## 9. Deferred Scope and Explicit Decisions

The following are intentionally outside the first production milestone unless separately approved:

- Online payment gateways beyond the current COD flow.
- Returns, refunds, partial fulfillment, and complex delivery-failure inventory restoration.
- Live Courier location telemetry and background route tracking.
- Signature proof, advanced fleet maintenance, vehicle capacity matching, and multi-hub Logistics operations.
- Subscription billing and subscription-based Logistics gating.
- Fully automated third-party route optimization; advisory provider integrations may be added behind a stable internal route contract.
- Multi-Shop checkout redesign beyond one order per Seller within a checkout batch.

These deferrals protect the core LuboSmart promise: a reliable local order can move from discovery to verified delivery with clear ownership at every handoff.

## 10. First Implementation Sprint

The first sprint should produce one vertical slice, not disconnected screens:

1. Confirm the MySQL/Docker baseline and API naming contract.
2. Seed one Admin, Seller, Buyer, Logistics operator, and Courier.
3. Authenticate all roles with Sanctum and enforce policies.
4. Publish one Seller product with inventory.
5. Complete one Buyer COD checkout.
6. Move the order through Seller readiness and one mocked first-mile task.
7. Record the decisions, failing tests, and schema gaps before expanding the flow.

That slice is the fastest proof that LuboSmart’s architecture, team ownership, and operational model agree with one another.
