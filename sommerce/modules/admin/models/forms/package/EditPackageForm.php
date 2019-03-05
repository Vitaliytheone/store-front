<?php

namespace admin\models\forms\package;

use admin\models\forms\BaseForm;
use common\models\sommerce\ActivityLog;
use yii\db\Query;
use common\models\sommerce\Packages;
use common\models\sommerces\Providers;
use common\models\sommerces\StoreProviders;
use common\models\sommerces\Stores;
use Yii;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;

/**
 * Class EditPackageForm
 * @package admin\models\forms\package
 */
class EditPackageForm extends BaseForm
{
    public $id;
    public $name;
    public $price;
    public $quantity;
    public $mode;
    public $best;
    public $visibility;
    public $provider_service;
    public $link_type;
    public $provider_id;

    /**
     * @var Packages
     */
    protected $_package;

    /**
     * @var array
     */
    protected $_store_providers;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'price', 'quantity',], 'required'],
            [['link_type', 'visibility', 'best', 'mode', 'provider_id', 'id'], 'integer'],
            ['price', 'number', 'min' => 0.01],
            [['name', 'provider_service'], 'string', 'max' => 255],
            ['provider_id', 'required', 'when' => function($model){
                return $model->mode == Packages::MODE_AUTO;
            }, 'message' => Yii::t('admin', 'products.message_choose_provider')],
            ['provider_service', 'required', 'when' => function($model){
                return $model->mode == Packages::MODE_AUTO;
            }, 'message' => Yii::t('admin', 'products.message_choose_service')],
        ];
    }

    /**
     * @param Packages $package
     */
    public function setPackage(Packages $package)
    {
        $this->_package = $package;
    }

    /**
     * @return bool
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $this->_package->attributes = $this->getAttributes([
            'name',
            'price',
            'quantity',
            'mode',
            'best',
            'visibility',
            'provider_service',
            'link_type',
            'provider_id',
        ]);

        /** @var Transaction $transaction */
        $transaction = Yii::$app->storeDb->beginTransaction();

        if (!$this->_package->save()) {
            $this->addErrors($this->_package->getErrors());
            $transaction->rollBack();
            return false;
        }

        ActivityLog::log($this->_user, ActivityLog::E_PACKAGES_PACKAGE_UPDATED, $this->_package->id, $this->_package->id);

        $transaction->commit();
        return true;
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('admin', 'products.create_package.name'),
            'price' => Yii::t('admin', 'products.create_package.price'),
            'quantity' => Yii::t('admin', 'products.create_package.quantity'),
            'best' => Yii::t('admin', 'products.create_package.best'),
            'link_type' => Yii::t('admin', 'products.create_package.link'),
            'visibility' => Yii::t('admin', 'products.create_package.availability'),
            'mode' => Yii::t('admin', 'products.create_package.mode'),
            'provider_id' => Yii::t('admin', 'products.create_package.provider'),
            'provider_service' => Yii::t('admin', 'products.create_package.provider_service'),
        ];
    }

    /**
     * @return array
     */
    public function getVisibilityVariants()
    {
        return [
            Packages::VISIBILITY_YES => Yii::t('admin', 'products.create_package.availability_enabled'),
            Packages::VISIBILITY_NO => Yii::t('admin', 'products.create_package.availability_disabled'),
        ];
    }

    /**
     * @return array
     */
    public function getBestVariants()
    {
        return [
            Packages::BEST_YES => Yii::t('admin', 'products.create_package.best_enabled'),
            Packages::BEST_NO => Yii::t('admin', 'products.create_package.best_disabled'),
        ];
    }

    /**
     * @return array
     */
    public function getModeVariants()
    {
        return [
            Packages::MODE_MANUAL => Yii::t('admin', 'products.create_package.mode_manual'),
            Packages::MODE_AUTO => Yii::t('admin', 'products.create_package.mode_auto'),
        ];
    }

    /**
     * @return mixed
     */
    public function getLinkTypes()
    {
        return ArrayHelper::merge([
            '' => Yii::t('admin', 'products.create_package.link_default'),
        ], Yii::$app->params['orderLinks']);
    }

    /**
     * Return store providers
     * @return array
     */
    public function getStoreProviders()
    {
        if (null === $this->_store_providers) {
            $this->_store_providers = (new Query())
                ->select([
                    'pr.id', 'pr.site',
                    'sp.store_id'
                ])
                ->from(['sp' => StoreProviders::tableName()])
                ->where(['sp.store_id' => $this->_store->id])
                ->leftJoin(['pr' => Providers::tableName()], 'pr.id = sp.provider_id')
                ->indexBy('id')
                ->all();

            $this->_store_providers = ArrayHelper::map($this->_store_providers, 'id', 'site');
        }

        return $this->_store_providers;
    }

    /**
     * @return array
     */
    public function getProviderServices()
    {
        return [
            '' => Yii::t('admin', 'products.package_service_default')
        ];
    }
}