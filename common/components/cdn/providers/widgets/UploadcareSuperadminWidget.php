<?php

namespace common\components\cdn\providers\widgets;

use common\models\panels\TicketFiles;

class UploadcareSuperadminWidget extends BaseUploadcareWidget
{

    /** @var TicketFiles */
    public $files;


    public function run()
    {
        return $this->render('_uploadcare', ['files' => $this->files->getDetails()]);
    }

}