<?php

use yii\db\Migration;

/**
 * Class m180328_093822_delete_sommerce_customers
 */
class m180328_093822_delete_sommerce_customers extends Migration
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
        echo "m180328_093822_delete_sommerce_customers cannot be reverted.\n";

        return false;
    }
    /** @inheritdoc */
    public function up()
    {
        $this->execute('
            USE `stores`;
            ALTER TABLE `stores` DROP FOREIGN KEY `fk_customer_id_stores`;
        ');

        $this->execute('
            USE `stores`;
            DROP TABLE IF EXISTS `customers`;
        ');
    }

    /** @inheritdoc */
    public function down()
    {
        echo "m180328_093822_delete_sommerce_customers cannot be reverted.\n";

        return false;
    }
}
