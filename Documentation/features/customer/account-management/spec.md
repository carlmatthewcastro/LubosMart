---
feature: account-management
title: Buyer Account Management
system: LUBOSMART
type: Feature Specification
version: 2.1
status: Phase 1 implemented
role: Buyer
scope: Buyer web application and Laravel API
---

# Buyer Account Management

## WHAT

- Provide an active Buyer a private self-service Account area for profile identity and account security, without mixing in marketplace data owned by other features.
- The implemented `/account/profile` area supports private profile-photo, personal-detail, and password management; the account navigation links to Profile, Addresses, Wishlist, Orders, and Recently Viewed.
- Address Book, Wishlist, Orders, and Recently Viewed remain separate owning features. This specification owns only Buyer profile/account changes and security settings.
- The Buyer role uses the existing Sanctum web session and `buyer.active` boundary. Guests, inactive accounts, other roles, and arbitrary Buyer IDs cannot read or mutate an account.

- Phase 1 adds editable basic profile fields, a private profile photo, and a secure password change. Phase 2 is explicitly deferred for email changes, notification preferences, MFA, device/session management, and account deletion/export.
- This feature does not change registration approval, role, account status, address records, payment data, order data, Wishlist data, or any Seller/Admin/Courier account.

## MUST

### Current foundation and ownership

- Keep `/account/profile` protected and make every displayed mutation state follow the authoritative Phase 1 API response.
- Keep account navigation links as links to their respective features; Account Management must not reimplement Address Book CRUD, Wishlist, Order history, or Recently Viewed history.
- Every API operation derives the Buyer from the authenticated Sanctum principal and confirms role `buyer` plus active status. Never accept `user_id`, role, status, approval state, or another account identifier in the request body.
- Same-email records in another role remain isolated because the API resolves the authenticated User record, not an email match.

### Phase 1 profile

- Add `GET /api/v1/buyer/account` returning a private, explicit Buyer account DTO: immutable ID/role/status, email read-only, and allowed profile fields.
- Add `PATCH /api/v1/buyer/account/profile` for only `first_name`, `middle_name`, `last_name`, `contact_number`, `sex`, and `birth_date` when those existing `buyer_profiles` columns are supported by the registration/profile policy.
- Validate and normalize input server-side. A profile change must not alter email, password hash, role, account status, registration data, profile-photo path, or another role's profile.
- Return a safe DTO after a successful update. Never return password hashes, Sanctum tokens, raw storage paths, internal registration evidence, Admin notes, or other Buyer data.
- Use `no-store` responses and do not put Buyer profile data in shared homepage or public discovery caches.

### Password change

- Add `PATCH /api/v1/buyer/account/password` only for an active authenticated Buyer. Require `current_password`, `password`, and `password_confirmation`.
- Laravel validates the current password against the authenticated Buyer and applies the same configured strength/confirmation rules as Buyer registration and password reset.
- Rate-limit the endpoint and return safe validation/authentication errors; never log, return, email, or place any password value in an audit payload.
- On success, rotate the current web session and revoke other Buyer personal-access tokens/sessions according to the decided policy. The exact multi-device revocation choice must be implemented and tested consistently, not implied by the UI.
- Password recovery remains owned by Buyer Authentication. This feature changes a password only for an already authenticated Buyer.

### Profile photo upload and Azure Blob storage

- Add `POST /api/v1/buyer/account/profile-photo`, `GET /api/v1/buyer/account/profile-photo`, and `DELETE /api/v1/buyer/account/profile-photo` inside the active-Buyer route group. Upload and removal derive ownership only from the authenticated Buyer.
- The POST accepts one `photo` under the shared upload policy: JPEG/JPG, PNG, or WebP only; strictly under `10 MiB`; decoded as a valid image; detected MIME/signature and normalized extension must agree; corrupt, double-extension, spoofed, and unlisted files are rejected with field-addressable `422` errors.
- Store bytes through Laravel's configured filesystem disk. Production uses the existing `FILESYSTEM_DISK=azure` Azure Blob disk; local/test environments may use their configured disk. Never hard-code a container URL, Azure credential, or disk name in Buyer client code.
- Generate a server-owned UUID filename beneath `buyer-profile-photos/{buyer UUID}/`. Store no client filename or browser blob URL as the object identity.
- Add an additive Buyer-profile migration for `profile_photo_disk`, `profile_photo_mime`, `profile_photo_size`, `profile_photo_width`, and `profile_photo_height`. Retain `profile_photo_path` as the generated relative path; do not modify the executed Buyer-profile creation migration.
- The account DTO returns a safe owner-only `profilePhotoUrl` pointing to the authorized GET endpoint, optionally cache-busted by profile update time. It never returns disk, path, blob URL, credentials, raw upload metadata, or an Azure signed URL.
- The GET endpoint streams the stored object only to its owning active Buyer with `private, no-store` and `nosniff` headers. Profile photos are not public marketplace assets.
- Replacement writes and validates the new object before atomically updating metadata. If the database update fails, remove the new object; after a committed replacement/removal, delete the old object best-effort without restoring stale metadata on deletion failure.
- Apply focused upload throttling, safe operational logging, and the shared maximum-dimension/decompression-bomb protections when those policy values are approved. Do not claim malware scanning exists until a scanner and pending/quarantine lifecycle are implemented.

