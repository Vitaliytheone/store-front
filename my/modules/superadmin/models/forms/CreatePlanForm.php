<?php
namespace my\modules\superadmin\models\forms;

use common\models\panels\Tariff;
use Yii;
use yii\base\Model;

/**
 * CreatePlanForm is the model behind the Create Plan form.
 */
class CreatePlanForm extends Model
{
    public $title;
    public $price;
    public $description;
    public $of_orders;
    public $before_orders;
    public $up;
    public $down;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['title', 'price', 'of_orders', 'before_orders', 'up', 'down','description'], 'required'],
            [['of_orders', 'before_orders', 'up', 'down'], 'integer'],
            [['price'], 'number'],
            [['title'], 'string', 'max' => 300],
            [['description'], 'string', 'max' => 1000],
        ];
    }

    /**
     * Save admin settings
     * @return bool
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $model = new Tariff();
        $model->attributes = $this->attributes;

        if (!$model->save()) {
            $this->addErrors($model->getErrors());
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'title' => Yii::t('app/superadmin', 'plan.create.column_name'),
            'price' => Yii::t('app/superadmin', 'plan.create.column_rate'),
            'of_orders' => Yii::t('app/superadmin', 'plan.create.column_from'),
            'before_orders' => Yii::t('app/superadmin', 'plan.create.column_to'),
            'up' => Yii::t('app/superadmin', 'plan.create.column_up'),
            'down' => Yii::t('app/superadmin', 'plan.create.column_down'),
            'description' => Yii::t('app/superadmin', 'plan.create.column_description')
        ];
    }
}
