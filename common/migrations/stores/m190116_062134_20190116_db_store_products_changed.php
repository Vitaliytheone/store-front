<?php

use yii\db\Migration;

/**
 * Class m190116_062134_20190116_db_store_products_changed
 */
class m190116_062134_20190116_db_store_products_changed extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            USE " . Yii::$app->params['storeDefaultDatabase'] . ";" . "
            ALTER TABLE `products` DROP `url`;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("
            USE " . Yii::$app->params['storeDefaultDatabase'] . ";" . "
            ALTER TABLE `products` ADD `url` VARCHAR(1000)  NULL  DEFAULT NULL  AFTER `position`;
        ");
    }
}
