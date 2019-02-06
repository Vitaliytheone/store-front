<?php

return [
    '/' => 'page/index',
    '/frozen' => 'site/frozen',
    '/index' => 'page/index',
    '/admin/logout' => 'admin/site/logout',
    '/admin/frozen' => 'admin/site/frozen',
    '/admin/settings' => 'admin/settings/payments',
    '/admin/super-login' => 'admin/site/super-login',
    'checkout' => 'payments/checkout',
    'processing' => 'payments/processing',

    [
        'pattern' => '/.well-known/acme-challenge/<filename:[-_a-zA-Z0-9]+>',
        'route' => 'site/ssl',
    ],
];