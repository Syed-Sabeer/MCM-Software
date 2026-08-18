<?php

return [
    [
        'key'   => 'dashboard',
        'name'  => 'admin::app.layouts.dashboard',
        'route' => ['admin.dashboard.index', 'admin.dashboard.stats'],
        'sort'  => 1,
    ], [
        'key'   => 'dashboard.business_details',
        'name'  => 'Business Details',
        'route' => [],
        'sort'  => 1,
    ], [
        'key'   => 'dashboard.customer_details',
        'name'  => 'Customer Details',
        'route' => [],
        'sort'  => 2,
    ], [
        'key'   => 'leads',
        'name'  => 'admin::app.acl.leads',
        'route' => 'admin.leads.index',
        'sort'  => 2,
    ], [
        'key'   => 'leads.create',
        'name'  => 'admin::app.acl.create',
        'route' => ['admin.leads.create', 'admin.leads.store', 'admin.leads.create_by_ai', 'admin.leads.product.add', 'admin.leads.emails.store'],
        'sort'  => 1,
    ], [
        'key'   => 'leads.view',
        'name'  => 'admin::app.acl.view',
        'route' => ['admin.leads.view', 'admin.leads.get', 'admin.leads.search', 'admin.leads.kanban.look_up', 'admin.leads.activities.index'],
        'sort'  => 2,
    ], [
        'key'   => 'leads.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => ['admin.leads.edit', 'admin.leads.update', 'admin.leads.mass_update', 'admin.leads.attributes.update', 'admin.leads.stage.update', 'admin.leads.product.remove', 'admin.leads.contacts.detach', 'admin.leads.emails.detach'],
        'sort'  => 3,
    ], [
        'key'   => 'leads.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => ['admin.leads.delete', 'admin.leads.mass_delete', 'admin.leads.quotes.delete'],
        'sort'  => 4,
    ], [
        'key'   => 'quotes',
        'name'  => 'admin::app.acl.quotes',
        'route' => 'admin.quotes.index',
        'sort'  => 3,
    ], [
        'key'   => 'quotes.create',
        'name'  => 'admin::app.acl.create',
        'route' => ['admin.quotes.create', 'admin.quotes.store', 'admin.products.quick_store'],
        'sort'  => 1,
    ], [
        'key'   => 'quotes.view',
        'name'  => 'admin::app.acl.view',
        'route' => ['admin.quotes.view', 'admin.quotes.search'],
        'sort'  => 2,
    ], [
        'key'   => 'quotes.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => ['admin.quotes.edit', 'admin.quotes.update', 'admin.quotes.status', 'admin.quotes.customer_visibility', 'admin.quotes.convert_to_proforma', 'admin.quotes.duplicate'],
        'sort'  => 3,
    ], [
        'key'   => 'quotes.print',
        'name'  => 'admin::app.acl.print',
        'route' => 'admin.quotes.print',
        'sort'  => 4,
    ], [
        'key'   => 'quotes.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => ['admin.quotes.delete', 'admin.quotes.mass_delete'],
        'sort'  => 5,
    ], [
        'key'   => 'proforma_invoices',
        'name'  => 'Proforma Invoices',
        'route' => 'admin.proforma_invoices.index',
        'sort'  => 4,
    ], [
        'key'   => 'proforma_invoices.create',
        'name'  => 'admin::app.acl.create',
        'route' => ['admin.proforma_invoices.create', 'admin.proforma_invoices.store', 'admin.proforma_invoices.receipts.store', 'admin.products.quick_store'],
        'sort'  => 1,
    ], [
        'key'   => 'proforma_invoices.view',
        'name'  => 'admin::app.acl.view',
        'route' => ['admin.proforma_invoices.view', 'admin.proforma_invoices.print'],
        'sort'  => 2,
    ], [
        'key'   => 'proforma_invoices.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => ['admin.proforma_invoices.edit', 'admin.proforma_invoices.update', 'admin.proforma_invoices.status', 'admin.proforma_invoices.customer_visibility', 'admin.proforma_invoices.receipts.delete'],
        'sort'  => 3,
    ], [
        'key'   => 'proforma_invoices.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => ['admin.proforma_invoices.delete', 'admin.proforma_invoices.mass_delete'],
        'sort'  => 4,
    ], [
        'key'   => 'invoices',
        'name'  => 'Final Invoices',
        'route' => 'admin.invoices.index',
        'sort'  => 5,
    ], [
        'key'   => 'invoices.create',
        'name'  => 'admin::app.acl.create',
        'route' => ['admin.invoices.store', 'admin.invoices.receipts.store'],
        'sort'  => 1,
    ], [
        'key'   => 'invoices.view',
        'name'  => 'admin::app.acl.view',
        'route' => ['admin.invoices.view', 'admin.invoices.print'],
        'sort'  => 2,
    ], [
        'key'   => 'invoices.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => ['admin.invoices.status', 'admin.invoices.customer_visibility', 'admin.invoices.receipts.delete'],
        'sort'  => 3,
    ], [
        'key'   => 'job_orders',
        'name'  => 'Job Orders',
        'route' => 'admin.job_orders.index',
        'sort'  => 4,
    ], [
        'key'   => 'job_orders.create',
        'name'  => 'admin::app.acl.create',
        'route' => ['admin.job_orders.create', 'admin.job_orders.store'],
        'sort'  => 1,
    ], [
        'key'   => 'job_orders.view',
        'name'  => 'admin::app.acl.view',
        'route' => ['admin.job_orders.view', 'admin.job_orders.job_card.pdf', 'admin.job_orders.job_card.csv', 'admin.job_orders.requirement_sheet.pdf', 'admin.job_orders.requirement_sheet.csv'],
        'sort'  => 2,
    ], [
        'key'   => 'job_orders.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => ['admin.job_orders.edit', 'admin.job_orders.update', 'admin.job_orders.customer_visibility'],
        'sort'  => 3,
    ], [
        'key'   => 'job_orders.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => ['admin.job_orders.delete', 'admin.job_orders.mass_delete'],
        'sort'  => 4,
    ], [
        'key'   => 'requirements',
        'name'  => 'Requirement Sheets',
        'route' => 'admin.requirements.index',
        'sort'  => 4,
    ], [
        'key'   => 'requirements.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => ['admin.requirements.delete', 'admin.requirements.mass_delete'],
        'sort'  => 1,
    ], [
        'key'   => 'vendor_quotes',
        'name'  => 'Vendor Quotes',
        'route' => 'admin.vendor_quotes.index',
        'sort'  => 4,
    ], [
        'key'   => 'vendor_quotes.create',
        'name'  => 'admin::app.acl.create',
        'route' => ['admin.vendor_quotes.create', 'admin.vendor_quotes.store'],
        'sort'  => 1,
    ], [
        'key'   => 'vendor_quotes.view',
        'name'  => 'admin::app.acl.view',
        'route' => 'admin.vendor_quotes.view',
        'sort'  => 2,
    ], [
        'key'   => 'vendor_quotes.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => ['admin.vendor_quotes.edit', 'admin.vendor_quotes.update'],
        'sort'  => 3,
    ], [
        'key'   => 'vendor_quotes.print',
        'name'  => 'admin::app.acl.print',
        'route' => 'admin.vendor_quotes.print',
        'sort'  => 4,
    ], [
        'key'   => 'vendor_quotes.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => ['admin.vendor_quotes.delete', 'admin.vendor_quotes.mass_delete'],
        'sort'  => 5,
    ], [
        'key'   => 'goods_receipts',
        'name'  => 'Goods Receipts',
        'route' => 'admin.goods_receipts.index',
        'sort'  => 4,
    ], [
        'key'   => 'goods_receipts.create',
        'name'  => 'admin::app.acl.create',
        'route' => ['admin.goods_receipts.create', 'admin.goods_receipts.store'],
        'sort'  => 1,
    ], [
        'key'   => 'goods_receipts.view',
        'name'  => 'admin::app.acl.view',
        'route' => 'admin.goods_receipts.view',
        'sort'  => 2,
    ], [
        'key'   => 'goods_receipts.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => ['admin.goods_receipts.edit', 'admin.goods_receipts.update'],
        'sort'  => 3,
    ], [
        'key'   => 'goods_receipts.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => ['admin.goods_receipts.delete', 'admin.goods_receipts.delete_fallback'],
        'sort'  => 4,
    ], [
        'key'   => 'inventory',
        'name'  => 'Inventory',
        'route' => 'admin.inventory.index',
        'sort'  => 4,
    ], [
        'key'   => 'inventory.view',
        'name'  => 'admin::app.acl.view',
        'route' => 'admin.inventory.view',
        'sort'  => 1,
    ], [
        'key'   => 'inventory.create',
        'name'  => 'Record Inventory Movements',
        'route' => ['admin.inventory.movements.store', 'admin.settings.material_references.store'],
        'sort'  => 2,
    ], [
        'key'   => 'inventory.edit',
        'name'  => 'Update Inventory Settings',
        'route' => ['admin.inventory.edit', 'admin.inventory.settings.update'],
        'sort'  => 3,
    ], [
        'key'   => 'vendor_payables',
        'name'  => 'Vendor Payables',
        'route' => 'admin.vendor_payables.index',
        'sort'  => 4,
    ], [
        'key'   => 'purchase_orders',
        'name'  => 'admin::app.acl.purchase-orders',
        'route' => 'admin.purchase_orders.index',
        'sort'  => 4,
    ], [
        'key'   => 'purchase_orders.create',
        'name'  => 'admin::app.acl.create',
        'route' => ['admin.purchase_orders.create', 'admin.purchase_orders.store'],
        'sort'  => 1,
    ], [
        'key'   => 'purchase_orders.view',
        'name'  => 'admin::app.acl.view',
        'route' => 'admin.purchase_orders.view',
        'sort'  => 2,
    ], [
        'key'   => 'purchase_orders.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => ['admin.purchase_orders.edit', 'admin.purchase_orders.update'],
        'sort'  => 3,
    ], [
        'key'   => 'purchase_orders.print',
        'name'  => 'admin::app.acl.print',
        'route' => 'admin.purchase_orders.print',
        'sort'  => 4,
    ], [
        'key'   => 'purchase_orders.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => ['admin.purchase_orders.delete', 'admin.purchase_orders.mass_delete'],
        'sort'  => 5,
    ], [
        'key'   => 'mail',
        'name'  => 'admin::app.acl.mail',
        'route' => 'admin.mail.index',
        'sort'  => 4,
    ], [
        'key'   => 'mail.inbox',
        'name'  => 'admin::app.acl.inbox',
        'route' => 'admin.mail.index',
        'sort'  => 1,
    ], [
        'key'   => 'mail.draft',
        'name'  => 'admin::app.acl.draft',
        'route' => 'admin.mail.index',
        'sort'  => 2,
    ], [
        'key'   => 'mail.outbox',
        'name'  => 'admin::app.acl.outbox',
        'route' => 'admin.mail.index',
        'sort'  => 3,
    ], [
        'key'   => 'mail.sent',
        'name'  => 'admin::app.acl.sent',
        'route' => 'admin.mail.index',
        'sort'  => 4,
    ], [
        'key'   => 'mail.trash',
        'name'  => 'admin::app.acl.trash',
        'route' => 'admin.mail.index',
        'sort'  => 5,
    ], [
        'key'   => 'mail.compose',
        'name'  => 'admin::app.acl.create',
        'route' => ['admin.mail.store', 'admin.mail.inbound_parse'],
        'sort'  => 6,
    ], [
        'key'   => 'mail.view',
        'name'  => 'admin::app.acl.view',
        'route' => ['admin.mail.view', 'admin.mail.attachment_download'],
        'sort'  => 7,
    ], [
        'key'   => 'mail.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => ['admin.mail.update', 'admin.mail.mass_update', 'admin.mail.tags.attach', 'admin.mail.tags.detach'],
        'sort'  => 8,
    ], [
        'key'   => 'mail.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => ['admin.mail.delete', 'admin.mail.mass_delete'],
        'sort'  => 9,
    ], [
        'key'   => 'activities',
        'name'  => 'admin::app.acl.activities',
        'route' => ['admin.activities.index', 'admin.activities.calendar', 'admin.activities.my_tasks', 'admin.activities.my_tasks_data', 'admin.activities.my_tasks_summary', 'admin.activities.get', 'admin.activities.search_employee_users', 'admin.activities.search_organizations', 'admin.activities.search_persons', 'admin.activities.file_download', 'admin.activities.file_preview'],
        'sort'  => 5,
    ], [
        'key'   => 'activities.create',
        'name'  => 'admin::app.acl.create',
        'route' => 'admin.activities.store',
        'sort'  => 1,
    ], [
        'key'   => 'activities.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => ['admin.activities.edit', 'admin.activities.update', 'admin.activities.mass_update'],
        'sort'  => 2,
    ], [
        'key'   => 'activities.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => ['admin.activities.delete', 'admin.activities.mass_delete'],
        'sort'  => 3,
    ], [
        'key'   => 'customers',
        'name'  => 'Customers',
        'route' => ['admin.customers.persons.index', 'admin.customers.organizations.index'],
        'sort'  => 6,
    ], [
        'key'   => 'customers.persons',
        'name'  => 'Customer Contacts',
        'route' => ['admin.customers.persons.index', 'admin.customers.persons.search'],
        'sort'  => 1,
    ], [
        'key'   => 'customers.persons.create',
        'name'  => 'admin::app.acl.create',
        'route' => ['admin.customers.persons.create', 'admin.customers.persons.store'],
        'sort'  => 1,
    ], [
        'key'   => 'customers.persons.view',
        'name'  => 'admin::app.acl.view',
        'route' => ['admin.customers.persons.view', 'admin.customers.persons.activities.index'],
        'sort'  => 2,
    ], [
        'key'   => 'customers.persons.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => ['admin.customers.persons.edit', 'admin.customers.persons.update', 'admin.customers.persons.tags.attach', 'admin.customers.persons.tags.detach'],
        'sort'  => 3,
    ], [
        'key'   => 'customers.persons.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => ['admin.customers.persons.delete', 'admin.customers.persons.mass_delete'],
        'sort'  => 4,
    ], [
        'key'   => 'customers.organizations',
        'name'  => 'Customer Companies',
        'route' => ['admin.customers.organizations.index', 'admin.customers.organizations.fetch', 'admin.customers.organizations.search_customers', 'admin.customers.organizations.show'],
        'sort'  => 2,
    ], [
        'key'   => 'customers.organizations.create',
        'name'  => 'admin::app.acl.create',
        'route' => ['admin.customers.organizations.create', 'admin.customers.organizations.store', 'admin.customers.organizations.quick_create'],
        'sort'  => 1,
    ], [
        'key'   => 'customers.organizations.view',
        'name'  => 'admin::app.acl.view',
        'route' => ['admin.customers.organizations.view', 'admin.customers.organizations.activities.index'],
        'sort'  => 2,
    ], [
        'key'   => 'customers.organizations.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => ['admin.customers.organizations.edit', 'admin.customers.organizations.update', 'admin.customers.organizations.files.store', 'admin.customers.organizations.files.delete', 'admin.customers.organizations.industries.index', 'admin.customers.organizations.industries.store', 'admin.customers.organizations.industries.edit', 'admin.customers.organizations.industries.update', 'admin.customers.organizations.industries.delete', 'admin.customers.organizations.portal_users.store', 'admin.customers.organizations.portal_users.update', 'admin.customers.organizations.portal_users.status', 'admin.customers.organizations.portal_users.resend', 'admin.customers.organizations.portal_users.destroy'],
        'sort'  => 3,
    ], [
        'key'   => 'customers.organizations.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => ['admin.customers.organizations.delete', 'admin.customers.organizations.mass_delete'],
        'sort'  => 4,
    ], [
        'key'   => 'vendors',
        'name'  => 'Vendors',
        'route' => ['admin.vendors.persons.index', 'admin.vendors.organizations.index'],
        'sort'  => 7,
    ], [
        'key'   => 'vendors.persons',
        'name'  => 'Vendor Contacts',
        'route' => ['admin.vendors.persons.index', 'admin.vendors.persons.search'],
        'sort'  => 1,
    ], [
        'key'   => 'vendors.persons.create',
        'name'  => 'admin::app.acl.create',
        'route' => ['admin.vendors.persons.create', 'admin.vendors.persons.store'],
        'sort'  => 1,
    ], [
        'key'   => 'vendors.persons.view',
        'name'  => 'admin::app.acl.view',
        'route' => ['admin.vendors.persons.view', 'admin.vendors.persons.activities.index'],
        'sort'  => 2,
    ], [
        'key'   => 'vendors.persons.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => ['admin.vendors.persons.edit', 'admin.vendors.persons.update', 'admin.vendors.persons.tags.attach', 'admin.vendors.persons.tags.detach'],
        'sort'  => 3,
    ], [
        'key'   => 'vendors.persons.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => ['admin.vendors.persons.delete', 'admin.vendors.persons.mass_delete'],
        'sort'  => 4,
    ], [
        'key'   => 'vendors.organizations',
        'name'  => 'Vendor Companies',
        'route' => ['admin.vendors.organizations.index', 'admin.vendors.organizations.fetch', 'admin.vendors.organizations.search_customers', 'admin.vendors.organizations.show'],
        'sort'  => 2,
    ], [
        'key'   => 'vendors.organizations.create',
        'name'  => 'admin::app.acl.create',
        'route' => ['admin.vendors.organizations.create', 'admin.vendors.organizations.store', 'admin.vendors.organizations.quick_create'],
        'sort'  => 1,
    ], [
        'key'   => 'vendors.organizations.view',
        'name'  => 'admin::app.acl.view',
        'route' => ['admin.vendors.organizations.view', 'admin.vendors.organizations.activities.index'],
        'sort'  => 2,
    ], [
        'key'   => 'vendors.organizations.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => ['admin.vendors.organizations.edit', 'admin.vendors.organizations.update', 'admin.vendors.organizations.files.store', 'admin.vendors.organizations.files.delete', 'admin.vendors.organizations.industries.index', 'admin.vendors.organizations.industries.store', 'admin.vendors.organizations.industries.edit', 'admin.vendors.organizations.industries.update', 'admin.vendors.organizations.industries.delete'],
        'sort'  => 3,
    ], [
        'key'   => 'vendors.organizations.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => ['admin.vendors.organizations.delete', 'admin.vendors.organizations.mass_delete'],
        'sort'  => 4,
    ], [
        'key'   => 'contacts',
        'name'  => 'All Customer & Vendor Records',
        'route' => ['admin.contacts.persons.index', 'admin.contacts.organizations.index'],
        'sort'  => 8,
        'hidden' => true,
    ], [
        'key'   => 'contacts.persons',
        'name'  => 'All Contacts',
        'route' => ['admin.contacts.persons.index', 'admin.contacts.persons.search'],
        'sort'  => 1,
    ], [
        'key'   => 'contacts.persons.create',
        'name'  => 'admin::app.acl.create',
        'route' => ['admin.contacts.persons.create', 'admin.contacts.persons.store'],
        'sort'  => 1,
    ], [
        'key'   => 'contacts.persons.view',
        'name'  => 'admin::app.acl.view',
        'route' => ['admin.contacts.persons.view', 'admin.contacts.persons.activities.index'],
        'sort'  => 2,
    ], [
        'key'   => 'contacts.persons.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => ['admin.contacts.persons.edit', 'admin.contacts.persons.update', 'admin.contacts.persons.tags.attach', 'admin.contacts.persons.tags.detach'],
        'sort'  => 3,
    ], [
        'key'   => 'contacts.persons.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => ['admin.contacts.persons.delete', 'admin.contacts.persons.mass_delete'],
        'sort'  => 4,
    ], [
        'key'   => 'contacts.organizations',
        'name'  => 'All Companies',
        'route' => ['admin.contacts.organizations.index', 'admin.contacts.organizations.fetch', 'admin.contacts.organizations.search_customers', 'admin.contacts.organizations.show'],
        'sort'  => 2,
    ], [
        'key'   => 'contacts.organizations.create',
        'name'  => 'admin::app.acl.create',
        'route' => ['admin.contacts.organizations.create', 'admin.contacts.organizations.store', 'admin.contacts.organizations.quick_create'],
        'sort'  => 1,
    ], [
        'key'   => 'contacts.organizations.view',
        'name'  => 'admin::app.acl.view',
        'route' => ['admin.contacts.organizations.view', 'admin.contacts.organizations.activities.index'],
        'sort'  => 2,
    ], [
        'key'   => 'contacts.organizations.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => ['admin.contacts.organizations.edit', 'admin.contacts.organizations.update', 'admin.contacts.organizations.files.store', 'admin.contacts.organizations.files.delete', 'admin.contacts.organizations.industries.index', 'admin.contacts.organizations.industries.store', 'admin.contacts.organizations.industries.edit', 'admin.contacts.organizations.industries.update', 'admin.contacts.organizations.industries.delete'],
        'sort'  => 3,
    ], [
        'key'   => 'contacts.organizations.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => ['admin.contacts.organizations.delete', 'admin.contacts.organizations.mass_delete'],
        'sort'  => 4,
    ], [
        'key'   => 'products',
        'name'  => 'admin::app.acl.products',
        'route' => 'admin.products.index',
        'sort'  => 7,
    ], [
        'key'   => 'products.create',
        'name'  => 'admin::app.acl.create',
        'route' => ['admin.products.create', 'admin.products.store', 'admin.products.quick_store', 'admin.products.duplicate', 'admin.settings.units.index', 'admin.settings.units.store'],
        'sort'  => 1,
    ], [
        'key'   => 'products.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => ['admin.products.edit', 'admin.products.update', 'admin.products.toggle_publish', 'admin.products.inventories.store', 'admin.products.tags.attach', 'admin.products.tags.detach', 'admin.settings.units.index', 'admin.settings.units.store'],
        'sort'  => 2,
    ], [
        'key'   => 'products.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => ['admin.products.delete', 'admin.products.mass_delete'],
        'sort'  => 3,
    ], [
        'key'   => 'products.view',
        'name'  => 'admin::app.acl.view',
        'route' => ['admin.products.view', 'admin.products.search', 'admin.products.warehouses', 'admin.products.check_slug', 'admin.products.activities.index'],
        'sort'  => 4,
    ], [
        'key'   => 'products.categories',
        'name'  => 'Product Categories',
        'route' => 'admin.product_categories.index',
        'sort'  => 5,
    ], [
        'key'   => 'products.categories.create',
        'name'  => 'admin::app.acl.create',
        'route' => ['admin.product_categories.create', 'admin.product_categories.store'],
        'sort'  => 1,
    ], [
        'key'   => 'products.categories.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => ['admin.product_categories.edit', 'admin.product_categories.update'],
        'sort'  => 2,
    ], [
        'key'   => 'products.categories.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => 'admin.product_categories.destroy',
        'sort'  => 3,
    ], [
        'key'   => 'settings',
        'name'  => 'admin::app.acl.settings',
        'route' => ['admin.settings.index', 'admin.settings.search'],
        'sort'  => 8,
    ], [
        'key'   => 'settings.user',
        'name'  => 'admin::app.acl.user',
        'route' => ['admin.settings.groups.index', 'admin.settings.roles.index', 'admin.settings.users.index'],
        'sort'  => 1,
    ], [
        'key'   => 'settings.user.groups',
        'name'  => 'admin::app.acl.groups',
        'route' => 'admin.settings.groups.index',
        'sort'  => 1,
    ], [
        'key'   => 'settings.user.groups.create',
        'name'  => 'admin::app.acl.create',
        'route' => 'admin.settings.groups.store',
        'sort'  => 1,
    ], [
        'key'   => 'settings.user.groups.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => ['admin.settings.groups.edit', 'admin.settings.groups.update'],
        'sort'  => 2,
    ], [
        'key'   => 'settings.user.groups.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => 'admin.settings.groups.delete',
        'sort'  => 3,
    ], [
        'key'   => 'settings.user.roles',
        'name'  => 'admin::app.acl.roles',
        'route' => 'admin.settings.roles.index',
        'sort'  => 2,
    ], [
        'key'   => 'settings.user.roles.create',
        'name'  => 'admin::app.acl.create',
        'route' => ['admin.settings.roles.create', 'admin.settings.roles.store'],
        'sort'  => 1,
    ], [
        'key'   => 'settings.user.roles.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => ['admin.settings.roles.edit', 'admin.settings.roles.update'],
        'sort'  => 2,
    ], [
        'key'   => 'settings.user.roles.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => 'admin.settings.roles.delete',
        'sort'  => 3,
    ],  [
        'key'   => 'settings.user.users',
        'name'  => 'Employees',
        'route' => ['admin.settings.users.index', 'admin.settings.users.search', 'admin.employees.persons.index', 'admin.employees.persons.search'],
        'sort'  => 3,
    ], [
        'key'   => 'settings.user.users.create',
        'name'  => 'admin::app.acl.create',
        'route' => ['admin.settings.users.store', 'admin.employees.persons.create', 'admin.employees.persons.store'],
        'sort'  => 1,
    ], [
        'key'   => 'settings.user.users.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => ['admin.settings.users.edit', 'admin.settings.users.update', 'admin.settings.users.mass_update', 'admin.employees.persons.edit', 'admin.employees.persons.update', 'admin.employees.persons.view', 'admin.employees.persons.activities.index', 'admin.employees.persons.tags.attach', 'admin.employees.persons.tags.detach'],
        'sort'  => 2,
    ], [
        'key'   => 'settings.user.users.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => ['admin.settings.users.delete', 'admin.settings.users.mass_delete', 'admin.employees.persons.delete', 'admin.employees.persons.mass_delete'],
        'sort'  => 3,
    ], [
        'key'   => 'settings.lead',
        'name'  => 'admin::app.acl.lead',
        'route' => ['admin.settings.pipelines.index', 'admin.settings.sources.index', 'admin.settings.types.index'],
        'sort'  => 2,
    ], [
        'key'   => 'settings.lead.pipelines',
        'name'  => 'admin::app.acl.pipelines',
        'route' => 'admin.settings.pipelines.index',
        'sort'  => 1,
    ], [
        'key'   => 'settings.lead.pipelines.create',
        'name'  => 'admin::app.acl.create',
        'route' => ['admin.settings.pipelines.create', 'admin.settings.pipelines.store'],
        'sort'  => 1,
    ], [
        'key'   => 'settings.lead.pipelines.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => ['admin.settings.pipelines.edit', 'admin.settings.pipelines.update', 'admin.settings.pipelines.rename', 'admin.settings.pipelines.stages.store', 'admin.settings.pipelines.stages.reorder', 'admin.settings.pipelines.stages.update', 'admin.settings.pipelines.stages.delete'],
        'sort'  => 2,
    ], [
        'key'   => 'settings.lead.pipelines.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => 'admin.settings.pipelines.delete',
        'sort'  => 3,
    ], [
        'key'   => 'settings.lead.sources',
        'name'  => 'admin::app.acl.sources',
        'route' => 'admin.settings.sources.index',
        'sort'  => 2,
    ], [
        'key'   => 'settings.lead.sources.create',
        'name'  => 'admin::app.acl.create',
        'route' => ['admin.settings.sources.store'],
        'sort'  => 1,
    ], [
        'key'   => 'settings.lead.sources.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => ['admin.settings.sources.edit', 'admin.settings.sources.update'],
        'sort'  => 2,
    ], [
        'key'   => 'settings.lead.sources.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => 'admin.settings.sources.delete',
        'sort'  => 3,
    ], [
        'key'   => 'settings.lead.types',
        'name'  => 'admin::app.acl.types',
        'route' => 'admin.settings.types.index',
        'sort'  => 3,
    ], [
        'key'   => 'settings.lead.types.create',
        'name'  => 'admin::app.acl.create',
        'route' => ['admin.settings.types.store'],
        'sort'  => 1,
    ], [
        'key'   => 'settings.lead.types.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => ['admin.settings.types.edit', 'admin.settings.types.update'],
        'sort'  => 2,
    ], [
        'key'   => 'settings.lead.types.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => 'admin.settings.types.delete',
        'sort'  => 3,
    ], [
        'key'   => 'settings.automation',
        'name'  => 'admin::app.acl.automation',
        'route' => ['admin.settings.attributes.index', 'admin.settings.email_templates.index', 'admin.settings.workflows.index'],
        'sort'  => 3,
    ], [
        'key'   => 'settings.automation.attributes',
        'name'  => 'admin::app.acl.attributes',
        'route' => ['admin.settings.attributes.index', 'admin.settings.attributes.download'],
        'sort'  => 1,
    ], [
        'key'   => 'settings.automation.attributes.create',
        'name'  => 'admin::app.acl.create',
        'route' => ['admin.settings.attributes.create', 'admin.settings.attributes.store'],
        'sort'  => 1,
    ], [
        'key'   => 'settings.automation.attributes.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => ['admin.settings.attributes.edit', 'admin.settings.attributes.update', 'admin.settings.attributes.mass_update', 'admin.settings.attributes.check_unique_validation', 'admin.settings.attributes.lookup', 'admin.settings.attributes.lookup_entity', 'admin.settings.attributes.options'],
        'sort'  => 2,
    ], [
        'key'   => 'settings.automation.attributes.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => ['admin.settings.attributes.delete', 'admin.settings.attributes.mass_delete'],
        'sort'  => 3,
    ], [
        'key'   => 'settings.automation.email_templates',
        'name'  => 'admin::app.acl.email-templates',
        'route' => 'admin.settings.email_templates.index',
        'sort'  => 7,
    ], [
        'key'   => 'settings.automation.email_templates.create',
        'name'  => 'admin::app.acl.create',
        'route' => ['admin.settings.email_templates.create', 'admin.settings.email_templates.store'],
        'sort'  => 1,
    ], [
        'key'   => 'settings.automation.email_templates.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => ['admin.settings.email_templates.edit', 'admin.settings.email_templates.update'],
        'sort'  => 2,
    ], [
        'key'   => 'settings.automation.email_templates.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => 'admin.settings.email_templates.delete',
        'sort'  => 3,
    ], [
        'key'   => 'settings.automation.workflows',
        'name'  => 'admin::app.acl.workflows',
        'route' => 'admin.settings.workflows.index',
        'sort'  => 2,
    ], [
        'key'   => 'settings.automation.workflows.create',
        'name'  => 'admin::app.acl.create',
        'route' => ['admin.settings.workflows.create', 'admin.settings.workflows.store'],
        'sort'  => 1,
    ], [
        'key'   => 'settings.automation.workflows.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => ['admin.settings.workflows.edit', 'admin.settings.workflows.update'],
        'sort'  => 2,
    ], [
        'key'   => 'settings.automation.workflows.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => 'admin.settings.workflows.delete',
        'sort'  => 3,
    ], [
        'key'   => 'settings.automation.web_forms',
        'name'  => 'Web Forms',
        'route' => ['admin.settings.web_forms.index', 'admin.settings.web_forms.view', 'admin.settings.web_forms.preview', 'admin.settings.web_forms.form_js'],
        'sort'  => 3,
    ], [
        'key'   => 'settings.automation.web_forms.create',
        'name'  => 'admin::app.acl.create',
        'route' => ['admin.settings.web_forms.create', 'admin.settings.web_forms.store', 'admin.settings.web_forms.form_store'],
        'sort'  => 1,
    ], [
        'key'   => 'settings.automation.web_forms.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => ['admin.settings.web_forms.edit', 'admin.settings.web_forms.update'],
        'sort'  => 2,
    ], [
        'key'   => 'settings.automation.web_forms.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => 'admin.settings.web_forms.delete',
        'sort'  => 3,
    ], [
        'key'   => 'settings.automation.events',
        'name'  => 'admin::app.acl.event',
        'route' => 'admin.settings.marketing.events.index',
        'sort'  => 2,
    ], [
        'key'   => 'settings.automation.events.create',
        'name'  => 'admin::app.acl.create',
        'route' => 'admin.settings.marketing.events.store',
        'sort'  => 1,
    ], [
        'key'   => 'settings.automation.events.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => ['admin.settings.marketing.events.edit', 'admin.settings.marketing.events.update'],
        'sort'  => 2,
    ], [
        'key'   => 'settings.automation.events.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => ['admin.settings.marketing.events.delete', 'admin.settings.marketing.events.mass_delete'],
        'sort'  => 3,
    ], [
        'key'   => 'settings.automation.campaigns',
        'name'  => 'admin::app.acl.campaigns',
        'route' => 'admin.settings.marketing.campaigns.index',
        'sort'  => 2,
    ], [
        'key'   => 'settings.automation.campaigns.create',
        'name'  => 'admin::app.acl.create',
        'route' => 'admin.settings.marketing.campaigns.store',
        'sort'  => 1,
    ], [
        'key'   => 'settings.automation.campaigns.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => ['admin.settings.marketing.campaigns.edit', 'admin.settings.marketing.campaigns.update', 'admin.settings.marketing.campaigns.events', 'admin.settings.marketing.campaigns.email-templates'],
        'sort'  => 2,
    ], [
        'key'   => 'settings.automation.campaigns.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => ['admin.settings.marketing.campaigns.delete', 'admin.settings.marketing.campaigns.mass_delete'],
        'sort'  => 3,
    ], [
        'key'   => 'settings.automation.webhooks',
        'name'  => 'admin::app.acl.webhook',
        'route' => 'admin.settings.webhooks.index',
        'sort'  => 1,
    ], [
        'key'   => 'settings.automation.webhooks.create',
        'name'  => 'admin::app.acl.create',
        'route' => ['admin.settings.webhooks.create', 'admin.settings.webhooks.store'],
        'sort'  => 1,
    ], [
        'key'   => 'settings.automation.webhooks.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => ['admin.settings.webhooks.edit', 'admin.settings.webhooks.update'],
        'sort'  => 2,
    ], [
        'key'   => 'settings.automation.webhooks.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => 'admin.settings.webhooks.delete',
        'sort'  => 3,
    ], [
        'key'   => 'settings.other_settings',
        'name'  => 'admin::app.acl.other-settings',
        'route' => ['admin.settings.tags.index', 'admin.settings.tags.search'],
        'sort'  => 4,
    ], [
        'key'   => 'settings.other_settings.tags',
        'name'  => 'admin::app.acl.tags',
        'route' => 'admin.settings.tags.index',
        'sort'  => 1,
    ], [
        'key'   => 'settings.other_settings.tags.create',
        'name'  => 'admin::app.acl.create',
        'route' => ['admin.settings.tags.store', 'admin.leads.tags.attach'],
        'sort'  => 1,
    ], [
        'key'   => 'settings.other_settings.tags.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => ['admin.settings.tags.edit', 'admin.settings.tags.update'],
        'sort'  => 1,
    ], [
        'key'   => 'settings.other_settings.tags.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => ['admin.settings.tags.delete', 'admin.settings.tags.mass_delete', 'admin.leads.tags.detach'],
        'sort'  => 2,
    ],
    [
        'key'   => 'settings.data_transfer',
        'name'  => 'admin::app.acl.data-transfer',
        'route' => 'admin.settings.data_transfer.imports.index',
        'sort'  => 10,
    ], [
        'key'   => 'settings.data_transfer.imports',
        'name'  => 'admin::app.acl.imports',
        'route' => 'admin.settings.data_transfer.imports.index',
        'sort'  => 1,
    ], [
        'key'   => 'settings.data_transfer.imports.create',
        'name'  => 'admin::app.acl.create',
        'route' => ['admin.settings.data_transfer.imports.create', 'admin.settings.data_transfer.imports.store'],
        'sort'  => 1,
    ], [
        'key'   => 'settings.data_transfer.imports.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => ['admin.settings.data_transfer.imports.edit', 'admin.settings.data_transfer.imports.update'],
        'sort'  => 2,
    ], [
        'key'   => 'settings.data_transfer.imports.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => 'admin.settings.data_transfer.imports.delete',
        'sort'  => 3,
    ], [
        'key'   => 'settings.data_transfer.imports.import',
        'name'  => 'admin::app.acl.import',
        'route' => ['admin.settings.data_transfer.imports.import', 'admin.settings.data_transfer.imports.validate', 'admin.settings.data_transfer.imports.start', 'admin.settings.data_transfer.imports.link', 'admin.settings.data_transfer.imports.index_data', 'admin.settings.data_transfer.imports.stats', 'admin.settings.data_transfer.imports.download_sample', 'admin.settings.data_transfer.imports.download', 'admin.settings.data_transfer.imports.download_error_report'],
        'sort'  => 4,
    ],
    [
        'key'   => 'configuration',
        'name'  => 'admin::app.acl.configuration',
        'route' => ['admin.configuration.index', 'admin.configuration.store', 'admin.configuration.search', 'admin.configuration.download'],
        'sort'  => 10,
    ], [
        'key'   => 'configuration.warehouses',
        'name'  => 'Warehouses',
        'route' => ['admin.settings.warehouses.index', 'admin.settings.warehouses.search', 'admin.settings.warehouses.products.index', 'admin.settings.warehouses.view', 'admin.settings.warehouse.activities.index'],
        'sort'  => 1,
    ], [
        'key'   => 'configuration.warehouses.create',
        'name'  => 'admin::app.acl.create',
        'route' => ['admin.settings.warehouses.create', 'admin.settings.warehouses.store'],
        'sort'  => 1,
    ], [
        'key'   => 'configuration.warehouses.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => ['admin.settings.warehouses.edit', 'admin.settings.warehouses.update', 'admin.settings.warehouses.tags.attach', 'admin.settings.warehouses.tags.detach', 'admin.settings.locations.search', 'admin.settings.locations.store', 'admin.settings.locations.update', 'admin.settings.locations.delete'],
        'sort'  => 2,
    ], [
        'key'   => 'configuration.warehouses.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => 'admin.settings.warehouses.delete',
        'sort'  => 3,
    ], [
        'key'   => 'configuration.material_references',
        'name'  => 'Material References',
        'route' => 'admin.settings.material_references.index',
        'sort'  => 2,
    ], [
        'key'   => 'configuration.material_references.create',
        'name'  => 'admin::app.acl.create',
        'route' => 'admin.settings.material_references.store',
        'sort'  => 1,
    ], [
        'key'   => 'configuration.material_references.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => ['admin.settings.material_references.edit', 'admin.settings.material_references.update'],
        'sort'  => 3,
    ], [
        'key'   => 'configuration.material_references.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => ['admin.settings.material_references.delete', 'admin.settings.material_references.mass_delete'],
        'sort'  => 4,
    ], [
        'key'   => 'configuration.color_references',
        'name'  => 'Color References',
        'route' => 'admin.settings.color_references.index',
        'sort'  => 2,
    ], [
        'key'   => 'configuration.color_references.create',
        'name'  => 'admin::app.acl.create',
        'route' => 'admin.settings.color_references.store',
        'sort'  => 1,
    ], [
        'key'   => 'configuration.color_references.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => ['admin.settings.color_references.edit', 'admin.settings.color_references.update'],
        'sort'  => 2,
    ], [
        'key'   => 'configuration.color_references.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => ['admin.settings.color_references.delete', 'admin.settings.color_references.mass_delete'],
        'sort'  => 3,
    ], [
        'key'   => 'configuration.units',
        'name'  => 'Units',
        'route' => 'admin.settings.units.index',
        'sort'  => 3,
    ], [
        'key'   => 'configuration.units.create',
        'name'  => 'admin::app.acl.create',
        'route' => 'admin.settings.units.store',
        'sort'  => 1,
    ], [
        'key'   => 'configuration.units.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => ['admin.settings.units.edit', 'admin.settings.units.update'],
        'sort'  => 2,
    ], [
        'key'   => 'configuration.units.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => ['admin.settings.units.delete', 'admin.settings.units.mass_delete'],
        'sort'  => 3,
    ], [
        'key'   => 'website_submissions',
        'name'  => 'Website Submissions',
        'route' => ['admin.website_submissions.index', 'admin.website_submissions.contacts', 'admin.website_submissions.contact.show', 'admin.website_submissions.careers', 'admin.website_submissions.career.show', 'admin.website_submissions.api.contacts', 'admin.website_submissions.api.careers'],
        'sort'  => 11,
    ],
];
