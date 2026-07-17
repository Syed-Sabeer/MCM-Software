<?php

return [
    /**
     * Dashboard.
     */
    [
        'key'        => 'dashboard',
        'name'       => 'admin::app.layouts.dashboard',
        'route'      => 'admin.dashboard.index',
        'permission' => 'dashboard',
        'sort'       => 1,
        'icon-class' => 'icon-dashboard',
    ],

    /**
     * Leads.
     */
    [
        'key'        => 'leads',
        'name'       => 'Cases',
        'route'      => 'admin.leads.index',
        'permission' => 'leads',
        'sort'       => 4,
        'icon-class' => 'icon-leads',
    ],

    /**
     * Sales.
     */
    [
        'key'        => 'sales',
        'name'       => 'Sales',
        'route'      => 'admin.quotes.index',
        'permission' => ['quotes', 'proforma_invoices', 'invoices'],
        'sort'       => 5,
        'icon-class' => 'icon-quote',
    ], [
        'key'        => 'sales.quotes',
        'name'       => 'admin::app.layouts.quotes',
        'route'      => 'admin.quotes.index',
        'permission' => 'quotes',
        'sort'       => 1,
        'icon-class' => '',
    ], [
        'key'        => 'sales.proforma_invoices',
        'name'       => 'Proforma Invoices',
        'route'      => 'admin.proforma_invoices.index',
        'permission' => 'proforma_invoices',
        'sort'       => 2,
        'icon-class' => '',
    ], [
        'key'        => 'sales.invoices',
        'name'       => 'Invoices',
        'route'      => 'admin.invoices.index',
        'permission' => 'invoices',
        'sort'       => 3,
        'icon-class' => '',
    ],

    [
        'key'        => 'erp_operations',
        'name'       => 'Purchasing',
        'route'      => 'admin.job_orders.index',
        'permission' => ['job_orders', 'vendor_quotes', 'purchase_orders', 'goods_receipts'],
        'sort'       => 6,
        'icon-class' => 'icon-activity',
    ], [
        'key'        => 'erp_operations.job_orders',
        'name'       => 'Job Orders',
        'route'      => 'admin.job_orders.index',
        'permission' => 'job_orders',
        'sort'       => 1,
        'icon-class' => '',
    ],
    // [
    //     'key'        => 'erp_operations.requirement_sheets',
    //     'name'       => 'Requirement Sheets',
    //     'route'      => 'admin.requirements.index',
    //     'sort'       => 2,
    //     'icon-class' => '',
    // ],
      [
        'key'        => 'erp_operations.vendor_quotes',
          'name'       => 'Vendor Quotes',
        'route'      => 'admin.vendor_quotes.index',
        'permission' => 'vendor_quotes',
        'sort'       => 3,
        'icon-class' => '',
    ], [
        'key'        => 'erp_operations.purchase_orders',
        'name'       => 'Vendor POs',
        'route'      => 'admin.purchase_orders.index',
        'permission' => 'purchase_orders',
        'sort'       => 4,
        'icon-class' => '',
    ], [
        'key'        => 'erp_operations.goods_receipts',
        'name'       => 'Goods Receipts',
        'route'      => 'admin.goods_receipts.index',
        'permission' => 'goods_receipts',
        'sort'       => 5,
        'icon-class' => '',
    ],

    /**
     * Emails.
     */
    [
        'key'        => 'mail',
        'name'       => 'admin::app.layouts.mail.title',
        'route'      => 'admin.mail.index',
        'permission' => 'mail',
        'params'     => ['route' => 'inbox'],
        'sort'       => 8,
        'icon-class' => 'icon-mail',
    ], [
        'key'        => 'mail.inbox',
        'name'       => 'admin::app.layouts.mail.inbox',
        'route'      => 'admin.mail.index',
        'permission' => 'mail.inbox',
        'params'     => ['route' => 'inbox'],
        'sort'       => 2,
        'icon-class' => '',
    ], [
        'key'        => 'mail.draft',
        'name'       => 'admin::app.layouts.mail.draft',
        'route'      => 'admin.mail.index',
        'permission' => 'mail.draft',
        'params'     => ['route' => 'draft'],
        'sort'       => 3,
        'icon-class' => '',
    ], [
        'key'        => 'mail.outbox',
        'name'       => 'admin::app.layouts.mail.outbox',
        'route'      => 'admin.mail.index',
        'permission' => 'mail.outbox',
        'params'     => ['route' => 'outbox'],
        'sort'       => 4,
        'icon-class' => '',
    ], [
        'key'        => 'mail.sent',
        'name'       => 'admin::app.layouts.mail.sent',
        'route'      => 'admin.mail.index',
        'permission' => 'mail.sent',
        'params'     => ['route' => 'sent'],
        'sort'       => 4,
        'icon-class' => '',
    ], [
        'key'        => 'mail.trash',
        'name'       => 'admin::app.layouts.mail.trash',
        'route'      => 'admin.mail.index',
        'permission' => 'mail.trash',
        'params'     => ['route' => 'trash'],
        'sort'       => 5,
        'icon-class' => '',
    ],
    // , [
    //     'key'        => 'mail.setting',
    //     'name'       => 'admin::app.layouts.mail.setting',
    //     'route'      => 'admin.mail.index',
    //     'params'     => ['route' => 'setting'],
    //     'sort'       => 5,
    // ]

    /**
     * Activities.
     */
    [
        'key'        => 'activities',
        'name'       => 'admin::app.layouts.activities',
        'route'      => 'admin.activities.my_tasks',
        'permission' => 'activities',
        'sort'       => 7,
        'icon-class' => 'icon-activity',
    ], [
        'key'        => 'activities.list',
        'name'       => 'Tasks',
        'route'      => 'admin.activities.my_tasks',
        'permission' => 'activities',
        'sort'       => 1,
        'icon-class' => '',
    ], [
        'key'        => 'activities.calendar',
        'name'       => 'Events',
        'route'      => 'admin.activities.calendar',
        'permission' => 'activities',
        'sort'       => 2,
        'icon-class' => '',
    ],

    /**
     * Customers.
     */
    [
        'key'        => 'customers',
        'name'       => 'Customer',
        'route'      => 'admin.customers.organizations.index',
        'permission' => ['customers.persons', 'customers.organizations'],
        'sort'       => 3,
        'icon-class' => 'icon-contact',
    ], [
        'key'        => 'customers.contacts',
        'name'       => 'Contact',
        'route'      => 'admin.customers.persons.index',
        'permission' => 'customers.persons',
        'sort'       => 1,
        'icon-class' => '',
    ], [
        'key'        => 'customers.companies',
        'name'       => 'Companies',
        'route'      => 'admin.customers.organizations.index',
        'permission' => 'customers.organizations',
        'sort'       => 2,
        'icon-class' => '',
    ],

    /**
     * Vendors.
     */
    [
        'key'        => 'vendors',
        'name'       => 'Vendor',
        'route'      => 'admin.vendors.organizations.index',
        'permission' => ['vendors.persons', 'vendors.organizations'],
        'sort'       => 3.5,
        'icon-class' => 'icon-contact',
    ], [
        'key'        => 'vendors.contacts',
        'name'       => 'Contact',
        'route'      => 'admin.vendors.persons.index',
        'permission' => 'vendors.persons',
        'sort'       => 1,
        'icon-class' => '',
    ], [
        'key'        => 'vendors.companies',
        'name'       => 'Companies',
        'route'      => 'admin.vendors.organizations.index',
        'permission' => 'vendors.organizations',
        'sort'       => 2,
        'icon-class' => '',
    ],

    /**
     * Products.
     */
    [
        'key'        => 'products',
        'name'       => 'admin::app.layouts.products',
        'route'      => 'admin.products.index',
        'permission' => 'products',
        'sort'       => 2,
        'icon-class' => 'icon-product',
    ],

    /**
     * Settings.
     */
    [
        'key'        => 'settings',
        'name'       => 'admin::app.layouts.settings',
        'route'      => 'admin.settings.index',
        'permission' => 'settings',
        'sort'       => 9,
        'icon-class' => 'icon-role',
    ],
     [
        'key'        => 'settings.user',
        'name'       => 'admin::app.layouts.user',
        'route'      => 'admin.settings.groups.index',
        'permission' => 'settings.user',
        'info'       => 'admin::app.layouts.user-info',
        'sort'       => 1,
        'icon-class' => 'icon-settings-group',
    ],
     // [
     //    'key'        => 'settings.user.groups',
     //    'name'       => 'admin::app.layouts.groups',
     //    'info'       => 'admin::app.layouts.groups-info',
     //    'route'      => 'admin.settings.groups.index',
     //    'sort'       => 1,
     //    'icon-class' => 'icon-settings-group',
     // ],
     [
        'key'        => 'settings.user.roles',
        'name'       => 'admin::app.layouts.roles',
        'info'       => 'admin::app.layouts.roles-info',
        'route'      => 'admin.settings.roles.index',
        'permission' => 'settings.user.roles',
        'sort'       => 2,
        'icon-class' => 'icon-role',
    ], [
        'key'        => 'settings.user.users',
        'name'       => 'Employees',
        'info'       => 'Create employee logins and assign access roles.',
        'route'      => 'admin.settings.users.index',
        'permission' => 'settings.user.users',
        'sort'       => 3,
        'icon-class' => 'icon-user',
    ],
    // [
    //     'key'        => 'settings.warehouse',
    //     'name'       => 'admin::app.layouts.warehouse',
    //     'info'       => 'admin::app.layouts.warehouses-info',
    //     'route'      => 'admin.settings.pipelines.index',
    //     'icon-class' => '',
    //     'sort'       => 2,
    // ], [
    //     'key'        => 'settings.warehouse.warehouses',
    //     'name'       => 'admin::app.layouts.warehouses',
    //     'info'       => 'admin::app.layouts.warehouses-info',
    //     'route'      => 'admin.settings.warehouses.index',
    //     'sort'       => 1,
    //     'icon-class' => 'icon-settings-warehouse',
    // ],
    // [
    //     'key'        => 'settings.automation.events',
    //     'name'       => 'admin::app.layouts.events',
    //     'info'       => 'admin::app.layouts.events-info',
    //     'route'      => 'admin.settings.marketing.events.index',
    //     'sort'       => 2,
    //     'icon-class' => 'icon-calendar',
    // ], [
    //     'key'        => 'settings.automation.campaigns',
    //     'name'       => 'admin::app.layouts.campaigns',
    //     'info'       => 'admin::app.layouts.campaigns-info',
    //     'route'      => 'admin.settings.marketing.campaigns.index',
    //     'sort'       => 2,
    //     'icon-class' => 'icon-note',
    // ], [
    //     'key'        => 'settings.automation.webhooks',
    //     'name'       => 'admin::app.layouts.webhooks',
    //     'info'       => 'admin::app.layouts.webhooks-info',
    //     'route'      => 'admin.settings.webhooks.index',
    //     'sort'       => 2,
    //     'icon-class' => 'icon-settings-webhooks',
    // ],
    // [
    //     'key'        => 'settings.automation.data_transfer',
    //     'name'       => 'admin::app.layouts.data_transfer',
    //     'info'       => 'admin::app.layouts.data_transfer_info',
    //     'route'      => 'admin.settings.data_transfer.imports.index',
    //     'sort'       => 4,
    //     'icon-class' => 'icon-download',
    // ], [
    //     'key'        => 'settings.other_settings',
    //     'name'       => 'admin::app.layouts.other-settings',
    //     'info'       => 'admin::app.layouts.other-settings-info',
    //     'route'      => 'admin.settings.tags.index',
    //     'sort'       => 4,
    //     'icon-class' => 'icon-settings',
    // ], [
    //     'key'        => 'settings.other_settings.tags',
    //     'name'       => 'admin::app.layouts.tags',
    //     'info'       => 'admin::app.layouts.tags-info',
    //     'route'      => 'admin.settings.tags.index',
    //     'sort'       => 1,
    //     'icon-class' => 'icon-settings-tag',
    // ],

    /**
     * Configuration.
     */
    [
        'key'        => 'general-settings',
        'name'       => 'admin::app.layouts.general-settings',
        'route'      => 'admin.configuration.index',
        'permission' => 'configuration',
        'params'     => ['general', 'general'],
        'sort'       => 10,
        'icon-class' => 'icon-setting',
    ],
    /**
     * Website Submissions.
     */
    [
        'key'        => 'website-submissions',
        'name'       => 'admin::app.layouts.website',
        'route'      => 'admin.website_submissions.index',
        'permission' => 'website_submissions',
        'sort'       => 12,
        'icon-class' => 'icon-settings-webhooks',
    ],

];
