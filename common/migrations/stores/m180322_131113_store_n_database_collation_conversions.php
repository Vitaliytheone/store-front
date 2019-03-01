<?php

use yii\db\Migration;

/**
 * Class m180322_131113_store_n_database_collation_conversions
 */
class m180322_131113_store_n_database_collation_conversions extends Migration
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
        echo "m180322_131113_store_n_database_collation_conversions cannot be reverted.\n";

        return false;
    }

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $store_db = [
            'store',
            'store_test',
            'store_fastinstafollowers',
        ];

        foreach ($store_db as $db) {

            $isDbExist = Yii::$app->db
                ->createCommand("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$db'")
                ->queryScalar();

            if ($isDbExist) {

                // Convert each DB collation
                $this->execute("
                    ALTER DATABASE `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;                
                ");

                // Convert each table collation
                $this->execute("
                    USE `$db`;
                    ALTER TABLE `activity_log` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
                    ALTER TABLE `blocks` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
                    ALTER TABLE `carts` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
                    ALTER TABLE `checkouts` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
                    ALTER TABLE `custom_themes` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
                    ALTER TABLE `files` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
                    ALTER TABLE `navigation` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
                    ALTER TABLE `orders` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
                    ALTER TABLE `packages` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
                    ALTER TABLE `pages` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
                    ALTER TABLE `payments` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
                    ALTER TABLE `payments_log` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
                    ALTER TABLE `products` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
                    ALTER TABLE `suborders` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
                ");
            } else {
                echo PHP_EOL . 'Database ' . $db . 'does not exist. Skipped!';
            }
        }
    }

    public function down()
    {
        echo "m180322_131113_store_n_database_collation_conversions cannot be reverted.\n";

        return false;
    }
}
