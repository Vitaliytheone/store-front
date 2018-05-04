<?php

use yii\db\Migration;

/**
 * Class m180503_115003_added_default_lang_messages
 */
class m180503_115003_added_default_lang_messages extends Migration
{
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->execute('
            INSERT INTO `store_default_messages` (`lang_code`, `section`, `name`, `value`)
            VALUES (\'en\', \'cart\', \'payment_description\', \'Order #{order_id}\');
            
            INSERT INTO `store_default_messages` (`lang_code`, `section`, `name`, `value`)
            VALUES (\'en\', \'order\', \'invalid_link\', \'Invalid {name} link.\');
        ');

        foreach ((new \yii\db\Query())->select([
            'db_name'
        ])->from(DB_STORES . '.stores')->all() as $store) {
            $db = $store['db_name'];
            $isDbExist = Yii::$app->db
                ->createCommand("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$db'")
                ->queryScalar();

            if (!$isDbExist) {
                continue;
            }

            $this->execute('
                INSERT INTO `' . $db . '`.`messages` (`lang_code`, `section`, `name`, `value`)
                VALUES (\'en\', \'cart\', \'payment_description\', \'Order #{order_id}\');
                
                INSERT INTO `' . $db . '`.`messages` (`lang_code`, `section`, `name`, `value`)
                VALUES (\'en\', \'order\', \'invalid_link\', \'Invalid {name} link.\');
            ');
        }
    }

    public function down()
    {
       $this->execute('DELETE FROM `store_default_messages` WHERE ((`lang_code` = \'en\') AND (`section` = \'cart\') AND (`name` = \'payment_description\'));');

        foreach ((new \yii\db\Query())->select([
            'db_name'
        ])->from(DB_STORES . '.stores')->all() as $store) {
            $db = $store['db_name'];
            $isDbExist = Yii::$app->db
                ->createCommand("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$db'")
                ->queryScalar();

            if (!$isDbExist) {
                continue;
            }

            $this->execute('DELETE FROM `' . $db . '`.`messages` WHERE ((`lang_code` = \'en\') AND (`section` = \'cart\') AND (`name` = \'payment_description\'));');
            $this->execute('DELETE FROM `' . $db . '`.`messages` WHERE ((`lang_code` = \'en\') AND (`section` = \'order\') AND (`name` = \'invalid_link\'));');
        }
    }
}
