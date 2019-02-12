<?php

namespace common\components\cdn\providers\widgets;

use common\components\cdn\Cdn;
use common\components\cdn\providers\Uploadcare;
use Yii;
use yii\base\Widget;

class UploadcareWidget extends Widget
{

    /** @var Uploadcare */
    public $cdn;

    /** @var array */
    public $files = [];


    /**
     * Get config for widget
     * @return string
     */
    public function getConfigCode(): string
    {
        $code = <<< TXT
UPLOADCARE_PUBLIC_KEY = "{$this->cdn->getPublicKey()}";
UPLOADCARE_CLEARABLE = true;
UPLOADCARE_LOCALE_TRANSLATIONS = {errors: {"fileMaximumSize": "File is too large (limit 5 Mb)"}};
TXT;
        return $code;
    }

    /**
     * Limit max file size for upload
     * @return string
     */
    public function setMaxSize(): string
    {
        $script = <<< JS
function fileSizeLimit(max) {
  return function(fileInfo) {
    if (fileInfo.size === null) {
      return;
    }
    if (max && fileInfo.size > max) {
      throw new Error("fileMaximumSize");
    }
  };
}
function setSize() {
  $('[role=uploadcare-uploader]').each(function() {
    var input = $(this);
    if (!input.data('maxSize')) {
      return;
    }
    var widget = uploadcare.Widget(input);
    widget.validators.push(fileSizeLimit(input.data('maxSize')));
  });
}
setSize();
JS;

        return $script;
    }


    /**
     * @return string
     */
    public function getWidget(): string
    {
        $code = $this->cdn->getWidget([
            'id' => 'file-uploader',
            'data-multiple' => true,
            'data-multiple-max' => Yii::$app->params['uploadFileLimit'],
            'data-max-size' =>  Uploadcare::FILE_SIZE,
        ]);
        $code .= "<script>if (typeof setSize === 'function') {setSize()}</script>";
        return $code;
    }


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

    /**
     * @var
     * @return string
     */
    public function run()
    {
        $this->view->registerJs($this->getConfigCode(), yii\web\View::POS_END);
        $this->view->registerJsFile($this->cdn->getScript());
        $this->view->registerJs($this->setMaxSize(), yii\web\View::POS_END);

        if (empty($this->files)) {
            $result = $this->getWidget();
        } else {
            $result = $this->render('_uploadcare', ['files' => $this->files]);
        }

        return $result;
    }

}