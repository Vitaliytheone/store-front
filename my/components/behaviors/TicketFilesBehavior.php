<?php

namespace my\components\behaviors;

use common\components\cdn\Cdn;
use common\components\cdn\providers\Uploadcare;
use common\models\panels\TicketFiles;
use common\models\panels\TicketMessages;
use Yii;
use yii\base\Behavior;
use yii\base\Event;
use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;

class TicketFilesBehavior extends Behavior
{

    /** @var Uploadcare */
    public $cdn;

    /** @var TicketMessages */
    public $message;

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_DELETE => 'deleteFiles',
        ];
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->cdn === null) {
            $this->cdn = Cdn::getCdn();
        }
    }

    /**
     * Delete ticket files before delete ticket message record
     * @throws \Exception
     */
    public function deleteFiles($event)
    {
        Yii::debug($event->sender);

        $files = TicketFiles::findOne(['message_id' => $event->sender->id]);
        Yii::debug($files, '$files');
        if (!empty($files)) {
            $this->cdn->deleteGroup($files->prepareIds());
        }
        Yii::debug('Deleted');
    }

}