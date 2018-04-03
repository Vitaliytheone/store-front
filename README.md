##Deploy 


1. Запустить команду для клонирования репозитория

    `git clone <source>`
    
2. Запустить команду для установки используемых компонентов php

    `composer install`
    
3. Запустить команду для установки используемых компонентов js

    `npm install`
    
4. Установить права на запись на необходимые для работы директории

    `chmod -R *** <pathProject>/runtime`   
    `chmod -R *** <pathProject>/web/assets`  
     
5. Настроить конфиги для подключени к базе данных и прокси серверам

   `config.json`     
    
6. Настроить конфиги для подключени к сервисам или другим настрокам в конфигам по следующей иерархии

   `common/config`  
   `<project>/config`
   
     
##Крон задачи
    
    `0 * * * * /usr/bin/php /path/yii cron/clear-cart-items`

Работа с очередями
php yii worker/start - старт воркера
php yii worker/stop - стоп воркера
php yii worker/restart - перезапуск ворвкера
php yii worker/start > /dev/null 2>&1 & - запуск воркера в фоновом режиме


##Текущий список консольных команд

###Sommerce

####Migrations 
	php yii migrate-sommerce/create {migration_name}
	php yii migrate-sommerce

####System 
	php yii system-sommerce/generate-assets
	php yii system-sommerce/add-admin


####Cron
	php yii cron-sommerce/clear-cart-items
	php yii cron-sommerce/sender
	php yii cron-sommerce/getstatus

###My

####Migrations 
	php yii migrate-my/create {migration_name}
	php yii migrate-my

####System
	php yii system-my/intersect-project-dns
	php yii system-my/recount-tickets
	php yii system-my/recount-orders
	php yii system-my/recount-invoices
	php yii system-my/intersect-terminated-domains
	php yii system-my/migrate-ticket-messages
	php yii system-my/add-item-id-to-orders
	php yii system-my/generate-panels-nginx-configs
	php yii system-my/generate-admin-log
	php yii system-my/generate-referrals
	php yii system-my/activate-child-panels
	php yii system-my/migrate-staff-rules
	php yii system-my/migrate-staff-rules

####Cron
	php yii cron-my/execute-order
	php yii cron-my/ssl-status
	php yii cron-my/create-invoice
	php yii cron-my/terminate-panel
	php yii cron-my/freeze-panel

####Panel-Scanners
	php yii	panel-scanner-my/scan-new {levopanel|smmfire}
	php yii	panel-scanner-my/check-all {levopanel|smmfire}
	

## Консольный сборщик JS, CSS...

В качестве менеджера задач применен [Gupl](https://gulpjs.com/).
Главный файл конфигурации заданий находится в корне проекта `./gulpfile.js`

Для сборки Sommerce выполнить в консоле из корня проекта:
    
    gulp js-so

Для сборки Sommerce выполнить в консоле из корня проекта:
        
    gulp js-my
    