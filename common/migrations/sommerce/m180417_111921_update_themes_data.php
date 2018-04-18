<?php

use yii\db\Migration;

/**
 * Class m180417_111921_update_themes_data
 */
class m180417_111921_update_themes_data extends Migration
{
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->execute("
            UPDATE `default_themes` SET
            `name` = 'SMM24'
            WHERE `folder` = 'smm24';
        ");

        $this->execute("
            UPDATE `default_themes` SET
            `name` = 'Bootstrap'
            WHERE `folder` = 'classic';
        ");
    }

    public function down()
    {
        $this->execute("
            UPDATE `default_themes` SET
            `name` = 'Smm24'
            WHERE `folder` = 'smm24';
        ");

        $this->execute("
            UPDATE `default_themes` SET
            `name` = 'Classic'
            WHERE `folder` = 'classic';
        ");
    }

}
