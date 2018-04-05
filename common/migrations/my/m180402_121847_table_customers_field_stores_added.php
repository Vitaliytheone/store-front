<?php

use yii\db\Migration;

/**
 * Class m180402_121847_table_customers_field_stores_added
 */
class m180402_121847_table_customers_field_stores_added extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180402_121847_table_customers_field_stores_added cannot be reverted.\n";

        return false;
    }

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->execute('
            ALTER TABLE `customers` ADD `stores` TINYINT(1)  NOT NULL  DEFAULT \'0\'  AFTER `child_panels`;
        ');
    }

    public function down()
    {
        $this->execute('
            ALTER TABLE `customers` DROP `stores`;
        ');
        return false;
    }
}
