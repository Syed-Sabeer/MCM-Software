# MCM Development Agent Brief

This document is the working specification for the MCM software family. Use it as the source of truth when planning features, making code changes, extending workflows, or explaining the system to another AI assistant.

## Project Identity

- Project name: MCM
- Codebase type: Laravel 10 CRM, quoting, procurement, production, and warehouse platform
- Primary domain: customer management, lead tracking, product and material planning, quote generation, vendor sourcing, purchase ordering, and operational workflow automation
- Schema source of truth: active package/root migrations and models; `SQL/V8.sql` is historical context only

## High-Level Architecture

- Backend: Laravel 10 on PHP 8.2
- Package architecture: Krayin-style modular Laravel app with Webkul modules registered through Concord
- Repositories: Prettus L5 Repository pattern is in use for many module layers
- Frontend build: Vite
- UI system: shared Blade components and module-specific screens
- Document rendering: shared document engine in admin component views
- Route surface: standard Laravel routes plus module route files

## Core Technical Stack

- Framework: laravel/framework
- Module system: konekt/concord
- Repositories: prettus/l5-repository
- Authentication and UI scaffolding: laravel/ui, sanctum
- Documents: barryvdh/laravel-dompdf, mpdf/mpdf, smalot/pdfparser
- Excel import/export: maatwebsite/excel
- Email: webklex/laravel-imap, laravel mail stack, email template module
- Utilities: doctrine/dbal, guzzlehttp/guzzle, svg sanitizer, AR PDF helpers

## Booted Modules

These modules are registered in `config/concord.php` and are part of the live application surface:

- Activity
- Admin
- Attribute
- Automation
- Contact
- Core
- DataGrid
- DataTransfer
- Email
- EmailTemplate
- Installer
- Lead
- Product
- PurchaseOrder
- Quote
- Tag
- User
- Warehouse
- WebForm

## Repository Layout Notes

- `app/` contains the Laravel application shell and base models/controllers
- `packages/Webkul/` contains the major business modules
- `Modules/Admin/` contains admin UI resources and module overrides
- `routes/` contains global and module-related routes
- `SQL/V8.sql` is a historical schema/seed snapshot; executable migrations and current models are authoritative
- `docs/document-ui-engine.md` describes the shared document rendering pattern

## Domain Model Overview

The application is attribute-driven. Many entities are not hard-coded with fixed form definitions; instead they are described by records in `attributes`, `attribute_values`, and `attribute_options`.

### Master Attribute System

- `attributes` defines fields per entity type
- `attribute_values` stores entity-specific values
- `attribute_options` stores selectable values for option-based attributes
- This is the foundation for dynamic forms, validations, display labels, and quick-add fields

### Core CRM Entities

- `users` and `roles` define access and ownership
- `groups` and `user_groups` support team organization
- `leads` represent pipeline-driven sales opportunities
- `persons` represent contacts
- `organizations` represent companies, vendors, or customer accounts
- `activities` track calls, meetings, tasks, notes, files, and system history
- `activity_files` stores attachments for activities
- `activity_participants` links users and persons to an activity
- `lead_activities`, `lead_persons`, `lead_quotes`, and `lead_tags` connect leads to related records
- `person_activities`, `person_tags`, `organization_activities`, `organization_files`, and similar pivot tables preserve entity activity history and associations

### Lead and Pipeline Logic

- `lead_pipelines` defines sales pipelines
- `lead_pipeline_stages` defines ordered stages inside a pipeline
- `lead_sources` defines lead origins such as Email, Web, Web Form, Phone, and Direct
- `lead_types` separates new business from existing business
- `lead_priorities` stores urgency labels
- `leads.status` and `lead_pipeline_stage_id` drive stage progression and closure state
- `leads.closed_at` and `lost_reason` capture end-of-life outcome

### Product and Material Logic

- `products` stores sellable items and internal products
- `product_categories`, `product_colors`, `product_other_images`, `product_key_points`, `product_pricing_charts`, `product_pricing_chart_tiers`, and `product_pricing_chart_types` support rich product catalogs
- `product_inventories` tracks stock and warehouse-level availability
- `product_consumptions` records material usage or allocation
- `material_references`, `material_reference_vendor`, and `material_units` support manufacturing and sourcing calculations
- `unit_references` links measurement conversion logic
- Product data is used in both sales docs and procurement docs

### Quote and Customer Proposal Logic

- `quotes` is the main sales quotation table
- `quote_items` stores quote line items
- Quotes carry subject, notes, terms, payment term, shipping method, production time, transit time, ETD, ETA, addresses, discount, tax, freight, adjustment, subtotal, grand total, status, and expiry data
- Quote items store SKU, code, product reference, color variant, preview image, quantity, pricing, discount, tax, and sort order
- Quotes are tied to `person_id`, `organization_id`, and `user_id`

