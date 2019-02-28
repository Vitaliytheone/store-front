<div class="container">
  <div class="row">
    <div class="col-md-4 col-md-offset-4">
      <div class="login-panel panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title"><?= Yii::t('app', 'checkout.view.header')?></h3>
        </div>
        <div class="panel-body">
          <?php 
            switch ($paymentType) {
              case "2": // Perfect Money
                ?>
          <form action="https://perfectmoney.is/api/step1.asp" method="POST" accept-charset="windows-1251" id="sendform" role="form">
            <input type="hidden" name="PAYEE_ACCOUNT" value="<?php echo $account ?>">
            <input type="hidden" name="PAYEE_NAME" value="PerfectPanel">
            <input type="hidden" name="PAYMENT_ID" value="<?php echo $paymentId ?>">
            <input type="hidden" name="PAYMENT_UNITS" value="USD">
            <input type="hidden" name="STATUS_URL" value="https://<?php echo $_SERVER['HTTP_HOST'] ?>/perfectmoney">
            <input type="hidden" name="PAYMENT_URL" value="https://<?php echo $_SERVER['HTTP_HOST'] ?>/invoices">
            <input type="hidden" name="PAYMENT_URL_METHOD" value="POST">
            <input type="hidden" name="NOPAYMENT_URL" value="https://<?php echo $_SERVER['HTTP_HOST'] ?>/invoices">
            <input type="hidden" name="NOPAYMENT_URL_METHOD" value="POST">
            <input type="hidden" name="SUGGESTED_MEMO" value="<?php echo $paymentDescription ?>">
            <input type="hidden" name="BAGGAGE_FIELDS" value="">
            <input type="hidden" name="INTERFACE_LANGUAGE" value="en_US">
            <input type="hidden" name="PAYMENT_AMOUNT" value="<?php echo $amount ?>" />
                <?php
              break;
              case "3": // WebMoney
                ?>
          <form action="https://merchant.wmtransfer.com/lmi/payment.asp" accept-charset="windows-1251" method="POST" id="sendform" role="form">
              <input type="hidden" name="LMI_RESULT_URL" value="https://<?php echo $_SERVER['HTTP_HOST'] ?>/webmoney">
              <input type="hidden" name="LMI_FAIL_URL" value="https://<?php echo $_SERVER['HTTP_HOST'] ?>/invoices">
              <input type="hidden" name="LMI_SUCCESS_URL" value="https://<?php echo $_SERVER['HTTP_HOST'] ?>/invoices">
              <input type="hidden" name="LMI_PAYMENT_NO" value="<?php echo $paymentId ?>">
            <input type="hidden" name="LMI_PAYMENT_AMOUNT" value="<?php echo $amount ?>">
            <input type="hidden" name="LMI_PAYMENT_DESC" value="<?php echo $paymentDescription ?>">
            <input type="hidden" name="LMI_PAYEE_PURSE" value="<?php echo $purse ?>">
            <input type="hidden" name="id" value="<?php echo $paymentId ?>">
            <input type="hidden" name="email" value="<?php echo $paymentId ?>@perfectpanel.com">
                <?php
              break;
              case "4": // Bitcoin
                ?>
            <form action="<?php echo $url ?>" accept-charset="windows-1251" method="GET" id="sendform" role="form">
                  <?php
                break;
                case "5": // 2Checkout
                  ?>
          <form action="https://www.2checkout.com/checkout/purchase" accept-charset="windows-1251" method="POST" id="sendform" role="form">
            <input type="hidden" name="sid" value="<?= $account_number ?>" >
            <input type="hidden" name="mode" value="2CO" >
            <input type="hidden" name="currency_code" value="USD" >
              <?php /* @var \common\models\sommerces\InvoiceDetails[] $items */?>
              <?php foreach ($items as $key => $item) : ?>
                  <input type="hidden" name="li_<?= $key ?>_product_id" value="<?= $paymentId ?>" >
                  <input type="hidden" name="li_<?= $key ?>_type" value="product" >
                  <input type="hidden" name="li_<?= $key ?>_name" value="<?= $item->getDescription() ?>" >
                  <input type="hidden" name="li_<?= $key ?>_price" value="<?= $item->amount ?>" >
                  <input type="hidden" name="li_<?= $key ?>_quantity" value="1" >
                  <input type="hidden" name="li_<?= $key ?>_tangible" value="N" >
              <?php endforeach; ?>
              <?php
              break;
              case "6": // CoinPayments
              ?>
              <form action="https://www.coinpayments.net/index.php" method="POST" id="sendform" role="form">
                  <input type="hidden" name="cmd" value="_pay">
                  <input type="hidden" name="reset" value="1">
                  <input type="hidden" name="want_shipping" value="0">
                  <input type="hidden" name="currency" value="USD">
                  <input type="hidden" name="merchant" value="<?= $merchantId ?>">
                  <input type="hidden" name="custom" value="<?= $paymentId ?>">
                  <input type="hidden" name="amountf" value="<?= $amount ?>">
                  <input type="hidden" name="item_name" value="<?= $paymentDescription ?>">
                  <input type="hidden" name="item_desc" value="<?= $paymentDescription ?>">
                  <input type="hidden" name="allow_extra" value="1">
                  <input type="hidden" name="success_url" value="https://<?= $_SERVER['HTTP_HOST'] ?>/invoices">
                  <input type="hidden" name="cancel_url" value="https://<?= $_SERVER['HTTP_HOST'] ?>/invoices">
                  <input type="hidden" name="ipn_url" value="https://<?php echo $_SERVER['HTTP_HOST'] ?>/coinpayments">
                  <?php
                break;
              }
            ?>
            <fieldset>
              <button type="submit" class="btn btn-outline btn-primary"><?= Yii::t('app', 'checkout.view.btn_submit')?></button>
            </fieldset>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  document.forms["sendform"].submit();
</script>