<?php

return [

    // ОБЩИЕ ПАРАМЕТРЫ
    'time' => '10800', // в данной переменной мы храним разницу между временем на сервере и времени админки

    'panelDomain' => 'mypanel.test', // Домен панели
    'my_domain' => 'mypanel.test', // Домен нашего сайта

    'devEmail' => [], // Адреса почты на которые шлем ошибки
    'failsEmail' => [], // Адреса почты на которые шлем неудачные действия - пока не используется нигде

    'debugIps' => ['*'], // маска для ИП имеющих доступ к Yii2 Debug панели
    'debugEmail' => null, // отладочный эмейл на который будут отправляться все письма если он указан

    'pending_orders' => 100, // Лимит создания доменов, панелей, ssl заказов
    'pending_tickets' => 50, // Лимит создания задач

    'mysqldump_exec_path' => '/Applications/MAMP/Library/bin/mysqldump', // для локальной работы указывам путь к бинарнику MySQL Dump
    'mysql_exec_path' => '/Applications/MAMP/Library/bin/mysql', // для локальной работы указывам путь к бинарнику MySQL

    'testPayPal' => true, // Включение тестовых платежей в Пейпал -- true - используем sandbox, false - рабочий аккаунт

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

    // Данные для сервиса по защите от DDOS
    'ddosGuardUrl' => 'http://mydev.perfectpanel.net/system/ddos-success',

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