### Proforma Invoice Logic

- `proforma_invoices` stores invoice-like commercial documents currently used in the system
- `proforma_invoice_items` stores line items
- `proforma_receipts` stores payment receipts against proforma invoices
- Proforma invoices can originate from quotes and can track approval, conversion, received amount, remaining amount, attachment, notes, terms, and payment terms
- Current statuses include draft and issued states in the seeded data

### Final Invoice Logic

- `invoices` stores final customer invoices created transactionally from one proforma invoice
- `invoice_items` preserves the final commercial line-item snapshot
- `invoice_receipts` stores payments received after final invoice issue
- Proforma receipts remain the advance-payment source and are applied through `invoices.advance_applied`
- `invoices.received_amount` contains later invoice receipts; `remaining_amount` subtracts both advances and later receipts
- A proforma can be converted only once through `proforma_invoices.converted_to_invoice_id`
- Final invoices inherit explicit customer publication from the source proforma and remain organization-scoped in the customer portal

### Procurement and Vendor Logic

- `vendor_quotes` stores supplier quotes linked to job orders and organizations
- `vendor_quote_items` stores material sourcing requests and vendor responses
- `purchase_orders` stores procurement orders
- `purchase_order_items` stores ordered materials and receiving progress
- `goods_receipts` and `goods_receipt_items` capture received materials against purchase orders
- `vendor_payables` tracks vendor balances and settlement progress

### Job and Production Logic

- `jobs` is a top-level production or order execution record
- `job_orders` links production requests to items and requirements
- `job_order_items` and `job_order_requirements` decompose the bill of materials or production plan
- `job_cards`, `job_card_sections`, and `job_card_section_items` support production execution and internal task tracking
- These tables indicate a manufacturing-oriented workflow where sales demand is converted into sourcing and execution steps

### Warehouse and Inventory Logic

- `warehouses` stores warehouse master data, contacts, and addresses
- `warehouse_locations` stores sub-locations inside warehouses
- `warehouse_activities` and `warehouse_tags` add relationships and categorization
- Warehouse data is used to ground stock, receiving, and logistics flows

### Marketing, Forms, and Automation

- `marketing_campaigns` and `marketing_events` support outbound activity
- `web_forms` stores embeddable lead capture forms
- `web_form_attributes` maps form fields to attribute definitions
- `webhooks` defines outbound integrations for entity events
- `workflows` defines server-side automation rules

### Email and Communication

- `emails` stores captured or sent mail records
- `email_attachments` and `email_tags` preserve mail metadata
- `email_templates` stores reusable email content
- `activity` records often drive email and timeline-based notifications

## Workflow Rules Found in the Database

The seeded automation in `workflows` currently includes:

- Send email to participants after activity creation
- Send email to participants after activity update

These workflows are triggered by activity events and filtered by activity type. The stored conditions show activity types such as call, meeting, and lunch, while actions reference the email-to-participants mechanism.

## Business Process Flow

### 1. Lead Capture and Qualification

- Leads may originate from manual entry, email, web, web form, phone, or direct outreach
- A lead is assigned a sales owner, pipeline, and pipeline stage
- Additional data can be collected through dynamic attributes
- Lead activity is logged continuously through the activity system
- Contacts and organizations are linked as the lead matures

### 2. Sales Opportunity Management

- Leads progress across ordered pipeline stages
- A lead can be marked open, won, or lost depending on workflow state
- Priority, source, type, and expected close date guide sales handling
- Quotes can be created from lead context and tied to organizations and persons

### 3. Quotation and Commercial Proposal

- Quote creation includes address capture, commercial terms, shipping, production timing, and line items
- Line item pricing is calculated with discount, tax, freight, adjustment, and subtotal logic
- Quotes use product and color variant data where applicable
- Attachments can be stored alongside the quote

### 4. Proforma Billing

- Quotes can be converted into proforma invoices
- Proforma invoices retain the commercial structure but add approval, receipt, and remaining balance tracking
- This layer is used as a billing or pre-invoice stage rather than a final invoice implementation
- The repository does not currently show a dedicated invoice module, so any future invoice work should reuse the shared document engine rather than introducing a separate layout system

### 5. Procurement and Supply Chain

- Internal requirements or job orders can generate vendor quotes
- Vendor quotes are compared and converted into purchase orders
- Purchase orders track expected receiving dates, shipping method, freight, and payment terms
- Goods receipts update received quantities and may create vendor payables
- Vendor payables track the remaining amount owed to suppliers

