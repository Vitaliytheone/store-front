<?php

return [
    '/checkout' => 'site/checkout',
    '/' => 'site/index',
    '/index' => 'site/index',
    '/cart' => 'cart/index',
    '/add-to-cart/<id:\d+>' => 'cart/add-to-cart',
    '/cart/remove/<id:[\w\d-]+>' => 'cart/remove',
];