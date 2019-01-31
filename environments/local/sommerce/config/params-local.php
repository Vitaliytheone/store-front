<?php

return [

    // ОБЩИЕ ПАРАМЕТРЫ
    'devEmail' => [], // Адреса почт на которые шлем ошибки
    'debugEmail' => null, // отладочный эмейл на который будут отправляться все письма если он указан

    'debugIps' => ['*'], // маска для ИП имеющих доступ к Yii2 Debug панели

    'senderOrdersLimit' => 100, // Задает лимит получаемых через консоль данных отправки заказов в очереди
    'getstatusOrdersLimit' => 100, // сколько статусов выполняемых заказов со всех магазинов можно получить через консоль за один раз


    // ДАННЫЕ ИСПОЛЬЗУЕМЫЕ ДЛЯ ПОДКЛЮЧЕНИЯ ИЛИ АВТОРИЗАЦИИ
    'auth_key' => '+^e91s&qm&9*hs9_z=1e8jq8rl@njmr#=ts16!f_23wo7-@s(r', // ключ авторизации используемый как соль, должен свопадать с таким-же тут my/config/params-local.php 'auth_key'!

    'reCaptcha.siteKey' => '6LeAmT4UAAAAAKz1c-wjHdI2XDp_PglfA1rl8RbG',
    'reCaptcha.secret' => '6LeAmT4UAAAAAI88eDxVJkusrAurV9A8EqwphqVw',

    'getyourpanelKey' => 'j84GG5H6CfkjeHZxWzdSGqFw8TpfP2Tb', // ключ используется для доступа к http://getyourpanel.com для проверки провайдера

    'localApiDomain' => 'http://localapi2/api/v2', // ссылка на АПИ для сендера

    'testPagseguro' => 'true', // включение тестового режима на Пагсегуро
];