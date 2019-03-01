<?php

use yii\db\Migration;

/**
 * Class m190116_061645_20190116_db_store_drop_tables
 */
class m190116_061645_20190116_db_store_drop_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            USE " . Yii::$app->params['storeDefaultDatabase'] . ";" . "
            SET FOREIGN_KEY_CHECKS = 0;
            DROP TABLE `blocks`;
            DROP TABLE `custom_themes`;
            DROP TABLE `navigation`;
            DROP TABLE `pages`;
            SET FOREIGN_KEY_CHECKS = 1;    
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190116_061645_20190116_db_store_drop_tables cannot be reverted.\n";

        return false;
    }
}
