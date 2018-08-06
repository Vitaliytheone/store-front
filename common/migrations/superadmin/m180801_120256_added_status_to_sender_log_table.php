<?php

use yii\db\Migration;

/**
 * Class m180801_120256_added_status_to_sender_log_table
 */
class m180801_120256_added_status_to_sender_log_table extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `sender_log`
            ADD `status` tinyint(1) NULL COMMENT '1 - Success; 2 - Error; 3 - Curl error' AFTER `send_method`;
        ");
    }

    public function down()
    {
        $this->execute("
            ALTER TABLE `sender_log` DROP `status`;
        ");
    }
}
