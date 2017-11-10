<?php

namespace frontend\modules\admin\forms;

use yii;
use yii\db\Query;
use yii\behaviors\AttributeBehavior;
use common\models\store\Products;

/**
 * Class PackageForm
 * @package frontend\modules\admin\forms
 */
class PackageForm extends \common\models\store\Packages
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
                    self::EVENT_BEFORE_INSERT => 'position',
                ],
                'value' => function ($event) {
                    return $this->getNewPosition();
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
            [['product_id', 'name', 'price', 'quantity'], 'required'],
            [['id', 'quantity', 'link_type', 'product_id', 'visibility', 'best', 'mode', 'provider_id', 'position'], 'integer'],
            [['price'], 'number'],
            [['name', 'provider_service'], 'string', 'max' => 255],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Products::className(), 'targetAttribute' => ['product_id' => 'id']],
        ];
    }

    /**
     * Calculate `position` for new package record
     * @return int
     */
    public function getNewPosition()
    {
        $maxPosition = self::getMaxPosition();
        $position = is_null($maxPosition) ? 0 : $maxPosition + 1;
        return $position;
    }

    /**
     * Get Max position for new inserts
     * @return array|bool
     */
    public static function getMaxPosition()
    {
        $db = yii::$app->store->getInstance()->db_name;
        $query = (new Query())
            ->select(['MAX(position) position'])
            ->from("$db.packages")
            ->one();

        return $query['position'];
    }
}