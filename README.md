Крон задачи
    
    `0 * * * * /usr/bin/php /path/yii cron/clear-cart-items`

Работа с очередями
php yii worker/start - старт воркера
php yii worker/stop - стоп воркера
php yii worker/restart - перезапуск ворвкера
php yii worker/start > /dev/null 2>&1 & - запуск воркера в фоновом режиме