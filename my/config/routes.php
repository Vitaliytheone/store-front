<?php

$superadmin = [];

foreach ([
     '' => '/site',
     '/dashboard' => '/dashboard',
     '/dashboard/index' => '/dashboard',
     '/account' => '/account',
     '/settings' => '/settings',
     '/settings/email' => '/settings/email',
     '/settings/edit-email' => '/settings/edit-email',
     '/settings/email-status' => '/settings/email-status',
     '/settings/staff' => '/settings/staff',
     '/settings/create-staff' => '/settings/create-staff',
     '/settings/edit-staff' => '/settings/edit-staff',
     '/settings/staff-password' => '/settings/staff-password',
     '/logout' => '/site/logout',
     '/settings/edit-payment' => '/settings/edit-payment',
     '/panels' => '/panels',
     '/panels/edit' => '/panels/edit',
     '/panels/generate-apikey' => '/panels/generate-apikey',
     '/orders' => '/orders',
     '/orders/details' => '/orders/details',
     '/orders/change-status' => '/orders/change-status',
     '/panels/change-status' => '/panels/change-status',
     '/panels/change-domain' => '/panels/change-domain',
     '/panels/edit-expiry' => '/panels/edit-expiry',
     '/panels/edit-providers' => '/panels/edit-providers',
     '/domains' => '/domains',
     '/domains/details' => '/domains/details',
     '/ssl' => '/ssl',
     '/ssl/details' => '/ssl/details',
     '/invoices' => '/invoices',
     '/customers' => '/customers',
     '/customers/change-status' => '/customers/change-status',
     '/customers/set-password' => '/customers/set-password',
     '/customers/edit' => '/customers/edit',
     '/customers/auth' => '/customers/auth',
     '/payments' => '/payments',
     '/tickets' => '/tickets',
     '/providers' => '/providers',
     '/reports' => '/reports',
     '/statuses' => '/statuses',
     '/statuses/getstatus' => '/statuses/getstatus',
     '/statuses/subscription' => '/statuses/subscription',
     '/statuses/sender' => '/statuses/sender',
     '/logs' => '/logs',
 ] as $key => $value) {
    $superadmin[$params['superadminUrl'] . $key] = "/" . $params['superadminUrl'] . $value;
    $superadmin[$params['superadminUrl'] . $key . '/'] = "/" . $params['superadminUrl'] . $value;
}

$routes = [
    '' => '/site/index',
    'index' => '/site/index',
    'activation/<token:>' => '/site/activation',
    'invoices/<id:>' => '/site/invoice',
    'paypalexpress/<id:>' => '/payments/paypalexpress',
    'webmoney' => '/payments/webmoney',
    'perfectmoney' => '/payments/perfectmoney',
    'bitcoin' => '/payments/bitcoin',
    '2checkout' => '/payments/2checkout',
    'coinpayments' => '/payments/coinpayments',
    'ticket/<id:>' => '/site/ticket',
    'message/<id:>' => '/site/message',
    'checkout/<id:>' => '/site/checkout',
    'reset/<token:>' => '/site/reset',
    'signin' => '/site/signin',
    'settings' => '/site/settings',
    'support' => '/site/support',
    'create-ticket' => '/site/create-ticket',
    'invoices' => '/site/invoices',
    'changeEmail' => '/site/changeemail',
    'changePassword' => '/site/changepassword',
    'tickets' => '/site/tickets',
    'signup' => '/site/signup',
    'logout' => '/site/logout',
    'forgot' => '/site/restore',
    'authSuperadmin/<key:>/<token:>' => '/system/superadminauth',
    'paypal-verify/<code:>' => '/site/paypal-verify',

    'ssl' => '/ssl/index',
    'ssl/order' => '/ssl/order',

    'order' => '/project/order',
    'panels' => '/project/panels',
    'staff/create/<id:>' => '/project/staffcreate',
    'staff/passwd/<id:>' => '/project/staffpasswd',
    'staff/edit/<id:>' => '/project/staffedit',
    'staff/<id:>' => '/project/staff',
    'search-domains' => '/project/search-domains',
    'domains' => '/domains/index',
    'domains/order' => '/domains/order',
    '/activitylog/<id:>' => '/activity/index',

    'paypal/ipn' => '/system/ppip',
    'paypal/ipn/' => '/system/ppip',
    'sysmail' => '/system/sysmail',
    'system/ddos-success/' => '/system/ddos-success/',
    'system/ddos-error/' => '/system/ddos-error/',
    '/dns/add-record.json' => '/system/dns/',
    '/dns/register.json' => '/system/dns/',
    '/dns/delete.json' => '/system/dns/',
    '/dns/delete-record.json' => '/system/dns/',
    '/dns/records.json' => '/system/dns-list/',
    '/dns/get-zone-info.json' => '/system/dns-list/',
    'panel-notify' => '/system/panel-notify',

    '/redirect' => '/site/redirect',

    '/referrals' => '/referrals/index',
    '/ref/<code:>' => '/referrals/ref',

    'childpanels' => '/child-project/panels',
    'childpanels/order' => '/child-project/order',
    'childpanels/order-domain' => '/child-project/order-domain',
    'childpanels/staff/create/<id:>' => '/child-project/staffcreate',
    'childpanels/staff/<id:>' => '/child-project/staff',
    'childpanels/staff/passwd/<id:>' => '/child-project/staff-passwd',
    'childpanels/staff/edit/<id:>' => '/child-project/staff-edit',

    'stores' => '/store/stores',
    'stores/order' => '/store/order',
    'stores/staff/<id:>' => '/store/staff',
    'stores/staff/create/<id:>' => '/store/staff-create',
    'stores/staff/edit/<id:>' => '/store/staff-edit',
    'stores/staff/password/<id:>' => '/store/staff-password',
    'webhook/<action:>' => '/webhook/index',
];

return array_merge($routes, $superadmin);
