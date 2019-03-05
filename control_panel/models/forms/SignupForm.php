<?php

namespace control_panel\models\forms;

use common\models\sommerces\Customers;
use common\models\sommerces\MyActivityLog;
use Yii;
use yii\base\Model;
use himiklab\yii2\recaptcha\ReCaptchaValidator;

/**
 * Class SignupForm
 * @package control_panel\models\forms
 */
class SignupForm extends Model
{
    public $first_name;
    public $last_name;
    public $email;
    public $password;
    public $password_confirm;
    public $terms;

    public $re_captcha;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        $rules = [];

        if ('test' != YII_ENV) {
            $rules[] = [['re_captcha'], ReCaptchaValidator::className(), 'uncheckedMessage' => Yii::t('app', 'error.signup.incorrect_captcha'), 'message' => Yii::t('app', 'error.signup.incorrect_captcha')];
        }

        $rules = array_merge($rules, [
            [['first_name', 'last_name'], 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'], // Clear for XSS
            [['first_name', 'last_name', 'email', 'password', 'password_confirm'], 'filter', 'filter' => function($value) { // Trim input values
                return is_string($value) || is_numeric($value) ? trim((string)$value) : null;
            }],

            [['email'], 'filter', 'filter' => 'mb_strtolower'],

            [['first_name', 'last_name', 'email', 'password', 'password_confirm'], 'required'],
            [['terms'], 'required', 'message' => Yii::t('app', 'error.signup.aggre_with_terms')],
            ['email', 'email'],
        ]);

        return $rules;
    }

    /**
     * Sign up method
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $model = new Customers();
        $model->scenario = Customers::SCENARIO_REGISTER;

        $model->first_name = $this->first_name;
        $model->last_name = $this->last_name;
        $model->email = $this->email;
        $model->date_create = time();
        $model->status = 1;
        $model->setPassword($this->password);
        $model->password_confirm = hash_hmac('sha256', $this->password_confirm, Yii::$app->params['auth_key']);
        $model->generateToken();

        $model->buy_domain = Customers::BUY_DOMAIN_NOT_ACTIVE;

        if (!$model->save()) {
            $this->addErrors($model->getErrors());
            return false;
        }

        MyActivityLog::log(MyActivityLog::E_CUSTOMER_REGISTRATION, $model->id, $model->id, null, $model->id);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'first_name' => Yii::t('app', 'form.signup.first_name'),
            'last_name' => Yii::t('app', 'form.signup.last_name'),
            'password' => Yii::t('app', 'form.signup.password'),
            'email' => Yii::t('app', 'form.signup.email'),
            'password_repeat' => Yii::t('app', 'form.signup.password_repeat'),
        ];
    }
}
