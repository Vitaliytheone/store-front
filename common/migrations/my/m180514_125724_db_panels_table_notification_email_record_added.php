<?php

use yii\db\Migration;

/**
 * Class m180514_125724_db_panels_table_notification_email_record_added
 */
class m180514_125724_db_panels_table_notification_email_record_added extends Migration
{
    public function up()
    {
        $this->execute('
            USE `' . DB_PANELS . '`;
            INSERT INTO `notification_email` (`subject`, `message`, `code`, `enabled`)
            VALUES
              (\'Domain renewed\', \'Domain renewed\', \'domain_renewed\', 0);        
        ');
    }

    public function down()
    {
        $this->execute('
            USE `' . DB_PANELS . '`;
            DELETE FROM `notification_email` WHERE `code` = \'domain_renewed\';
        ');
    }
}
