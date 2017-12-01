<?php
namespace frontend\models\forms;

use yii\behaviors\AttributeBehavior;
use common\models\store\Packages;

/**
 * Class ProductViewForm
 * @property Packages[] $packages
 * @package frontend\models\forms
 */
class ProductViewForm extends \common\models\store\Products
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    self::EVENT_AFTER_FIND => 'properties',
                ],
                'value' => function ($event) {
                    /* @var $event \yii\base\Event */
                    /* @var $model $this */
                    $model = $event->sender;
                    $properties = $model->getAttribute('properties');
                    return $properties ? json_decode($properties,true) : [];
                },
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPackages()
    {
        return $this->hasMany(Packages::className(), ['product_id' => 'id'])
            ->where([
                'deleted' => Packages::DELETED_NO,
                'visibility' => Packages::VISIBILITY_YES,
            ])
            ->orderBy(['position' => SORT_ASC]);
    }
}