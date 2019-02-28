<?php

namespace common\models\sommerce;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\sommerce\queries\CartsQuery;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%carts}}".
 *
 * @property integer $id
 * @property string $key
 * @property integer $package_id
 * @property string $link
 * @property integer $created_at
 */
class Carts extends ActiveRecord
{
    public static function getDb()
    {
        return Yii::$app->storeDb;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%carts}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['package_id', 'created_at'], 'integer'],
            [['key'], 'string', 'max' => 64],
            [['link'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'key' => Yii::t('app', 'Key'),
            'package_id' => Yii::t('app', 'Package ID'),
            'link' => Yii::t('app', 'Link'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * Generate random key
     */
    public function generateKey()
    {
        do {
            $key = md5(rand() . uniqid() . rand() . time()) . md5(rand() . uniqid() . time() . rand());

        } while (static::find()->andWhere([
            'key' => $key
        ])->exists());

        $this->key = $key;
    }

    /**
     * @inheritdoc
     * @return CartsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CartsQuery(get_called_class());
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
                ],
                'value' => function() {
                    return time();
                },
            ],
        ];
    }

    /**
     * Remove cart item by cart key
     * @param $key
     * @return bool|int
     */
    public static function removeItemByKey($key)
    {
        $item = static::findOne([
            'key' => $key,
        ]);

        if (!$item) {
            return false;
        }

        return $item->delete();
    }

    /**
     * Remove all checkout items
     * @param Checkouts $checkout
     * @return void
     */
    public static function clearCheckoutItems(Checkouts $checkout)
    {
        $items = $checkout->getDetails();

        if (empty($items)) {
            return;
        }

        foreach ($items as $item) {
            static::removeItemByKey($item['cart_key']);
        }
    }
}
