<?php

namespace common\models\store;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;
use common\models\store\queries\PackagesQuery;

/**
 * This is the model class for table "{{%packages}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $price
 * @property integer $quantity
 * @property integer $overflow
 * @property integer $link_type
 * @property integer $product_id
 * @property integer $visibility
 * @property integer $best
 * @property integer $mode
 * @property integer $provider_id
 * @property string $provider_service
 * @property integer $deleted
 * @property integer $position
 * @property string $icon
 *
 * @property Products $product
 * @property Suborders[] $suborders
 */
class Packages extends ActiveRecord
{
    const VISIBILITY_YES = 1;
    const VISIBILITY_NO = 0;

    const MODE_MANUAL = 0;
    const MODE_AUTO = 1;

    const DELETED = 1;
    const DELETED_NO = 0;

    public static function getDb()
    {
        return Yii::$app->storeDb;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%packages}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'quantity', 'link_type', 'product_id', 'visibility', 'best', 'mode', 'provider_id', 'deleted', 'position'], 'integer'],
            [['price'], 'number'],
            [['name', 'provider_service'], 'string', 'max' => 255],
            ['overflow', 'integer', 'min' => -100, 'max' => 100],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Products::class, 'targetAttribute' => ['product_id' => 'id']],
            [['icon'], 'string', 'max' => 180],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'price' => Yii::t('app', 'Price'),
            'quantity' => Yii::t('app', 'Quantity'),
            'overflow' => Yii::t('app', 'Overflow, %'),
            'link_type' => Yii::t('app', 'Link Type'),
            'product_id' => Yii::t('app', 'Product ID'),
            'visibility' => Yii::t('app', 'Visibility'),
            'best' => Yii::t('app', 'Best'),
            'mode' => Yii::t('app', 'Mode'),
            'provider_id' => Yii::t('app', 'Provider ID'),
            'provider_service' => Yii::t('app', 'Provider Service'),
            'deleted' => Yii::t('app', 'Deleted'),
            'position' => Yii::t('app', 'Position'),
            'icon' => Yii::t('app', 'Icon'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Products::class, ['id' => 'product_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSuborders()
    {
        return $this->hasMany(Suborders::class, ['package_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return PackagesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PackagesQuery(get_called_class());
    }

    /**
     * Get Max position of current packages
     * @return array|bool
     */
    public function getMaxPosition()
    {
        $productId = $this->getAttribute('product_id');
        $db = yii::$app->store->getInstance()->db_name;
        $query = (new Query())
            ->select(['MAX(position) position'])
            ->from("$db.packages")
            ->where(['product_id' => $productId])
            ->one();

        return $query['position'];
    }

    /**
     * Virtual deleting package
     * Mark deleted items as 'deleted' and
     * level up items position with same product ID
     * @return bool
     */
    public function deleteVirtual()
    {
        if ($this->deleted == self::DELETED) {
            return false;
        }

        $oldPosition = $this->getAttribute('position');

        $this->setAttributes([
            'deleted' => self::DELETED,
            'position' => NULL
        ]);

        if (!$this->save(false)) {
            return false;
        }

        // Update position after delete
        $db = $this->getDb();
        $table = static::tableName();

        $productId = $this->getAttribute('product_id');
        $query = $db->createCommand("UPDATE $table SET `position` = `position`-1 WHERE `product_id` = :product AND `position` > :oldPos AND `deleted` = :deleted")
            ->bindValue(':product', $productId)
            ->bindValue(':oldPos', $oldPosition)
            ->bindValue(':deleted', self::DELETED_NO)
            ->execute();

        return true;
    }

}
