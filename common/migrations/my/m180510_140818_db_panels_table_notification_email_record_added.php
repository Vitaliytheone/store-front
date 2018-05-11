<?php

use yii\db\Migration;

/**
 * Class m180510_140818_db_panels_table_notification_email_record_added
 */
class m180510_140818_db_panels_table_notification_email_record_added extends Migration
{

    public function up()
    {
        $this->execute('
            USE `' . DB_PANELS . '`;
            INSERT INTO `notification_email` (`subject`, `message`, `code`, `enabled`)
            VALUES
              (\'SSL certificate renewed\', \'SSL certificate renewed\', \'ssl_renewed\', 0);        
        ');
    }

    public function down()
    {
        $this->execute('
            USE `' . DB_PANELS . '`;
            DELETE FROM `notification_email` WHERE `code` = \'ssl_renewed\';
        ');
    }
}
