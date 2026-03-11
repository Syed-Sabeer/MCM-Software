# AGENTS.md

## Purpose
This repository is a Laravel 10 + Krayin CRM codebase extended for ERP-style workflows. It is not a plain Laravel monolith. Most business logic lives inside `packages/Webkul/*` modules, with the admin UI assembled from package routes, package controllers, Blade views, reusable Blade components, Vue widgets embedded inside Blade templates, repositories, and Concord model proxies.

An AI agent working in this repo should treat it as a modular package-based application with these goals:
- preserve Krayin package conventions
- avoid fighting the existing attribute/entity system
- prefer extending package modules over adding duplicate app-layer logic
- keep backward compatibility with legacy CRM behavior where possible

This file explains how the project is organized, how data flows, and how an AI agent should safely change the code.

## High-Level Stack
- Backend: Laravel 10, PHP 8.2
- Modular framework layer: Konekt Concord
- Repository pattern: `prettus/l5-repository`
- Admin UI: Blade + reusable Blade components + inline Vue components
- PDF generation: `barryvdh/laravel-dompdf` and `mpdf/mpdf`
- Datatables/listing system: custom Webkul `DataGrid`
- Dynamic attributes: Webkul `Attribute` package via `CustomAttribute` trait
- Activity logging: Webkul `Activity` package via `LogsActivity` trait
- Storage: standard Laravel `storage/app/public`, served from `public/storage`

## Top-Level Directory Map
- `app/`: minimal application-level Laravel code
- `bootstrap/`: Laravel bootstrap
- `config/`: global Laravel and Concord configuration
- `database/`: base Laravel migrations/seeders/factories
- `packages/Webkul/`: main business modules and most source code
- `public/`: web root and symlink target for public files
- `resources/`: global non-package assets/views when used
- `routes/`: root Laravel routes for base app bootstrapping
- `storage/`: logs, compiled views, cache, uploaded files via disks
- `tests/`: currently minimal Pest test suite
- `Modules/`: present but not the primary architectural pattern for core CRM code
- `SQL/`, `database.sql`, `db-crm.sql`, `sql-updates.sql`: legacy/manual database artifacts; inspect before doing DB work, but do not assume they are the canonical source of truth over migrations currently in use

## Source of Truth
For active application behavior, use these sources in this order:
1. `packages/Webkul/*/src` code
2. `config/*.php`
3. package migrations under `packages/Webkul/*/src/Database/Migrations`
4. root `database/migrations`
5. current database schema
6. SQL dump files only as historical references

Do not assume old SQL dumps reflect the live schema.

## Package Architecture
The project is split into package modules under `packages/Webkul/*`.

Important modules currently present:
- `Activity`
- `Admin`
- `Attribute`
- `Automation`
- `Contact`
- `Core`
- `DataGrid`
- `DataTransfer`
- `Email`
- `EmailTemplate`
- `Installer`
- `Lead`
- `Product`
- `PurchaseOrder`
- `Quote`
- `Tag`
- `User`
- `Warehouse`
- `WebForm`

### How modules are registered
Concord module registration is defined in:
- [config/concord.php](e:/xampp/htdocs/crm/config/concord.php)

Global service providers are registered in:
- [config/app.php](e:/xampp/htdocs/crm/config/app.php)

Most package modules use:
- [BaseModuleServiceProvider.php](e:/xampp/htdocs/crm/packages/Webkul/Core/src/Providers/BaseModuleServiceProvider.php)

That provider automatically registers:
- migrations
- models
- enums
- request types
- views
- routes

This means adding a package migration/model/view/route in the correct package location is usually enough if the package provider already declares the module.

## Admin Application Structure
The admin application is loaded by:
- [AdminServiceProvider.php](e:/xampp/htdocs/crm/packages/Webkul/Admin/src/Providers/AdminServiceProvider.php)

Key behavior there:
- admin routes are prefixed by `config('app.admin_path')`
- admin middleware includes `web`, `admin_locale`, and `user`
- package views load from `packages/Webkul/Admin/src/Resources/views`
- translations load from `packages/Webkul/Admin/src/Resources/lang`
- Blade anonymous components load from `packages/Webkul/Admin/src/Resources/views/components`
- morph map includes `leads`, `organizations`, `persons`, `products`, `quotes`, `warehouses`

