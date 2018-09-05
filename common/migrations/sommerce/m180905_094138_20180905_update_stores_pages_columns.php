<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Class m180905_094138_20180905_update_stores_pages_columns
 */
class m180905_094138_20180905_update_stores_pages_columns extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $stores = $this->getStores();

        foreach ($stores as $key => $store) {
            $this->addColumn($store['db_name'] . '.pages', 'is_default', $this->integer(1)->defaultValue(1));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $stores = $this->getStores();

        foreach ($stores as $key => $store) {
            $this->dropColumn($store['db_name'] . '.pages', 'is_default');
        }
    }

    /**
     * Get stores list
     * @return array
     */
    private function getStores()
    {
        return (new Query())
            ->select([
                'db_name'
            ])
            ->from(DB_STORES . '.stores')
            ->where('db_name IS NOT NULL')
            ->all();
    }
}
