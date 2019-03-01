<?php

use yii\db\Migration;

/**
 * Class m180601_072549_added_default_lang_messages
 */
class m180601_072549_added_default_lang_messages extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->execute("
            USE `" . DB_STORES . "`;

            INSERT INTO `store_default_messages` (`lang_code`, `section`, `name`, `value`)
            VALUES ('en', 'vieworder', 'title', 'Order #{id}');
            
            INSERT INTO `store_default_messages` (`lang_code`, `section`, `name`, `value`)
            VALUES ('en', 'vieworder', 'package', 'Package name');
            
            INSERT INTO `store_default_messages` (`lang_code`, `section`, `name`, `value`)
            VALUES ('en', 'vieworder', 'details', 'Details');
            
            INSERT INTO `store_default_messages` (`lang_code`, `section`, `name`, `value`)
            VALUES ('en', 'vieworder', 'quantity', 'Quantity');
            
            INSERT INTO `store_default_messages` (`lang_code`, `section`, `name`, `value`)
            VALUES ('en', 'vieworder', 'price', 'Price');
            
            INSERT INTO `store_default_messages` (`lang_code`, `section`, `name`, `value`)
            VALUES ('en', 'vieworder', 'status', 'Status');
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->execute('
            USE `' . DB_STORES . '`;
            DELETE FROM `store_default_messages` WHERE ((`lang_code` = \'en\') AND (`section` = \'vieworder\') AND (`name` = \'title\'));
            DELETE FROM `store_default_messages` WHERE ((`lang_code` = \'en\') AND (`section` = \'vieworder\') AND (`name` = \'package\'));
            DELETE FROM `store_default_messages` WHERE ((`lang_code` = \'en\') AND (`section` = \'vieworder\') AND (`name` = \'details\'));        
            DELETE FROM `store_default_messages` WHERE ((`lang_code` = \'en\') AND (`section` = \'vieworder\') AND (`name` = \'quantity\'));
            DELETE FROM `store_default_messages` WHERE ((`lang_code` = \'en\') AND (`section` = \'vieworder\') AND (`name` = \'price\'));
            DELETE FROM `store_default_messages` WHERE ((`lang_code` = \'en\') AND (`section` = \'vieworder\') AND (`name` = \'status\'));
        ');
    }
}
