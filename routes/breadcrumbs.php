<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Dashboard
Breadcrumbs::for('dashboard', function (BreadcrumbTrail $trail) {
    $trail->push(trans('admin::app.layouts.dashboard'), route('admin.dashboard.index'));
});

// Dashboard > Leads
Breadcrumbs::for('leads', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(trans('admin::app.layouts.leads'), route('admin.leads.index'));
});

// Dashboard > Leads > Create
Breadcrumbs::for('leads.create', function (BreadcrumbTrail $trail) {
    $trail->parent('leads');
    $trail->push(trans('admin::app.leads.create.title'), route('admin.leads.create'));
});

// Leads Edit
Breadcrumbs::for('leads.edit', function (BreadcrumbTrail $trail, $lead) {
    $trail->parent('leads');
    $trail->push(trans('admin::app.leads.edit.title'), route('admin.leads.edit', $lead->id));
});

// Dashboard > Leads > Title
Breadcrumbs::for('leads.view', function (BreadcrumbTrail $trail, $lead) {
    $trail->parent('leads');
    $trail->push('#'.$lead->id, route('admin.leads.view', $lead->id));
});

// Dashboard > Quotes
Breadcrumbs::for('quotes', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(trans('admin::app.layouts.quotes'), route('admin.quotes.index'));
});

// Dashboard > Quotes > Add Quote
Breadcrumbs::for('quotes.create', function (BreadcrumbTrail $trail) {
    $trail->parent('quotes');
    $trail->push(trans('admin::app.quotes.create.title'), route('admin.quotes.create'));
});

// Dashboard > Quotes > Edit Quote
Breadcrumbs::for('quotes.edit', function (BreadcrumbTrail $trail, $quote) {
    $trail->parent('quotes');
    $trail->push(trans('admin::app.quotes.edit.title'), route('admin.quotes.edit', $quote->id));
});

// Dashboard > Purchase Orders
Breadcrumbs::for('purchase_orders', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(trans('admin::app.layouts.purchase-orders'), route('admin.purchase_orders.index'));
});

// Dashboard > Purchase Orders > Create
Breadcrumbs::for('purchase_orders.create', function (BreadcrumbTrail $trail) {
    $trail->parent('purchase_orders');
    $trail->push(trans('admin::app.purchase-orders.create.title'), route('admin.purchase_orders.create'));
});

// Dashboard > Purchase Orders > Edit
Breadcrumbs::for('purchase_orders.edit', function (BreadcrumbTrail $trail, $purchaseOrder) {
    $trail->parent('purchase_orders');
    $trail->push(trans('admin::app.purchase-orders.edit.title'), route('admin.purchase_orders.edit', $purchaseOrder->id));
});

// Mail
Breadcrumbs::for('mail', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(trans('admin::app.layouts.mail.title'), route('admin.mail.index', ['route' => 'inbox']));
});

// Mail > [Compose | Inbox | Outbox | Draft | Sent | Trash]
Breadcrumbs::for('mail.route', function (BreadcrumbTrail $trail, $route) {
    $trail->parent('mail');
    $trail->push(trans('admin::app.mail.index.'.$route), route('admin.mail.index', ['route' => $route]));
});

// Mail > [Inbox | Outbox | Draft | Sent | Trash] > Title
Breadcrumbs::for('mail.route.view', function (BreadcrumbTrail $trail, $route, $email) {
    $trail->parent('mail.route', $route);
    $trail->push($email->subject ?? '', route('admin.mail.view', ['route' => $route, 'id' => $email->id]));
});

// Dashboard > Activities
Breadcrumbs::for('activities', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(trans('admin::app.layouts.activities'), route('admin.activities.index'));
});

// Dashboard > Activities > Calendar
Breadcrumbs::for('activities.calendar', function (BreadcrumbTrail $trail) {
    $trail->parent('activities');
    $trail->push('Events', route('admin.activities.calendar'));
});

// Dashboard > activities > Edit Activity
Breadcrumbs::for('activities.edit', function (BreadcrumbTrail $trail, $activity) {
    $trail->parent('activities');
    $trail->push(trans('admin::app.activities.edit.title'), route('admin.activities.edit', $activity->id));
});

