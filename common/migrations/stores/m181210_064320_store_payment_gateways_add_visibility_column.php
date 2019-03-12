<?php

use yii\db\Migration;

/**
 * Class m181210_064320_store_payment_gateways_add_visibility_column
 */
class m181210_064320_store_payment_gateways_add_visibility_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            USE `" . DB_STORES . "`;
            
            ALTER TABLE `payment_gateways`
            ADD `visibility` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '0 - hide, 1 - visible' AFTER `options`

        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('
            ALTER TABLE `' . DB_STORES . '`.`payment_gateways`
            DROP `visibility`;
        ');
    }
}
