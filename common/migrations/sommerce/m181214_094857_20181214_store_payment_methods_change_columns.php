<?php

use yii\db\Migration;
use yii\db\Query;
use common\models\stores\PaymentMethods;
use common\models\stores\StorePaymentMethods;
use common\models\stores\PaymentMethodsCurrency;

/**
 * Class m181214_094857_20181214_store_payment_methods_change_columns
 */
class m181214_094857_20181214_store_payment_methods_change_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $methods = (new Query())
            ->select('id, method')
            ->from(DB_STORES . '.store_payment_methods')
            ->indexBy('id')
            ->all();

        $this->renameColumn(DB_STORES . '.store_payment_methods', 'details', 'options');
        $this->renameColumn(DB_STORES . '.store_payment_methods', 'active', 'visibility');

//        $this->alterColumn(DB_STORES . '.store_payment_methods', 'id', $this->integer(11)->unsigned());
        $this->execute('ALTER TABLE `store_payment_methods` CHANGE `id` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT;');
        $this->alterColumn(DB_STORES . '.store_payment_methods', 'store_id', $this->integer(11)->notNull());
        $this->alterColumn(DB_STORES . '.store_payment_methods', 'options', $this->text()->null());
        $this->alterColumn(DB_STORES . '.store_payment_methods', 'visibility', $this->tinyInteger(1)->notNull()->defaultValue(1));

        $this->dropColumn(DB_STORES . '.store_payment_methods', 'method');

        $this->addColumn(DB_STORES . '.store_payment_methods', 'method_id', $this->integer(11)->unsigned());
        $this->addColumn(DB_STORES . '.store_payment_methods', 'currency_id', $this->integer(11)->unsigned()->null());
        $this->addColumn(DB_STORES . '.store_payment_methods', 'name', $this->string(255));
        $this->addColumn(DB_STORES . '.store_payment_methods', 'position', $this->integer(11));
        $this->addColumn(DB_STORES . '.store_payment_methods', 'created_at', $this->integer(11)->null());
        $this->addColumn(DB_STORES . '.store_payment_methods', 'updated_at', $this->integer(11)->null());

        $this->dropIndex('fk_store_id_method_idx', DB_STORES . '.store_payment_methods');

        $this->createIndex('idx_store_id', DB_STORES . '.store_payment_methods', 'store_id');
        $this->createIndex('idx_method_id', DB_STORES . '.store_payment_methods', 'method_id');
        $this->createIndex('idx_currency_id', DB_STORES . '.store_payment_methods', 'currency_id');

        $this->addForeignKey(
            'fk_method_id_to_payment_methods',
            'store_payment_methods',
            'method_id',
            'payment_methods',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_currency_id_to_methods_currency',
            'store_payment_methods',
            'currency_id',
            'payment_methods_currency',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_store_id_to_stores',
            'store_payment_methods',
            'store_id',
            'stores',
            'id',
            'CASCADE',
            'CASCADE'
        );

        foreach ($methods as $key => $methodName) {
            $method = PaymentMethods::findOne(['method_name' => $methodName['method']]);
            $storeMethod = StorePaymentMethods::findOne($key);
            if (!$method || !$storeMethod) {
                continue;
            }
            $storeCurrency = PaymentMethodsCurrency::findOne(['method_id' => $method->id]);

            $lastPositions = StorePaymentMethods::find()
                ->where(['store_id' => $storeMethod->store_id])
                ->max('position');

            $storeMethod->method_id = $method->id;
            $storeMethod->currency_id = $storeCurrency->id;
            $storeMethod->name = $method->name;
            $storeMethod->position = isset($lastPositions) ? $lastPositions + 1 : 1;
            if (empty($storeMethod->options) || $storeMethod->options === '[]') {
                $storeMethod->options = $storeMethod->setClearOptions($method->id);
            }
            $storeMethod->created_at = time();
            $storeMethod->updated_at = time();
            $storeMethod->save(false);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_method_id_to_payment_methods', DB_STORES . '.store_payment_methods');
        $this->dropForeignKey('fk_currency_id_to_methods_currency', DB_STORES . '.store_payment_methods');
        $this->dropForeignKey('fk_store_id_to_stores', DB_STORES . '.store_payment_methods');

        $this->dropIndex('idx_store_id', DB_STORES . '.store_payment_methods');
        $this->dropIndex('idx_method_id', DB_STORES . '.store_payment_methods');
        $this->dropIndex('idx_currency_id', DB_STORES . '.store_payment_methods');

        $this->renameColumn(DB_STORES . '.store_payment_methods', 'options', 'details');
        $this->renameColumn(DB_STORES . '.store_payment_methods', 'visibility', 'active');

        $this->alterColumn(DB_STORES . '.store_payment_methods', 'id', $this->integer(11));
        $this->alterColumn(DB_STORES . '.store_payment_methods', 'store_id', $this->integer(11)->null());
        $this->alterColumn(DB_STORES . '.store_payment_methods', 'details', $this->text()->null());
        $this->alterColumn(DB_STORES . '.store_payment_methods', 'active', $this->tinyInteger(1)->null());

        $this->addColumn(DB_STORES . '.store_payment_methods', 'method', $this->string(255)->null());

        $this->dropColumn(DB_STORES . '.store_payment_methods', 'method_id');
        $this->dropColumn(DB_STORES . '.store_payment_methods', 'currency_id');
        $this->dropColumn(DB_STORES . '.store_payment_methods', 'name');
        $this->dropColumn(DB_STORES . '.store_payment_methods', 'position');
        $this->dropColumn(DB_STORES . '.store_payment_methods', 'created_at');
        $this->dropColumn(DB_STORES . '.store_payment_methods', 'updated_at');

        $this->createIndex('fk_store_id_method_idx', DB_STORES . '.store_payment_methods', 'store_id');
    }
}
