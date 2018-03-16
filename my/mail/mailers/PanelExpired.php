<?php
namespace my\mail\mailers;

use Yii;
use common\models\panels\Notifications;
use yii\helpers\ArrayHelper;

/**
 * Class PanelExpired
 * @package my\mail\mailers
 */
class PanelExpired extends BaseMailer {

    public $code = 'panel_expired';

    /**
     * Init options
     */
    public function init()
    {
        $project = ArrayHelper::getValue($this->options, 'project');
        $daysExpired = ArrayHelper::getValue($this->options, 'days_expired', 1);

        $now = time();
        $day = 24 * 60 * 60;

        $this->code = $this->code . '_' . $daysExpired;

        if (Notifications::find()->andWhere([
            'item_id' => $project->id,
            'item' => Notifications::ITEM_PANEL,
            'type' => $this->code
        ])->andWhere(['between', 'date', $now - $day, $now + $day])->exists()) {
            $this->notificationEmail = null;
            return false;
        }

        $this->notificationOptions = [
            'item' => Notifications::ITEM_PANEL,
            'item_id' => $project->id
        ];

        $this->message = ArrayHelper::getValue($this->notificationEmail, 'message');
        $this->subject = ArrayHelper::getValue($this->notificationEmail, 'subject');
        $days = Yii::t('app', 'in {n, plural, =1{# day} other{# days}}', ['n' => $daysExpired]);

        $this->subject = str_replace([
            '{{days}}'
        ], [
            $days
        ], $this->subject);

        $this->message = str_replace([
            '{{days}}'
        ], [
            $days
        ], $this->message);

        $this->to = $project->customer->email;
    }
}