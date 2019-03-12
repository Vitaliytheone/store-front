<?php

namespace store\modules\admin\models\forms;

use common\models\store\ActivityLog;
use common\models\stores\StoreAdminAuth;
use common\models\stores\StorePaymentMethods;

/**
 * Class EditPaymentMethodForm
 * @package store\modules\admin\models\forms
 */
class EditPaymentMethodForm extends StorePaymentMethods
{
    /**
     * @var StoreAdminAuth
     */
    private $_user;

    /**
     * @inheritdoc
     */
    public function formName(): string
    {
        return 'PaymentsForm';
    }

    /**
     * Set current user
     * @param StoreAdminAuth $user
     */
    public function setUser(StoreAdminAuth $user)
    {
        $this->_user = $user;
    }

    /**
     * Return current user
     * @return StoreAdminAuth
     */
    public function getUser()
    {
        return $this->_user;
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
        $identity = $this->getUser();

        ActivityLog::log($identity, ActivityLog::E_SETTINGS_PAYMENTS_PG_ACTIVE_STATUS_CHANGED, $this->id, $this->method_id);

        return $active;
    }

    /**
     * Change store_payment_methods settings
     * @param $postData
     * @return bool
     * @throws \Throwable
     */
    public function changeSettings($postData): bool
    {
        if (empty($postData[$this->formName()])) {
            return false;
        }

        $data = $postData[$this->formName()];

        $this->setOptions($data);

        if (!$this->save()) {
            return false;
        }

        /** @var StoreAdminAuth $identity */
        $identity = $this->getUser();

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
