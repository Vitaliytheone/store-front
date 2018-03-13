<?php

use yii\db\Migration;

class m180313_131117_stores_themes_thumbnails extends Migration
{
    public function safeUp()
    {

    }

    public function safeDown()
    {
        echo "m180313_131117_stores_themes_thumbnails cannot be reverted.\n";

        return false;
    }

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->execute('
            USE `stores`;
            UPDATE `default_themes` SET `thumbnail` = \'/img/themes/preview_classic.png\' WHERE `id` = \'1\';
            UPDATE `default_themes` SET `thumbnail` = \'/img/themes/preview_green.png\' WHERE `id` = \'2\';
            UPDATE `default_themes` SET `thumbnail` = \'/img/themes/preview_seocrack.png\' WHERE `id` = \'3\';
            UPDATE `default_themes` SET `thumbnail` = \'/img/themes/preview_smm24.png\' WHERE `id` = \'4\';
        ');
    }

    public function down()
    {
        echo "m180313_131117_stores_themes_thumbnails cannot be reverted.\n";

        return false;
    }
}
