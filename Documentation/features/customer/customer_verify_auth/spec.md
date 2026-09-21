---
feature: buyer_verify_auth
title: Buyer Auth-Aware Navigation and Route Access
system: LUBOSMART
type: Feature Specification
version: 1.1
status: Implemented
role: Buyer
scope: Buyer storefront and Laravel session integration
---

# Buyer Auth-Aware Navigation and Route Access

## WHAT

- **Purpose:** Keep the React storefront aware of the current active Buyer session while leaving public shopping public.
- **Current implementation:** A single shared `AuthProvider` performs a deduplicated browser `GET /api/v1/buyer/auth/me` check per app load, retains identity in memory across navigation, and exposes `loading`, `guest`, or `authenticated` state to the navbar and route boundary.
- Guests can browse homepage, shops, search, product detail, and other public marketplace pages. Private Buyer APIs remain protected by Laravel, not by the UI.
- The signed-in AccountMenu links to Profile, Addresses, Orders, Wishlist, Settings, and Logout. Auth-only `/login` and `/register` routes redirect an active Buyer to `/`.
- **Canonical terms:** `buyer` is the API/database role; “Buyer” is storefront copy only. Seller, Admin, Logistics, and Courier apps keep separate session/routing contracts.
- **Non-goals:** implementing registration/login business rules, account/profile features, role switching, a React API authorization layer, or Courier UI.

```text
app load → one credentialed Buyer /me check
→ loading (no private render) → guest or active Buyer
→ public shell or protected route boundary
→ login/logout/session signal → update cache → revalidate through /me
```

## MUST

### Session authority and bootstrap

- Treat Laravel's `auth:sanctum` + `buyer.active` response as the only authentication/authorization authority. A cookie or client state is a routing hint, never proof.
- Call `/api/v1/buyer/auth/me` once per app load through the shared provider; deduplicate concurrent calls. Keep the result in memory only—never store session credentials or trusted login flags in `localStorage`, `sessionStorage`, IndexedDB, or URL parameters.
- While state is `loading`, do not render private Buyer data or flash a guest Sign-in control in place of a known session. If startup fails with a network/5xx error, retain no unverified identity and show a retryable state.
- A confirmed `401` or explicit Buyer role/status denial clears the cached session. Ordinary resource `403`, `429`, and `5xx` responses do not by themselves log the Buyer out.
- A `419` CSRF response triggers one deduplicated `/me` recheck; never replay commerce mutations automatically. An online-recovery retry may resolve an unresolved startup check.

### Public, protected, and auth-only routes

- Keep public browsing available to guests: `/`, `/shops`, `/shops/{slug}`, product/search/detail pages, and other routes owned by their feature specs.
- Protect `/account/*`, `/cart`, `/checkout`, `/orders`, `/notifications`, and other Buyer-private pages with `AuthRouteBoundary` for UX. Each Laravel API independently enforces `auth:sanctum`, `buyer.active`, and ownership.
- A guest visiting a protected path is redirected to `/login?next=<validated same-origin path/query>`. Validate return paths; reject external URLs and unsafe schemes.
- An active Buyer visiting `/login` or `/register` is redirected to `/`; pending/rejected/inactive states remain in the status-specific auth flow rather than being treated as active.
- After login, use the validated `next` path or `/`; after logout, return to guest navigation and `/`.

### Navigation and logout

- Render guest Sign in/Register controls only for `guest`; render an accessible profile trigger and AccountMenu for `authenticated`.
- The menu opens by click, Enter, or Space; closes on Escape, outside click, and selection; uses labels/focus/ARIA and 44px-compatible controls. Keep all destinations available on mobile.
- AccountMenu destinations are `/account/profile`, `/account/addresses`, `/orders`, `/account/wishlist`, and `/account/settings`. Notifications remain available through the navbar notification control.
- Logout initializes CSRF and calls `POST /api/v1/buyer/auth/logout` with credentialed cookies. On success clear cached identity and broadcast a sign-out signal. On failure keep identity and show a recoverable error.
- Login responses may update the cached identity, but a session-change signal must be verified by `/me`; signals carry no credentials or Buyer data.

### Cross-tab, privacy, and acceptance

- Use same-origin `BroadcastChannel("lubosmart-buyer-session")` when available for `signed-out` and `session-changed`; close it on unmount and tolerate unsupported browsers.
- Do not poll `/me` on every page change, focus, or render. Explicit retry, CSRF failure, startup network recovery, and another-tab session signals are the only revalidation triggers.
- Private personalized data must use `cache: "no-store"`/private responses and be fetched from its owning API. A frontend redirect is never the data-security boundary.
- [x] Guests see public shopping and no personal AccountMenu data.
- [x] Active Buyers restore through one deduplicated `/me` check and keep identity across client navigation.
- [x] Protected paths preserve a safe login return and reject guest access at the UI while APIs enforce role/status/ownership.
- [x] Active Buyers are redirected away from `/login` and `/register`.
- [x] Logout, stale session failures, CSRF recheck, cross-tab signals, loading, and retry states are handled without token storage.
- [x] AccountMenu links and controls are keyboard/focus accessible and include Addresses/Orders.

## HOW

### Current implementation

- `resources/js/components/auth/auth-provider.tsx` owns the deduplicated session controller, `/me` call, in-memory state, logout, retry, CSRF failure handling, and BroadcastChannel signals.
- `resources/js/components/auth/auth-route-boundary.tsx` handles UI-only protected/auth-only redirects; `resources/js/lib/auth/navigation.ts` classifies protected paths and validates returns.
- `resources/js/components/marketplace/account-menu.tsx` and the shared navbar render the responsive profile menu; App Router layouts wrap the storefront with `AuthProvider`.
- Laravel's current-user endpoint is `GET /api/v1/buyer/auth/me`; login/logout and status/error semantics are defined by `buyer-auth/spec.md`.

### Test and ownership contract

- Auth regression tests cover one-call bootstrap/deduplication, guest/authenticated navigation, safe return paths, active-only redirects, logout success/failure, stale responses, `401`/`403`/`419`, network retry, BroadcastChannel signals, and no token persistence.
- UI checks cover keyboard menu behavior, focus/error announcements, no private-content flash, mobile navigation parity, and public browsing during auth failure.
- Do not add a second page-level `/me` query or a React API proxy that becomes an authorization substitute. Extend the shared provider/route boundary only when a new Buyer-private destination is approved.
- Related contracts: `Documentation/features/buyer/buyer-auth/spec.md`, `Documentation/design.md`, `Documentation/workspace.md`, `Documentation/architecture.md`, and the owning Account, Cart, Checkout, Order, Wishlist, and Notification specs.