// Dashboard > Contacts
Breadcrumbs::for('contacts', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(trans('admin::app.layouts.contacts'), route('admin.contacts.persons.index'));
});

// Dashboard > Contacts > Persons
Breadcrumbs::for('contacts.persons', function (BreadcrumbTrail $trail) {
    $trail->parent('contacts');
    $trail->push(trans('admin::app.layouts.persons'), route('admin.contacts.persons.index'));
});

// Dashboard > Contacts > Persons > Create
Breadcrumbs::for('contacts.persons.create', function (BreadcrumbTrail $trail) {
    $trail->parent('contacts.persons');
    $trail->push(trans('admin::app.contacts.persons.create.title'), route('admin.contacts.persons.create'));
});

// Dashboard > Contacts > Persons > Edit
Breadcrumbs::for('contacts.persons.edit', function (BreadcrumbTrail $trail, $person) {
    $trail->parent('contacts.persons');
    $trail->push(trans('admin::app.contacts.persons.edit.title'), route('admin.contacts.persons.edit', $person->id));
});

// Dashboard > Contacts > Persons > View
Breadcrumbs::for('contacts.persons.view', function (BreadcrumbTrail $trail, $person) {
    $trail->parent('contacts.persons');
    $trail->push('#'.$person->id, route('admin.contacts.persons.index'));
});

// Dashboard > Contacts > Organizations
Breadcrumbs::for('contacts.organizations', function (BreadcrumbTrail $trail) {
    $trail->parent('contacts');
    $trail->push(trans('admin::app.layouts.organizations'), route('admin.contacts.organizations.index'));
});

// Dashboard > Contacts > Organizations > Create
Breadcrumbs::for('contacts.organizations.create', function (BreadcrumbTrail $trail) {
    $trail->parent('contacts.organizations');
    $trail->push(trans('admin::app.contacts.organizations.create.title'), route('admin.contacts.organizations.create'));
});

// Dashboard > Contacts > Organizations > Edit
Breadcrumbs::for('contacts.organizations.edit', function (BreadcrumbTrail $trail, $organization) {
    $trail->parent('contacts.organizations');
    $trail->push(trans('admin::app.contacts.organizations.edit.title'), route('admin.contacts.organizations.edit', $organization->id));
});

// Dashboard > Contacts > Organizations > View
Breadcrumbs::for('contacts.organizations.view', function (BreadcrumbTrail $trail, $organization) {
    $trail->parent('contacts.organizations');
    $trail->push('#'.$organization->id, route('admin.contacts.organizations.view', $organization->id));
});

// Dashboard > Customer
Breadcrumbs::for('customers', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Customer', route('admin.customers.organizations.index'));
});

// Dashboard > Customer > Contacts
Breadcrumbs::for('customers.persons', function (BreadcrumbTrail $trail) {
    $trail->parent('customers');
    $trail->push('Contacts', route('admin.customers.persons.index'));
});

// Dashboard > Customer > Contacts > Create
Breadcrumbs::for('customers.persons.create', function (BreadcrumbTrail $trail) {
    $trail->parent('customers.persons');
    $trail->push('Create Contact', route('admin.customers.persons.create'));
});

// Dashboard > Customer > Contacts > Edit
Breadcrumbs::for('customers.persons.edit', function (BreadcrumbTrail $trail, $person) {
    $trail->parent('customers.persons');
    $trail->push('Edit Contact', route('admin.customers.persons.edit', $person->id));
});

// Dashboard > Customer > Contacts > View
Breadcrumbs::for('customers.persons.view', function (BreadcrumbTrail $trail, $person) {
    $trail->parent('customers.persons');
    $trail->push('#'.$person->id, route('admin.customers.persons.view', $person->id));
});

// Dashboard > Customer > Companies
Breadcrumbs::for('customers.organizations', function (BreadcrumbTrail $trail) {
    $trail->parent('customers');
    $trail->push('Companies', route('admin.customers.organizations.index'));
});

