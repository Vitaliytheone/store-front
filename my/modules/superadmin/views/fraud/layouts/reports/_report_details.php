<?php
    /* @var $details \common\models\panels\PaypalFraudReports */

    use my\helpers\SpecialCharsHelper;

    $detailsDecode = SpecialCharsHelper::multiPurifier($details->getDetails());
?>
<pre>
    <?php
    if (isset($detailsDecode)) {
        print_r($detailsDecode);
    } else {
        print_r(SpecialCharsHelper::multiPurifier($details->transaction_details));
    }
    ?>
</pre>