<?php

use yii\db\Migration;

/**
 * Class m180726_123900_db_stores_updated_stores_added_no_referral_column
 */
class m180726_123900_db_stores_updated_stores_added_no_referral_column extends Migration
{
    public function up()
    {
        $this->execute("
            USE `" . DB_STORES . "`;

            ALTER TABLE `stores`
            ADD `no_referral` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - disabled, 1 - enabled';
        ");
    }

    public function down()
    {
        $this->execute('
            ALTER TABLE `' . DB_STORES . '`.`stores`
            DROP `no_referral`;
        ');
    }
}
