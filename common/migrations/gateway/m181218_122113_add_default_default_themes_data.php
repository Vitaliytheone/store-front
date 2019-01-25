<?php

use yii\db\Migration;

/**
 * Class m181218_122113_add_default_default_themes_data
 */
class m181218_122113_add_default_default_themes_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            USE " . DB_GATEWAYS . ";
            
            INSERT INTO `default_themes` (`id`, `name`, `folder`, `position`, `thumbnail`) VALUES
            (1,	'Bootstrap',	'bootstrap',	0,	'/img/themes/preview_classic.png');
            
            INSERT INTO `default_themes` (`id`, `name`, `folder`, `position`, `thumbnail`) VALUES
            (2,	'Green',	'green',	1,	'/img/themes/preview_green.png');
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("
            USE " . DB_GATEWAYS . ";
            
            SET foreign_key_checks = 0;

            TRUNCATE TABLE `default_themes`;
        ");
    }
}