// Dashboard > Customer > Companies > Create
Breadcrumbs::for('customers.organizations.create', function (BreadcrumbTrail $trail) {
    $trail->parent('customers.organizations');
    $trail->push('Create Company', route('admin.customers.organizations.create'));
});

// Dashboard > Customer > Companies > Edit
Breadcrumbs::for('customers.organizations.edit', function (BreadcrumbTrail $trail, $organization) {
    $trail->parent('customers.organizations');
    $trail->push('Edit Company', route('admin.customers.organizations.edit', $organization->id));
});

// Dashboard > Customer > Companies > View
Breadcrumbs::for('customers.organizations.view', function (BreadcrumbTrail $trail, $organization) {
    $trail->parent('customers.organizations');
    $trail->push('#'.$organization->id, route('admin.customers.organizations.view', $organization->id));
});

// Dashboard > Vendor
Breadcrumbs::for('vendors', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Vendor', route('admin.vendors.organizations.index'));
});

// Dashboard > Vendor > Contacts
Breadcrumbs::for('vendors.persons', function (BreadcrumbTrail $trail) {
    $trail->parent('vendors');
    $trail->push('Contacts', route('admin.vendors.persons.index'));
});

// Dashboard > Vendor > Contacts > Create
Breadcrumbs::for('vendors.persons.create', function (BreadcrumbTrail $trail) {
    $trail->parent('vendors.persons');
    $trail->push('Create Contact', route('admin.vendors.persons.create'));
});

// Dashboard > Vendor > Contacts > Edit
Breadcrumbs::for('vendors.persons.edit', function (BreadcrumbTrail $trail, $person) {
    $trail->parent('vendors.persons');
    $trail->push('Edit Contact', route('admin.vendors.persons.edit', $person->id));
});

// Dashboard > Vendor > Contacts > View
Breadcrumbs::for('vendors.persons.view', function (BreadcrumbTrail $trail, $person) {
    $trail->parent('vendors.persons');
    $trail->push('#'.$person->id, route('admin.vendors.persons.view', $person->id));
});

// Dashboard > Vendor > Vendors
Breadcrumbs::for('vendors.organizations', function (BreadcrumbTrail $trail) {
    $trail->parent('vendors');
    $trail->push('Vendors', route('admin.vendors.organizations.index'));
});

// Dashboard > Vendor > Vendors > Create
Breadcrumbs::for('vendors.organizations.create', function (BreadcrumbTrail $trail) {
    $trail->parent('vendors.organizations');
    $trail->push('Create Vendor', route('admin.vendors.organizations.create'));
});

// Dashboard > Vendor > Vendors > Edit
Breadcrumbs::for('vendors.organizations.edit', function (BreadcrumbTrail $trail, $organization) {
    $trail->parent('vendors.organizations');
    $trail->push('Edit Vendor', route('admin.vendors.organizations.edit', $organization->id));
});

// Dashboard > Vendor > Vendors > View
Breadcrumbs::for('vendors.organizations.view', function (BreadcrumbTrail $trail, $organization) {
    $trail->parent('vendors.organizations');
    $trail->push('#'.$organization->id, route('admin.vendors.organizations.view', $organization->id));
});

// Dashboard > Employees
Breadcrumbs::for('employees', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Employees', route('admin.employees.persons.index'));
});

// Dashboard > Employees > Employees
Breadcrumbs::for('employees.persons', function (BreadcrumbTrail $trail) {
    $trail->parent('employees');
    $trail->push('Employees', route('admin.employees.persons.index'));
});

// Dashboard > Employees > Employees > Create
Breadcrumbs::for('employees.persons.create', function (BreadcrumbTrail $trail) {
    $trail->parent('employees.persons');
    $trail->push('Create Employee', route('admin.employees.persons.create'));
});

// Dashboard > Employees > Employees > Edit
Breadcrumbs::for('employees.persons.edit', function (BreadcrumbTrail $trail, $person) {
    $trail->parent('employees.persons');
    $trail->push('Edit Employee', route('admin.employees.persons.edit', $person->id));
});

// Dashboard > Employees > Employees > View
Breadcrumbs::for('employees.persons.view', function (BreadcrumbTrail $trail, $person) {
    $trail->parent('employees.persons');
    $trail->push('#'.$person->id, route('admin.employees.persons.view', $person->id));
});