### Concurrency, errors, and privacy

- Profile/password writes must be transactional for the User/Profile rows they touch. Concurrent retries must not partially apply a profile update or leave an unusable password/session state.
- A stale or unauthenticated request returns the project's normal `401`/`403`; a valid Buyer attempting a forbidden field receives `422`, not a silently ignored mutation.
- Buyer-facing errors may explain how to recover but must not disclose another account, registration state, token, or password detail.
- Changes to name/profile data should refresh the authenticated navigation display after the API confirms success. Never optimistically claim a password or profile update succeeded after a failed request.

### Deferred capabilities

- Email change is deferred. It requires a unique email/role policy, current-password confirmation, verification of the new address, collision handling, notification to the old address, and session/token consequences.
- Notification preferences are deferred until Buyer notification types, delivery channels, defaults, consent, and durable preference storage are specified.
- MFA, remembered devices, session list/revocation UI, account deactivation/deletion, export, and formal security-event/audit visibility are separate approved features or policies.

### Buyer experience and accessibility

- `/account/profile` remains protected by the existing same-origin `next` redirect pattern. A signed-out visitor returns to the requested Account route only after successful active-Buyer sign-in.
- Phase 1 presents profile and password as separate forms with clear required-field labels, inline validation, saving/success/error states, disabled duplicate submission, and focus moved to the relevant error or confirmation.
- Do not render an editable email, status, role, or approval control as if it were functional. Link Buyers to Addresses, Orders, Wishlist, and Recently Viewed rather than duplicating their contents.
- The profile photo control states accepted JPEG/PNG/WebP formats and the 10 MB maximum before selection, gives client-side early feedback only, shows transfer/persistence progress and server errors, and updates the visible photo only after Laravel confirms success. Local preview object URLs are revoked on replacement/unmount.
- Maintain responsive keyboard-accessible account navigation and use `autocomplete` attributes suitable for name, telephone, birth date, current password, and new password fields.

### Acceptance criteria

- [x] An active Buyer can open the protected `/account/profile` page; guests are redirected to sign in.
- [x] Address, Wishlist, Orders, and Recently Viewed remain distinct Buyer Account navigation destinations.
- [x] The account API and Phase 1 forms expose and update only the authenticated active Buyer's allow-listed profile fields.
- [x] A forged Buyer ID, another role, inactive account, forbidden field, or cross-role same-email record cannot read or change the Buyer profile.
- [x] Password change requires the correct current password, confirmed policy-compliant replacement password, throttling, and safe session/token handling.
- [x] A Buyer can upload, replace, view, and remove only their own JPEG/PNG/WebP profile photo under 10 MiB through the configured Azure Blob/local disk.
- [x] Spoofed, corrupt, oversized, multiple-extension, cross-account, and unauthenticated photo requests are rejected; raw blob paths/credentials are never returned.
- [x] Private account responses and errors never expose tokens, hashes, raw media paths, registration evidence, or another Buyer's data.
- [x] UI loading, validation, retry, success, upload progress, and keyboard/focus states work without falsely reporting a saved change.

## HOW

### Existing project integration

- Reuse `BuyerProfile`, the shared `User` identity, existing Buyer Auth session restoration, `buyer.active` middleware, Buyer protected-route shell, and Account navigation.
- Use the dedicated allow-listed `BuyerAccountResource` for account data; `BuyerUserResource` remains limited to its registration/authentication contexts and must not serve Account Management.
- Keep `BuyerAddressController`, Wishlist, Orders, and Recently Viewed endpoints untouched except for normal navigation/auth refresh integration.
- Phase 1 profile fields need no schema change; profile photo uses the required additive metadata migration. Do not modify previous migrations.

