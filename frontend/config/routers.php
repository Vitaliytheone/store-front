<?php

return [
    '/checkout' => 'site/checkout',
    '/' => 'site/index',
    '/index' => 'site/index',
    '/cart' => 'cart/index',
    '/order/<id:\d+>' => 'cart/order',
    '/delete/<key:[\w\d-]+>' => 'cart/delete',

    '/admin/logout' => 'admin/account/logout',
];