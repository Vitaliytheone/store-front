<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Class m181214_122547_20181214_store_checkouts_add_column
 */
class m181214_122547_20181214_store_checkouts_add_column extends Migration
{

    public function getQuery($db)
    {
        return 'USE `' . $db . '`;
            ALTER TABLE checkouts ADD `currency_id` int(11) unsigned NULL AFTER `method_id`;';
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $stores = (new Query())
            ->select('db_name')
            ->from('stores')
            ->where('db_name is not null')
            ->andWhere('db_name != ""')
            ->all();

        $templateDb = Yii::$app->params['storeDefaultDatabase'];
        $stores[] = ['db_name' => $templateDb];

        foreach ($stores as $store) {
            $this->execute($this->getQuery($store['db_name']));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $stores = (new Query())
            ->select('db_name')
            ->from('stores')
            ->where('db_name is not null')
            ->andWhere('db_name != ""')
            ->all();

        $templateDb = Yii::$app->params['storeDefaultDatabase'];
        $stores[] = ['db_name' => $templateDb];

        foreach ($stores as $store) {
            $this->dropColumn($store['db_name'] . 'checkouts', 'currency_id');
        }
    }

}
