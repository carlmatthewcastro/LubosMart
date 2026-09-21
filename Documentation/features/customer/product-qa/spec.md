---
feature: product-qa
title: Buyer Product Q&A
system: LUBOSMART
type: Feature Specification
version: 1.2
status: Implemented (Phase 1) — Buyer public read/ask, owning Seller answer API, notifications, and Product Detail Q&A UI are implemented; Seller answer UI is implemented under the Seller Product Q&A contract
role: Buyer
scope: Buyer storefront and owning Seller answer surface through the Laravel API
source_coverage: Documentation/requirements.md, Documentation/workspace.md, Documentation/schema.md, Documentation/domains/Buyer.md, Documentation/domains/Seller.md, Documentation/design.md
---

# Buyer Product Q&A

## WHAT

- **Purpose:** Let a Buyer ask a public, Product-specific question and let the owning Seller publish one official answer for future shoppers.
- **Terminology:** `buyer` is the persisted/API role; “Buyer” is the storefront term. Questions belong to a Product, and Seller authority comes from that Product's Shop ownership.
- **Current state:** Phase 1 is implemented. The API provides public Product-scoped reads, active-Buyer question creation, owning-Seller official answers, actor-scoped idempotency, after-commit notifications, and the Buyer Product Detail Q&A section. A dedicated Seller answer-management screen remains deferred.
- **MVP flow:**

  ```text
  public Product Detail → read paginated Q&A
  authenticated Buyer → submit question
  API → validate visible Product and persist question
  after commit → notify owning Seller
  owning Seller → publish official answer
  after commit → notify asking Buyer
  Product Detail → display the answer publicly
  ```

- **Boundaries:** Q&A is reusable public product knowledge. Chat/Messaging remains private support; Reviews & Ratings remain verified-purchase feedback; Notifications own delivery/read state; Seller catalog owns Product records.
- **Non-goals:** Buyer answers, anonymous questions, private chat mirroring, Q&A attachments, voting, comments, multiple official answers, AI answers, full-text search, Seller ownership changes, and unapproved moderation/edit/delete behavior.

## MUST

### Public read and Product visibility

- `GET /api/v1/products/{product}/questions?page=&limit=` is a public, paginated endpoint.
- Allow guests and authenticated Buyers to read Q&A only when the Product passes `Product::storefrontVisible()` and the Product Detail is publicly accessible.
- Reapply the visibility scope in the Q&A query. A hidden, draft, archived, compliance-restricted, inactive-Shop, vacation-Shop, suspended-Shop, or inactive-Seller Product must return a scoped `404`/empty public result and must not leak its Q&A.
- Use a bounded `limit` (project maximum 50) and deterministic `asked_at DESC, id DESC` ordering. Preserve valid page state and Product scope on every page.
- Public DTOs contain only Q&A data needed for display: Q&A ID, question text, asked time, nullable answer text, answer time, and a safe Seller/Shop label when approved.
- Never expose Buyer email, phone, address, role/status, registration evidence, raw model serialization, or internal moderation notes.

### Buyer question creation

- `POST /api/v1/products/{product}/questions` requires an active Buyer and a UUID `Idempotency-Key` header.
- Require an authenticated active `buyer` through Sanctum. Guests receive `401` and may be redirected to sign-in by the UI, but their question is never stored locally or submitted anonymously.
- Derive the Buyer, Product, Shop, and Seller from the authenticated session and relationships. Reject or ignore client `buyer_id`, `buyer_id`, `seller_id`, role, timestamps, answered flags, and notification recipients.
- Resolve the Product through `storefrontVisible()` inside the mutation. A Buyer may ask about a visible Product without proving a purchase; Q&A is not a review.
- Accept plain text only. Trim and normalize Unicode, reject empty/whitespace-only content, enforce a server-side maximum (initial recommendation: 1,000 characters), and reject malformed input. Client limits are only UX hints.
- Treat the question as untrusted content. Render it as escaped text; do not accept executable HTML, Markdown, scripts, or arbitrary embeds.
- Return `401` unauthenticated, `403` wrong role/inactive account, `404` unavailable Product, `409` idempotency/conflict failure, `422` validation failure, and `429` rate limit. Error bodies follow existing API conventions.
- Rate-limit question creation per Buyer and optionally IP using the project throttle convention. A retry or repeated submit must not create another logical question or notification.

