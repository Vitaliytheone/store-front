<?php
namespace control_panel\mail\mailers;
use common\models\sommerces\Notifications;
use yii\helpers\ArrayHelper;

/**
 * Class PanelFrozen
 * @package control_panel\mail\mailers
 */
class PanelFrozen extends BaseMailer {

    public $code = 'panel_frozen';

    /**
     * Init options
     */
    public function init()
    {
        $project = ArrayHelper::getValue($this->options, 'project');

        $this->notificationOptions = [
            'item' => Notifications::ITEM_PANEL,
            'item_id' => $project->id
        ];

        $this->message = ArrayHelper::getValue($this->notificationEmail, 'message');
        $this->subject = ArrayHelper::getValue($this->notificationEmail, 'subject');
        $this->to = $project->customer->email;
    }
}