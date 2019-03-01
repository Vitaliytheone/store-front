<?php

use yii\db\Migration;

/**
 * Class m180417_133627_update_themes_data
 */
class m180417_133627_update_themes_data extends Migration
{
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->execute("
            UPDATE `default_themes` SET
            `folder` = 'bootstrap'
            WHERE `folder` = 'classic';
        ");
    }

    public function down()
    {
        $this->execute("
            UPDATE `default_themes` SET
            `folder` = 'classic'
            WHERE `folder` = 'bootstrap';
        ");
    }
}
