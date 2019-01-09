<?php

use yii\db\Migration;

/**
 * Class m181225_070157_update_default_themes
 */
class m181225_070157_update_default_themes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            USE `" . DB_GATEWAYS . "`;

            DELETE FROM `default_themes`
                WHERE ((`folder` = 'green'));
                
            UPDATE `default_themes` SET
                `name` = 'Default',
                `folder` = 'default'
                WHERE `folder` = 'bootstrap';
                
            UPDATE sites
                SET `theme_name` = 'Default', `theme_name` = 'default'
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('
            USE `' . DB_GATEWAYS . '`;
            
            INSERT INTO `default_themes` (`name`, `folder`, `position`, `thumbnail`) VALUES
             (\'Green\',	\'green\',	1,	\'/img/themes/preview_green.png\');
            
            UPDATE `default_themes` SET
                `name` = \'Bootstrap\',
                `folder` = \'bootstrap\'
                WHERE `folder` = \'default\';
        ');
    }
}