Admin route entrypoint:
- [web.php](e:/xampp/htdocs/crm/packages/Webkul/Admin/src/Routes/Admin/web.php)

That file composes sub-route files such as:
- `contacts-routes.php`
- `products-routes.php`
- `quote-routes.php`
- `proforma-invoice-routes.php`
- `purchase-order-routes.php`

When adding admin functionality, match this route organization.

## Typical Request Flow
A normal admin feature usually follows this path:
1. route declared in `packages/Webkul/Admin/src/Routes/Admin/*.php`
2. controller in `packages/Webkul/Admin/src/Http/Controllers/*`
3. controller uses a repository from another package, often `Webkul\X\Repositories\...`
4. repository persists Eloquent model(s)
5. Blade view in `packages/Webkul/Admin/src/Resources/views/...`
6. view may include inline Vue components defined in `<script type="text/x-template">` or `<script type="module">`
7. listing pages usually use a `DataGrid`

For anything non-trivial, inspect all of these layers before changing behavior.

## Repository Pattern
Most write logic should live in repositories, not directly in controllers.

Base repository class:
- [Repository.php](e:/xampp/htdocs/crm/packages/Webkul/Core/src/Eloquent/Repository.php)

Common repository behavior:
- wraps Prettus repository pattern
- supports criteria/scope methods
- uses `find`, `findOrFail`, `findWhere`, `findOneWhere`, `scopeQuery`, etc.

Guideline:
- keep controllers thin
- centralize create/update synchronization logic in repositories
- if create/update needs child-table syncing, totals recalculation, uploads, or status transitions, put that in the repository or a dedicated service, not the Blade view

## Model Proxies and Concord
Many relations use proxy classes instead of direct model classes, for example:
- `QuoteProxy`
- `ProductProxy`
- `OrganizationProxy`

This is normal in Krayin/Concord.

Guideline:
- when copying an existing relation pattern, preserve proxies where already used
- when a model already directly references a local class and is working, keep that convention consistent within the package
- do not refactor proxies away casually

## Dynamic Attribute System
A critical part of this codebase is the custom attribute system.

Trait:
- [CustomAttribute.php](e:/xampp/htdocs/crm/packages/Webkul/Attribute/src/Traits/CustomAttribute.php)

What it does:
- loads dynamic attribute values from `attribute_values`
- maps values based on attribute type
- injects custom attribute values into model attribute access and `attributesToArray()`
- deletes attribute values when the owning entity is deleted

Implications for agents:
- some entity fields are not normal table columns
- forms may be generated by `<x-admin::attributes ... />`
- changing an entity may require checking both physical columns and dynamic attributes
- never assume every form field exists as a direct DB column
- when you remove a field from UI, make sure you are not breaking a required custom attribute path

Common entity types using this system include:
- persons
- organizations
- leads
- products in some legacy areas
- quotes in some legacy areas

## Activity Logging
Some models automatically log create/update/delete system activity.

Trait:
- [LogsActivity.php](e:/xampp/htdocs/crm/packages/Webkul/Activity/src/Traits/LogsActivity.php)

Implications:
- models using `LogsActivity` will create `Activity` records automatically
- attribute value changes may also create activity entries
- if you add a new entity and want audit history, follow the same trait + relation pattern
- do not manually duplicate activity creation unless there is a specific business event beyond normal model mutation

## DataGrid System
Listing pages are not built with generic Blade loops. They usually use Webkul DataGrids.

Example:
- [QuoteDataGrid.php](e:/xampp/htdocs/crm/packages/Webkul/Admin/src/DataGrids/Quote/QuoteDataGrid.php)

What to expect:
- `prepareQueryBuilder()` defines SQL selects and joins
- `addFilter()` maps logical filters to DB columns
- `prepareColumns()` defines list columns and formatting closures
- `prepareActions()` defines row actions
- `prepareMassActions()` defines bulk actions

