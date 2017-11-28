<?php

namespace frontend\modules\admin\models\forms;

use yii;
use yii\behaviors\AttributeBehavior;
use common\models\store\Products;
/**
 * Class CreatePackageForm
 * @package frontend\modules\admin\models\forms
 */
class CreatePackageForm extends \common\models\store\Packages
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
            [['id', 'quantity', 'link_type', 'product_id', 'visibility', 'best', 'mode', 'provider_id', 'deleted', 'position'], 'integer'],
            ['quantity', 'integer', 'min' => 1],
            ['price', 'number', 'min' => 0.01],
            [['name', 'provider_service'], 'string', 'max' => 255],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Products::className(), 'targetAttribute' => ['product_id' => 'id']],
            [['provider_id', 'provider_service'], 'required', 'when' => function($model){
                return $model->getAttribute('mode') == self::MODE_AUTO;
            }],
        ];
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
     * @return $this|bool
     */
    public function create($postData) {
        if (!$this->load($postData) || !$this->save()) {
            return false;
        }

        return $this;
    }

    /**
     * Delete exiting package
     * @param $postData
     * @return $this|bool
     */
    public function edit($postData) {
        if (!$this->load($postData) || !$this->save()) {
            return false;
        }

        return $this;
    }
}