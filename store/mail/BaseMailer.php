<?php
namespace store\mail;

use common\models\panels\BackgroundTasks;
use libs\premailer\HtmlString;
use Yii;

use common\components\email\Mailgun;
use common\tasks\Client;
use yii\base\ErrorException;
use Exception;
use yii\helpers\ArrayHelper;

/**
 * Class BaseMailer
 * @package store\mail
 */
abstract class BaseMailer {

    /**
     * @var integer
     */
    public $type;

    /**
     * @var string
     */
    public $code;

    /**
     * @var string
     */
    public $from;

    /**
     * @var string
     */
    public $fromName;

    /**
     * @var string
     */
    public $replyTo;

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
    public $now = false;

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
        $this->type = BackgroundTasks::TYPE_PANELS;
        $this->options = $options;
        $to = ArrayHelper::getValue($options, 'to');

        if (is_string($to)) {
            $this->to = $to;
        }

        if (isset(Yii::$app->params['mailer.sendNow'])) {
            $this->now = (boolean)Yii::$app->params['mailer.sendNow'];
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
            'from' => $this->from,
            'from_name' => $this->fromName,
            'reply_to' => $this->replyTo,
            'to' => $this->to,
            'html' => $this->html,
            'text' => $this->text,
            'subject' => $this->subject,
        ];
    }

    /**
     * Send
     * @param mixed $response
     * @return boolean
     */
    public function send(&$response = null)
    {
        if ($this->now) {
            return static::sendNow($this->getData(), $response);
        } else {
            return (bool)Client::addTask($this->type, 'mail', $this->getData());
        }
    }

    /**
     * Send now
     * @param mixed $data
     * @param mixed $response
     * @return bool
     */
    public static function sendNow($data, &$response = null)
    {
        $to = ArrayHelper::getValue($data, 'to');
        $from = ArrayHelper::getValue($data, 'from');
        $fromName = ArrayHelper::getValue($data, 'from_name');
        $replyTo = ArrayHelper::getValue($data, 'reply_to');
        $subject = ArrayHelper::getValue($data, 'subject');
        $text = ArrayHelper::getValue($data, 'text');
        $html = ArrayHelper::getValue($data, 'html');

        if (!empty(Yii::$app->params['debugEmail'])) {
            $to = Yii::$app->params['debugEmail'];
        }

        if ($html) {
            try {
                $preMailer = new HtmlString($html);
                $preMailer->setOption($preMailer::OPTION_HTML_COMMENTS, $preMailer::OPTION_HTML_COMMENTS_REMOVE);
                $preMailer->setOption($preMailer::OPTION_HTML_CLASSES, $preMailer::OPTION_HTML_CLASSES_REMOVE);

                $html = $preMailer->getHtml();
            } catch (ErrorException $e) {
                Yii::error($e->getMessage() . "\r\n" . $e->getTraceAsString() . "\r\n html: \r\n" . $html);
                return false;
            } catch (Exception $e) {
                Yii::error($e->getMessage() . "\r\n" . $e->getTraceAsString() . "\r\n html: \r\n" . $html);
                return false;
            }
        }

        return (bool)Mailgun::send([
            'to' => $to,
            'subject' => $subject,
            'content' => [
                'text' => $text,
                'html' => $html,
            ],
            'from' => $from,
            'from_name' => $fromName,
            'reply_to' => $replyTo
        ], $response);
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