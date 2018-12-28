<?php
namespace admin\models\forms;

use common\models\gateways\PaymentMethods;
use common\models\gateways\SitePaymentMethods;
use common\models\gateways\Sites;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class EditPaymentMethodForm
 * @package admin\models\forms
 */
class EditPaymentMethodForm extends Model
{
    public $method_id;
    public $details;

    /**
     * @var PaymentMethods
     */
    protected $_paymentMethod;

    /**
     * @var SitePaymentMethods
     */
    protected $_sitePaymentMethod;

    /**
     * @var Sites
     */
    protected $_gateway;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['details'], 'safe'],
        ];
    }

    /**
     * @param Sites $gateway
     */
    public function setGateway($gateway)
    {
        $this->_gateway = $gateway;
    }

    /**
     * @param PaymentMethods $paymentMethod
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->_paymentMethod = $paymentMethod;
        $this->method_id = $this->_paymentMethod->id;
    }

    /**
     * @return PaymentMethods
     */
    public function getPaymentMethod()
    {
        return $this->_paymentMethod;
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $sitePaymentMethod = $this->getSitePaymentMethod();
        $sitePaymentMethod->setOptionsDetails($this->details);

        if (!$sitePaymentMethod->save()) {
            $this->addError('details', Yii::t('admin', "settings.message_settings_error"));
            return false;
        }

        return true;
    }

    /**
     * @return SitePaymentMethods|null
     */
    public function getSitePaymentMethod()
    {
        if (null === $this->_sitePaymentMethod) {
            $attributes = [
                'site_id' => $this->_gateway->id,
                'method_id' => $this->_paymentMethod->id
            ];

            if (!($this->_sitePaymentMethod = SitePaymentMethods::findOne($attributes))) {
                $this->_sitePaymentMethod = new SitePaymentMethods($attributes);
            }
        }

        return $this->_sitePaymentMethod;
    }

    /**
     * @return array
     */
    public function getDetails()
    {
        $settingsForm = [];
        $siteOptions = $this->getSitePaymentMethod()->getOptionsDetails();

        foreach ($this->_paymentMethod->getFormSettings() as $field) {
            $field['value'] = ArrayHelper::getValue($siteOptions, $field['name']);
            $field['name'] = $this->formName() . '[details][' . $field['name'] . ']';
            $settingsForm[] = $field;
        }

        return $settingsForm;
    }
}