<?php
namespace sommerce\modules\admin\models\forms;

use common\models\sommerce\NotificationAdminEmails;
use common\models\sommerce\NotificationTemplates;
use Yii;
use yii\base\Model;

/**
 * Class EditAdminEmailForm
 * @package app\modules\superadmin\models\forms
 */
class EditAdminEmailForm extends Model {

    /**
     * @var string
     */
    public $email;

    /**
     * @var NotificationAdminEmails
     */
    private $_email;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['email'], 'filter', 'filter' => function($value) { // Trim input values
                return is_string($value) ? mb_strtolower(trim((string)$value)) : null;
            }],
            [['email'], 'required'],
            [['email'], 'email'],
            [['email'], 'string', 'max' => 255],
            [['email'], 'unique', 'targetClass' => NotificationAdminEmails::class, 'when' => function ($model, $attribute) {
                if (empty($model->_email)) {
                    return true;
                }

                return $model->email !== $model->_email->email;
            }],
        ];
    }

    /**
     * Set email
     * @param NotificationAdminEmails $email
     */
    public function setEmail($email)
    {
        $this->_email = $email;
        $this->email = $email->email;
    }

    /**
     * Save email
     * @return bool
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        if (empty($this->_email)) {
            $this->_email = new NotificationAdminEmails();
        }

        $this->_email->email = $this->email;

        if (!$this->_email->save()) {
            $this->addErrors($this->_email->getErrors());
            return false;
        }

        return true;
    }
}