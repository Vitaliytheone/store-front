<?php
/* @var $this yii\web\View */
/* @var $details string */

$detailsDecode = json_decode($details, true);
?>
<pre>
    <?php if(isset($detailsDecode)) {
        print_r($detailsDecode);
    } else {
        print_r($details);
    } ?>
</pre>
