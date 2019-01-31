<?php

return [

    // ОБЩИЕ ПАРАМЕТРЫ
    'auth_key' => '+^e91s&qm&9*hs9_z=1e8jq8rl@njmr#=ts16!f_23wo7-@s(r', // ключ авторизации используемый как соль, должен свопадать с таким-же тут sommerce/config/params-local.php 'auth_key'!
    'admin_auth_key' => '+^e91s&qm&9*hs9_z=1e8jq8rl@njmr#=ts16!f_23wo7-@s(r',

    'reCaptcha.siteKey' => '6LeAmT4UAAAAAKz1c-wjHdI2XDp_PglfA1rl8RbG',
    'reCaptcha.secret' => '6LeAmT4UAAAAAI88eDxVJkusrAurV9A8EqwphqVw',

    'time' => '10800', // в данной переменной мы храним разницу между временем на сервере и времени админки

    'noreplyEmail' => 'noreply@getyourpanel.local', // Адрес почты для отправки писем (from)
    'supportEmail' => 'werewind@yandex.local', // Адрес почты саппорта
    'sysmailSupportEmail' => 'werewind@yandex.local', // Адрес почты саппорта для sysmail метода

    'panelDomain' => 'mypanel.test', // Домен панели
    'my_domain' => 'mypanel.test', // Домен нашего сайта

    'devEmail' => [], // Адреса почты на которые шлем ошибки
    'failsEmail' => [], // Адреса почты на которые шлем неудачные действия

    'debugIps' => ['*'], // маска для ИП имеющих доступ к Yii2 Debug панели
    'debugEmail' => null, // отладочный эмейл на который будут отправляться все письма если он указан

    'manualProviderId' => 6, // ИД провайдера используемый про сохранении истекшей панели

    'referral_percent' => 20, // процент реферальных отчислений
    'referral_expiry' => 10, // сколько месяцев действует рефералка для пользователя
    'referral_link_expiry' => 120, // сколько дней действует реф ссылка (живут куки)

    'pending_orders' => 100, // Лимит создания доменов, панелей, ssl заказов
    'pending_tickets' => 50, // Лимит создания задач

    'system.sslScriptUrl' => 'http://mydev.perfectpanel.net/nginx_config.php', // ссылка на скрипт создания конфига для SSL
    'system.sslScriptKey' => '', // ключ для создания конфига SSL
    'superadminUrl' => 'superadmin', // ссылка на суперадминку

    'payment_verification_time' => 1 * 24 * 60 * 60, // таймаут проверки платежа
    'curl.timeout' => '20', // таймаут операции для курла
    'getstatus_info_url' => '', // если не пустой, то производится поиск статусов по этому пути

    'paypal_fraud_settings' => [
        'accept_high' => 1,
        'accept_critical' => 1,
    ], // настройки фрауд защиты от пейпала

    'mysqldump_exec_path' => '/Applications/MAMP/Library/bin/mysqldump', // для локальной работы указывам путь к бинарнику MySQL Dump
    'mysql_exec_path' => '/Applications/MAMP/Library/bin/mysql', // для локальной работы указывам путь к бинарнику MySQL

    'testTwoCheckout' => true, // Включение тестовых платежей в Twocheckout -- true - используем sandbox, false - рабочий аккаунт
    'testPayPal' => true, // Включение тестовых платежей в Пейпал -- true - используем sandbox, false - рабочий аккаунт
    'testNamesilo' => 'dev', // dev - используем sandbox, prod - основной url

    'free_ssl.create' => false,  // Создавать заказ/выполнять заказ на бесплатный сертификат или нет
    'free_ssl.prolong' => false, // Создавать заказ/выполнять заказ на продление бесплатного сертификата или нет


    // Параметры по умолчанию при создании панели
    'projectDefaults' => [
        'theme_path' => 'default_light',
        'lang' => 'en',
        'plan' => 1,
        'comments' => 1,
        'mentions_wo_hashtag' => 1,
        'mentions' => 1,
        'mentions_custom' => 1,
        'mentions_hashtag' => 1,
        'mentions_follower' => 1,
        'mentions_likes' => 1,
        'start_count' => 1,
        'custom' => 1,
        'ticket_per_user' => 3,
    ],

    // созданиия стора
    'store.defaults' => [
        'timezone' => 0,
        'language' => 'en',
        'theme_name' => 'SMM24',
        'theme_folder' => 'smm24',
        'block_slider' => 1,
        'block_features' => 1,
        'block_reviews' => 1,
        'block_process' => 1,
    ],

    // ДАННЫЕ ИСПОЛЬЗУЕМЫЕ ДЛЯ ПОДКЛЮЧЕНИЯ ИЛИ АВТОРИЗАЦИИ
    'sysmailSecret' => '461e058179caa43104004272284355d0ef6827553fc4fbb1948a697e757c12a0', // используется при проверки для отправки почты

    // Mailgun mailer
    'mailgun.key' => 'key-cf10921abd5862ddd4b4b55692031fad',
    'mailgun.domain' => 'perfectpanel.local',

    // Данные для Swift mailer
    'swift.host' => 'ssl://smtp.yandex.local',
    'swift.username' => 'noreply@perfectpanel.com',
    'swift.password' => 'aD213kfio34',
    'swift.port' => '465',

    // Данные для сервиса по защите от DDOS
    'ddosGuardUrl' => 'http://mydev.perfectpanel.net/system/ddos-success',
    'ddosGuardOptions' => [
        'siteIP' => '5.45.78.24',
        'sitePort' => '80',
        'protectedIP' => '',
        'isSSL' => true,
    ],

    // SSL параметры для goGetSSL
    'goGetSSLUsername' => 'thirteen@13.uz', // dev account
    'goGetSSLPassword' => 'KhHUqC7If91zx62', // dev account или 0kfWF58UlJC2t9u
    'testSSL' => true, // true - используем sandbox, false - рабочий аккаунт

    // DNS параметры
    'dnsService' => 'http://mydev.perfectpanel.net', // можно указать наш домен для отладки который возвращает успешное добавление всегда
    'dnsId' => '',
    'dnsLogin' => '',
    'dnsPassword' => '',

    'whoisxml' => 0, // Не проверяем домен через whoisxml

    // конфиг для работы с сервисом ahnames
    'ahnames.url' => 'https://demo-api.ahnames.com',
    'ahnames.login' => 'demo',
    'ahnames.password' => 'demo',

];