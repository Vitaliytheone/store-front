<?php

namespace console\helpers;

use common\models\store\Carts;
use common\models\stores\Stores;
use Yii;
use yii\db\Query;

/**
 * Class SommerceHelper
 * @package console\helpers
 */
class SommerceHelper
{
    /**
     * Return active stores list
     * indexed by store id
     * @param array $fields
     * @return array
     */
    public static function getStoresList($fields = [])
    {
        $fields = $fields ? $fields : '*';

        return (new Query())
            ->select(array_merge(['id'], $fields))
            ->from(Stores::tableName())
            ->andWhere(['status' => Stores::STATUS_ACTIVE])
            ->andWhere(['not', ['db_name' => null]])
            ->andWhere(['not', ['db_name' => '']])
            ->indexBy('id')
            ->all();
    }

    /**
     * Clear all stores carts older when $olderWhen seconds
     * @param int $olderWhenDays
     */
    public static function clearStoresCarts($olderWhenDays = 30)
    {
        $stores = static::getStoresList(['db_name']);

        /** @var yii\db\Connection $connection */
        $connection = Yii::$app->storeDb;

        $tableName = Carts::tableName();

        foreach ($stores as $storeId =>$store) {

            $db = $store['db_name'];

            $connection
                ->createCommand("DELETE FROM $db.$tableName WHERE created_at < :created_at")
                ->bindValues([
                    ':created_at' => time() - $olderWhenDays * 24 * 60 * 60,
                ])
                ->execute();
        }
    }
}