<?php
namespace control_panel\models\forms;

use control_panel\helpers\UserHelper;
use control_panel\mail\mailers\EmailChanged;
use common\models\panels\Customers;
use common\models\panels\MyActivityLog;
use Yii;
use yii\base\Model;

/**
 * Class ChangeEmailForm
 * @package control_panel\models\forms
 */
class ChangeEmailForm extends Model
{
    public $old_email;
    public $email;
    public $password;

    /**
     * @var Customers
     */
    private $_customer;

    public function __construct(array $config = [])
    {
        $this->_customer = Yii::$app->user->identity;

        $this->old_email = $this->_customer->email;

        return parent::__construct($config);
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['email'], 'filter', 'filter' => function($value) { // Trim input values
                return is_string($value) ? trim((string)$value) : null;
            }],
            [['email', 'password'], 'required'],
            [['email'], 'email'],
            [['email', 'password'], 'string', 'max' => 255],
            ['password', 'validatePassword'],
            ['email', 'validateEmail'],
        ];
    }

    /**
     * Save customer settings
     * @return bool
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $this->_customer->email = $this->email;
        $this->_customer->access_token = hash_hmac('sha256',  $this->_customer->email . '_' . Yii::$app->user->identity->password . '_' . $_SERVER['REMOTE_ADDR'], Yii::$app->params['access_key']);

        if (!$this->_customer->save()) {
            $this->addErrors($this->_customer->getErrors());
            return false;
        }

        Yii::$app->user->loginByAccessToken($this->_customer->access_token);

        $mail = new EmailChanged([
            'customer' => $this->_customer
        ]);
        $mail->send();

        MyActivityLog::log(MyActivityLog::E_SETTINGS_UPDATE_EMAIL, $this->_customer->id, $this->_customer->id, UserHelper::getHash());

        return true;
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if (hash_hmac('sha256', $this->password, Yii::$app->params['auth_key']) != $this->_customer->password) {
                $this->addError($attribute, Yii::t('app', 'error.customer.incorrect_password'));
            }
        }
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateEmail($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if ((Customers::find()->where('email = :email AND id <> :id', [
                ':email' => $this->email,
                ':id' => $this->_customer->id
            ])->one())) {
                $this->addError($attribute, Yii::t('app', 'error.customer.email_already_exist'));
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'old_email' => Yii::t('app', 'form.settings_change_email.old_email'),
            'email' => Yii::t('app', 'form.settings_change_email.email'),
            'password' => Yii::t('app', 'form.settings_change_email.password'),
        ];
    }
}
