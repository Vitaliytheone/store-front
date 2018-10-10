<?php

namespace my\modules\superadmin\models\search;

use yii\helpers\ArrayHelper;
use common\helpers\CurrencyHelper;
use common\models\panels\PanelPaymentMethods;
use common\models\panels\PaymentMethods;
use common\models\panels\Project;
use Exception;
use yii\base\Model;
use \yii\db\Query;
use Yii;

/**
 * Class PaymentMethodsSearch
 * @package my\modules\superadmin\models\search
 */
class PaymentMethodsSearch extends Model
{
    /**
     * @var PaymentMethods[]
     */
    protected static $paymentMethods;

    /**
     * @var Project
     */
    protected $_panel;

    /**
     * @param Project $panel
     */
    public function setPanel(Project $panel)
    {
        $this->_panel = $panel;
    }

    /**
     * Build main search query
     * @return \yii\db\Query|null
     */
    private function buildQuery()
    {
        $query = (new Query())
            ->select([
                'id',
                'method_id',
                'name',
                'minimal',
                'maximal',
                'options',
                'visibility',
                'new_users',
                'take_fee_from_user',
            ])
            ->from(['ppm' => DB_PANELS . '.' . PanelPaymentMethods::tableName()])
            ->andWhere([
                'panel_id' => $this->_panel->id
            ])
            ->orderBy([
                'position' => SORT_ASC
            ]);

        return $query;
    }

    /**
     * Search tickets
     * @return array
     */
    public function search()
    {
        try {
            $query = clone $this->buildQuery();
        } catch (Exception $e) {
            return [
                'models' => []
            ];
        }

        $methods = $query
            ->all();

        return [
            'models' => $this->preparePaymentMethods($methods),
        ];
    }

    /**
     * @return array
     */
    public function getPaymentMethods()
    {
        if (null === static::$paymentMethods) {
            static::$paymentMethods = CurrencyHelper::getPaymentMethodsByCurrency($this->_panel->getCurrencyCode());
        }

        return static::$paymentMethods;
    }

    /**
     * @param $methods
     * @return array
     */
    public function preparePaymentMethods($methods)
    {
        $paymentMethods = $this->getPaymentMethods();
        $returnMethods = [];

        foreach ((array)$methods as $method) {
            $paymentMethod = ArrayHelper::getValue($paymentMethods, $method['method_id']);
            if (empty($paymentMethod)) {
                continue;
            }

            $options = [];

            $methodOptions = !empty($method['options']) ? json_decode($method['options'], true) : [];

            foreach ($paymentMethod['settings_form'] as $field => $details) {
                if (PaymentMethods::FIELD_TYPE_MULTI_INPUT == $details['type']) {
                    $values = ArrayHelper::getValue($methodOptions, $field);
                    $options[] = ArrayHelper::merge([
                        'values' => !empty($values) ? $values : [''],
                        'add_label' => Yii::t('admin/settings', 'payments.edit.add_multi_input', [
                            'name' => $details['name']
                        ])
                    ], $details);
                } else {
                    $options[] = ArrayHelper::merge([
                        'value' => ArrayHelper::getValue($methodOptions, $field)
                    ], $details);
                }
            }

            $returnMethods[] = [
                'id' => $method['id'],
                'method_id' => $method['method_id'],
                'name' => $method['name'],
                'min' => $method['minimal'],
                'max' => $method['maximal'],
                'new_users' => $method['new_users'],
                'visibility' => $method['visibility'],
                'details' => [
                    'title' => $paymentMethod['method_name'],
                    'name' => $method['name'],
                    'minimal' => $method['minimal'],
                    'maximal' => $method['maximal'],
                    'new_users' => $method['new_users'],
                    'description' => $paymentMethod['settings_form_description'],
                    'options' => $options,
                ]
            ];
        }

        return $returnMethods;
    }
}