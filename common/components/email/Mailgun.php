<?php
namespace common\components\email;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

/**
 * Class Mailgun
 * @package common\components\email
 */
class Mailgun
{
    /**
     * @var string
     */
    private static $_mailgunKey;

    /**
     * @var string
     */
    private static $_mailgunDomain;

    /**
     * @var string
     */
    private static $_fromEmail;

    public function init()
    {
        if (!static::$_mailgunKey) {
            static::$_mailgunKey = ArrayHelper::getValue(Yii::$app->params, 'mailgun.key');
            static::$_mailgunDomain = ArrayHelper::getValue(Yii::$app->params, 'mailgun.domain');

            static::$_fromEmail = ArrayHelper::getValue(Yii::$app->params, 'support_email');
        }
    }

    /**
     * Send contact form email
     * @param string $toEmail
     * @param string $subject
     * @param mixed $content
     * @param string $fromEmail
     * @param mixed $result
     * @return bool
     */
    public static function send($toEmail, $subject, $content, $fromEmail = null, &$result = [])
    {
        $fromEmail = $fromEmail ? $fromEmail : static::$_fromEmail;
        $result = static::_send([
            'to' => $toEmail,
            'from' => $fromEmail,
            'subject' => $subject,
            'content' => is_string($content) ? ['text' => $content] : $content // По умолчанию текст
        ]);

        if (!is_array($result) || empty($result['id'])) {
            return false;
        }

        return true;
    }

    /**
     * Send custom email throw Mailgun service
     * @param array $options
     * @return mixed
     * @throws Exception
     */
    private static function _send($options)
    {
        static::init();

        if (!static::$_mailgunKey || !static::$_mailgunDomain) {
            throw new Exception('Mailgun is not yet configured! Check your app config params!');
        }

        if (!static::$_fromEmail) {
            throw new Exception('Support email not yet configured! Check your app config params!');
        }

        $content = ArrayHelper::getValue($options, 'content');

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_USERPWD => 'api:' . static::$_mailgunKey,
            CURLOPT_URL => "https://api.mailgun.net/v3/" . static::$_mailgunDomain . "/messages",
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => [
                'from' => ArrayHelper::getValue($options, 'from', static::$_fromEmail),
                'to' => ArrayHelper::getValue($options, 'to'),
                'subject' => ArrayHelper::getValue($options, 'subject'),
                'text' => ArrayHelper::getValue($content, 'text'),
                'html' => ArrayHelper::getValue($content, 'html'),
            ]
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            curl_close($ch);
            return false;
        }

        curl_close($ch);

        $jsonResponse = json_decode($response, true);

        return $jsonResponse;
    }

}