<?php
/* @var $this yii\web\View */
/* @var $cdn \common\components\cdn\providers\Uploadcare */


/** Get config for widget */
$configCode = <<< TXT
UPLOADCARE_PUBLIC_KEY = "{$cdn->getPublicKey()}";
UPLOADCARE_CLEARABLE = true;
UPLOADCARE_LOCALE_TRANSLATIONS = {errors: {"fileMaximumSize": "File is too large (limit 5 Mb)"}};
UPLOADCARE_SYSTEM_DIALOG = true;
TXT;


/** Limit max file size for upload */
$maxSize = <<< JS
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
function removeClass() {
  $('button[type=button]').removeClass('uploadcare--widget__button uploadcare--widget__button_type_open');
}
setSize();
removeClass();
JS;


/** Generate code with options for widget */
$code = $cdn->getWidget([
    'id' => 'file-uploader',
    'data-multiple' => true,
    'data-multiple-max' => Yii::$app->params['uploadFileLimit'],
    'data-max-size' => Yii::$app->params['uploadFileSize'],
]);
$code .= "<script>if (typeof setSize === 'function') {setSize();}if (typeof removeClass === 'function') {removeClass();}</script>";


$this->registerJs($configCode, yii\web\View::POS_END);
$this->registerJsFile($cdn->getScript());
$this->registerJs($maxSize, yii\web\View::POS_END);

echo $code;