### 6. Production and Execution

- Jobs and job orders drive operational work after the commercial and sourcing layers
- Job orders split into required materials and execution sections
- Job cards document production or operational steps
- Production data is tied back to materials, warehouses, and vendor sourcing where needed

### 7. Activity and Collaboration

- Activities are the shared timeline object for calls, meetings, tasks, notes, files, and system events
- Activities can belong to leads, persons, organizations, products, warehouses, and other entity types via polymorphic-style fields
- Participants may be users or persons
- File attachments can be linked to activity records
- System activities log field-level change history in JSON-like payloads

## Shared Document Engine

The repository uses a shared document renderer described in `docs/document-ui-engine.md`.

Current shared views:

- `admin::components.documents.standard`
- `admin::components.documents.form-styles`

Current document consumers:

- Quote print views
- Proforma invoice detail and print views
- Purchase order detail and print views
- Create/edit forms for quote, proforma, and purchase order flows

Document views should continue to pass the same logical data blocks:

- company
- meta
- partyBlocks
- summaryRows
- columns
- items
- totals
- notes
- signatures

## Data Modeling Rules

- Monetary values commonly use `decimal(12,4)` and should be treated as exact decimals, not floats
- Addresses are often stored as JSON or longtext blobs because the UI expects structured address blocks
- Status fields are usually string enums or small state strings such as draft, open, issued, fully_received, or similar domain-specific states
- Many relations are modeled through pivot tables instead of hard-coded foreign object graphs
- Activity history is treated as first-class audit data
- Table and entity names are usually plural and match business nouns directly

## Technical Conventions

- Prefer module-local changes over framework-wide rewrites
- Reuse shared components and shared document layouts before introducing new view systems
- Preserve attribute-driven forms and repository abstractions
- Keep controllers thin and let module services, repositories, or model layers do the domain work
- Maintain existing status transitions and lifecycle hooks when extending lead, quote, invoice, procurement, or workflow logic
- Do not create a separate invoice system if the existing commercial document flow can extend the current shared renderer and data model

## AI Working Instructions For Future Changes

When extending this repository, follow this order:

1. Identify the owning module and the exact table or route that controls the behavior
2. Check whether the behavior already exists in a shared component, workflow, repository, or attribute definition
3. Preserve existing business state transitions and calculate totals using the current rounding and decimal conventions
4. Update the UI only through the shared document and form primitives when possible
5. Add or update tests for the narrow slice being changed
6. Avoid inventing parallel models for the same business concept

## Important Gaps To Remember

- Global route registration is minimal; most behavior lives inside package modules
- The SQL dump is the best source for entity relationships, seed data, and domain assumptions
- If a new feature touches commercial docs, use the shared document engine first

## Customer Portal Security Boundary

- Customer portal accounts live in `customer_portal_users` and authenticate only through the `customer` guard; internal `users`, roles, and the `user` guard remain staff-only.
- The shared admin login uses one OTP password-recovery flow for both guards. Six-digit codes are hashed in `password_reset_otps`, expire after 10 minutes, allow five failed attempts, and must be verified in-session before a new password can be saved.
- Multiple portal users may belong to one organization and may optionally link to a person in that organization.
- Portal invitations are one-time SHA-256 token hashes in `customer_portal_invitations`; password resets use the dedicated `customer_portal_password_resets` broker table.
- Every customer query derives `organization_id` from `auth('customer')->user()` and repeats ownership checks for view, PDF, and attachment routes.
- Quotes, proforma invoices, and job orders are private unless `customer_visible_at` is set. Draft proformas and draft job orders remain hidden even if data is malformed.
- Portal document downloads reuse the existing quote/proforma PDF views behind customer-authenticated routes. Internal admin print URLs must never be linked from the portal.
- Deploy by running migrations, configuring mail/queue, then dry-running `php artisan customer-portal:backfill-legacy`. Review output before using `--apply`; unambiguous accounts whose role contains only the portal permission are disabled internally unless `--keep-legacy-access` is supplied. The command preserves hashes and never deletes users or clears `organizations.user_id`.

## Suggested Future Extension Areas

- Final invoice module reusing the shared document engine
- Stronger workflow builder coverage for non-activity events
- Better audit trail documentation for system activities and field changes
- More explicit warehouse receiving and stock movement docs
- Clearer separation between customer-facing quotes and internal procurement documents

## Source References In This Repo

- `config/concord.php`
- `docs/document-ui-engine.md`
- `SQL/V8.sql`
- `packages/Webkul/*`

This file should stay aligned with the schema and module registry. When the database or module map changes, update this document first so downstream AI work remains consistent.
