<?php
/* @var $this yii\web\View */
/* @var $details string */

use my\helpers\SpecialCharsHelper;

$details = SpecialCharsHelper::multiPurifier($details);
$detailsDecode = json_decode($details, true);
?>
<pre>
    <?php if(isset($detailsDecode)) {
        print_r($detailsDecode);
    } else {
        print_r($details);
    } ?>
</pre>
