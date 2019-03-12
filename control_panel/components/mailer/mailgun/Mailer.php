<?php

namespace control_panel\components\mailer\mailgun;

use Yii;
use yii\mail\BaseMailer;
use Mailgun\Mailgun;

/**
 * Mailer implements a mailer based on Mailgun.
 *
 * To use Mailer, you should configure it in the application configuration like the following,
 *
 * ~~~
 * 'components' => [
 *     ...
 *     'mailer' => [
 *         'class' => 'control_panel\components\mailer\mailgun\Mailer',
 *         'key' => 'key-example',
 *         'domain' => 'mg.example.com',
 *     ],
 *     ...
 * ],
 * ~~~
 *
 * To send an email, you may use the following code:
 *
 * ~~~
 * Yii::$app->mailer->compose('contact/html', ['contactForm' => $form])
 *     ->setFrom('from@domain.com')
 *     ->setTo($form->email)
 *     ->setSubject($form->subject)
 *     ->send();
 * ~~~
 */
class Mailer extends BaseMailer
{
    /**
     * [$messageClass description]
     * @var string message default class name.
     */
    public $messageClass = 'control_panel\components\mailer\mailgun\Message';
    public $domain;
    public $key;
    public $fromAddress;
    public $fromName;
    public $tags = [];
    public $campaignId;
    public $enableDkim;
    public $enableTestMode;
    public $enableTracking;
    public $clicksTrackingMode; // true, false, "html"
    public $enableOpensTracking;
    private $_mailgunMailer;
    /**
     * @return Mailgun Mailgun mailer instance.
     */
    public function getMailgunMailer()
    {
        if (!is_object($this->_mailgunMailer)) {
            $this->_mailgunMailer = $this->createMailgunMailer();
        }
        return $this->_mailgunMailer;
    }
    /**
     * @param Message $message
     * @inheritdoc
     */
    protected function sendMessage($message)
    {
        $mailer = $this->getMailgunMailer();
        $message->setClickTracking($this->clicksTrackingMode)
            ->addTags($this->tags);
        Yii::info('Sending email', __METHOD__);

        $response = $mailer->post(
            "{$this->domain}/messages",
            $message->getMessage(),
            $message->getFiles()
        );
        Yii::info('Response : '.print_r($response, true), __METHOD__);
        return true;
    }
    /**
     * Creates Mailgun mailer instance.
     * @return Mailgun mailer instance.
     */
    protected function createMailgunMailer()
    {
        return (new Mailgun($this->key));
    }
}