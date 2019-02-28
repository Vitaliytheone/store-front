<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Class m190227_094601_store_table_pages_changes_columns
 */
class m190227_094601_store_table_pages_changes_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $stores = (new Query())
            ->select('db_name')
            ->from(DB_STORES . '.stores')
            ->where('db_name is not null')
            ->andWhere('db_name != ""')
            ->all();

        $templateDb = Yii::$app->params['storeDefaultDatabase'];
        $stores[] = ['db_name' => $templateDb];

        foreach ($stores as $store) {
            if (Yii::$app->db->getTableSchema($store['db_name'] . '.pages', true) === null) {
                continue;
            }
            $this->execute('
            USE `' . $store['db_name'] . '`;
            ALTER TABLE `pages` ADD `name` VARCHAR(300) NOT NULL AFTER `id`;
            ALTER TABLE `pages` CHANGE `title` `seo_title` varchar(300) NULL;
        ');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $stores = (new Query())
            ->select('db_name')
            ->from(DB_STORES . '.stores')
            ->where('db_name is not null')
            ->andWhere('db_name != ""')
            ->all();

        $templateDb = Yii::$app->params['storeDefaultDatabase'];
        $stores[] = ['db_name' => $templateDb];

        foreach ($stores as $store) {
            if (Yii::$app->db->getTableSchema($store['db_name'] . '.pages', true) === null) {
                continue;
            }
            $this->execute('
            USE `' . $store['db_name'] . '`;
            ALTER TABLE `pages` DROP COLUMN `name`;
            ALTER TABLE `pages` CHANGE `seo_title` `title` varchar(300) NULL;
        ');
        }
    }
}
