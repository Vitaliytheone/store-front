<?php
namespace my\mail\mailers;
use common\models\panels\Notifications;
use yii\helpers\ArrayHelper;

/**
 * Class InvoiceCreated
 * @package my\mail\mailers
 */
class InvoiceCreated extends BaseMailer {

    public $code = 'invoice_created';

    /**
     * Init options
     */
    public function init()
    {
        $project = ArrayHelper::getValue($this->options, 'project');
        $domain = ArrayHelper::getValue($this->options, 'domain');
        $ssl = ArrayHelper::getValue($this->options, 'ssl');
        $store = ArrayHelper::getValue($this->options, 'store');

        if ($project) {
            $this->to = $project->customer->email;
            $this->notificationOptions = [
                'item' => Notifications::ITEM_PANEL,
                'item_id' => $project->id
            ];
        } else if ($domain) {
            $this->to = $domain->customer->email;
            $this->notificationOptions = [
                'item' => Notifications::ITEM_DOMAIN,
                'item_id' => $domain->id
            ];
        } else if ($ssl) {
            $this->to = $ssl->customer->email;
            $this->notificationOptions = [
                'item' => Notifications::ITEM_SSL,
                'item_id' => $ssl->id
            ];
        } else if ($store) {
            $this->to = $store->customer->email;
            $this->notificationOptions = [
                'item' => Notifications::ITEM_STORE,
                'item_id' => $store->id
            ];
        } else {
            $this->notificationEmail = false;
            return false;
        }

        $this->message = ArrayHelper::getValue($this->notificationEmail, 'message');
        $this->subject = ArrayHelper::getValue($this->notificationEmail, 'subject');
    }
}