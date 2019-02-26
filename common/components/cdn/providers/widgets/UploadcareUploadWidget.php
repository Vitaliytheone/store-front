<?php

namespace common\components\cdn\providers\widgets;

class UploadcareUploadWidget extends BaseUploadcareWidget
{

    public function run()
    {
        return $this->render('_uploadcare_upload', ['cdn' => $this->cdn]);
    }

}