<?php
namespace sommerce\modules\admin\models\forms;

use common\models\sommerce\NotificationTemplates;
use Yii;
use yii\base\Model;

/**
 * Class EditNotificationForm
 * @package app\modules\superadmin\models\forms
 */
class EditNotificationForm extends Model {

    /**
     * @var string
     */
    public $code;

    /**
     * @var string
     */
    public $subject;

    /**
     * @var string
     */
    public $body;

    /**
     * @var NotificationTemplates
     */
    private $_notification;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['body', 'subject'], 'safe']
        ];
    }

    /**
     * Set notification
     * @param NotificationTemplates $notification
     */
    public function setNotification($notification)
    {
        $this->_notification = $notification;
        $this->subject = $notification->subject;
        $this->body = $notification->body;
        $this->code = $notification->notification_code;
    }

    /**
     * Save notification
     * @return bool
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $this->_notification->subject = $this->subject;
        $this->_notification->body = $this->body;

        if (!$this->_notification->save()) {
            $this->addErrors($this->_notification->getErrors());
            return false;
        }

        return true;
    }
}