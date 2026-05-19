<?php

return [
    /**
     * Dashboard.
     */
    [
        'key'        => 'dashboard',
        'name'       => 'admin::app.layouts.dashboard',
        'route'      => 'admin.dashboard.index',
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
        'sort'       => 5,
        'icon-class' => 'icon-quote',
    ], [
        'key'        => 'sales.quotes',
        'name'       => 'admin::app.layouts.quotes',
        'route'      => 'admin.quotes.index',
        'sort'       => 1,
        'icon-class' => '',
    ], [
        'key'        => 'sales.proforma_invoices',
        'name'       => 'Proforma Invoices',
        'route'      => 'admin.proforma_invoices.index',
        'sort'       => 2,
        'icon-class' => '',
    ],

    [
        'key'        => 'erp_operations',
        'name'       => 'Purchasing',
        'route'      => 'admin.job_orders.index',
        'sort'       => 6,
        'icon-class' => 'icon-activity',
    ], [
        'key'        => 'erp_operations.job_orders',
        'name'       => 'Job Orders',
        'route'      => 'admin.job_orders.index',
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
        'name'       => 'Vendor Quotes / RFQs',
        'route'      => 'admin.vendor_quotes.index',
        'sort'       => 3,
        'icon-class' => '',
    ], [
        'key'        => 'erp_operations.purchase_orders',
        'name'       => 'Vendor POs',
        'route'      => 'admin.purchase_orders.index',
        'sort'       => 4,
        'icon-class' => '',
    ], [
        'key'        => 'erp_operations.goods_receipts',
        'name'       => 'Goods Receipts / Payables',
        'route'      => 'admin.goods_receipts.index',
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
        'params'     => ['route' => 'inbox'],
        'sort'       => 8,
        'icon-class' => 'icon-mail',
    ], [
        'key'        => 'mail.inbox',
        'name'       => 'admin::app.layouts.mail.inbox',
        'route'      => 'admin.mail.index',
        'params'     => ['route' => 'inbox'],
        'sort'       => 2,
        'icon-class' => '',
    ], [
        'key'        => 'mail.draft',
        'name'       => 'admin::app.layouts.mail.draft',
        'route'      => 'admin.mail.index',
        'params'     => ['route' => 'draft'],
        'sort'       => 3,
        'icon-class' => '',
    ], [
        'key'        => 'mail.outbox',
        'name'       => 'admin::app.layouts.mail.outbox',
        'route'      => 'admin.mail.index',
        'params'     => ['route' => 'outbox'],
        'sort'       => 4,
        'icon-class' => '',
    ], [
        'key'        => 'mail.sent',
        'name'       => 'admin::app.layouts.mail.sent',
        'route'      => 'admin.mail.index',
        'params'     => ['route' => 'sent'],
        'sort'       => 4,
        'icon-class' => '',
    ], [
        'key'        => 'mail.trash',
        'name'       => 'admin::app.layouts.mail.trash',
        'route'      => 'admin.mail.index',
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
        'sort'       => 7,
        'icon-class' => 'icon-activity',
    ], [
        'key'        => 'activities.list',
        'name'       => 'Tasks',
        'route'      => 'admin.activities.my_tasks',
        'sort'       => 1,
        'icon-class' => '',
    ], [
        'key'        => 'activities.calendar',
        'name'       => 'Events',
        'route'      => 'admin.activities.calendar',
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
        'sort'       => 3,
        'icon-class' => 'icon-contact',
    ], [
        'key'        => 'customers.contacts',
        'name'       => 'Contact',
        'route'      => 'admin.customers.persons.index',
        'sort'       => 1,
        'icon-class' => '',
    ], [
        'key'        => 'customers.companies',
        'name'       => 'Companies',
        'route'      => 'admin.customers.organizations.index',
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
        'sort'       => 3.5,
        'icon-class' => 'icon-contact',
    ], [
        'key'        => 'vendors.contacts',
        'name'       => 'Contact',
        'route'      => 'admin.vendors.persons.index',
        'sort'       => 1,
        'icon-class' => '',
    ], [
        'key'        => 'vendors.companies',
        'name'       => 'Companies',
        'route'      => 'admin.vendors.organizations.index',
        'sort'       => 2,
        'icon-class' => '',
    ],

    /**
     * Employees.
     */
    [
        'key'        => 'employees',
        'name'       => 'Employees',
        'route'      => 'admin.employees.persons.index',
        'sort'       => 3.75,
        'icon-class' => 'icon-user',
    ],

    /**
     * Products.
     */
    [
        'key'        => 'products',
        'name'       => 'admin::app.layouts.products',
        'route'      => 'admin.products.index',
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
        'sort'       => 9,
        'icon-class' => 'icon-role',
    ],
     [
        'key'        => 'settings.user',
        'name'       => 'admin::app.layouts.user',
        'route'      => 'admin.settings.groups.index',
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
        'sort'       => 2,
        'icon-class' => 'icon-role',
    ], [
        'key'        => 'settings.user.users',
        'name'       => 'admin::app.layouts.users',
        'info'       => 'admin::app.layouts.users-info',
        'route'      => 'admin.settings.users.index',
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
        'params'     => ['general', 'general'],
        'sort'       => 10,
        'icon-class' => 'icon-setting',
    ],
    [
        'key'        => 'configuration',
        'name'       => 'admin::app.layouts.configuration',
        'route'      => 'admin.configuration.index',
        'sort'       => 11,
        'icon-class' => 'icon-configuration',
    ],

    /**
     * Website Submissions.
     */
    [
        'key'        => 'website-submissions',
        'name'       => 'admin::app.layouts.website',
        'route'      => 'admin.website_submissions.index',
        'sort'       => 12,
        'icon-class' => 'icon-settings-webhooks',
    ],

];
