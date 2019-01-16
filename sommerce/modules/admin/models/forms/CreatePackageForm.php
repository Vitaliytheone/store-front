<?php

namespace sommerce\modules\admin\models\forms;

use common\models\store\ActivityLog;
use common\models\stores\StoreAdminAuth;
use yii;
use yii\web\User;
use yii\behaviors\AttributeBehavior;
use common\models\store\Products;
use \common\models\store\Packages;

/**
 * Class CreatePackageForm
 * @package sommerce\modules\admin\models\forms
 * @inheritdoc
 */
class CreatePackageForm extends Packages
{
    /**
     * @var User
     */
    protected $_user;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => 'position',
                ],
                'value' => function ($event) {
                    return $this->getNewPosition();
                },
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => 'provider_id',
                    self::EVENT_BEFORE_UPDATE => 'provider_id',
                ],
                'value' => function ($event) {
                    return $this->mode == self::MODE_MANUAL ? NULL : $this->provider_id;
                },
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => 'provider_service',
                    self::EVENT_BEFORE_UPDATE => 'provider_service',
                ],
                'value' => function ($event) {
                    return $this->mode == self::MODE_MANUAL ? NULL : $this->provider_service;
                },
            ],

        ];
    }

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return 'PackageForm';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'name', 'price', 'quantity',], 'required'],
            [['id', 'link_type', 'product_id', 'visibility', 'best', 'mode', 'provider_id', 'deleted', 'position'], 'integer'],
            ['quantity', 'integer', 'min' => 1],
            ['overflow', 'integer', 'min' => -100, 'max' => 100],
            ['price', 'number', 'min' => 0.01],
            [['name', 'provider_service'], 'string', 'max' => 255],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Products::className(), 'targetAttribute' => ['product_id' => 'id']],

            ['provider_id', 'required', 'when' => function($model){
                return $model->getAttribute('mode') == self::MODE_AUTO;
            }, 'message' => Yii::t('admin', 'products.message_choose_provider')],
            ['provider_service', 'required', 'when' => function($model){
                return $model->getAttribute('mode') == self::MODE_AUTO;
            }, 'message' => Yii::t('admin', 'products.message_choose_service')],
        ];
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->_user = $user;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * Calculate `position` for new package record
     * @return int
     */
    public function getNewPosition()
    {
        $maxPosition = $this->getMaxPosition();
        $position = is_null($maxPosition) ? 0 : $maxPosition + 1;
        return $position;
    }

    /**
     * Crate new package
     * @param $postData
     * @return array|bool
     * @throws \Throwable
     */
    public function create($postData)
    {
        if (!$this->load($postData) || !$this->save()) {
            return false;
        }

        /** @var StoreAdminAuth $identity */
        $identity = $this->getUser()->getIdentity(false);

        ActivityLog::log($identity, ActivityLog::E_PACKAGES_PACKAGE_ADDED, $this->id, $this->id);

        return $this->attributes;
    }

    /**
     * Delete exiting package
     * @param $postData
     * @return $this|bool
     */
    public function edit($postData)
    {
        if (!$this->load($postData) || !$this->validate()) {
            return false;
        }

        $this->_changeLog(clone $this);

        if (!$this->save(false)) {
            return false;
        }

        return $this;
    }

    /**
     * Write interesting Package changes to log
     * @param Packages $model
     */
    private function _changeLog($model)
    {
        /** @var StoreAdminAuth $identity */
        $identity = $this->getUser()->getIdentity(false);

        ActivityLog::log($identity, ActivityLog::E_PACKAGES_PACKAGE_UPDATED, $this->id, $this->id);

        if ((float)$model->getAttribute('price') !== (float)$model->getOldAttribute('price')) {
            ActivityLog::log($identity, ActivityLog::E_PACKAGES_PACKAGE_PRICE_CHANGED, $this->id, $this->id);
        }
        if ((int)$model->getAttribute('quantity') !== (int)$model->getOldAttribute('quantity')) {
            ActivityLog::log($identity, ActivityLog::E_PACKAGES_PACKAGE_QUANTITY_CHANGED, $this->id, $this->id);
        }
        if ((int)$model->getAttribute('link_type') !== (int)$model->getOldAttribute('link_type')) {
            ActivityLog::log($identity, ActivityLog::E_PACKAGES_PACKAGE_QUANTITY_CHANGED, $this->id, $this->id);
        }
        if ((int)$model->getAttribute('visibility') !== (int)$model->getOldAttribute('visibility')) {
            ActivityLog::log($identity, ActivityLog::E_PACKAGES_PACKAGE_QUANTITY_CHANGED, $this->id, $this->id);
        }
        if ((int)$model->getAttribute('mode') !== (int)$model->getOldAttribute('mode')) {
            ActivityLog::log($identity, ActivityLog::E_PACKAGES_PACKAGE_QUANTITY_CHANGED, $this->id, $this->id);
        }
        if ((int)$model->getAttribute('provider_id') !== (int)$model->getOldAttribute('provider_id')) {
            ActivityLog::log($identity, ActivityLog::E_PACKAGES_PACKAGE_QUANTITY_CHANGED, $this->id, $this->id);
        }
        if ((int)$model->getAttribute('provider_service') !== (int)$model->getOldAttribute('provider_service')) {
            ActivityLog::log($identity, ActivityLog::E_PACKAGES_PACKAGE_QUANTITY_CHANGED, $this->id, $this->id);
        }
    }
}