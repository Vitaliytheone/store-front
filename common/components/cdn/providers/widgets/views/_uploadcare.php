<?php
/* @var $this yii\web\View */
/* @var $files array */
/* @var $result string */

$result = '';
?>

    <span class="fa fa-paperclip"></span>
<?php foreach ($files as $file) {
    $result .= '<a href = "' . $file['link'] . '" target="_blank" class="attachments-file">' . $file['name'] . '</a>, ';
}
echo rtrim($result, ' ,');
?>