<?php

return [
    '/checkout' => 'site/checkout',
    '/' => 'site/index',
    '/index' => 'site/index',
    '/cart' => 'cart/index',
    '/order/<id:\d+>' => 'cart/order',
    '/delete/<key:[\w\d-]+>' => 'cart/delete',
    '/frozen' => 'site/frozen',

    '/admin/logout' => 'admin/account/logout',
    '/admin/frozen' => 'admin/site/frozen',
    '/admin/super-login' => 'admin/site/super-login',
];