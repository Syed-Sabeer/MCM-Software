<?php

return [
    'name'                      => env('CUSTOMER_PORTAL_NAME', 'MCM Customer Portal'),
    'invitation_expiry_hours'   => (int) env('CUSTOMER_PORTAL_INVITATION_EXPIRY', 72),
    'support_email'             => env('CUSTOMER_PORTAL_SUPPORT_EMAIL', env('MAIL_FROM_ADDRESS')),
    'legacy_permission'         => 'customer_portal.access',
];
