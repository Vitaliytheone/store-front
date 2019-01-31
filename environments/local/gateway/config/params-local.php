<?php

return [

    // ОБЩИЕ ПАРАМЕТРЫ
    'devEmail' => [], // Адреса почт на которые шлем ошибки
    'debugEmail' => null, // отладочный эмейл на который будут отправляться все письма если он указан

    'debugIps' => ['*'], // маска для ИП имеющих доступ к Yii2 Debug панели

    'auth_key' => '+^e91s&qm&9*hs9_z=1e8jq8rl@njmr#=ts16!f_23wo7-@s(R', // ключ авторизации используемый как соль, должен свопадать с таким-же тут my/config/params-local.php 'auth_key'!

    'reCaptcha.siteKey' => '6LeAmT4UAAAAAKz1c-wjHdI2XDp_PglfA1rl8RbG',
    'reCaptcha.secret' => '6LeAmT4UAAAAAI88eDxVJkusrAurV9A8EqwphqVw',

    'getyourpanelKey' => 'j84GG5H6CfkjeHZxWzdSGqFw8TpfP2Tb', // ключ используется для доступа к http://getyourpanel.com для проверки провайдера

];