// Products
Breadcrumbs::for('products', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(trans('admin::app.layouts.products'), route('admin.products.index'));
});

// Dashboard > Products > Create Product
Breadcrumbs::for('products.create', function (BreadcrumbTrail $trail) {
    $trail->parent('products');
    $trail->push(trans('admin::app.products.create.title'), route('admin.products.create'));
});

// Dashboard > Products > View Product
Breadcrumbs::for('products.view', function (BreadcrumbTrail $trail, $product) {
    $trail->parent('products');
    $trail->push('#'.$product->id, route('admin.products.view', $product->id));
});

// Dashboard > Products > Edit Product
Breadcrumbs::for('products.edit', function (BreadcrumbTrail $trail, $product) {
    $trail->parent('products');
    $trail->push(trans('admin::app.products.edit.title'), route('admin.products.edit', $product->id));
});

// Settings
Breadcrumbs::for('settings', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(trans('admin::app.layouts.settings'), route('admin.settings.index'));
});

// Settings > Groups
Breadcrumbs::for('settings.groups', function (BreadcrumbTrail $trail) {
    $trail->parent('settings');
    $trail->push(trans('admin::app.layouts.groups'), route('admin.settings.groups.index'));
});

// Dashboard > Groups > Create Group
Breadcrumbs::for('settings.groups.create', function (BreadcrumbTrail $trail) {
    $trail->parent('settings.groups');
    $trail->push(trans('admin::app.settings.groups.create-title'), route('admin.settings.groups.create'));
});

// Dashboard > Groups > Edit Group
Breadcrumbs::for('settings.groups.edit', function (BreadcrumbTrail $trail, $role) {
    $trail->parent('settings.groups');
    $trail->push(trans('admin::app.settings.groups.edit-title'), route('admin.settings.groups.edit', $role->id));
});

// Settings > Roles
Breadcrumbs::for('settings.roles', function (BreadcrumbTrail $trail) {
    $trail->parent('settings');
    $trail->push(trans('admin::app.layouts.roles'), route('admin.settings.roles.index'));
});

// Dashboard > Roles > Create Role
Breadcrumbs::for('settings.roles.create', function (BreadcrumbTrail $trail) {
    $trail->parent('settings.roles');
    $trail->push(trans('admin::app.settings.roles.create.title'), route('admin.settings.roles.create'));
});

// Dashboard > Roles > Edit Role
Breadcrumbs::for('settings.roles.edit', function (BreadcrumbTrail $trail, $role) {
    $trail->parent('settings.roles');
    $trail->push(trans('admin::app.settings.roles.edit.title'), route('admin.settings.roles.edit', $role->id));
});

// Settings > Users
Breadcrumbs::for('settings.users', function (BreadcrumbTrail $trail) {
    $trail->parent('settings');
    $trail->push(trans('admin::app.layouts.users'), route('admin.settings.users.index'));
});

// Dashboard > Users > Create Role
Breadcrumbs::for('settings.users.create', function (BreadcrumbTrail $trail) {
    $trail->parent('settings.users');
    $trail->push(trans('admin::app.settings.users.create-title'), route('admin.settings.users.create'));
});

// Dashboard > Users > Edit Role
Breadcrumbs::for('settings.users.edit', function (BreadcrumbTrail $trail, $user) {
    $trail->parent('settings.users');
    $trail->push(trans('admin::app.settings.users.edit-title'), route('admin.settings.users.edit', $user->id));
});

// Settings > Attributes
Breadcrumbs::for('settings.attributes', function (BreadcrumbTrail $trail) {
    $trail->parent('settings');
    $trail->push(trans('admin::app.layouts.attributes'), route('admin.settings.attributes.index'));
});

// Dashboard > Attributes > Create Attribute
Breadcrumbs::for('settings.attributes.create', function (BreadcrumbTrail $trail) {
    $trail->parent('settings.attributes');
    $trail->push(trans('admin::app.settings.attributes.create.title'), route('admin.settings.attributes.create'));
});

