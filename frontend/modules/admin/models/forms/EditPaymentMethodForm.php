<?php

namespace frontend\modules\admin\models\forms;

use yii\behaviors\AttributeBehavior;

/**
 * Class EditPaymentMethodForm
 * @package frontend\modules\admin\models\forms
 */
class EditPaymentMethodForm extends \common\models\stores\PaymentMethods
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
                    self::EVENT_BEFORE_VALIDATE => 'details',
                ],
                'value' => function ($event) {
                    /* @var $event \yii\base\Event */
                    /* @var $model $this */
                    $model = $event->sender;
                    return json_encode($model->getAttribute('details'));
                },
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    self::EVENT_AFTER_FIND => 'details',
                ],
                'value' => function ($event) {
                    /* @var $event \yii\base\Event */
                    /* @var $model $this */
                    $model = $event->sender;
                    return json_decode($model->getAttribute('details'),true);
                },
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return 'PaymentsForm';
    }
}