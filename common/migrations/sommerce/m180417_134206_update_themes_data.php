<?php

use yii\db\Migration;

/**
 * Class m180417_134206_update_themes_data
 */
class m180417_134206_update_themes_data extends Migration
{
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->execute("UPDATE `stores` SET `theme_name` = 'Bootstrap', `theme_folder` = 'bootstrap' WHERE `theme_folder` = 'classic';");
    }

    public function down()
    {
        $this->execute("UPDATE `stores` SET `theme_name` = 'Classic', `theme_folder` = 'classic' WHERE `theme_folder` = 'bootstrap';");
    }
}
