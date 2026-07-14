# MCM Customer Portal — Codex Implementation Prompt

You are working inside [`Syed-Sabeer/MCM-Software`](https://github.com/Syed-Sabeer/MCM-Software), a mature Laravel 10 / PHP 8.2 modular CRM, quotation, procurement, production, and warehouse system. Implement a complete, production-quality **customer portal** by refactoring and extending the partial portal already in the repository. Preserve the existing architecture and UI.

Do not only produce a plan. Inspect the repository, implement the feature, run the relevant tests/build, and report the finished changes and any genuine blockers.

## Start Here

Before editing:

1. Read `AGENTS.md` first, then `config/concord.php`, `docs/document-ui-engine.md`, active package/root migrations, and the relevant models. Follow the source-of-truth order in `AGENTS.md`; `SQL/V8.sql` is historical/schema context, not a replacement for active migrations and code.
2. Inspect `git status` and preserve all unrelated/user changes.
3. Search the entire repository for existing or partially started customer/portal code, guards, routes, controllers, models, views, mail templates, organization create/edit/view screens, policies, shared layouts, document components, and tests. **Extend working code; do not create a competing implementation.**
4. Trace the real organization creation flow behind the admin customer/company pages, including the equivalent of `/admin/customers/organizations/view/{id}`. Identify the owning module, repositories/services, events, validation, and route registration before changing anything.
5. Verify every class/path named below against the checked-out repository. The requirements are authoritative; example names are not permission to invent duplicate layers.

## Existing Architecture and Data Facts

- Laravel 10, PHP 8.2, Vite, Blade, Webkul/Krayin-style modules registered through Concord, and Prettus repositories.
- Business behavior primarily lives in `packages/Webkul/*`; prefer module-local changes. `Modules/` is present but is not the primary core architecture.
- Reuse the existing admin/customer layouts, Blade components, design tokens, responsive patterns, datagrids, form controls, badges, alerts, empty states, pagination, and shared document engine. Do not introduce a new CSS framework or visually redesign MCM.
- `organizations` represents customer/vendor companies; `persons.organization_id` represents company contacts.
- `users`/`roles` and the `user` guard are the internal staff authentication/authorization system. The partial portal incorrectly reuses them. Do not continue that coupling or give customers internal CRM roles.
- `organizations.user_id` belongs to the existing internal-user relationship and is currently overloaded by the partial portal as a one-user login link. Stop using it for portal authentication. Do not destructively repurpose/drop the column because existing CRM behavior and data may rely on it.
- Customer-owned records are already related through:
  - `persons.organization_id`
  - `products.customer_organization_id`
  - `quotes.organization_id`
  - `proforma_invoices.organization_id`
  - `job_orders.organization_id`
- Quote → proforma invoice → job order → job cards is the customer order/process chain.
- Procurement, vendors, purchase orders, goods receipts, warehouses, material requirements, internal costs, internal remarks, audit activities, and staff-only notes are confidential.
- Commercial document views must reuse the shared document renderer (`admin::components.documents.standard` and its logical blocks) instead of cloning document markup.
- Monetary columns are exact decimals, commonly `decimal(12,4)`; never calculate them as floats.

## Existing Partial Portal — Refactor It, Do Not Duplicate It

The following code already exists and must be evolved in place where appropriate:

- route prefix and names: `packages/Webkul/Admin/src/Routes/Front/web.php` (`/customer-portal`, `customer_portal.*`)
- middleware alias/registration: `packages/Webkul/Admin/src/Providers/AdminServiceProvider.php`
- current middleware: `packages/Webkul/Admin/src/Http/Middleware/CustomerPortal.php`
- current controller: `packages/Webkul/Admin/src/Http/Controllers/CustomerPortal/PortalController.php`
- current portal UI shell and pages: `packages/Webkul/Admin/src/Resources/views/customer-portal/**`
- organization create/edit/show flow: `packages/Webkul/Admin/src/Http/Controllers/Contact/OrganizationController.php`, `packages/Webkul/Admin/src/Routes/Admin/contacts-routes.php`, and `packages/Webkul/Admin/src/Resources/views/contacts/organizations/{create,edit,view}.blade.php`
- organization persistence: `packages/Webkul/Contact/src/Repositories/OrganizationRepository.php` and `packages/Webkul/Contact/src/Models/Organization.php`
- shared staff login branching: `packages/Webkul/Admin/src/Http/Controllers/User/SessionController.php`

Known problems to correct:

- customer login uses the internal `user` guard and admin login page
- customer detection relies on role names containing `customer`, `portal`, or `client`, or a string permission lookup
- one portal user is linked through `organizations.user_id`
- all organization documents are returned without explicit customer publication, so drafts/internal records can appear
- the portal controller contains direct repeated queries instead of policies/scopes/services
- product detail eager-loads and displays `consumptions`, and displays `internal_code`; remove these internal manufacturing details
- portal money/quantity formatting casts exact decimal values to float and hardcodes `PKR`; use the existing configured currency/decimal conventions
- there are no dedicated invitation, password reset, company, contacts, profile/security, protected PDF, or portal-user management flows

Keep the existing `/customer-portal` URL family, route-name family, and `admin::customer-portal.layouts.app` visual shell unless a concrete conflict requires a compatible redirect. Add missing pages/components to this surface; do not build a second portal beside it.

## Required Architecture

### 1. Separate Customer Authentication Boundary

Use a dedicated customer portal guard/provider and customer account model/table (use an existing partial implementation if sound). Do **not** authenticate portal customers through the internal `users`/roles guard.

The portal account design must support multiple users per organization and include, at minimum:

- `organization_id` (required)
- optional linked `person_id`
- name and unique normalized email
- hashed password
- active/suspended status
- organization role such as `organization_admin` or `member`
- optional granular permissions using the project’s established convention
- email verification/invitation timestamps
- last-login timestamp
- remember token and timestamps as appropriate

Use proper foreign keys, indexes, unique constraints, casts, hidden password/token attributes, relationships, factories, and migrations. Never store or log plaintext passwords.

Register a dedicated `customer` (or clearly named `customer_portal`) guard, provider, and password broker in `config/auth.php`. Refactor the existing `customer_portal` middleware alias to authenticate this guard. Remove the customer-role sniffing/redirect branches from the internal `SessionController`; internal staff login and customer login must be separate and must not accept each other’s accounts.

Provide a safe legacy migration path for organizations currently linked to qualifying portal-only internal users through `organizations.user_id`:

- create a dedicated, idempotent backfill command/service with dry-run output rather than hiding destructive data movement inside a schema migration
- only migrate customer organizations and users that unambiguously match the existing portal-role/permission convention
- preserve the existing password hash so migrated customers can continue signing in, then require a password change or verification as appropriate
- do not delete staff/user rows or clear `organizations.user_id` automatically
- prevent migrated portal-only legacy accounts from retaining usable internal-admin access after cutover, without disabling genuine staff accounts
- document the command and deployment order and test idempotency

Implement a secure invitation/set-password and reset-password flow:

- cryptographically secure, hashed, one-time token
- configurable expiry (default 72 hours for invitations)
- token invalidated after successful use
- rate-limited login, forgot-password, resend-invitation, and token endpoints
- session regeneration on login and session invalidation on logout/password reset
- generic forgot-password responses to prevent email enumeration
- inactive/suspended accounts cannot authenticate

Use Laravel’s established auth/password/mail facilities and the repository’s mail/template conventions. Do not hand-roll insecure token logic.

### 2. Organization Creation and Portal Access Management

Replace the current single **Portal User** dropdown (`user_id`) in the existing organization create/edit views with a well-designed **Customer Portal Access** section. Show it only when organization type is `customer`.

Required controls:

- `Create portal access` checkbox
- portal user/contact name
- portal login email (prefill from the selected/primary contact when available)
- optional link to an existing person/contact in that organization
- role: organization admin/member (default organization admin for the first account)
- credential method:
  - **Send secure invitation to set password** (recommended/default)
  - **Set temporary password manually**, with confirmation and current password-policy validation
- `Send invitation/login email` checkbox, default checked

Behavior:

- Organization and portal account creation must be validated and performed through the owning service/action/repository in a database transaction.
- Only a customer organization may receive customer portal access.
- Duplicate emails must return a clear validation error without partially creating data.
- Dispatch email only after the transaction commits, preferably through the existing queue convention.
- Email failure must not delete an otherwise valid organization/account. Record/log failure safely and allow resend.
- The email may show the login email, portal URL, expiry, support guidance, and secure set-password link. **Never email a plaintext or temporary password.** If an admin sets a temporary password, require the customer to change it on first login and send a set-password/reset link instead of the password.
- If `Send invitation/login email` is unchecked, create the account and let the admin copy the invitation link once or resend later, without exposing stored tokens afterward.
- Existing customers without access must be supported from `admin::contacts.organizations.view` (the route behind `/admin/customers/organizations/view/{id}`).
- Do not put credential orchestration directly into the already-large `OrganizationController`; introduce focused FormRequests and a transaction/action/service while preserving the controller’s existing events, dynamic attributes, address normalization, and route-specific redirects.

Add a **Portal Access** tab/card to the existing organization detail UI. It must list all portal users for that organization and allow authorized staff to:

- create/invite another user
- see verified/invited/active/suspended state and last login
- resend invitation or password setup
- copy a fresh invitation link where permitted
- suspend/reactivate access
- revoke access with confirmation
- update role/permissions

Protect these operations using the existing admin authorization system and audit the actions without logging secrets.

### 3. Customer Portal Route and UI Surface

Keep the current `/customer-portal` prefix and `customer_portal.*` route names. Split guest and authenticated route groups cleanly, supply dedicated customer guest/auth middleware, and prevent guard crossover. Add `/customer-portal/login`; stop redirecting customer guests to `admin.session.create`.

Required guest pages:

- Login
- Accept invitation / set password
- Forgot password
- Reset password

Required authenticated pages:

1. **Dashboard**
   - customer company identity and welcome panel
   - counts for visible/open quotations, proforma invoices, active orders, upcoming deliveries, and outstanding proforma balance
   - recent customer-visible documents
   - active order progress cards
   - clear empty states and responsive layout

2. **My Company**
   - safe organization details, billing/shipping addresses, phone, website, industry, and description
   - read-only initially unless an existing approved customer-update workflow already exists

3. **Contacts**
   - contacts where `persons.organization_id` equals the authenticated account’s organization
   - show only appropriate business fields; do not expose internal ownership/audit fields

4. **Products**
   - products where `customer_organization_id` equals the authenticated organization
   - safe customer-facing details and images only; never show cost price, internal sourcing, stock internals, or unpublished/internal-only data
   - specifically remove the current `consumptions` eager load/material-consumption panel and `internal_code` output from the portal

5. **Quotations**
   - paginated/searchable list and detail pages scoped to `quotes.organization_id`
   - line items, totals, commercial terms, dates, status, attachment, and PDF/print download through the shared document engine

6. **Proforma Invoices / Payments**
   - scoped list/detail views for `proforma_invoices.organization_id`
   - items, totals, received amount, remaining amount, due date, payment term, customer PO reference, customer-safe receipts, attachment, and document download
   - this is not permission to invent a separate final invoice system

7. **Orders & Production Progress**
   - orders scoped to `job_orders.organization_id`
   - show order number, subject, customer PO reference, issue/delivery dates, ordered items/quantities, and a polished progress timeline
   - centralize the customer-facing mapping in one enum/service/presenter; the existing job-order statuses are `draft`, `open`, `in_progress`, `ready_to_ship`, `completed`, `closed`, and `cancelled`, while job cards/sections begin with `open`/`not_started`
   - use accurate labels supported by existing data (for example Order Confirmed → In Production → Ready to Ship → Completed). Do not claim separate material/quality/dispatched milestones unless the stored workflow can actually prove them
   - show only aggregated customer-safe progress; never expose job-card remarks, section instructions, material requirements, vendor identities/quotes, purchase orders, costs, warehouse details, or internal staff activity

8. **Profile & Security**
   - update own name and allowed contact data
   - change password with current-password confirmation
   - show last login and allow secure logout from the current session; add other-session logout only if supported cleanly by the existing session setup

All screens must match the current MCM visual language and work on desktop/tablet/mobile. Use breadcrumbs, status badges, tables/cards, loading/empty/error states, accessible labels, keyboard focus, pagination, and existing shared components. Avoid decorative redesign or duplicated component systems.

### 4. Visibility and Tenant-Isolation Rules

This is the highest-priority requirement.

- Every portal query must derive `organization_id` from the authenticated customer account. Never accept it from request input, query strings, hidden fields, or route parameters.
- Enforce ownership in policies/query scopes/services and again on downloads/actions. Hiding links in Blade is not authorization.
- A customer changing an ID in the URL must never see another organization’s record; use a consistent 404/403 policy matching the application.
- Do not serialize full admin models into portal views. Build explicit customer-safe projections/view models/resources.
- The repository currently has no reliable customer-publication mechanism. Add a minimal explicit, indexed `customer_visible_at` mechanism for quotes, proforma invoices, and job orders, with admin Publish/Unpublish actions on the existing document/detail surfaces and centralized `visibleToCustomer()` scopes. Existing rows default to private. Do not equate quote `open` with published. Proforma `draft` and job-order `draft` must always remain hidden. Auto-publication is allowed only on an already-verified send/issue/approval transition and must be covered by tests.
- Do not expose generic `activities` or system audit history: it contains internal notes and field changes. Only display records explicitly marked customer-visible through an existing mechanism; otherwise omit the timeline.
- Protect attachments/PDFs through authenticated controllers and authorization. Never expose private storage paths as public URLs.
- Do not link to internal `admin.quotes.print` or `admin.proforma_invoices.print` routes from the portal. Add customer-guarded download actions that authorize organization ownership + publication, then reuse the existing `admin::quotes.pdf`, `admin::proforma-invoices.pdf`, and shared document engine data preparation.
- Escape customer-controlled content and preserve existing file validation/download conventions.

### 5. Maintainable Implementation

- Keep controllers thin. Put credential creation, invitation delivery, dashboard aggregation, document visibility, tenant scoping, and progress mapping in focused services/actions/scopes/policies.
- Reuse module repositories, events, mail templates, Blade components, the current customer-portal layout, datagrids, and the shared document engine.
- Avoid N+1 queries; eager-load deliberately and paginate large lists.
- Use indexed foreign keys/status/visibility fields needed by portal queries.
- Use config/translations for portal name, invitation expiry, support email, and labels; do not hardcode localhost URLs or company-specific secrets.
- Preserve all current admin behavior, document calculations, state transitions, and routes.
- Update `agent.md` and schema/domain documentation so future agents understand the new guard, tables, routes, visibility rules, and workflow. Update `SQL/V8.sql` only if that snapshot is intentionally maintained by the repository’s normal workflow; migrations remain the executable source of change.

## Tests and Verification

Add focused feature/unit tests using the project’s current conventions. Cover at minimum:

- admin can create a customer organization with portal access
- portal fields are rejected/ignored for vendor organizations
- organization/account transaction and duplicate-email validation
- checked email option dispatches the expected mail after commit; unchecked does not
- invitation token expiry, one-time use, verification, password setup, and resend invalidation
- login success, throttling, logout, reset password, forced password change, and suspended account denial
- multiple portal users in one organization and role/permission enforcement
- strict cross-organization isolation for every index/show/download/action, including manually changed IDs
- drafts/unpublished records are hidden
- portal views never expose internal cost, vendor, procurement, warehouse, job-card remarks, or audit data
- dashboard aggregates and outstanding balances are correct using decimal-safe calculations
- organization view portal-management operations are admin-authorized
- existing admin customer/company and commercial-document tests remain green

Run the narrowest relevant test suites first, then the broader affected suite. Run the repository’s formatter/static analysis and frontend build if configured. Fix failures caused by this work. Do not silently weaken tests.

## Completion Criteria

The work is complete only when:

- an admin can create a customer and optionally create portal credentials in the same workflow
- the invitation email checkbox works safely and invitations can be resent
- existing customers can receive/manage multiple portal accounts
- a customer can securely log in and see only their company, contacts, products, customer-visible quotations, proforma/payment information, and customer-safe order/production progress
- the portal looks native to the existing MCM UI
- direct URL manipulation and downloads cannot cross organization boundaries
- internal operational/commercial data is not leaked
- migrations, tests, docs, and verification are included

## Final Response Format

When finished, report concisely:

1. architecture/guard and schema changes
2. admin workflow changes
3. portal pages and customer-visible data
4. security/tenant-isolation measures
5. tests and commands run with results
6. any migration, queue worker, mail configuration, or deployment steps required
7. any remaining blocker, with exact evidence

Do not claim completion for unimplemented sections, do not replace mature existing code with a prototype, and do not create parallel customer/company models when the current organization/person models already own that business data.
