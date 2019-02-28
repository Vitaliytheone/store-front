<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Class m190226_142903_store_table_pages_add_column
 */
class m190226_142903_store_table_pages_add_column extends Migration
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
            ALTER TABLE `pages` ADD `seo_description` VARCHAR(2000) NULL DEFAULT NULL AFTER `title`;
            ALTER TABLE `pages` ADD `seo_keywords` VARCHAR(2000) NULL DEFAULT NULL AFTER `seo_description`;
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
            ALTER TABLE `pages` DROP COLUMN `seo_description`;
            ALTER TABLE `pages` DROP COLUMN `seo_keywords`;
        ');
        }
    }


}
