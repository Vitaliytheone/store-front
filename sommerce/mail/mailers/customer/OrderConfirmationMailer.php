<?php
namespace sommerce\mail\mailers;

use Yii;
use common\models\stores\Stores;
use yii\helpers\ArrayHelper;
use common\mail\mailers\BaseMailer;
use yii\web\View;

/**
 * Class OrderConfirmationMailer
 * @package app\mail\mailers
 */
class OrderConfirmationMailer extends BaseMailer {

    /**
     * Init options
     */
    public function init()
    {
        /**
         * @var Stores $store
         */
        $store = ArrayHelper::getValue($this->options, 'store');
        $clientIp = (string)ArrayHelper::getValue($this->options, 'clientIp');
        $clientBrowser = (string)ArrayHelper::getValue($this->options, 'clientBrowser');
        $name = (string)ArrayHelper::getValue($this->options, 'name');
        $subject = (string)ArrayHelper::getValue($this->options, 'subject');
        $email = (string)ArrayHelper::getValue($this->options, 'email');
        $message = (string)ArrayHelper::getValue($this->options, 'message');

        /**
         * @var \common\components\View
         */
        $view = Yii::$app->view;
        $this->message = $view->renderDynamic('//my/view/');;

        $this->subject = $subject;
        $this->to = $store->admin_email;
    }
}