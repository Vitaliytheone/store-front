<?php

use yii\db\Migration;

/**
 * Class m190116_063817_20190116_db_store_pages_added
 */
class m190116_063817_20190116_db_store_pages_added extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            USE " . Yii::$app->params['storeDefaultDatabase'] . ";" . "
            
            CREATE TABLE `pages` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `url` varchar(300) DEFAULT NULL,
              `title` varchar(300) DEFAULT NULL,
              `visibility` tinyint(1) DEFAULT '0',
              `twig` text COMMENT 'editor twig source',
              `styles` text COMMENT 'editor styles source',
              `json` text COMMENT 'editor published json',
              `json_dev` text COMMENT 'editor unpublished json',
              `created_at` int(11) DEFAULT NULL,
              `updated_at` int(11) DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("
            USE " . Yii::$app->params['storeDefaultDatabase'] . ";" . "
            
            DROP TABLE `pages`;
        ");
    }
}
