<?php

return [
    '/' => 'site/index',
    '/product' => 'site/product',
    '/product/<id:\d+>' => 'site/product',
    '/cart' => 'cart/index',
    '/add-to-cart/<id:\d+>' => 'cart/add-to-cart',
    '/cart/remove/<id:[\w\d-]+>' => 'cart/remove',
    '/checkout' => 'site/checkout',
    '/contact' => 'site/contact',
    '/content' => 'site/content',
];