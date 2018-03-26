<?php

namespace common\models\panels;

use Yii;
use yii\db\ActiveRecord;
use common\models\panels\queries\TariffQuery ;

/**
 * This is the model class for table "{{%tariff}}".
 *
 * @property integer $id
 * @property string $title
 * @property string $price
 * @property string $description
 * @property integer $of_orders
 * @property integer $before_orders
 * @property integer $up
 * @property integer $down
 */
class Tariff extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tariff';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'price', 'description', 'of_orders', 'before_orders', 'up', 'down'], 'required'],
            [['price'], 'number'],
            [['of_orders', 'before_orders', 'up', 'down'], 'integer'],
            [['title'], 'string', 'max' => 300],
            [['description'], 'string', 'max' => 1000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Title'),
            'price' => Yii::t('app', 'Price'),
            'description' => Yii::t('app', 'Description'),
            'of_orders' => Yii::t('app', 'Of Orders'),
            'before_orders' => Yii::t('app', 'Before Orders'),
            'up' => Yii::t('app', 'Up'),
            'down' => Yii::t('app', 'Down'),
        ];
    }

    /**
     * Get full formatted name
     * @return string
     */
    public function getFullName()
    {
        return $this->title . (!empty($this->description) ? ' (' . $this->description . ')' : '');
    }

    /**
     * @inheritdoc
     * @return TariffQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TariffQuery(get_called_class());
    }
}