// Dashboard > Attributes > Edit Attribute
Breadcrumbs::for('settings.attributes.edit', function (BreadcrumbTrail $trail, $attribute) {
    $trail->parent('settings.attributes');
    $trail->push(trans('admin::app.settings.attributes.edit.title'), route('admin.settings.attributes.edit', $attribute->id));
});

// Settings > Pipelines
Breadcrumbs::for('settings.pipelines', function (BreadcrumbTrail $trail) {
    $trail->parent('settings');
    $trail->push(trans('admin::app.layouts.pipelines'), route('admin.settings.pipelines.index'));
});

// Dashboard > Pipelines > Create Pipeline
Breadcrumbs::for('settings.pipelines.create', function (BreadcrumbTrail $trail) {
    $trail->parent('settings.pipelines');
    $trail->push(trans('admin::app.settings.pipelines.create.title'), route('admin.settings.pipelines.create'));
});

// Dashboard > Pipelines > Edit Pipeline
Breadcrumbs::for('settings.pipelines.edit', function (BreadcrumbTrail $trail, $pipeline) {
    $trail->parent('settings.pipelines');
    $trail->push(trans('admin::app.settings.pipelines.edit.title'), route('admin.settings.pipelines.edit', $pipeline->id));
});

// Settings > Sources
Breadcrumbs::for('settings.sources', function (BreadcrumbTrail $trail) {
    $trail->parent('settings');
    $trail->push(trans('admin::app.layouts.sources'), route('admin.settings.sources.index'));
});

// Dashboard > Sources > Edit Source
Breadcrumbs::for('settings.sources.edit', function (BreadcrumbTrail $trail, $source) {
    $trail->parent('settings.sources');
    $trail->push(trans('admin::app.settings.sources.edit-title'), route('admin.settings.sources.edit', $source->id));
});

// Settings > Types
Breadcrumbs::for('settings.types', function (BreadcrumbTrail $trail) {
    $trail->parent('settings');
    $trail->push(trans('admin::app.layouts.types'), route('admin.settings.types.index'));
});

// Dashboard > Types > Edit Type
Breadcrumbs::for('settings.types.edit', function (BreadcrumbTrail $trail, $type) {
    $trail->parent('settings.types');
    $trail->push(trans('admin::app.settings.types.edit-title'), route('admin.settings.types.edit', $type->id));
});

// Settings > Email Templates
Breadcrumbs::for('settings.email_templates', function (BreadcrumbTrail $trail) {
    $trail->parent('settings');
    $trail->push(trans('admin::app.settings.email-template.index.title'), route('admin.settings.email_templates.index'));
});

// Dashboard > Email Templates > Create Email Template
Breadcrumbs::for('settings.email_templates.create', function (BreadcrumbTrail $trail) {
    $trail->parent('settings.email_templates');
    $trail->push(trans('admin::app.settings.email-template.create.title'), route('admin.settings.email_templates.create'));
});

// Dashboard > Email Templates > Edit Email Template
Breadcrumbs::for('settings.email_templates.edit', function (BreadcrumbTrail $trail, $emailTemplate) {
    $trail->parent('settings.email_templates');
    $trail->push(trans('admin::app.settings.email-template.edit.title'), route('admin.settings.email_templates.edit', $emailTemplate->id));
});

// Settings > Marketing Events
Breadcrumbs::for('settings.marketing.events', function (BreadcrumbTrail $trail) {
    $trail->parent('settings');
    $trail->push(trans('admin::app.settings.marketing.events.index.title'), route('admin.settings.marketing.events.index'));
});

Breadcrumbs::for('settings.marketing.campaigns', function (BreadcrumbTrail $trail) {
    $trail->parent('settings');
    $trail->push(trans('admin::app.settings.marketing.campaigns.index.title'), route('admin.settings.marketing.campaigns.index'));
});

// Settings > Workflows
Breadcrumbs::for('settings.workflows', function (BreadcrumbTrail $trail) {
    $trail->parent('settings');
    $trail->push(trans('admin::app.layouts.workflows'), route('admin.settings.workflows.index'));
});

