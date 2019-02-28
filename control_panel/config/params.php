<?php

/**
 * @var $proxyParams - global params from common bootstrap.php
 */

$serverIp = "54.37.92.228";

return [
    'access_key' => 'z1k05^d=^8*7rwh5t1h9+(kf9+907rg=(h$mq50rf*z3(zb1^_',
    'auth_key' => '+^e91s&qm&9*hs9_z=1e8jq8rl@njmr#=ts16!f_23wo7-@s(r', // Must be a same as sommerce/config/params.php 'auth_key'!
    'admin_auth_key' => '+^e91s&qm&9*hs9_z=1e8jq8rl@njmr#=ts16!f_23wo7-@s(r',
    'captcha_public' => '6LfQQg4UAAAAAB62JyN6ItlfpxXpY7Az2s_4IxLR',
    'captcha_private' => '6LfQQg4UAAAAAApcLHRHzdh84liBAP4lBZzMgpyF',
    'sysmailSecret' => '461e058179caa43104004272284355d0ef6827553fc4fbb1948a697e757c12a0',

    'time' => '10800',

    'gypAuth' => '6LfQQg4UAAAAAApcLHRHzdh84liBAP4',
    'dnsLogin' => 'werewind',
    'dnsPasswd' => 'isoa41bh',
    'currencies' => require(__DIR__ . '/currency/currencies.php'),
    'legacy_currencies' => require(__DIR__ . '/currency/legacy_currencies.php'),
    'timezones' => require(__DIR__ . '/timezone/timezones.php'),
    'countries' => require(__DIR__ . '/countries/countries.php'),
    'languages' => require(__DIR__ . '/languages/languages.php'),
    'themes' => require(__DIR__ . '/themes/themes.php'),
    'activityTypes' => require(__DIR__ . '/activity/types.php'),
    'activityTypesByGroups' => require(__DIR__ . '/activity/types_by_groups.php'),
    'noreplyEmail' => 'noreply@getyourpanel.com', // Адрес почты для отправки писем (from)
    'supportEmail' => 'werewind@yandex.ru', // Адрес почты саппорта
    'sysmailSupportEmail' => 'werewind@yandex.ru', // Адрес почты саппорта для sysmail метода
    'provider_service_id_label_list' => require(__DIR__ . '/services/provider_service_id_label_list.php'),

    // Mailgun mailer
    'mailgun.key' => 'key-cf10921abd5862ddd4b4b55692031fad',
    'mailgun.domain' => 'perfectpanel.net',

    // Swift mailer
    'swift.host' => 'ssl://smtp.yandex.ru',
    'swift.username' => 'noreply@perfectpanel.com',
    'swift.password' => 'aD213kfio34',
    'swift.port' => '465',

    // Ddos сервис по защите
    'ddosGuardUrl' => 'https://control.ddosa.net/client/api/task?token=f8ea293ffcb13a263f26410032fa0db6',
    'ddosGuardOptions' => [
        'siteIP' => "5.45.78.24",
        'sitePort' => "80",
        'protectedIP' => $serverIp,
        'isSSL' => true,
    ],

    // SSL параметры
    //'goGetSSLUsername' => 'thirteen@13.uz', // dev account
    //'goGetSSLPassword' => '0kfWF58UlJC2t9u', // dev account
    'goGetSSLUsername' => 'werewind@yandex.ru', // prod account
    'goGetSSLPassword' => '6Oip41CgJGs8rD2', // prod account
    'testSSL' => false, // true - используем sandbox, false - рабочий аккаунт

    // DNS параметры
    // адрес можно указать наш домен для отладки который возвращает успешное добавление всегда
    'dnsService' => 'https://api.cloudns.net',
    'dnsId' => '1181',
    'dnsPassword' => '2njujbuhwrZSgW96JynTN7JASe6Q8X64',
    'dnsIp' => $serverIp,

    'panelDomain' => 'myperfectpanel.com',// Домен нашего сайта
    'my_domain' => 'myperfectpanel.com',// Домен панели

    //
    'testPayPal' => false, // true - используем sandbox, false - рабочий аккаунт

    'devEmail' => ['myerror@13.uz'], // Адреса почты на которые шлем ошибки
    'failsEmail' => ['myerror@13.uz'], // Адреса почты на которые шлем неудачные действия - пока не используется нигде

    // Ahnames auth data
    'ahnames.url' => 'https://api.ahnames.com',
    'ahnames.login' => '',
    'ahnames.password' => '',
    'ahnames.contact_id' => '',

    'gearmanIp' => '127.0.0.1',
    'gearmanPort' => 4730,
    'gearmanPrefix' => 'my_',

    'reCaptcha.siteKey' => '6LeQyxkUAAAAACZsRNUpGI-5rw7ifdLgP0mUyE4O',
    'reCaptcha.secret' => '6LeQyxkUAAAAAEe6JEo4yITBbTqtCqzr9pzK1EgA',

    'system.sslGuardUrl' => '',
    'system.sslGuardKey' => '',

    'manualProviderId' => 6,

    'referral_percent' => 20,
    'referral_expiry' => 10, // месяцев
    'referral_link_expiry' => 120, // дней
    
    'pending_orders' => 10, // Лимит создания доменов, панелей, ssl заказов
    'pending_tickets' => 5, // Лимит создания задач
    'whoisxml' => 1, // Проверяем или нет домен через whoisxml
    'system.sslScriptUrl' => '',
    'system.sslScriptKey' => '',
    'superadminUrl' => 'superadmin',
    'nginx.tasksFile' => '@runtime/nginx.txt',
    'debugIps' => ['*'],
    'debugEmail' => null,

    'levopanel_scanner' => [
        'apiKey' => 'b9f1d6f809b793321c700f45ca382f59ef83bf644c48118e6d3b9902ab0cb86f',
        'proxy' => [
            'ip' => PROXY_CONFIG['main']['ip'],
            'port' => PROXY_CONFIG['main']['port'],
            'type' => CURLPROXY_HTTP,
        ],
        'timeouts' => [
            'timeout' => 20,
            'connection_timeout' => 10,
        ],
    ],

    'payment_verification_time' => 1 * 24 * 60 * 60,
    'curl.timeout' => '20',
    'getstatus_info_url' => '',

    'paypal_fraud_settings' => [
        'accept_high' => 1,
        'accept_critical' => 1,
    ],

    'ssl_order_delay' => 2 * 60, // Задержка на выполнение заказа продления GoGet SSL -> Letsencrypt SSL
    'free_ssl.create' => true,  // Создавать заказ/выполнять заказ на бесплатный сертификат или нет
    'free_ssl.prolong' => true, // Создавать заказ/выполнять заказ на продление бесплатного сертификата или нет

    'cdn' => [
        'uploadcare' => [
            'active' => true,
            'class_name' => 'Uploadcare',
            'public_key' => '2b57ca4e85ca588704a4',
            'secret_key' => '15b9d150192983929861',
        ],
    ],

    'uploadFileLimit' => 3,
    'uploadFileSize' => 5242880, // max file size in byte
];