Guideline:
- list-page column changes normally belong in the DataGrid, not the Blade template
- if a filter is added, update both query/filter mapping and column definition
- keep joins aligned with actual current schema; stale joins are a common source of runtime SQL errors

## Frontend Pattern: Blade + Vue Hybrids
The admin UI uses a hybrid pattern.

Common view patterns:
- `<x-admin::layouts>` wrapper
- `<x-admin::form>` and `<x-admin::form.control-group>` components
- inline Vue template blocks via `<script type="text/x-template">`
- inline Vue app components via `app.component(...)`
- direct HTML/CSS utility classes, mostly Tailwind-like classes already compiled into the admin theme

Examples:
- [products/create.blade.php](e:/xampp/htdocs/crm/packages/Webkul/Admin/src/Resources/views/products/create.blade.php)
- [quotes/create.blade.php](e:/xampp/htdocs/crm/packages/Webkul/Admin/src/Resources/views/quotes/create.blade.php)
- [contacts/persons/create.blade.php](e:/xampp/htdocs/crm/packages/Webkul/Admin/src/Resources/views/contacts/persons/create.blade.php)

Guidelines:
- preserve existing component style when editing a screen
- many pages define Vue behavior locally inside the Blade file; inspect the bottom of the same file before patching the markup
- if you add a reactive field, ensure the hidden inputs match what the backend expects
- server-side validation errors are shown with `<x-admin::form.control-group.error control-name="..." />`
- do not trust only browser-side values for totals or child rows; backend must recalculate

## Forms and Validation
Validation is mixed across the codebase.

Patterns currently present:
- explicit request classes in `packages/Webkul/Admin/src/Http/Requests/*`
- inline controller validation in legacy code
- Blade component rules passed as strings, e.g. `rules="required|max:100"`

Guideline:
- for new or significantly reworked flows, prefer dedicated FormRequest classes
- for legacy screens, match existing conventions if a full request-class refactor would be risky
- always validate child arrays and server-recalculate derived totals

## Uploads and File Handling
There are multiple upload patterns in the codebase. Inspect before adding new file logic.

Known patterns:
- activity-linked files via `Webkul\Activity`
- email attachments via `Webkul\Email`
- custom entity-specific file tables, e.g. organization files
- direct model string path fields like product images, quote/proforma attachment paths

General rules:
- store files on Laravel storage disks, usually `public`
- persist relative storage paths, not hard-coded machine paths
- derive display URLs with `Storage::url(...)` or the existing helper/path convention already used by that feature
- for PDFs, remote HTTPS URLs may fail; convert to local `public_path(...)` when rendering embedded images if the feature already does that
- if replacing files, ensure old references are updated and detail/edit screens read the latest stored value

## Database and Migrations
Migrations exist in both root and package locations.

Primary locations:
- `database/migrations`
- `packages/Webkul/*/src/Database/Migrations`

Guidelines:
- place module-specific migrations inside that module’s package
- avoid destructive schema changes unless clearly safe
- prefer backward-compatible additive migrations for live ERP work
- if old CRM fields are no longer needed in UI, hide them first rather than dropping columns immediately
- when DB schema and code disagree, inspect the live schema before patching joins or inserts

## Current Business Modules of Interest
The repo has been customized beyond stock Krayin. These areas are especially relevant.

### Contacts / Organizations
Primary package:
- `packages/Webkul/Contact`

Admin controllers:
- `packages/Webkul/Admin/src/Http/Controllers/Contact/*`

Important model:
- [Organization.php](e:/xampp/htdocs/crm/packages/Webkul/Contact/src/Models/Organization.php)

Current conventions already in this repo:
- organizations are the master table for customers/vendors
- `type` is used to distinguish organization kinds
- person records belong to organizations
- organization screens may include custom file handling and activity feeds

### Products / Catalog
Primary package:
- `packages/Webkul/Product`

Admin controller:
- `packages/Webkul/Admin/src/Http/Controllers/Products/ProductController.php`

Important model:
- [Product.php](e:/xampp/htdocs/crm/packages/Webkul/Product/src/Models/Product.php)