### Seller official answer

- The owning Seller is the only role allowed to answer. Seller authority is derived through `question.product.shop.seller`, never from a request field.
- `POST /api/v1/seller/product-questions/{question}/answer` requires the active Seller that owns the Product's Shop. The endpoint is implemented for the future Seller surface.
- Require an authenticated active `seller`, load the Q&A with its Product/Shop, and return an ownership-safe `403`/`404` for another Seller, Buyer, Admin, or same-email account.
- Store one official answer in the MVP. The mutation must not rewrite the Buyer question and must reject a second concurrent answer with a stable result or `409`.
- Validate answer text as non-empty, normalized, bounded plain text (initial recommendation: 2,000 characters), and escaped on every public render. Answer editing/history is open and must not be invented silently.
- The Seller cannot answer a hidden Product through a public answer flow. Historical Q&A may remain internal when the Product later becomes unavailable.

### Data and consistency

- The additive `product_qas` record contains UUID `id`, UUID `product_id`, UUID `buyer_id`, `question_text`, Buyer-scoped question idempotency/hash fields, nullable `answer_text`, nullable `answered_by_seller_id`, Seller-scoped answer idempotency/hash fields, `asked_at`, nullable `answered_at`, and timestamps.
- Foreign keys and indexes must scope lookups by Product, Buyer, and answer state. The Product relationship is authoritative; `answered_by_seller_id` is an audit value populated from the verified owner.
- Keep one row per question and at most one official answer for the MVP. Use a transaction plus a uniqueness/conditional update guard so concurrent answer attempts cannot create two answers.
- Support an actor-scoped idempotency key for question and answer mutations. Replays return the original committed projection and do not duplicate rows or alerts.
- Q&A writes, answer writes, and any audit/outbox record commit atomically. A notification or queue failure never rolls back a committed question or answer.
- Do not cascade-delete public history merely because a Product, Shop, or Seller is archived. Retention, tombstones, and moderation are separate decisions.

### Notifications

- After a question commits, notify the Seller resolved from Product ownership. Include only Q&A/Product identifiers, a short safe question preview, and an allow-listed answer destination.
- After an official answer commits, notify the Buyer stored on that Q&A. Do not notify every Product viewer.
- Use the existing notification domain and configured channels; the exact database, push, email, or SMS combination is open. Do not require a new provider in this feature.
- Dispatch notifications after commit and deduplicate by Q&A event/actor. Delivery failure is observable and retryable but cannot undo Q&A persistence.

### Buyer and Seller UI

- Add a Q&A section to the existing Product Detail page only after the read API exists. Guests can read; the ask form requires Buyer sign-in and an intentional submit.
- Buyer states: loading, empty, loaded/unanswered, answered, pagination loading, validation error, throttled, unauthorized/login-required, Product-unavailable, network failure, and retry.
- Use semantic headings, a labelled question field, keyboard-accessible pagination and submit controls, visible focus, field-level errors, and announced success/failure states. Never claim success before the API projection returns.
- Seller answer UI belongs to a future Seller Q&A management surface, not to the Buyer application. The Seller feature must consume this same ProductQA ownership contract.
- Do not add Q&A data to shared public caches when it contains Buyer-specific state. Public lists may be cached only as Product-scoped, non-personal projections.

### Acceptance criteria

