# MCM Customer Portal

## Authentication

The `/customer-portal` surface uses the `customer` session guard, the `customer_portal_users` provider, and the `customer_portal_users` password broker. Staff accounts cannot authenticate on this guard, and portal accounts cannot authenticate through the admin login.

Invitations store only a SHA-256 token hash, expire after `CUSTOMER_PORTAL_INVITATION_EXPIRY` hours (72 by default), and are invalidated on use or resend. Invitation and reset emails should be processed by the configured Laravel queue worker.

## Visibility And Isolation

All portal queries derive their organization from the authenticated account. Quotes, proforma invoices, and job orders require a non-null `customer_visible_at`; draft proformas and draft job orders are always excluded. Portal PDF and attachment controllers repeat both publication and organization constraints.

## Deployment

1. Back up the database and deploy the application code.
2. Run `php artisan migrate`.
3. Configure `CUSTOMER_PORTAL_NAME`, `CUSTOMER_PORTAL_SUPPORT_EMAIL`, mail transport, and a queue worker.
4. Run `php artisan customer-portal:backfill-legacy` and review every proposed account.
5. Run `php artisan customer-portal:backfill-legacy --apply`. It creates dedicated accounts and disables internal login only when the legacy role contains exactly the portal permission. Use `--keep-legacy-access` during a staged rollout if required.
6. The command never deletes users or clears `organizations.user_id`; accounts with staff permissions remain active internally and must be reviewed manually.

The backfill is idempotent by normalized unique email. Migrated users retain their password hash and must change their password through the dedicated portal flow.