Current customizations already present:
- `customer_organization_id` for customer-specific products
- `internal_code`, `sku`, `size`
- pricing fields like `cost_price`, `selling_price`
- color variants and other images
- material consumption rows
- production sections and section items
- cover image + color-linked images

When modifying products:
- inspect the controller, repository, model relations, create/edit Blade files, and any resource/JSON endpoints used by quote/proforma lookups
- preserve color/image mapping behavior because downstream quote/proforma screens depend on it

### Quotes
Primary package:
- `packages/Webkul/Quote`

Admin controller:
- `packages/Webkul/Admin/src/Http/Controllers/Quote/QuoteController.php`

Important model:
- [Quote.php](e:/xampp/htdocs/crm/packages/Webkul/Quote/src/Models/Quote.php)

Current customizations already present:
- editable sequential quote numbers
- customer organization instead of lead-centric quote creation for ERP flow
- quote items with product search by item code, color variant, preview image, pricing fields
- printable quote PDF
- quote-to-proforma workflow

When modifying quotes:
- inspect the create/edit Blade files and repository sync logic together
- quote item schema may differ between legacy and ERP paths; be tolerant if backward compatibility is required

### Proforma Invoices
Primary package:
- `packages/Webkul/Quote`

Admin routes:
- `packages/Webkul/Admin/src/Routes/Admin/proforma-invoice-routes.php`

Current customizations already present:
- proforma invoices and receipts are implemented as quote-adjacent entities
- proforma create/edit follow a quote-style UI
- receipt tracking updates received and remaining amounts
- printable proforma PDF exists

When modifying proformas:
- inspect model fillables, repository total recalculation, request validation, create/edit/view/pdf blades, and route/controller together
- never trust frontend totals; server must recalculate

### Purchase Orders
Primary package:
- `packages/Webkul/PurchaseOrder`

This is a custom extension package. It follows the same modular pattern but may not match older Krayin modules exactly. Inspect package-local conventions before editing.

## Routing Conventions
There are two route layers:
- base Laravel routes in `routes/*.php`
- admin/feature routes in `packages/Webkul/Admin/src/Routes/*`

Most ERP/admin work belongs in package admin routes, not root `routes/web.php`.

Admin URL prefix is controlled by:
- `config('app.admin_path')`

Do not hard-code `/admin` in generated links if a route helper already exists.

## Views, Translations, and Components
Admin package view base:
- `packages/Webkul/Admin/src/Resources/views`

Admin translations:
- `packages/Webkul/Admin/src/Resources/lang`

Reusable Blade components:
- `packages/Webkul/Admin/src/Resources/views/components`

Guidelines:
- use translation keys when extending existing translated areas
- for one-off ERP custom labels already present as raw strings, match local style instead of forcing a broad translation refactor
- keep view logic readable; if the Blade file already contains substantial Vue logic, extend in place rather than inventing a separate frontend build pipeline

## Searchable Dropdowns / Lookups
This codebase uses several search patterns:
- lookup attributes via attribute repository
- custom Vue lookup components embedded in Blade files
- AJAX search endpoints in controllers like `search`, `search-customers`, `fetch`
- datagrid searchable dropdown filters using repository metadata

Guideline:
- copy an existing searchable lookup pattern from the closest working screen instead of inventing a new widget style
- ensure hidden ID fields stay in sync with displayed labels
- search endpoints should return the exact fields the consuming Vue component expects

## PDFs and Printing
PDF features are implemented at the admin controller + Blade template level.

Typical pattern:
- controller loads entity and relations
- controller renders a dedicated `pdf.blade.php`
- PDF helper trait streams/downloads PDF
- images in PDFs often need local filesystem paths rather than browser URLs

Before changing a PDF:
- inspect current quote/proforma print implementation
- handle null relations defensively
- avoid assuming optional person/customer/image records exist

## Logging and Debugging
Important local debugging files/areas:
- `storage/logs/laravel.log`
- `storage/framework/views` for compiled Blade output

When diagnosing errors:
- Blade syntax issues may persist in compiled views until cleared
- use `php artisan view:clear` after Blade fixes
- if route/view changes appear stale, also clear optimized caches

