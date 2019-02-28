<?php

namespace sommerce\modules\admin\models\forms;

use common\models\sommerces\PaymentMethods;
use common\models\sommerces\PaymentMethodsCurrency;
use common\models\sommerces\StorePaymentMethods;
use common\models\sommerces\Stores;
use yii\base\Model;
use Yii;

/**
 * Class AddPaymentMethodForm
 * @package sommerce\modules\admin\models\forms
 */
class AddPaymentMethodForm extends Model
{
    public $method;

    /** @var Stores */
    private $storeId;

    /**
     * {@inheritdoc}
     * @return array
     */
    public function rules()
    {
        return [
            ['method', 'required'],
            ['method', 'integer'],
        ];
    }

    /**
     * Set store
     * @param int $storeId
     */
    public function setStoreId(int $storeId)
    {
        $this->storeId = $storeId;
    }

    /**
     * Save new store payment method
     * @return bool
     */
    public function save(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $currency = PaymentMethodsCurrency::findOne($this->method);
        if (!$currency) {
            return false;
        }

        $storePaymentMethod = StorePaymentMethods::find()
            ->where(['store_id' => $this->storeId, 'method_id' => $currency->method_id])
            ->exists();

        if ($storePaymentMethod) {
            return false;
        }

        $paymentMethod = PaymentMethods::findOne($currency->method_id);

        if (!$paymentMethod) {
            return false;
        }

        $settingsForm = $currency->settings_form ?? $paymentMethod->settings_form;

        $newStoreMethod = new StorePaymentMethods();
        $newStoreMethod->store_id = $this->storeId;
        $newStoreMethod->method_id = $currency->method_id;
        $newStoreMethod->currency_id = $currency->id;
        $newStoreMethod->name = !empty($paymentMethod->name) ? $paymentMethod->name : $paymentMethod->method_name;
        $newStoreMethod->visibility = StorePaymentMethods::VISIBILITY_DISABLED;
        $newStoreMethod->options = $this->getOptions($settingsForm);
        $newStoreMethod->position = StorePaymentMethods::getLastPosition() + 1;

        if (!$newStoreMethod->save()) {
            $this->addErrors($newStoreMethod->getErrors());
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'method' => Yii::t('admin', 'settings.payments_modal_payment_method'),
        ];
    }

    /**
     * Get payment method options
     * @param $settingsForm
     * @return string
     */
    private function getOptions($settingsForm): string
    {
        $settingsForm = json_decode($settingsForm, true);

        $options = array_keys($settingsForm);
        $result = [];

        foreach ($options as $option) {
            $result[$option] = '';
        }

        return json_encode($result);
    }
}
