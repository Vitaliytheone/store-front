<?php

namespace sommerce\modules\admin\models\forms;

use common\models\panels\PaymentMethodsCurrency;
use common\models\store\ActivityLog;
use common\models\stores\StoreAdminAuth;
use common\models\stores\StorePaymentMethods;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\helpers\ArrayHelper;
use common\models\stores\PaymentMethods;
use yii\helpers\Html;
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

    public function getMethodFormData()
    {
        /** @var PaymentMethodsCurrency $method */
        $method = $this->getStorePaymentMethodCurrency()->one();

        if (!isset($method->settings_form)) {
            /** @var PaymentMethods $method */
            $method = $this->getPaymentMethod()->one();
        }

        $settingForm = $method->getSettingsForm();
        $options = $this->getOptions();
        $result = [];

        foreach ($settingForm as $key => $settings) {
            $commonLabel = Html::label(Yii::t('admin', $settings['label']), 'editpaymentmethod-' . $settings['code']);

            switch ($settings['type']) {
                case 'input':
                    $result[$key] =
                        '<div class="form-group">' .
                        $commonLabel .
                        Html::input(
                            'text', $this->getFormElementName($settings['name']),
                            $options[$settings['code']],
                            [
                                'id' => 'editpaymentmethod-' . $settings['code'],
                                'class' => 'form-control',
                            ]
                        ) .
                        '</div>';
                    break;

                case 'checkbox':
                    $result[$key] =
                        '<div class="form-group">' .
                        '<label class="form-check-label">' .
                        Html::checkbox(
                            $this->getFormElementName($settings['name']),
                            $options[$settings['code']],
                            [
                                'id' => 'editpaymentmethod-' . $settings['code'],
                                'class' => 'form-check-input',
                            ]
                        ) . ' ' .
                        Yii::t('admin', $settings['label']) .
                        '</label>';
                    break;

                case 'multi_input':
                    $result[$key] =
                        '<div id="multi_input_container_descriptions">';

                    $id = 1;
                    foreach ((array)$options[$settings['code']] as $description) {
                        $result[$key] .=
                            '<div class="form-group form-group-description">' .
                            '<span class="fa fa-times remove-description"></span>' .
                            Html::label(Yii::t('admin', $settings['label']), 'edit-payment-method-options-descriptions' . $id) .
                            Html::input('text', $this->getFormElementName($settings['name']) . '[]', $description, [
                                'class' => 'form-control multi-input-item',
                                'id' => 'edit-payment-method-options-descriptions' . $id,
                            ]) .
                            '</div>';

                        $id++;
                    }
                    $result[$key] .= '</div>' .
                        Html::a('<span>' . Yii::t('admin', 'settings.payments.multi_input.add_description') . '</span>', '#',
                            [
                                'class' => 'add-multi-input',
                                'data-name' => $this->getFormElementName($settings['name']) . '[]',
                                'data-label' => $settings['label'],
                                'data-class' => 'multi-input-item',
                                'data-id' => 'edit-payment-method-options-descriptions' . $id,
                            ]
                        );
                    break;

                case 'select':
                    $result[$key] =
                        '<div class="form-group">' .
                        $commonLabel .
                        Html::dropDownList(
                            $this->getFormElementName($settings['name']),
                            $options[$settings['code']],
                            $settings['options'],
                            [
                                'id' => 'editpaymentmethod-' . $settings['code'],
                                'class' => 'form-control',
                            ]
                        ) .
                        '</div>';
                    break;

                case 'textarea':
                    $result[$key] =
                        '<div class="form-group">' .
                        $commonLabel .
                        Html::textarea(
                            $this->getFormElementName($settings['name']),
                            $options[$settings['code']],
                            [
                                'id' => 'editpaymentmethod-' . $settings['code'],
                                'class' => 'form-control',
                            ]
                        ) .
                        '</div>';
                    break;
            }
        }

        return $result;
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
        if (!$this->load($postData) || !$this->save()) {
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
    private function getFormElementName(string $name): string
    {
        return $this->formName() . "[$name]";
    }
}