// Dashboard > Workflows > Create Workflow
Breadcrumbs::for('settings.workflows.create', function (BreadcrumbTrail $trail) {
    $trail->parent('settings.workflows');
    $trail->push(trans('admin::app.settings.workflows.create.title'), route('admin.settings.workflows.create'));
});

// Dashboard > Workflows > Edit Workflow
Breadcrumbs::for('settings.workflows.edit', function (BreadcrumbTrail $trail, $workflow) {
    $trail->parent('settings.workflows');
    $trail->push(trans('admin::app.settings.workflows.edit.title'), route('admin.settings.workflows.edit', $workflow->id));
});

// Settings > Webhooks
Breadcrumbs::for('settings.webhooks', function (BreadcrumbTrail $trail) {
    $trail->parent('settings');
    $trail->push(trans('admin::app.settings.webhooks.index.title'), route('admin.settings.webhooks.index'));
});

// Dashboard > Webhooks > Create Workflow
Breadcrumbs::for('settings.webhooks.create', function (BreadcrumbTrail $trail) {
    $trail->parent('settings.webhooks');
    $trail->push(trans('admin::app.settings.webhooks.create.title'), route('admin.settings.webhooks.create'));
});

// Dashboard > Webhooks > Edit Workflow
Breadcrumbs::for('settings.webhooks.edit', function (BreadcrumbTrail $trail, $workflow) {
    $trail->parent('settings.webhooks');
    $trail->push(trans('admin::app.settings.webhooks.edit.edit-btn'), route('admin.settings.workflows.edit', $workflow->id));
});

// Settings > Tags
Breadcrumbs::for('settings.tags', function (BreadcrumbTrail $trail) {
    $trail->parent('settings');
    $trail->push(trans('admin::app.layouts.tags'), route('admin.settings.tags.index'));
});

// Dashboard > Tags > Edit Tag
Breadcrumbs::for('settings.tags.edit', function (BreadcrumbTrail $trail, $tag) {
    $trail->parent('settings.tags');
    $trail->push(trans('admin::app.settings.tags.edit-title'), route('admin.settings.tags.edit', $tag->id));
});

// Settings > Web Form
Breadcrumbs::for('settings.web_forms', function (BreadcrumbTrail $trail) {
    $trail->parent('settings');
    $trail->push(trans('admin::app.settings.webforms.index.title'), route('admin.settings.web_forms.index'));
});

// Dashboard > Web Form > Create Web Form
Breadcrumbs::for('settings.web_forms.create', function (BreadcrumbTrail $trail) {
    $trail->parent('settings.web_forms');
    $trail->push(trans('admin::app.settings.webforms.create.title'), route('admin.settings.web_forms.create'));
});

// Dashboard > Web Form > Edit Web Form
Breadcrumbs::for('settings.web_forms.edit', function (BreadcrumbTrail $trail, $webForm) {
    $trail->parent('settings.web_forms');
    $trail->push(trans('admin::app.settings.webforms.edit.title'), route('admin.settings.web_forms.edit', $webForm->id));
});

// Settings > Warehouse
Breadcrumbs::for('settings.warehouses', function (BreadcrumbTrail $trail) {
    $trail->parent('settings');
    $trail->push(trans('admin::app.settings.warehouses.index.title'), route('admin.settings.warehouses.index'));
});

// Dashboard > Settings > Warehouse > Create Warehouse
Breadcrumbs::for('settings.warehouses.create', function (BreadcrumbTrail $trail) {
    $trail->parent('settings.warehouses');
    $trail->push(trans('admin::app.settings.warehouses.create.title'), route('admin.settings.warehouses.create'));
});

// Dashboard > Settings > Warehouse > Edit Warehouse
Breadcrumbs::for('settings.warehouses.edit', function (BreadcrumbTrail $trail, $warehouse) {
    $trail->parent('settings.warehouses');
    $trail->push(trans('admin::app.settings.warehouses.edit.title'), route('admin.settings.warehouses.edit', $warehouse->id));
});

// Dashboard > Settings > Warehouse > View Warehouse
Breadcrumbs::for('settings.warehouses.view', function (BreadcrumbTrail $trail, $warehouse) {
    $trail->parent('settings.warehouses');
    $trail->push('#'.$warehouse->id, route('admin.settings.warehouses.view', $warehouse->id));
});

