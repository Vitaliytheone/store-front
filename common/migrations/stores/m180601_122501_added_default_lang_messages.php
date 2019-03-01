<?php

use yii\db\Migration;

/**
 * Class m180601_122501_added_default_lang_messages
 */
class m180601_122501_added_default_lang_messages extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->execute("
            USE `" . DB_STORES . "`;

            INSERT INTO `store_default_messages` (`lang_code`, `section`, `name`, `value`)
            VALUES ('en', 'orders', 'status_awaiting', 'Awaiting');
            
            INSERT INTO `store_default_messages` (`lang_code`, `section`, `name`, `value`)
            VALUES ('en', 'orders', 'status_pending', 'Pending');
            
            INSERT INTO `store_default_messages` (`lang_code`, `section`, `name`, `value`)
            VALUES ('en', 'orders', 'status_in_progress', 'In progress');
            
            INSERT INTO `store_default_messages` (`lang_code`, `section`, `name`, `value`)
            VALUES ('en', 'orders', 'status_completed', 'Completed');
            
            INSERT INTO `store_default_messages` (`lang_code`, `section`, `name`, `value`)
            VALUES ('en', 'orders', 'status_canceled', 'Canceled');
            
            INSERT INTO `store_default_messages` (`lang_code`, `section`, `name`, `value`)
            VALUES ('en', 'orders', 'status_failed', 'Failed');
            
            INSERT INTO `store_default_messages` (`lang_code`, `section`, `name`, `value`)
            VALUES ('en', 'orders', 'status_error', 'Error');
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->execute('
            USE `' . DB_STORES . '`;
            DELETE FROM `store_default_messages` WHERE ((`lang_code` = \'en\') AND (`section` = \'orders\') AND (`name` = \'status_awaiting\'));
            DELETE FROM `store_default_messages` WHERE ((`lang_code` = \'en\') AND (`section` = \'orders\') AND (`name` = \'status_pending\'));
            DELETE FROM `store_default_messages` WHERE ((`lang_code` = \'en\') AND (`section` = \'orders\') AND (`name` = \'status_in_progress\'));        
            DELETE FROM `store_default_messages` WHERE ((`lang_code` = \'en\') AND (`section` = \'orders\') AND (`name` = \'status_completed\'));
            DELETE FROM `store_default_messages` WHERE ((`lang_code` = \'en\') AND (`section` = \'orders\') AND (`name` = \'status_canceled\'));
            DELETE FROM `store_default_messages` WHERE ((`lang_code` = \'en\') AND (`section` = \'orders\') AND (`name` = \'status_failed\'));
            DELETE FROM `store_default_messages` WHERE ((`lang_code` = \'en\') AND (`section` = \'orders\') AND (`name` = \'status_error\'));
        ');
    }
}
