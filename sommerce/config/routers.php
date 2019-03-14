<?php

return [
    '/checkout' => 'payments/checkout',
    '/' => 'page/index',
    '/index' => 'page/index',
    '/order' => 'order/index',
    '/delete/<key:[\w\d-]+>' => 'cart/delete',
    '/frozen' => 'site/frozen',

    '/admin/logout' => 'admin/account/logout',
    '/admin/frozen' => 'admin/site/frozen',
    '/admin/super-login' => 'admin/site/super-login',

    '/admin/pages/edit-block/<code:[\w\d-]+>' => 'admin/pages/edit-block',
    '/admin/pages/edit-page/<id:\d++>' => 'admin/pages/edit-page',

    [
        'pattern' => '<filename:[\w\d]+>',
        'route' => 'site/ssl',
        'suffix' => '.txt',
    ],
    [
        'pattern' => '/.well-known/pki-validation/<filename:[\w\d]+>',
        'route' => 'site/ssl',
        'suffix' => '.txt',
    ],
    [
        'pattern' => '/.well-known/acme-challenge/<filename:[-_a-zA-Z0-9]+>',
        'route' => 'site/ssl',
    ],

    '/css/<name:[\w\d\-.]+.css>' => 'page/styles',
    '/js/<name:[\w\d\-.]+.js>' => 'page/scripts',

    'admin/pages/get-page' => 'admin/pages/get-page',
    'admin/pages/get-pages' => 'admin/pages/get-pages',
    'admin/pages/draft' => 'admin/pages/draft',
    'admin/pages/draft/<id:\d++>' => 'admin/pages/draft',
    'admin/pages/publish/<id:\d++>' => 'admin/pages/publish',
    'admin/pages/get-products' => 'admin/pages/get-products',
    'admin/pages/get-product' => 'admin/pages/get-product',
    'admin/pages/set-product/<id:\d++>' => 'admin/pages/set-product',
    'admin/pages/set-package/<id:\d++>' => 'admin/pages/set-package',
    'admin/pages/set-image' => 'admin/pages/set-image',
    'admin/pages/get-images' => 'admin/pages/get-images',
    'admin/pages/unset-image/<id:\d++>' => 'admin/pages/unset-image',
];