<?php

namespace admin\models\forms\package;

use admin\models\forms\BaseForm;
use common\models\sommerce\ActivityLog;
use common\models\sommerce\Products;
use common\models\sommerce\Packages;
use Yii;
use yii\db\Transaction;

/**
 * Class EditPackageForm
 * @package admin\models\forms\package
 */
class EditPackageForm extends BaseForm
{
    public $name;
    public $price;
    public $quantity;
    public $overflow;
    public $mode;
    public $best;
    public $visibility;
    public $product_id;
    public $provider_service;
    public $deleted;

    /**
     * @var Packages
     */
    protected $_package;

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
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Products::class, 'targetAttribute' => ['product_id' => 'id']],

            ['provider_id', 'required', 'when' => function(Packages $model){
                return $model->getAttribute('mode') == Packages::MODE_AUTO;
            }, 'message' => Yii::t('admin', 'products.message_choose_provider')],
            ['provider_service', 'required', 'when' => function(Packages $model){
                return $model->getAttribute('mode') == Packages::MODE_AUTO;
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

        $this->_package->attributes = $this->attributes;

        /** @var Transaction $transaction */
        $transaction = Yii::$app->storeDb->beginTransaction();

        if (!$this->_package->save()) {
            $this->addErrors($this->_package->getErrors());
            $transaction->rollBack();
            return false;
        }

        ActivityLog::log($this->_user, ActivityLog::E_PACKAGES_PACKAGE_ADDED, $this->_package->id, $this->_package->id);

        $transaction->commit();
        return true;
    }

    public function attributeLabels()
    {
        return [

        ];
    }
}