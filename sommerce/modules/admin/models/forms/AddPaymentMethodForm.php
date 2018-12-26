<?php

namespace sommerce\modules\admin\models\forms;


use common\models\stores\PaymentMethods;
use common\models\stores\PaymentMethodsCurrency;
use common\models\stores\StorePaymentMethods;
use common\models\stores\Stores;
use my\components\ActiveForm;
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
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $currency = PaymentMethodsCurrency::findOne($this->method);
        $storePaymentMethod = StorePaymentMethods::find()
            ->where(['currency_id' => $currency->id, 'method_id' => $currency->method_id])
            ->exists();

        if ($storePaymentMethod) {
            return false;
        }

        $paymentMethod = PaymentMethods::findOne($currency->method_id);

        $newStoreMethod = new StorePaymentMethods();
        $newStoreMethod->store_id = $this->storeId;
        $newStoreMethod->method_id = $currency->method_id;
        $newStoreMethod->currency_id = $currency->id;
        $newStoreMethod->name = $paymentMethod->name != '' ? $paymentMethod->name : $paymentMethod->method_name;
        $newStoreMethod->visibility = StorePaymentMethods::VISIBILITY_DISABLED;
        $newStoreMethod->options = isset($currency->settings_form) ? $currency->settings_form : $paymentMethod->settings_form;
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
}
