<?php
namespace common\mail\mailers;

use libs\premailer\HtmlString;
use Yii;

use common\components\email\Mailgun;
use common\tasks\Client;
use yii\helpers\ArrayHelper;

/**
 * Class BaseMailer
 * @package app\mail\mailers
 */
abstract class BaseMailer {

    /**
     * @var string
     */
    public $code;

    /**
     * @var string
     */
    public $to;

    /**
     * @var string
     */
    public $text;

    /**
     * @var string
     */
    public $html;

    /**
     * @var string
     */
    public $subject;

    /**
     * @var bool
     */
    public $unique = true;

    /**
     * @var bool - Is send email now or use gearman
     */
    public $now = true;

    /**
     * @var array
     */
    public $notificationOptions = [];

    /**
     * @var array
     */
    public $options = [];

    abstract public function init();

    /**
     * CreatedProject constructor.
     * @param $options
     */
    public function __construct($options)
    {
        $this->options = $options;
        $to = ArrayHelper::getValue($options, 'to');

        if (is_string($to)) {
            $this->to = $to;
        }

        if (isset(Yii::$app->params['mailer.status'])) {
            $this->now = (boolean)Yii::$app->params['mailer.status'];
        }

        $this->init();
    }

    /**
     * Get mail data
     * @return array
     */
    public function getData()
    {
        return [
            'to' => $this->to,
            'html' => $this->html,
            'text' => $this->text,
            'subject' => $this->subject,
        ];
    }

    /**
     * Send
     */
    public function send()
    {
        if ($this->now) {
            return static::sendNow($this->getData());
        } else {
            return (bool)Client::addTask('mail', $this->getData());
        }
    }

    /**
     * Send now
     * @param $data
     * @return bool
     */
    public static function sendNow($data)
    {
        $to = ArrayHelper::getValue($data, 'to');
        $subject = ArrayHelper::getValue($data, 'subject');
        $text = ArrayHelper::getValue($data, 'text');
        $html = ArrayHelper::getValue($data, 'html');

        if (!empty(Yii::$app->params['debugEmail'])) {
            $to = Yii::$app->params['debugEmail'];
        }

        if ($html) {
            $preMailer = new HtmlString($html);
            $preMailer->setOption($preMailer::OPTION_HTML_COMMENTS, $preMailer::OPTION_HTML_COMMENTS_REMOVE);
            $preMailer->setOption($preMailer::OPTION_HTML_CLASSES, $preMailer::OPTION_HTML_CLASSES_REMOVE);

            $html = $preMailer->getHtml();
        }

        return (bool)Mailgun::send($to, $subject, [
            'text' => $text,
            'html' => $html
        ]);
    }

    /**
     * Render twig content by string
     * @param string $content
     * @param array $params
     * @return string
     */
    public function renderTwig(string $content, array $params = []):string
    {
        return Yii::$app->view->renderContent($content, $params);
    }
}