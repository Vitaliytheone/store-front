<?php

namespace admin\models\forms\product;

use admin\models\forms\BaseForm;
use common\models\sommerce\ActivityLog;
use common\models\sommerce\Pages;
use common\models\sommerce\Products;
use Yii;
use yii\db\Transaction;

/**
 * Class CreateProductForm
 * @package admin\models\forms\product
 */
class CreateProductForm extends BaseForm
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $url;

    /**
     * @var integer
     */
    public $create_page;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name',], 'required'],
            [['create_page'], 'integer'],
            [['name', 'url'], 'string', 'max' => 255],
        ];
    }

    /**
     * @return bool
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->storeDb->beginTransaction();

        $product = new Products();
        $product->name = $this->name;
        $product->url = $this->url;
        if (!$product->save(false)) {
            $this->addError('name', Yii::t('admin', 'product.error.can_not_save'));
            $transaction->rollBack();
            return false;
        }

        if ($this->create_page) {
            $page = new Pages();
            $page->url = $this->url;
            $page->name = $this->name;
            if (!$page->save()) {
                $this->addError('name', Yii::t('admin', 'product.error.can_not_save'));
                $transaction->rollBack();
                return false;
            }
        }

        ActivityLog::log($this->_user, ActivityLog::E_PRODUCTS_PRODUCT_ADDED, $product->id, $product->id);

        $transaction->commit();

        return true;
    }

    public function attributeLabels()
    {
        return [
            'name' => Yii::t('admin', 'products.create_product.name'),
            'create_page' => Yii::t('admin', 'products.create_product.create_page'),
            'url' => Yii::t('admin', 'products.create_product.url'),
        ];
    }
}