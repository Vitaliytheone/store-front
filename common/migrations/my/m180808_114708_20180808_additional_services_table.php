<?php

use yii\db\Migration;

/**
 * Class m180808_114708_20180808_additional_services_table
 */
class m180808_114708_20180808_additional_services_table extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `additional_services` ADD `service_view` TINYINT(1)  NOT NULL  COMMENT '0 - простой провайдер, 1 - PP, 2 - сложный провайдер'  AFTER `provider_rate`;
            ALTER TABLE `additional_services` ADD `service_options` TEXT  NULL  COMMENT '[], key — id услуги провайдера, value – параметры услуги (refill, cancel, shown_fields). Для service_view=[0,2]'  AFTER `service_view`;
            ALTER TABLE `additional_services` ADD `provider_service_id_label` SMALLINT(3)  NULL COMMENT 'индекс label для поля формы provider_service_id' AFTER `service_options`;
            ALTER TABLE `additional_services` ADD `provider_service_settings` TEXT  NULL COMMENT '[], настройка экшинов сервисов провайдера и параметры формы'  AFTER `provider_service_id_label`;
            ALTER TABLE `additional_services` ADD `provider_service_api_error` TEXT  NULL  COMMENT 'json массив признака ошибок в ответе от API провайдера'  AFTER `provider_service_settings`;
            ALTER TABLE `additional_services` ADD `store` TINYINT(1) NOT NULL DEFAULT '0' AFTER `type`;
        ");
    }

    public function down()
    {
        $this->execute("
            ALTER TABLE `additional_services` DROP `provider_service_settings`;
            ALTER TABLE `additional_services` DROP `provider_service_api_error`;
            ALTER TABLE `additional_services` DROP `service_view`;
            ALTER TABLE `additional_services` DROP `service_options`;
            ALTER TABLE `additional_services` DROP `provider_service_id_label`;
            ALTER TABLE `additional_services` DROP `store`;
        ");
    }
}