- [x] Guests can read paginated Q&A only for a currently buyer-visible Product.
- [x] Only an authenticated active Buyer can create a question; guest and cross-role requests are denied.
- [x] Product, Buyer, Seller ownership, answered state, and notification recipient are server-derived.
- [x] Empty, oversized, malformed, executable, and rate-limited question/answer input is rejected safely.
- [x] Each question belongs to one Product and Buyer; a Buyer cannot attach it to another Product or Seller.
- [x] Only the Product-owning Seller can publish one official answer, and the Buyer question remains unchanged.
- [x] Public DTOs contain no private Buyer/Seller data, credentials, evidence, or raw storage paths.
- [x] Product visibility is rechecked for reads and mutations; hidden Products do not expose public Q&A.
- [x] Concurrent/retried question and answer requests are idempotent and do not duplicate records or notifications.
- [x] Notification delivery is after-commit and cannot reverse a committed Q&A decision.
- [x] Buyer Product Detail exposes accessible loading, empty, answered, validation, unauthorized, unavailable, pagination, and retry states.
- [x] The dedicated Seller queue/detail/answer UI is implemented and owned by `Documentation/features/seller/product-qa/spec.md`.
- [x] Q&A remains separate from private Chat/Messaging and verified-purchase Reviews & Ratings.

## HOW

### Current project boundary

- `Documentation/domains/Buyer.md` records the implemented public Buyer question/Seller answer API boundary. `Documentation/domains/Seller.md` keeps the dedicated Seller answer screen deferred.
- `ProductDetailController` continues to expose the core Product projection; `ProductQAController`, `ProductQAService`, the additive `product_qas` migration/model, notification job, and Buyer Product Detail section provide the Q&A contract. Seller answer UI is not part of the Buyer application.
- The implementation includes route/migration/authorization/privacy/idempotency/notification tests. Future Seller UI must consume the existing ownership contract rather than invent a second answer model or route.

### Laravel implementation plan

- The additive migration and `ProductQA` model/relations are implemented. Enum-like fields remain string-backed, and ownership is derived from authenticated users, Product, Shop, and Seller relationships.
- Form Requests, thin Buyer/Seller controllers, a safe `ProductQAResource`, and the shared `ProductQAService` enforce the contract.
- Public reads apply `storefrontVisible()` before pagination. The answer mutation locks the Q&A row; actor-scoped idempotency fields and deterministic after-commit notification jobs prevent duplicate decisions/alerts.
- Existing Buyer/Seller notification resources and the configured queue/database channel are reused. Notification payloads contain only safe identifiers, previews, and destinations.

### React implementation plan

- `ProductQASection` under the existing Product Detail route uses the existing API client and Buyer auth/return-path behavior. It renders escaped plain text, pagination, sign-in gating, field-level validation, throttling, unavailable/network retry, and announced loading/success states.
- Render Q&A as escaped plain text, not `dangerouslySetInnerHTML`. Keep Product Detail's existing safe Markdown renderer limited to the Seller-authored description, not Q&A content.
- A separate Seller dashboard component remains deferred until a Seller Q&A spec defines its route and screen. The implemented Seller answer endpoint is live and covered by the Buyer Product Q&A API contract.

### Verification and rollout

- Laravel `ProductQATest` covers guest/role gates, Product visibility, Product/Seller ownership, validation, pagination, DTO privacy, one-answer conflict, idempotency, notification persistence, and hidden Product behavior.
- The Buyer Product Detail component covers public/empty/answered states, login-required ask, validation/throttle/retry, pagination, unavailable/network retry, escaped rendering, keyboard flow, and announced feedback through the existing webapp lint/type checks. Dedicated Seller answer UI tests remain deferred with that UI.
- Release the migration and API together with the Product Detail Q&A section. Monitor scoped `404`s, validation/throttle rates, duplicate suppression, notification failures, and public DTO errors without logging question/answer contents.
- Open decisions: exact question/answer limits, page size/order, Seller inbox placement, answer edit/history policy, Buyer display label, retention/moderation/reporting, vacation-mode behavior, and notification channels.

**Sources:** `Documentation/domains/Buyer.md`, `Documentation/domains/Seller.md`, `Documentation/requirements.md`, `Documentation/workspace.md`, `Documentation/schema.md`, `Documentation/design.md`, [Laravel Authorization](https://laravel.com/Documentation/12.x/authorization), [Laravel Notifications](https://laravel.com/Documentation/12.x/notifications), [Laravel Validation](https://laravel.com/Documentation/12.x/validation), and [OWASP XSS Prevention](https://cheatsheetseries.owasp.org/cheatsheets/Cross_Site_Scripting_Prevention_Cheat_Sheet.html).
