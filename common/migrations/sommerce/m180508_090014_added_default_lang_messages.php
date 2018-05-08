<?php

use yii\db\Migration;

/**
 * Class m180508_090014_added_default_lang_messages
 */
class m180508_090014_added_default_lang_messages extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->execute('
            INSERT INTO `store_default_messages` (`lang_code`, `section`, `name`, `value`)
            VALUES (\'en\', \'cart\', \'phone\', \'Phone\');
            
            INSERT INTO `store_default_messages` (`lang_code`, `section`, `name`, `value`)
            VALUES (\'en\', \'cart\', \'error.phone\', \'Incorrect phone.\');
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->execute('
          DELETE FROM `store_default_messages` WHERE ((`lang_code` = \'en\') AND (`section` = \'cart\') AND (`name` = \'phone\'));
          DELETE FROM `store_default_messages` WHERE ((`lang_code` = \'en\') AND (`section` = \'cart\') AND (`name` = \'error.phone\'));
        ');
    }
}
