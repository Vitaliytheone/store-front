<?php

namespace common\components\cdn\providers\widgets;

use common\components\cdn\Cdn;
use common\components\cdn\providers\Uploadcare;
use yii\base\Widget;

class UploadcareUploadWidget extends Widget
{

    /** @var Uploadcare */
    public $cdn;


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
        return $this->render('_uploadcare_upload', ['cdn' => $this->cdn]);
    }

}