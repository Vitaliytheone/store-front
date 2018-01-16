<?php

return [
    '/checkout' => 'site/checkout',
    '/' => 'site/index',
    '/index' => 'site/index',
    '/cart' => 'cart/index',
    '/order/<id:\d+>' => 'cart/order',
    '/cart/remove/<id:[\w\d-]+>' => 'cart/remove',

    '/admin/logout' => 'admin/account/logout',
];