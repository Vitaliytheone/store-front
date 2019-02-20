<?php

namespace common\components\cdn\providers\widgets;

use yii\base\Widget;
use common\components\cdn\Cdn;
use common\components\cdn\providers\Uploadcare;

abstract class BaseUploadcareWidget extends Widget
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

}