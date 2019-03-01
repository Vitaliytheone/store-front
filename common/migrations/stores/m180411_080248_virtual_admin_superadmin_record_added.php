<?php

use yii\db\Migration;

/**
 * Class m180411_080248_virtual_admin_superadmin_record_added
 */
class m180411_080248_virtual_admin_superadmin_record_added extends Migration
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
        echo "m180411_080248_virtual_admin_superadmin_record_added cannot be reverted.\n";

        return false;
    }

    public function up()
    {
        $this->execute('
            USE `stores`;
            
            SET FOREIGN_KEY_CHECKS = 0;
            INSERT INTO `store_admins` (`store_id`, `username`, `password`, `auth_key`, `first_name`, `last_name`, `status`, `ip`, `last_login`, `rules`, `created_at`, `updated_at`) VALUES
	        (-1, \'superadmin\', \'d99de990v9vte87v6v5d7ererfer8we9wecv8tre87ewesds77wefwf88vb99bne\', \'\', \'SUPERADMIN\', NULL, 1, NULL, NULL, \'{\"orders\":1,\"products\":1,\"payments\":1,\"settings\":1}\', 0, 0);
            SET FOREIGN_KEY_CHECKS = 1;
        ');
    }

    public function down()
    {
        echo "m180411_080248_virtual_admin_superadmin_record_added cannot be reverted.\n";

        return false;
    }
}
