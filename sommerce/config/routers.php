<?php

return [
    '/checkout' => 'site/checkout',
    '/' => 'site/index',
    '/index' => 'site/index',
    '/cart' => 'cart/index',
    '/order/<id:\d+>' => 'cart/order',
    '/vieworder/<code:[\d\w]+>' => 'order/view',
    '/delete/<key:[\w\d-]+>' => 'cart/delete',
    '/frozen' => 'site/frozen',

    '/admin/logout' => 'admin/account/logout',
    '/admin/frozen' => 'admin/site/frozen',
    '/admin/super-login' => 'admin/site/super-login',

    '/admin/settings/edit-block/<code:[\w\d-]+>' => 'admin/settings/edit-block',
    '/admin/settings/edit-page/<id:\d++>' => 'admin/settings/edit-page',

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

    '/<url:[\w*-]+>/css/style.css' => 'page/styles',

    'admin/settings/pages/get-page' => 'admin/settings/get-page',
    'admin/settings/pages/get-pages' => 'admin/settings/get-pages',
    'admin/settings/pages/draft' => 'admin/settings/draft',
    'admin/settings/pages/draft/<id:\d++>' => 'admin/settings/draft',
    'admin/settings/pages/publish/<id:\d++>' => 'admin/settings/publish',
    'admin/settings/pages/get-products' => 'admin/settings/get-products',
    'admin/settings/pages/get-product' => 'admin/settings/get-product',
    'admin/settings/pages/set-product/<id:\d++>' => 'admin/settings/set-product',
    'admin/settings/pages/set-package/<id:\d++>' => 'admin/settings/set-package',
];