### Laravel API

- Add a Buyer-scoped `AccountController`, Form Requests, and an `AccountService` inside the existing `v1/buyer` authenticated group.
- Load exactly the authenticated `User` and `buyerProfile`; use a transaction and explicit allow-list for profile writes. Return `BuyerAccountResource` with camelCase fields aligned to Buyer web types.
- Validate password changes with Laravel's current-password validation and configured Password rule. Use the existing hash/session/token conventions rather than inventing an alternative credential store.
- Set private cache headers, CSRF protection for the web session flow, and focused throttling for sensitive mutations. Record only redacted operational/security events if an approved Buyer audit policy exists.
- Mirror the existing Admin/Seller account-photo service pattern with a Buyer-scoped service and Form Request, but use the shared upload reference as the authority. Inspect/decode the image server-side, generate the path, write it to `Storage::disk(config('filesystems.default'))`, persist metadata transactionally, and serve it only through the owner-authorized endpoint.

### Buyer application

- Add private API helpers/types separate from public marketplace fetch helpers, with credentialed no-store requests and safe retry behaviour.
- Replace the read-only-profile notice only when the GET/PATCH profile contract exists. Keep password editing isolated in a Security section; do not collect a current password for ordinary profile edits unless a future policy requires re-authentication.
- Add a separate profile-photo component using multipart upload. It must display the existing private delivery endpoint, accessible replacement/removal controls, format/size guidance, upload/pending/error states, and an image fallback without converting the file to Base64.
- Refresh the shared Buyer auth/navigation state after a confirmed name change. On password success, follow the implemented session policy and show the resulting sign-in/session state clearly.
- Keep the generic `/account/[[...segments]]` unavailable-state page for deferred settings paths; do not manufacture empty preferences or MFA screens.

### Testing and rollout

- Laravel tests: active Buyer self-read/update; guest/inactive/other-role denial; allow-list enforcement; same-email role isolation; validation; concurrent profile write behaviour; current-password failure; password policy/confirmation; rate limit; session/token outcome; safe resource/error payloads; and no-store headers.
- Photo tests: accepted formats/exact byte boundary; MIME/extension/signature spoofing; corrupt/dimension failures; generated Azure/local path; metadata persistence; owner-only delivery/replacement/removal; rollback cleanup; old-object cleanup; throttling; and no raw storage-path response.
- Buyer tests: protected redirect/return; initial read-only state; profile/photo/password validation/success/failure; blocked file feedback; upload progress; private image refresh; disabled duplicate submits; navigation refresh; focus/keyboard behaviour; and responsive account navigation.
- Run focused Buyer API tests, storefront lint, strict JavaScript, and production build. Append a dated `Documentation/PROGRESS.md` implementation entry only when a phase is built; this revision is documentation-only.

### Open implementation choices

- Decided: a successful password change rotates and preserves the current web session or current bearer token, while revoking the Buyer's other Sanctum personal-access tokens. Other cookie sessions follow the configured session lifetime because the platform does not yet expose a durable session registry.
- Confirm which existing personal fields remain editable after registration and whether changing birth date requires an age/verification review rule.
- Decide email-change verification, notification, and collision policy as its own approved sub-feature.
- Decide whether Buyer security/profile changes need a Buyer-visible history or an internal redacted audit event.

### Sources

- Existing foundation: `resources/js/app/(buyer)/account/profile/page.tsx`, `resources/js/components/account/account-navigation.tsx`, `app/Http/Controllers/Buyer/AuthController.php`, `app/Models/BuyerProfile.php`, `app/Services/Seller/SellerAccountService.php`, `app/config/filesystems.php`, and `Documentation/schema.md`.
- Shared policy: `Documentation/references/file-upload-requirements.md`.
- [Laravel password confirmation/current-password support](https://laravel.com/Documentation/12.x/middleware) and [Laravel password validation](https://laravel.com/Documentation/12.x/validation) support the intended authenticated password-change boundary.
- [OWASP Authentication Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Authentication_Cheat_Sheet.html) supports re-authentication and secure handling for sensitive credential changes.
- [Laravel file uploads and configured disks](https://laravel.com/Documentation/12.x/requests#storing-uploaded-files) supports disk-agnostic storage through the configured filesystem.
