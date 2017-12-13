<?php

return [
    '/checkout' => 'site/checkout',
    '/cart' => 'cart/index',
    '/add-to-cart/<id:\d+>' => 'cart/add-to-cart',
    '/cart/remove/<id:[\w\d-]+>' => 'cart/remove',
];