<?php

namespace sommerce\modules\admin\models\forms;


use common\models\store\ActivityLog;
use common\models\stores\StoreAdminAuth;
use common\models\stores\StorePaymentMethods;
use sommerce\helpers\SettingsFormHelper;
use yii\behaviors\AttributeBehavior;
use yii\helpers\ArrayHelper;
use common\models\stores\PaymentMethods;
use yii\web\User;

/**
 * Class EditPaymentMethodForm
 * @package sommerce\modules\admin\models\forms
 */
class EditPaymentMethodForm extends StorePaymentMethods
{

    // TODO rename to StoreEditPaymentMethodForm

    /**
     * @var User
     */
    private $_user;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    self::EVENT_BEFORE_VALIDATE => 'options',
                ],
                'value' => function ($event) {
                    /* @var $event \yii\base\Event */
                    /* @var $model $this */
                    $model = $event->sender;
                    $details = (array)$model->getAttribute('options');

                    foreach ($details as $key => $elem) {
                        if (is_string($elem)) {
                            $details[$key] = trim($elem);
                        }
                    }

                    // Prepare PayPal details
                    if ($model->getPaymentMethod()->one()->name == PaymentMethods::METHOD_PAYPAL) {
                        $apiUsername = ArrayHelper::getValue($details, 'username');
                        $details['username'] = trim($apiUsername);
                    }

                    return json_encode($details);
                },
            ],
            [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    self::EVENT_AFTER_FIND => 'options',
                ],
                'value' => function ($event) {
                    /* @var $event \yii\base\Event */
                    /* @var $model $this */
                    $model = $event->sender;
                    return json_decode($model->getAttribute('options'),true);
                },
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return 'PaymentsForm';
    }

    /**
     * Set current user
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->_user = $user;
    }

    /**
     * Return current user
     * @return User
     */
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function getMethodFormData()
    {
        return SettingsFormHelper::getMethodFormData($this);
    }

    /**
     * Change StorePayMethod visibility status
     * @param $active int visibility status (1 - show, 0 - hide)
     * @return mixed
     * @throws \Throwable
     */
    public function setActive(int $active)
    {
        $this->setAttribute('visibility', $active);
        $this->save();

        /** @var StoreAdminAuth $identity */
        $identity = $this->getUser()->getIdentity(false);

        ActivityLog::log($identity, ActivityLog::E_SETTINGS_PAYMENTS_PG_ACTIVE_STATUS_CHANGED, $this->id, $this->method_id);

        return $active;
    }

    /**
     * Change store_payment_methods settings
     * @param $postData
     * @return bool
     * @throws \Throwable
     */
    public function changeSettings($postData)
    {
        $data = $postData[$this->formName()];
        if (!$this->validateOptions($data)) {
            return false;
        }

        $this->setOptions($data);
        if (!$this->save()) {
            return false;
        }

        /** @var StoreAdminAuth $identity */
        $identity = $this->getUser()->getIdentity(false);

        ActivityLog::log($identity, ActivityLog::E_SETTINGS_PAYMENTS_PG_SETTINGS_CHANGED, $this->id, $this->method_id);

        return true;
    }

    /**
     * @param string $name
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function getFormElementName(string $name): string
    {
        return $this->formName() . "[$name]";
    }
}