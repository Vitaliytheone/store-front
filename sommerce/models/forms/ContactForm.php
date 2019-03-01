<?php

namespace sommerce\models\forms;

use common\models\sommerces\Stores;
use sommerce\mail\mailers\ContactFormMailer;
use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class ContactForm
 * @package sommerce\models\forms
 */
class ContactForm extends Model
{
    public $subject;
    public $name;
    public $email;
    public $message;
    public $recaptcha;

    public $_sentSuccess;

    /** @var  Stores */
    private $_store;

    public function init()
    {
        parent::init();

        $this->_store = Yii::$app->store->getInstance();

        // Get sent result from session
        $this->_sentSuccess = Yii::$app->session->getFlash('sent_success');
    }

    public function formName()
    {
        return '';
    }

    public function rules()
    {
        return [
            ['recaptcha', 'recaptchaValidator', 'message' => 'reCAPTCHA validation error! Try some times latter!'],
            ['recaptcha', 'required', 'message' => 'Please solve captcha'],
            [['subject', 'name', 'email', 'message'], 'required'],
            [['subject', 'name', 'message'], 'string'],
            ['email', 'emailValidator'],
        ];
    }

    public function load($data, $formName = null)
    {
        $this->setAttributes([
            'recaptcha' =>  ArrayHelper::getValue($data, 'g-recaptcha-response')
        ]);

        return parent::load($data, $formName);
    }

    /**
     * Return true if message successfully sent
     * @return mixed
     */
    public function getSentSuccess()
    {
        return $this->_sentSuccess;
    }

    /**
     * Validating and sent contact form
     * @return bool
     */
    public function contact()
    {
        if (!$this->validate()) {
            return false;
        }

        $mail = new ContactFormMailer([
            'store' => $this->_store,
            'clientIp' => Yii::$app->getRequest()->userIP,
            'clientBrowser' => Yii::$app->getRequest()->userAgent,
            'name' => $this->name,
            'subject' => $this->subject,
            'email' => $this->email,
            'message' => $this->message,
        ]);
//        $sentResult = $mail->send();
        $sentResult = true; // fixme

        if ($sentResult === true) {
            // Store sent result to session
            Yii::$app->session->setFlash('sent_success', $sentResult);
        }  else {
            // Set validation error
            $this->addError(null, Yii::t('app', 'contact.form.message.error'));
        }

        return $sentResult;
    }

    /**
     * Custom email validator
     * @param $attribute
     * @param $params
     * @param $validator
     * @return bool
     */
    public function emailValidator($attribute, $params, $validator) {
        if ($this->$attribute !== filter_var($this->$attribute, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        return true;
    }

    /**
     * Custom reCAPTCHA validator
     * @param $attribute
     * @param $params
     * @param $validator
     * @return bool
     * @throws Exception
     */
    public function recaptchaValidator($attribute, $params, $validator) {

        $recaptchaResponse = $this->$attribute;

        $cKey = ArrayHelper::getValue(Yii::$app->params, 'reCaptcha.siteKey');
        $cSecret = ArrayHelper::getValue(Yii::$app->params, 'reCaptcha.secret');

        if (!$cKey || !$cSecret) {
            throw new Exception('reCAPTCHA is not yet configured! Check your app config params!');
        }

        $response = static::_request($cSecret, $recaptchaResponse);
        $responseHostName = ArrayHelper::getValue($response, 'hostname', null);
        $responseSuccess = ArrayHelper::getValue($response, 'success', null);

        // Check domains
        if ($this->_store->domain !== $responseHostName) {
            return false;
        }

        // Check captcha
        if (!$responseSuccess) {
            return false;
        }

        return true;
    }


    /**
     * Captcha validation request
     * @param $secret
     * @param $response
     * @return bool|mixed
     */
    private static function _request($secret, $response)
    {
        // Get cURL resource
        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => 'https://www.google.com/recaptcha/api/siteverify',
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => [
                'secret' => $secret,
                'response' => $response,
            ]
        ));

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $firstError = curl_error($ch);
            curl_close($ch);

            // throw new Exception("Curl initialisation error: $firstError");
            return false;
        }

        curl_close($ch);

        $jsonResponse = json_decode($response, true);

        return $jsonResponse;
    }

}