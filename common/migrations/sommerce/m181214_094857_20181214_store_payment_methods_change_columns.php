<?php

use yii\db\Migration;
use yii\db\Query;
use common\models\stores\PaymentMethods;
use common\models\stores\StorePaymentMethods;
use common\models\stores\PaymentMethodsCurrency;

/**
 * Class m181214_094857_20181214_store_payment_methods_change_columns
 */
class m181214_094857_20181214_store_payment_methods_change_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $methods = (new Query())
            ->select('id, method')
            ->from(DB_STORES . '.store_payment_methods')
            ->indexBy('id')
            ->all();

        $this->execute('
            ALTER TABLE store_payment_methods
              CHANGE `details` `options` text NULL;
            
            ALTER TABLE store_payment_methods
              CHANGE `active` `visibility` smallint(1) NOT NULL DEFAULT 1;
            
            ALTER TABLE store_payment_methods
              CHANGE `id` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT;
            
            ALTER TABLE store_payment_methods
              CHANGE `store_id` `store_id` INT(11) NOT NULL;
            
            ALTER TABLE store_payment_methods
              ADD `method_id` int(11) unsigned;
            
            ALTER TABLE store_payment_methods
              ADD `currency_id` int(11) unsigned NULL;
            
            ALTER TABLE store_payment_methods
              ADD `name` varchar(255);
            
            ALTER TABLE store_payment_methods
              ADD `position` int(11);
            
            ALTER TABLE store_payment_methods
              ADD `created_at` int(11) NULL;
            
            ALTER TABLE store_payment_methods
              ADD `updated_at` int(11) NULL;
            
            ALTER TABLE `store_payment_methods`
              DROP INDEX `fk_store_id_method_idx`;
            
            CREATE INDEX idx_store_id
              ON `store_payment_methods` (store_id);
            
            CREATE INDEX idx_method_id
              ON `store_payment_methods` (method_id);
            
            CREATE INDEX idx_currency_id
              ON `store_payment_methods` (currency_id);
            
            ALTER TABLE `store_payment_methods`
              ADD CONSTRAINT `fk_store_payment_methods_payment_methods` FOREIGN KEY (`method_id`) REFERENCES `payment_methods`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
            
            ALTER TABLE `store_payment_methods`
              ADD CONSTRAINT `fk_store_payment_methods_payment_methods_currency` FOREIGN KEY (`currency_id`) REFERENCES `payment_methods_currency`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
            
            ALTER TABLE `store_payment_methods`
              ADD CONSTRAINT `fk_store_payment_methods_stores` FOREIGN KEY (`store_id`) REFERENCES `stores`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
        ');

        $this->execute('ALTER TABLE `store_payment_methods` DROP COLUMN `method`;');

        foreach ($methods as $key => $methodName) {
            $method = PaymentMethods::findOne(['method_name' => $methodName['method']]);
            $storeMethod = StorePaymentMethods::findOne($key);
            if (!$method || !$storeMethod) {
                continue;
            }
            $storeCurrency = PaymentMethodsCurrency::findOne(['method_id' => $method->id]);

            $lastPositions = StorePaymentMethods::find()
                ->where(['store_id' => $storeMethod->store_id])
                ->max('position');

            $storeMethod->method_id = $method->id;
            $storeMethod->currency_id = $storeCurrency->id;
            $storeMethod->name = $method->name;
            $storeMethod->position = isset($lastPositions) ? $lastPositions + 1 : 1;
            if (empty($storeMethod->options) || $storeMethod->options === '[]') {
                $storeMethod->options = $storeMethod->setClearOptions($method->id);
            }
            $storeMethod->created_at = time();
            $storeMethod->updated_at = time();
            $storeMethod->save(false);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->execute('
            ALTER TABLE `store_payment_methods`
              DROP FOREIGN KEY `fk_method_id_to_payment_methods`;

            ALTER TABLE `store_payment_methods`
              DROP FOREIGN KEY `fk_currency_id_to_methods_currency`;

            ALTER TABLE `store_payment_methods`
              DROP FOREIGN KEY `fk_store_id_to_stores`;

            ALTER TABLE `store_payment_methods`
              DROP INDEX `idx_store_id`;

            ALTER TABLE `store_payment_methods`
              DROP INDEX `idx_method_id`;

            ALTER TABLE `store_payment_methods`
              DROP INDEX `idx_currency_id`;

            ALTER TABLE store_payment_methods
              CHANGE `options` `details` text NULL;

            ALTER TABLE store_payment_methods
              CHANGE `visibility` `active` tinyint(1) NULL;

            ALTER TABLE store_payment_methods
              CHANGE `id` `id` int(11) NOT NULL;

            ALTER TABLE store_payment_methods
              CHANGE `store_id` `store_id` int(11) NULL;

            ALTER TABLE store_payment_methods
              ADD `method` varchar(255) NULL;

            ALTER TABLE payment_methods
              DROP COLUMN `method_id`;

            ALTER TABLE payment_methods
              DROP COLUMN `currency_id`;

            ALTER TABLE payment_methods
              DROP COLUMN `name`;

            ALTER TABLE payment_methods
              DROP COLUMN `position`;

            ALTER TABLE payment_methods
              DROP COLUMN `created_at`;

            ALTER TABLE payment_methods
              DROP COLUMN `updated_at`;

            CREATE INDEX fk_store_id_method_idx
              ON `store_payment_methods` (store_id);
        ');
    }
}
