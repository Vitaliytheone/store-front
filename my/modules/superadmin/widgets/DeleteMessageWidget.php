<?php
namespace my\modules\superadmin\widgets;

use yii\base\Widget;

class DeleteMessageWidget extends Widget
{
    public $message;
    /**
     * @return string
     */
    public function run()
    {
        return $this->render('_delete_message', [
            'message' => $this->message,
        ]);
    }

}