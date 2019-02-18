<?php

namespace common\components\cdn\providers\widgets;

use common\components\cdn\Cdn;
use common\components\cdn\providers\Uploadcare;
use common\models\panels\TicketFiles;
use yii\base\Widget;

class UploadcareSuperadminWidget extends Widget
{

    /** @var Uploadcare */
    public $cdn;

    /** @var TicketFiles */
    public $files;


    /**
     * @throws \yii\base\Exception
     * @throws \yii\base\UnknownClassException
     */
    public function init()
    {
        parent::init();
        if ($this->cdn === null) {
            $this->cdn = Cdn::getCdn();
        }
    }

    public function run()
    {
        return $this->render('_uploadcare', ['files' => $this->files->getDetails()]);
    }

}