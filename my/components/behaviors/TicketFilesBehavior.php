<?php

namespace my\components\behaviors;

use common\components\cdn\Cdn;
use common\components\cdn\providers\Uploadcare;
use common\models\panels\TicketFiles;
use common\models\panels\TicketMessages;
use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class TicketFilesBehavior extends Behavior
{

    /** @var Uploadcare */
    public $cdn;

    /** @var string */
    public $link;

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_DELETE => 'deleteFiles',
            ActiveRecord::EVENT_AFTER_INSERT => 'createFile',
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
        $files = TicketFiles::findOne(['message_id' => $event->sender->id]);
        if (!empty($files)) {
            $this->cdn->deleteGroup($files->prepareIds());
        }
    }

    /**
     * Create ticket files
     * @param \yii\base\Event $event
     * @return bool
     * @throws \Exception
     */
    public function createFile($event)
    {
        $link = $this->owner->post;

        /** @var TicketMessages $ticket */
        $ticket = $event->sender;

        if (!empty($link)) {
            $transaction = Yii::$app->db->beginTransaction();

            $ticketFilesModel = new TicketFiles();
            $ticketFilesModel->customer_id = $ticket->customer_id ?? 0;
            $ticketFilesModel->ticket_id = $ticket->ticket_id;
            $ticketFilesModel->admin_id = $ticket->admin_id ?? 0;
            $ticketFilesModel->message_id = $ticket->id;
            $ticketFilesModel->link = $link;
            $ticketFilesModel->cdn_id = $this->cdn->getId($link);
            $ticketFilesModel->created_at = time();
            $ticketFilesModel->setDetails($this->cdn->getFiles($link, true));

            if (!$ticketFilesModel->save()) {
                $transaction->rollBack();
                return false;
            }
            $this->cdn->storeGroup($link);

            $transaction->commit();
            return true;
        }
    }

}