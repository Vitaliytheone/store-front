<?php
namespace my\modules\superadmin\models\forms;

use Yii;
use common\models\panels\NotificationEmail;
use yii\base\Model;

/**
 * Class EditNotificationEmailForm
 * @package my\modules\superadmin\models\forms
 */
class EditNotificationEmailForm extends Model
{
    public $subject;
    public $message;
    public $code;
    public $enabled;

    /**
     * @var NotificationEmail
     */
    private $_email;

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
     * Set email
     * @param NotificationEmail $email
     */
    public function setEmail(NotificationEmail $email)
    {
        $this->_email = $email;

        $this->attributes = $email->attributes;
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

        $this->_email->subject = $this->subject;
        $this->_email->message = $this->message;
        $this->_email->code = $this->code;
        $this->_email->enabled = (int)$this->enabled;

        if (!$this->_email->save()) {
            $this->addErrors($this->_email->getErrors());
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
