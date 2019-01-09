<?php

use yii\db\Migration;
use yii\db\Query;
use common\models\stores\PaymentMethodsCurrency;

/**
 * Class m181214_085452_20181214_payment_methods_change_columns
 */
class m181214_085452_20181214_payment_methods_change_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $methods = (new Query())
            ->select(['id', 'currencies', 'options', 'position'])
            ->from(DB_STORES . '.payment_methods')
            ->all();

        foreach ($methods as $method) {
            $currencies = json_decode($method['currencies'], true);

            foreach ($currencies as $currency) {
                $paymentMethodCurrency = new PaymentMethodsCurrency();
                $paymentMethodCurrency->method_id = $method['id'];
                $paymentMethodCurrency->currency = $currency;
                $paymentMethodCurrency->position = $method['position'];
                $paymentMethodCurrency->created_at = time();
                $paymentMethodCurrency->updated_at = time();
                $paymentMethodCurrency->save(false);
            }
        }

        $this->renameColumn(DB_STORES . '.payment_methods', 'method', 'method_name');
        $this->renameColumn(DB_STORES . '.payment_methods', 'options', 'settings_form');

        $this->dropColumn(DB_STORES . '.payment_methods', 'currencies');
        $this->dropColumn(DB_STORES . '.payment_methods', 'position');
        $this->dropColumn(DB_STORES . '.payment_methods', 'visibility');

        $this->alterColumn(DB_STORES . '.payment_methods', 'name', $this->string(255)->notNull());
        $this->alterColumn(DB_STORES . '.payment_methods', 'method_name', $this->string(255)->notNull());
        $this->alterColumn(DB_STORES . '.payment_methods', 'class_name', $this->string(255)->notNull());
        $this->alterColumn(DB_STORES . '.payment_methods', 'url', $this->string(255)->notNull());
        $this->alterColumn(DB_STORES . '.payment_methods', 'settings_form', $this->text()->null());

        $this->addColumn(DB_STORES . '.payment_methods', 'icon', $this->string(64));
        $this->addColumn(DB_STORES . '.payment_methods', 'addfunds_form', $this->text()->null());
        $this->addColumn(DB_STORES . '.payment_methods', 'settings_form_description', $this->text()->null());
        $this->addColumn(DB_STORES . '.payment_methods', 'manual_callback_url', $this->smallInteger(1)->notNull()->defaultValue(0));
        $this->addColumn(DB_STORES . '.payment_methods', 'created_at', $this->integer(11)->null());
        $this->addColumn(DB_STORES . '.payment_methods', 'updated_at', $this->integer(11)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameColumn(DB_STORES . '.payment_methods', 'method_name', 'method');
        $this->renameColumn(DB_STORES . '.payment_methods', 'settings_form', 'options');

        $this->addColumn(DB_STORES . '.payment_methods', 'currencies', $this->string(3000)->null());
        $this->addColumn(DB_STORES . '.payment_methods', 'position', $this->tinyInteger(2));

        $this->alterColumn(DB_STORES . '.payment_methods', 'name', $this->string(300)->null());
        $this->alterColumn(DB_STORES . '.payment_methods', 'options', $this->text()->notNull());

        $this->dropColumn(DB_STORES . '.payment_methods', 'addfunds_form');
        $this->dropColumn(DB_STORES . '.payment_methods', 'settings_form_description');
        $this->dropColumn(DB_STORES . '.payment_methods', 'manual_callback_url');
        $this->dropColumn(DB_STORES . '.payment_methods', 'created_at');
        $this->dropColumn(DB_STORES . '.payment_methods', 'updated_at');
    }
}