// Dashboard > Settings > Warehouse > View Warehouse > Products
Breadcrumbs::for('settings.warehouses.view.products', function (BreadcrumbTrail $trail, $warehouse) {
    $trail->parent('settings.warehouses.view', $warehouse);
    $trail->push(trans('admin::app.settings.warehouses.products'), route('admin.settings.warehouses.products.index', $warehouse->id));
});

// Dashboard > Settings > Locations
Breadcrumbs::for('settings.locations', function (BreadcrumbTrail $trail) {
    $trail->parent('settings');
    $trail->push(trans('admin::app.settings.locations.title'), route('admin.settings.locations.index'));
});

// Dashboard > Settings > Locations > Create Warehouse
Breadcrumbs::for('settings.locations.create', function (BreadcrumbTrail $trail) {
    $trail->parent('settings.locations');
    $trail->push(trans('admin::app.settings.locations.create-title'), route('admin.settings.locations.create'));
});

// Dashboard > Settings > Locations > Edit Warehouse
Breadcrumbs::for('settings.locations.edit', function (BreadcrumbTrail $trail, $location) {
    $trail->parent('settings.locations');
    $trail->push(trans('admin::app.settings.locations.edit-title'), route('admin.settings.locations.edit', $location->id));
});

// Dashboard > Settings > Data Transfers
Breadcrumbs::for('settings.data_transfers', function (BreadcrumbTrail $trail) {
    $trail->parent('settings');
    $trail->push(trans('admin::app.settings.data-transfer.imports.index.title'), route('admin.settings.data_transfer.imports.index'));
});

// Dashboard > Settings > Data Transfers > Create Data Transfer
Breadcrumbs::for('settings.data_transfers.create', function (BreadcrumbTrail $trail) {
    $trail->parent('settings.data_transfers');
    $trail->push(trans('admin::app.settings.data-transfer.imports.create.title'), route('admin.settings.data_transfer.imports.create'));
});

// Dashboard > Settings > Data Transfers > Edit Data Transfer
Breadcrumbs::for('settings.data_transfers.edit', function (BreadcrumbTrail $trail, $import) {
    $trail->parent('settings.data_transfers');
    $trail->push(trans('admin::app.settings.data-transfer.imports.edit.title'), route('admin.settings.data_transfer.imports.edit', $import->id));
});

// Dashboard > Settings > Data Transfers > Import Data Transfer
Breadcrumbs::for('settings.data_transfers.import', function (BreadcrumbTrail $trail, $import) {
    $trail->parent('settings.data_transfers');
    $trail->push(trans('admin::app.settings.data-transfer.imports.import.title'), route('admin.settings.data_transfer.imports.import', $import->id));
});

// Configuration
Breadcrumbs::for('configuration', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(trans('admin::app.layouts.configuration'), route('admin.configuration.index'));
});

// Configuration > Config
Breadcrumbs::for('configuration.slug', function (BreadcrumbTrail $trail, $slug) {
    $trail->parent('configuration');
    $trail->push('', route('admin.configuration.index', ['slug' => $slug]));
});

// Dashboard > Website Submissions
Breadcrumbs::for('website-submissions', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(trans('admin::app.layouts.website'), route('admin.website_submissions.index'));
});

// Website Submissions > Contacts
Breadcrumbs::for('website-submissions.contacts', function (BreadcrumbTrail $trail) {
    $trail->parent('website-submissions');
    $trail->push(trans('admin::app.website-submissions.contacts'), route('admin.website_submissions.contacts'));
});

// Website Submissions > Careers
Breadcrumbs::for('website-submissions.careers', function (BreadcrumbTrail $trail) {
    $trail->parent('website-submissions');
    $trail->push(trans('admin::app.website-submissions.careers'), route('admin.website_submissions.careers'));
});

// Dashboard > Account > Edit
Breadcrumbs::for('dashboard.account.edit', function (BreadcrumbTrail $trail, $user) {
    $trail->parent('dashboard');
    $trail->push(trans('admin::app.account.edit.title'), route('admin.user.account.edit', $user->id));
});
