<?php

namespace superadmin\models\forms;

use common\helpers\CurrencyHelper;
use common\models\panels\PanelPaymentMethods;
use common\models\panels\Project;
use common\models\panels\services\GetPanelPaymentMethodsService;
use yii\base\Model;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class EditPanelPaymentMethodsForm
 * @package superadmin\models\forms
 */
class EditPanelPaymentMethodsForm extends Model
{
    public $methods;

    /**
     * @var Project
     */
    private $_panel;

    /**
     * @var array
     */
    protected static $paymentMethods;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['methods'], 'safe'],
        ];
    }

    /**
     * Set project
     * @param Project $panel
     */
    public function setPanel(Project $panel)
    {
        $this->_panel = $panel;
    }

    /**
     * Save panel payment methods
     * @return bool
     */
    public function save(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $paymentMethods = $this->getPaymentMethods();
        $currentMethods = ArrayHelper::index(PanelPaymentMethods::find()->andWhere([
            'panel_id' => $this->_panel->id
        ])->all(), 'method_id');

        $transaction = Yii::$app->db->beginTransaction();

        foreach ((array)$this->methods as $methodId => $value) {
            if (!empty($currentMethods[$methodId])) {
                unset($currentMethods[$methodId]);
                continue;
            }

            if (empty($paymentMethods[$methodId])) {
                continue;
            }

            $model = new PanelPaymentMethods();
            $model->method_id = $methodId;
            $model->panel_id = $this->_panel->id;
            $model->name = ArrayHelper::getValue($paymentMethods, [$methodId, 'method_name']);
            $model->setOptions([]);

            if (!$model->save()) {
                $this->addError('methods', Yii::t('app/superadmin', 'panels.edit.payment_methods.error'));
                return false;
            }
        }

        if (!empty($currentMethods)) {
            /**
             * @var $currentMethod PanelPaymentMethods
             */
            foreach ($currentMethods as $currentMethod) {
                $currentMethod->delete();
            }
        }

        $transaction->commit();

        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'methods' => Yii::t('app/superadmin', 'providers.modal_edit_provider.provider_id'),
        ];
    }

    /**
     * @return array
     */
    public function getPaymentMethods()
    {
        if (null === static::$paymentMethods) {
            $paymentMethods = CurrencyHelper::getPaymentMethodsByCurrency($this->_panel->getCurrencyCode());
            $panelPaymentMethods = Yii::$container->get(GetPanelPaymentMethodsService::class, [$this->_panel])->get();

            foreach ($paymentMethods as $method) {
                static::$paymentMethods[$method['id']] = [
                    'id' => $method['id'],
                    'method_name' => $method['method_name'],
                    'active' => (int)!empty($panelPaymentMethods[$method['id']])
                ];
            }
        }

        return static::$paymentMethods;
    }
}
