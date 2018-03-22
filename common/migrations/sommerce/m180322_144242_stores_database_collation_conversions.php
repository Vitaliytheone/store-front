<?php

use yii\db\Migration;

/**
 * Class m180322_124242_stores_database_collation_conversions
 */
class m180322_144242_stores_database_collation_conversions extends Migration
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
        echo "m180322_124242_stores_database_collation_conversions cannot be reverted.\n";

        return false;
    }

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->execute('
            ALTER DATABASE `stores` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;       
        ');

        $this->execute('
            USE `stores`;
            ALTER TABLE customers CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
            ALTER TABLE default_themes CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
            ALTER TABLE payment_methods CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;  
            ALTER TABLE providers CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
            ALTER TABLE store_admins CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
            ALTER TABLE store_admins_hash CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;    
            ALTER TABLE store_domains CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
            ALTER TABLE store_providers CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
            ALTER TABLE stores CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
            ALTER TABLE stores_send_orders CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
            ALTER TABLE system_migrations CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
        ');
    }

    public function down()
    {
        echo "m180322_124242_stores_database_collation_conversions cannot be reverted.\n";

        return false;
    }
}
