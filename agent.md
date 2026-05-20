# MCM-Software Agent Overview

This document provides a detailed, system-wide technical overview of the MCM-Software repository. It is intended for an AI agent that must understand the application architecture, data model, module boundaries, and end-to-end business flows.

Sources:
- Codebase structure under packages/Webkul/* and config/
- db.sql in repo root (MariaDB dump, May 20, 2026)
- AGENTS.md

Important:
- This is a Laravel 10 + Krayin CRM system extended into ERP workflows.
- Most logic is in packages/Webkul/*, not app/.
- db.sql is a snapshot; migrations and live DB can differ.


## 1) System Architecture
- Backend: Laravel 10, PHP 8.2
- Modular framework: Konekt Concord
- Repositories: Prettus l5-repository pattern
- Admin UI: Blade + Blade components + inline Vue (x-template)
- Lists: Webkul DataGrid (query builder + column definitions)
- Attributes: Dynamic attribute system (attributes + attribute_values)
- Activity logging: Activities + link tables to entities
- Storage: storage/app/public with public/storage


## 2) Code Layout and Conventions

### 2.1 Key Directories
- packages/Webkul/Admin: Admin UI, controllers, routes, views, DataGrids
- packages/Webkul/<Module>: Models, repositories, services, migrations
- config/: Concord and service providers, app settings
- database/migrations: core migrations
- public/: web root, compiled assets
- resources/: global assets and views (minor)

### 2.2 Concord Modules
Typical modules (non-exhaustive):
- Activity, Admin, Attribute, Automation, Contact, Core, DataGrid
- Email, EmailTemplate, Lead, Product, PurchaseOrder, Quote, Tag, User
- Warehouse, WebForm, DataTransfer

### 2.3 Request Pipeline (Admin)
1) Route in packages/Webkul/Admin/src/Routes/Admin/*.php
2) Controller in packages/Webkul/Admin/src/Http/Controllers/*
3) Repository in packages/Webkul/<Module>/src/Repositories
4) Model in packages/Webkul/<Module>/src/Models
5) Blade view + inline Vue templates
6) Lists use DataGrid classes

### 2.4 DataGrid Pattern
- prepareQueryBuilder(): SQL selects/joins
- prepareColumns(): columns, formatters, visibility
- prepareActions(): row actions
- prepareMassActions(): bulk actions

### 2.5 Dynamic Attributes
- attributes: metadata by entity_type
- attribute_values: values per entity_id
- Used for leads, persons, organizations, products, quotes, warehouses
- Do not assume all fields are physical columns

### 2.6 Activity Logging
- activities table + entity link tables (lead_activities, person_activities, etc.)
- Used for calls, notes, tasks, system changes


## 3) Data Model by Domain (db.sql)

### 3.1 Core Entities
- organizations
  - Purpose: customer/vendor master
  - Key columns: name, type, address json, billing/shipping fields, user_id
- persons
  - Purpose: contacts under organizations
  - Key columns: name, type, organization_id, emails/contact_numbers json
- users, roles, groups, user_groups
  - Purpose: auth + access control

### 3.2 CRM Leads
- leads
  - case_no, title, description, lead_value, priority, status
  - user_id, person_id, organization_id
  - lead_pipeline_id, lead_pipeline_stage_id, lead_source_id, lead_type_id
- lead_pipelines, lead_pipeline_stages
- lead_sources, lead_types, lead_priorities
- lead_activities, lead_tags, lead_products, lead_quotes

### 3.3 Email
- emails
  - subject, reply (body), from/sender/reply_to, folders, message_id
  - lead_id, person_id, parent_id (threading)
- email_attachments
- email_tags
- email_templates

### 3.4 Product and Catalog
- products
  - sku, internal_code, name
  - customer_organization_id (customer-specific catalog)
  - price, cost_price, selling_price, size, weight, cover_image
- product_colors, product_other_images, product_key_points
- product_consumptions (materials per product)
- product_production_sections, product_production_section_items
- product_pricing_charts, product_pricing_chart_tiers, product_pricing_chart_types
- product_inventories (warehouse location stock)

### 3.5 Sales: Quotes and Proformas
- quotes
  - quote_number, subject, quote_date
  - billing/shipping, discounts, tax, totals
  - organization_id, person_id, user_id
- quote_items
  - item_code, item_name, qty, unit_price, totals
  - color_variant_id, preview_image
- proforma_invoices
  - proforma_number, quote_id, organization_id
  - totals, received/remaining, status
  - sales_owner_id, created_by, approved_by
- proforma_invoice_items
- proforma_receipts

### 3.6 Production: Job Orders
- job_orders
  - created from proformas
  - proforma_invoice_id, organization_id, person_id
  - total_order_qty, required_delivery_date
- job_order_items
  - item-level details and pricing
- job_order_requirements
  - material requirements for job order items

### 3.7 Procurement
- vendor_quotes
  - vendor_quote_number, job_order_id, organization_id
  - billing/shipping, terms, totals
- vendor_quote_items
  - material, qty, unit_price, lead_time
- purchase_orders
  - po_number, job_order_id, vendor_quote_id
  - organization_id, person_id, user_id
  - totals, status, terms
- purchase_order_items
  - requirement_id, product_id, qty, pricing
- goods_receipts
  - purchase_order_id, receipt_date, status
- goods_receipt_items
  - purchase_order_item_id, received_qty
- vendor_payables
  - payable against purchase_order and goods_receipt

### 3.8 Inventory and Warehouse
- warehouses, warehouse_locations
- warehouse_activities, warehouse_tags
- product_inventories

### 3.9 Config, Reference, and Support Tables
- core_config
- tags
- countries, country_states
- material_references, material_reference_vendor
- material_units, unit_references, color_references
- datagrid_saved_filters
- imports, import_batches
- jobs, job_batches, failed_jobs
- web_forms, web_form_attributes, webhooks
- workflows


## 4) Key Relationships (Selected Foreign Keys)
- persons.organization_id -> organizations.id
- organizations.user_id -> users.id
- leads.user_id -> users.id
- leads.person_id -> persons.id
- leads.organization_id -> organizations.id
- emails.person_id -> persons.id
- emails.lead_id -> leads.id
- email_attachments.email_id -> emails.id
- products.customer_organization_id -> organizations.id
- product_inventories.warehouse_id -> warehouses.id
- product_inventories.warehouse_location_id -> warehouse_locations.id
- quotes.organization_id -> organizations.id
- quotes.person_id -> persons.id
- quote_items.quote_id -> quotes.id
- proforma_invoices.quote_id -> quotes.id
- proforma_invoice_items.proforma_invoice_id -> proforma_invoices.id
- job_orders.proforma_invoice_id -> proforma_invoices.id
- vendor_quotes.job_order_id -> job_orders.id
- purchase_orders.vendor_quote_id -> vendor_quotes.id
- purchase_order_items.purchase_order_id -> purchase_orders.id
- goods_receipts.purchase_order_id -> purchase_orders.id
- goods_receipt_items.purchase_order_item_id -> purchase_order_items.id
- vendor_quote_items.vendor_quote_id -> vendor_quotes.id


## 5) End-to-End Business Flows

### 5.1 CRM Lead Flow
1) Lead created (leads + attribute_values)
2) Activities logged (activities + lead_activities)
3) Lead linked to person/organization
4) Lead can be converted into a quote

### 5.2 Customer and Vendor Management
- organizations table holds both customers and vendors via type
- persons are contacts attached to organizations
- dynamic attributes expand standard fields

### 5.3 Product and Catalog Flow
1) Product created in catalog
2) Optional: colors, images, key points
3) Production data: material consumption + production sections
4) Pricing charts and tiers for volume pricing
5) Inventory tracked by warehouse location

### 5.4 Sales Flow (Quote -> Proforma -> Job Order)
1) Quote created (quote + quote_items)
2) Proforma created from quote (proforma_invoices + items)
3) Job order generated (job_orders + items)
4) Material requirements generated (job_order_requirements)

### 5.5 Procurement Flow (Vendor Quote -> PO -> Receipt -> Payable)
1) Vendor quotes generated from job requirements
2) Purchase order issued
3) Goods received and recorded
4) Vendor payable created

### 5.6 Email Flow
- Inbound and outbound messages stored in emails
- Attachments stored in email_attachments
- Message lists built via DataGrid in Admin UI


## 6) UI and Frontend Behavior
- Admin UI uses Blade components plus Vue inline templates
- Each list page has a DataGrid class that defines data shape
- Most forms use <x-admin::form> and component control groups
- Validation errors are handled server-side and surfaced in Blade


## 7) Operational and Infrastructure Notes
- Storage: file paths saved in DB; URLs resolved via Storage helpers
- Jobs: queued jobs use jobs/job_batches tables
- Imports: imports and import_batches control bulk data operations
- Webhooks and web forms exist for external integrations
- Use config/app.php and config/concord.php for global wiring


## 8) Development Rules for Agents
- Prefer package-level changes over app/ layer
- Preserve dynamic attribute behavior
- Update DataGrid when list columns or filters change
- Keep controller thin; put sync and totals in repositories
- Recalculate totals on server, not only on frontend
- Avoid breaking existing ERP flows when changing CRM features


## 9) Limitations and Source of Truth
- db.sql is a snapshot; migrations can differ
- If schema conflicts arise, confirm live DB and migrations
- Some tables are legacy; do not remove without impact analysis