Useful commands:
- `php artisan optimize:clear`
- `php artisan view:clear`
- `php artisan view:cache`
- `php artisan route:list`
- `php artisan migrate:status`

## Testing State
Current automated test coverage is very small:
- `tests/Feature/AuthenticationTest.php`
- `tests/Unit/BasicTest.php`

Implication:
- do not assume tests will catch regressions in business modules
- for significant changes, perform targeted manual verification on affected pages and flows
- if adding a high-risk feature, consider adding focused tests, but expect most validation to be manual unless the area already has coverage

## Safe Change Workflow for AI Agents
Before editing any feature:
1. inspect the route file
2. inspect the admin controller
3. inspect the repository used by that controller
4. inspect the model and its relations/casts/fillable fields
5. inspect the create/edit/view/list Blade files
6. inspect any DataGrid for list-page behavior
7. inspect request validation if present
8. inspect migrations/schema if the change touches persistence
9. inspect logs if runtime behavior already fails

This repo is easy to break by changing only one layer.

## Strong Recommendations for Agents
- Prefer package-local changes under `packages/Webkul/*` over `app/*` unless the base Laravel app is clearly the right layer.
- Preserve repository-driven write logic.
- Preserve Concord module structure.
- Preserve the attribute system; do not flatten it blindly into raw columns.
- Use route helpers, not hard-coded admin URLs.
- Use non-destructive migrations where possible.
- Keep legacy fields in DB unless you are sure removal is safe.
- Reuse existing searchable dropdown and inline Vue patterns from nearby screens.
- Reuse activity/file patterns instead of creating a second attachment system.
- Recalculate monetary totals server-side.
- Handle null relations defensively in views and PDFs.

## Common Failure Modes in This Repo
These are recurring break points:
- stale SQL joins after schema changes
- Blade parse errors from unbalanced `@if/@endif` or commented Blade blocks
- Vue expressions accidentally parsed by Blade instead of `@{{ ... }}`
- writing fields that do not exist in legacy child tables
- relying on frontend totals for quote/proforma items
- mismatched file URL vs filesystem path in PDFs
- search dropdown label/value desynchronization
- empty string foreign keys inserted instead of `null`
- changing a list page in Blade instead of in the DataGrid

## Where to Put New Code
- new admin page route: `packages/Webkul/Admin/src/Routes/Admin/*.php`
- new admin controller: `packages/Webkul/Admin/src/Http/Controllers/...`
- new business repository/model logic: feature package under `packages/Webkul/<Module>/src`
- new package migration: `packages/Webkul/<Module>/src/Database/Migrations`
- new admin view: `packages/Webkul/Admin/src/Resources/views/...`
- new list configuration: `packages/Webkul/Admin/src/DataGrids/...`
- new request validation: `packages/Webkul/Admin/src/Http/Requests/...`
- new translation strings: `packages/Webkul/Admin/src/Resources/lang/*/app.php`

## Practical Example: How to Change a CRUD Screen Correctly
If an agent is asked to change a screen like product, quote, organization, or proforma:
1. inspect the route file to find controller methods
2. inspect the controller for which repository and view are used
3. inspect the repository create/update methods for child sync, uploads, totals, status rules
4. inspect the model for casts/fillables/relations
5. inspect the create/edit Blade file for hidden inputs, inline Vue state, and validation components
6. inspect the list page DataGrid if columns/actions/filters must change
7. inspect print/pdf blades if the document output must reflect the same changes
8. only then patch the code

## Environment Notes
This project is often run in XAMPP-style local environments.

Relevant consequences:
- URLs may look like `http://localhost/crm/...` or `https://localhost/crm/...`
- PDF image rendering may require local path conversion
- stale Apache/PHP state can make a fixed Blade file appear still broken until caches are cleared or Apache is restarted

## Final Rule
Treat this repo as a modular ERP-customized Krayin installation, not a clean-slate Laravel app. The fastest safe path is usually to copy the nearest existing working pattern in the same module, then make the smallest coherent change across route, controller, repository, model, view, and datagrid layers.
