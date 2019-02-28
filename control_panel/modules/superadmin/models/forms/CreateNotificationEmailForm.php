<?php
namespace superadmin\models\forms;

use Yii;
use common\models\panels\NotificationEmail;
use yii\base\Model;

/**
 * Class CreateNotificationEmailForm
 * @package superadmin\models\forms
 */
class CreateNotificationEmailForm extends Model
{
    public $subject;
    public $message;
    public $code;
    public $enabled;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['subject', 'message', 'code'], 'required'],
            ['enabled', 'safe']
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

        $model = new NotificationEmail();
        $model->subject = $this->subject;
        $model->message = $this->message;
        $model->code = $this->code;
        $model->enabled = (int)$this->enabled;

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
            'subject' => Yii::t('app/superadmin', 'email.create.subject'),
            'message' => Yii::t('app/superadmin', 'email.create.message'),
            'code' => Yii::t('app/superadmin', 'email.create.code'),
            'enabled' => Yii::t('app/superadmin', 'email.create.is_enabled'),
        ];
    }
}
