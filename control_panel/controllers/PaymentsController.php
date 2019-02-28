<?php

namespace control_panel\controllers;

use common\helpers\PaymentHelper;
use common\models\panels\Params;
use control_panel\components\bitcoin\Bitcoin;
use common\components\filters\DisableCsrfToken;
use control_panel\components\payments\BasePayment;
use control_panel\helpers\PaymentsHelper;
use control_panel\mail\mailers\PaypalFailed;
use control_panel\mail\mailers\PaypalPassed;
use control_panel\mail\mailers\PaypalReviewed;
use control_panel\mail\mailers\PaypalVerificationNeeded;
use control_panel\mail\mailers\TwoCheckoutFailed;
use control_panel\mail\mailers\TwoCheckoutPass;
use control_panel\mail\mailers\TwoCheckoutReview;
use Yii;
use common\models\panels\Invoices;
use common\models\panels\Payments;
use common\models\panel\PaymentsLog;
use common\models\panels\PaymentHash;
use control_panel\components\payments\Paypal;
use yii\helpers\ArrayHelper;

class PaymentsController extends CustomController
{
	public $enableDomainValidation = false;

	public function behaviors()
    {
        return [
            'token' => [
                'class' => DisableCsrfToken::class,
            ],
        ];
    }

    public function init()
    {

    }

    /**
     * @return \yii\web\Response
     * @throws \Throwable
     */
    public function actionPaypalexpress()
    {
        $invoice = null;
    	$paymentSignature = md5(rand().rand().time().rand().rand());

		$this->logging(array("POST" => $_POST, "GET" => $_GET, "SERVER" => $_SERVER), 'Paypalexpress', $paymentSignature);
        try {
            if (isset($_GET['token']) && !empty($_GET['token'])) {
                $paypal = new Paypal;

                $checkoutDetails = $paypal->request('GetExpressCheckoutDetails', array('TOKEN' => $_GET['token']));

                BasePayment::validateResponse(
                    $checkoutDetails,
                    ['PAYMENTREQUEST_0_AMT', 'AMT']
                );

                $requestParams = array(
                    'PAYMENTREQUEST_0_PAYMENTACTION' => 'Sale',
                    'PAYERID' => $_GET['PayerID'],
                    'TOKEN' => $_GET['token'],
                    'PAYMENTREQUEST_0_AMT' => $checkoutDetails['PAYMENTREQUEST_0_AMT'],
                );

                $response = $paypal->request('DoExpressCheckoutPayment', $requestParams);

                BasePayment::validateResponse(
                    $response,
                    ['ACK', 'PAYMENTINFO_0_TRANSACTIONID']
                );

                $this->logging(array("POST" => $_POST, "GET" => $_GET, "SERVER" => $_SERVER, 'response' => $response), 'Paypalexpress', $paymentSignature);

                $payments = Payments::findActual($_GET['id'], Params::CODE_PAYPAL);

                if ($payments !== null) {

                    $this->paymentLog([
                        'DoExpressCheckoutPayment' => $response
                    ], $payments->id);

                    $payments->date_update = time();
                    $payments->response = 1;
                    $payments->update(false);

                    $invoice = Invoices::findOne(['id' => $payments->iid]);

                    if ($invoice->status == Invoices::STATUS_UNPAID && $payments->status == Payments::STATUS_PENDING) {
                        if (is_array($response) && $response['ACK'] == 'Success') {
                            $GetTransactionDetails = $paypal->request('GetTransactionDetails', [
                                'TRANSACTIONID' => $response['PAYMENTINFO_0_TRANSACTIONID']
                            ]);

                            BasePayment::validateResponse(
                                $GetTransactionDetails,
                                ['FEEAMT', 'CURRENCYCODE', 'EMAIL', 'PAYERID']
                            );

                            $this->logging(array("POST" => $_POST, "GET" => $_GET, "SERVER" => $_SERVER, 'response' => $response, 'GetTransactionDetails' => $GetTransactionDetails), 'Paypalexpress', $paymentSignature);

                            $this->paymentLog([
                                'GetTransactionDetails' => $GetTransactionDetails
                            ], $payments->id);

                            $payments->refresh();
                            $payments->comment = $GetTransactionDetails['EMAIL'] . '; ' . $response['PAYMENTINFO_0_TRANSACTIONID'];
                            $payments->transaction_id = $response['PAYMENTINFO_0_TRANSACTIONID'];
                            $payments->fee = ArrayHelper::getValue($GetTransactionDetails, 'FEEAMT');
                            $payments->update(false);

                            $getTransactionDetailsStatus = ArrayHelper::getValue($GetTransactionDetails, 'PAYMENTSTATUS', '');
                            $doExpressCheckoutPaymentStatus = ArrayHelper::getValue($response, 'PAYMENTINFO_0_PAYMENTSTATUS', $getTransactionDetailsStatus);
                            $getTransactionDetailsStatus = strtolower($getTransactionDetailsStatus);
                            $doExpressCheckoutPaymentStatus = strtolower($doExpressCheckoutPaymentStatus);

                            $responseCurrency = $GetTransactionDetails['CURRENCYCODE'];
                            $responseAmount = $checkoutDetails['AMT'];

                            if ($getTransactionDetailsStatus == 'completed' && $getTransactionDetailsStatus == $doExpressCheckoutPaymentStatus) {
                                $hash = PaymentHash::findOne(['hash' => $response['PAYMENTINFO_0_TRANSACTIONID']]);
                                if ($hash === null) {
                                    if ($responseAmount == $payments->amount) {

                                        if ($responseCurrency == 'USD') {

                                            $payerId = $GetTransactionDetails['PAYERID'];
                                            $payerEmail = $GetTransactionDetails['EMAIL'];

                                            if (PaymentsHelper::validatePaypalPayment($payments, $payerId, $payerEmail)) {

                                                if ($payments->complete()) {

                                                    $paymentHashModel = new PaymentHash();
                                                    $paymentHashModel->load(array('PaymentHash' => array(
                                                        'hash' => $response['PAYMENTINFO_0_TRANSACTIONID'],
                                                    )));
                                                    $paymentHashModel->save();

                                                    // Send email notification
                                                    $mail = new PaypalPassed([
                                                        'payment' => $payments,
                                                        'customer' => $invoice->customer
                                                    ]);
                                                    $mail->send();
                                                }
                                            } else {

                                                $code = $payments->verification($payerId, $payerEmail);

                                                if ($code && filter_var($payerEmail, FILTER_VALIDATE_EMAIL)) {
                                                    $mail = new PaypalVerificationNeeded([
                                                        'payment' => $payments,
                                                        'email' => $payerEmail,
                                                        'code' => $code
                                                    ]);
                                                    $mail->send();
                                                }
                                            }
                                        } else {
                                            $this->Errorlogging("bad currency: " . $responseCurrency, "Paypalexpress", $paymentSignature);
                                        }
                                    } else {
                                        $this->Errorlogging("bad amount: " . $responseAmount, "Paypalexpress", $paymentSignature);
                                    }
                                } else {
                                    $this->Errorlogging("dublicate response", "Paypalexpress", $paymentSignature);
                                }
                            } else {
                                if ('pending' == $doExpressCheckoutPaymentStatus) {
                                    $payments->status = Payments::STATUS_WAIT;
                                    $payments->update(false);

                                    // Send email notification
                                    $mail = new PaypalReviewed([
                                        'payment' => $payments,
                                        'customer' => $invoice->customer
                                    ]);
                                    $mail->send();

                                } elseif ('failed' == $doExpressCheckoutPaymentStatus) {
                                    $payments->status = Payments::STATUS_FAIL;
                                    $payments->update(false);

                                    $payments->makeNotActive();

                                    // Send email notification
                                    $mail = new PaypalFailed([
                                        'payment' => $payments,
                                        'customer' => $invoice->customer
                                    ]);
                                    $mail->send();
                                } else {
                                    $payments->status = Payments::STATUS_PENDING;
                                    $payments->update(false);
                                }

                                $this->Errorlogging("no final status: " . $getTransactionDetailsStatus, "Paypalexpress", $paymentSignature);
                            }
                        } else {
                            $this->Errorlogging("bad response", "Paypalexpress", $paymentSignature);
                        }
                    } else {
                        $this->Errorlogging("bad invoice status", "Paypalexpress", $paymentSignature);
                    }
                } else {
                    $this->Errorlogging("bad payment id", "Paypalexpress", $paymentSignature);
                }
            } else {
                $this->Errorlogging("no data", "Paypalexpress", $paymentSignature);
            }
        } catch(\Exception $e) {
            $this->Errorlogging($e->getMessage(), "Paypalexpress", $paymentSignature);
            return $this->_redirectWithInvoice($invoice);
        }

        return $this->_redirectWithInvoice($invoice);
    }

    /**
     * @throws \Throwable
     */
    public function actionWebmoney()
    {
		$paymentSignature = md5(rand().rand().time().rand().rand());

		$this->logging(array("POST" => $_POST, "GET" => $_GET, "SERVER" => $_SERVER), 'Webmoney', $paymentSignature);

        $purse = ArrayHelper::getValue(Params::get(Params::CATEGORY_PAYMENT, Params::CODE_WEBMONEY), ['credentials', 'purse']);
		$secret_key = ArrayHelper::getValue(Params::get(Params::CATEGORY_PAYMENT, Params::CODE_WEBMONEY), ['credentials', 'secret_key']);

		if(!empty($_POST['LMI_PREREQUEST'])) {
			if(trim($_POST['LMI_PAYEE_PURSE']) != $purse) {
				echo "ERR: НЕВЕРНЫЙ КОШЕЛЕК ПОЛУЧАТЕЛЯ ".$_POST['LMI_PAYEE_PURSE'];
				exit;
			} else {
				echo "YES";
			}
		} else {
            try {
                BasePayment::validateResponse($_POST, [
                    'id',
                    'LMI_PAYEE_PURSE',
                    'LMI_PAYMENT_AMOUNT',
                    'LMI_PAYMENT_NO',
                    'LMI_MODE',
                    'LMI_HASH',
                    'LMI_SYS_INVS_NO',
                    'LMI_SYS_TRANS_NO',
                    'LMI_SYS_TRANS_DATE',
                    'LMI_PAYER_PURSE',
                    'LMI_PAYER_WM'
                ]);
                $payment = Payments::findActual($_POST['id'], Params::CODE_WEBMONEY);

                if ($payment !== null) {

                    $this->paymentLog($_POST, $payment->id);

                    $payments = Payments::findOne(['id' => $_POST['id']]);
                    $payments->date_update = time();
                    $payments->response = 1;
                    $payments->update(false);

                    $invoice = Invoices::findOne(['id' => $payment->iid]);

                    if ($invoice->status == Invoices::STATUS_UNPAID && $payment->status == Payments::STATUS_PENDING) {
                        $common_string = $_POST['LMI_PAYEE_PURSE'] .
                        $_POST['LMI_PAYMENT_AMOUNT'] .
                        $_POST['LMI_PAYMENT_NO'] .
                        $_POST['LMI_MODE'] .
                        $_POST['LMI_SYS_INVS_NO'] .
                        $_POST['LMI_SYS_TRANS_NO'] .
                        $_POST['LMI_SYS_TRANS_DATE'] .
                        $secret_key .
                        $_POST['LMI_PAYER_PURSE'] .
                        $_POST['LMI_PAYER_WM'];

                        $signature = strtoupper(hash('sha256', $common_string));

                        if ($signature == $_POST['LMI_HASH']) {
                            if ($payments->amount == $_POST['LMI_PAYMENT_AMOUNT']) {
                                $hash = PaymentHash::findOne(['hash' => $_POST['LMI_HASH']]);
                                if ($hash === null) {

                                    // Mark invoice paid
                                    $invoice->paid(Params::CODE_WEBMONEY);

                                    $payments->refresh();
                                    $payments->transaction_id = $_POST['LMI_PAYER_PURSE'];
                                    $payments->comment = $_POST['LMI_PAYER_PURSE'];
                                    $payments->status = Payments::STATUS_COMPLETED;
                                    $payments->update(false);

                                    $paymentHashModel = new PaymentHash();
                                    $paymentHashModel->load(array('PaymentHash' => array(
                                        'hash' => $_POST['LMI_HASH'],
                                    )));
                                    $paymentHashModel->save();

                                    echo 'Ok';
                                    exit;
                                } else {
                                    $this->Errorlogging("dublicate response", "Webmoney", $paymentSignature);
                                }
                            } else {
                                $this->Errorlogging("bad invoice amount", "Webmoney", $paymentSignature);
                            }
                        } else {
                            $this->Errorlogging("bad signature", "Webmoney", $paymentSignature);
                        }
                    } else {
                        $this->Errorlogging("bad invoice status", "Webmoney", $paymentSignature);
                    }
                } else {
                    $this->Errorlogging("no invoice", "Webmoney", $paymentSignature);
                }
            } catch (\Exception $e) {
                $this->Errorlogging($e->getMessage(), "Webmoney", $paymentSignature);
            }
		}
    }

    /**
     * @throws \Throwable
     */
    public function actionPerfectmoney()
    {
    	$paymentSignature = md5(rand().rand().time().rand().rand());

		$this->logging(array("POST" => $_POST, "GET" => $_GET, "SERVER" => $_SERVER), 'Perfectmoney', $paymentSignature);
        try {
            BasePayment::validateResponse($_POST, [
                'PAYMENT_ID',
                'PAYEE_ACCOUNT',
                'PAYMENT_AMOUNT',
                'PAYMENT_UNITS',
                'PAYMENT_BATCH_NUM',
                'PAYER_ACCOUNT',
                'TIMESTAMPGMT',
                'V2_HASH',
                'PAYMENT_AMOUNT'
            ]);

			$payment = Payments::findActual($_POST['PAYMENT_ID'], Params::CODE_PERFECT_MONEY);

	        if ($payment !== null) {

	        	$this->paymentLog($_POST, $payment->id);

	        	$payments = Payments::findOne(['id' => $_POST['PAYMENT_ID']]);
	            $payments->date_update = time();
	            $payments->response = 1;
	            $payments->update(false);

	            $invoice = Invoices::findOne(['id' => $payment->iid]);

	            if ($invoice->status == Invoices::STATUS_UNPAID && $payment->status == Payments::STATUS_PENDING) {

					$account = ArrayHelper::getValue(Params::get(Params::CATEGORY_PAYMENT, Params::CODE_PERFECT_MONEY), ['credentials', 'account']);
					$passphrase = ArrayHelper::getValue(Params::get(Params::CATEGORY_PAYMENT, Params::CODE_PERFECT_MONEY), ['credentials', 'passphrase']);

					if (!empty($passphrase)) {
                        $passphrase = strtoupper(md5($passphrase));
                    }

	            	$string = $_POST['PAYMENT_ID'].':'.$_POST['PAYEE_ACCOUNT'].':'.$_POST['PAYMENT_AMOUNT'].':'.$_POST['PAYMENT_UNITS'].':'.$_POST['PAYMENT_BATCH_NUM'].':'.$_POST['PAYER_ACCOUNT'].':'.$passphrase.':'.$_POST['TIMESTAMPGMT'];

                    $signature = strtoupper(md5($string));

          			if ($signature == $_POST['V2_HASH']){ 
          				if($_POST['PAYMENT_UNITS'] == 'USD'){
              				if ($payments->amount == $_POST['PAYMENT_AMOUNT']) {
              					$hash = PaymentHash::findOne(['hash' => $_POST['V2_HASH']]);
		            			if ($hash === null) {

                                    // Mark invoice paid
                                    $invoice->paid(Params::CODE_PERFECT_MONEY);

					                $payments->refresh();
                                    $payments->transaction_id = $_POST['PAYER_ACCOUNT'];
						            $payments->comment = $_POST['PAYER_ACCOUNT'];
                                    $payments->status = Payments::STATUS_COMPLETED;
						            $payments->update(false);

                					$paymentHashModel = new PaymentHash();
									$paymentHashModel->load(array('PaymentHash' => array(
										'hash' => $_POST['V2_HASH'],
									)));
									$paymentHashModel->save();

									echo 'Ok';
                  					exit;

		            			} else {
		            				$this->Errorlogging("bad hash", "Perfectmoney", $paymentSignature);
		            			}
              				} else {
              					$this->Errorlogging("bad amount", "Perfectmoney", $paymentSignature);
              				}
              			} else {
              				$this->Errorlogging("bad currency", "Perfectmoney", $paymentSignature);
              			}
          			} else {
          				$this->Errorlogging("bad signature", "Perfectmoney", $paymentSignature);
          			}
	            } else {
	            	$this->Errorlogging("dublicate response", "Perfectmoney", $paymentSignature);
	            }
	        } else {
	        	$this->Errorlogging("no invoice", "Perfectmoney", $paymentSignature);
	        }
		} catch(\Exception $e) {
			$this->Errorlogging($e->getMessage(), "Perfectmoney", $paymentSignature);
		}
    }

    /**
     * @throws \Throwable
     */
    public function actionBitcoin()
    {
    	$paymentSignature = md5(rand().rand().time().rand().rand());

    	$this->logging(array("POST" => $_POST, "GET" => $_GET, "SERVER" => $_SERVER), 'Bitcoin', $paymentSignature);

      	try {
            BasePayment::validateResponse($_GET, [
                'callback_data',
                'status',
                'address',
                'tid'
            ]);
      		$payment = Payments::findActual($_GET['callback_data'], Params::CODE_BITCOIN);

	        if ($payment !== null) {

	        	$this->paymentLog($_GET, $payment->id);

	        	$payments = Payments::findOne(['id' => $_GET['callback_data']]);
	            $payments->date_update = time();
	            $payments->response = 1;
	            $payments->update(false);

	            $invoice = Invoices::findOne(['id' => $payment->iid]);

	            if ($invoice->status == Invoices::STATUS_UNPAID && in_array($payment->status, [Payments::STATUS_PENDING, Payments::STATUS_WAIT])) {

                    $id = ArrayHelper::getValue(Params::get(Params::CATEGORY_PAYMENT, Params::CODE_BITCOIN), ['credentials', 'id']);
                    $secret = ArrayHelper::getValue(Params::get(Params::CATEGORY_PAYMENT, Params::CODE_BITCOIN), ['credentials', 'secret']);

                    $signature = Bitcoin::generateSignature($_SERVER['REQUEST_URI'], $secret);

                    $payments->refresh();
                    $payments->comment = $_GET['address'];
                    $payments->transaction_id = $_GET['address'];

                    if ($signature != $_SERVER['HTTP_X_SIGNATURE']) {
                        $this->Errorlogging("bad signature", "Bitcoin", $paymentSignature);
                        $payments->update(false);
                        exit;
                    }

                    $amountPaid = ArrayHelper::getValue($_GET, 'amount_paid_in_btc', 0);
                    $amount = ArrayHelper::getValue($_GET, 'amount_in_btc', 0);

                    if ($amountPaid < $amount) {
                        $this->Errorlogging("bad amount", "Bitcoin", $paymentSignature);
                        $payments->update(false);
                        exit;
                    }

                    if (!in_array($_GET['status'], [2, 4])) {
                        $payments->status = Payments::STATUS_PENDING;
                        $payments->update(false);

                        $this->Errorlogging("no final status", "Bitcoin", $paymentSignature);
                        exit;
                    }

                    if (PaymentHash::findOne(['hash' => $_GET['address']])) {
                        $payments->update(false);
                        $this->Errorlogging("bad hash", "Bitcoin", $paymentSignature);
                        exit;
                    }

                    // Mark invoice paid
                    $invoice->paid(Params::CODE_BITCOIN);

                    $payments->status = Payments::STATUS_COMPLETED;
                    $payments->update(false);


                    $paymentHashModel = new PaymentHash();
                    $paymentHashModel->load(array('PaymentHash' => array(
                        'hash' => $_GET['address'],
                    )));
                    $paymentHashModel->save();

                    echo 'Ok';
                    exit;

				} else {
					$this->Errorlogging("dublicate response", "Bitcoin", $paymentSignature);
				}
			} else {
				$this->Errorlogging("no invoice", "Bitcoin", $paymentSignature);
			}
      	} catch(\Exception $e) {
      		$this->Errorlogging($e->getMessage(), "Bitcoin", $paymentSignature);
      	}
    }

    /**
     * @throws \Throwable
     */
    public function action2checkout()
    {
    	$paymentSignature = md5(rand().rand().time().rand().rand());

		$this->logging(array("POST" => $_POST, "GET" => $_GET, "SERVER" => $_SERVER), '2Checkout', $paymentSignature);

		try {
            BasePayment::validateResponse($_POST, [
                'sale_id',
                'invoice_id',
                'item_id_1',
                'fraud_status',
                'list_currency',
                'md5_hash',
                'invoice_list_amount'
            ]);
			$payment = Payments::findActual($_POST['item_id_1'], Params::CODE_TWO_CHECKOUT);

	        if ($payment !== null && $payment->status != Payments::STATUS_COMPLETED) {

	        	$this->paymentLog($_POST, $payment->id);

                $payment->date_update = time();
                $payment->response = 1;
                $payment->update(false);

                $invoice = Invoices::findOne(['id' => $payment->iid]);

                if ($invoice->status == Invoices::STATUS_UNPAID) {

					$account_number = ArrayHelper::getValue(Params::get(Params::CATEGORY_PAYMENT, Params::CODE_TWO_CHECKOUT), ['credentials', 'account_number']);
					$secret_word = ArrayHelper::getValue(Params::get(Params::CATEGORY_PAYMENT, Params::CODE_TWO_CHECKOUT), ['credentials', 'secret_word']);

					$hashSid = $account_number; #Input your seller ID (2Checkout account number)
					$hashOrder = $_POST['sale_id'];
					$hashInvoice = $_POST['invoice_id'];
					$StringToHash = strtoupper(md5($hashOrder . $hashSid . $hashInvoice . $secret_word));

                    $payment->refresh();
                    $payment->comment = $hashOrder . '; ' . $hashInvoice;
                    $payment->transaction_id = $hashOrder;
                    $payment->update(false);

                    $responseStatus = strtolower($_POST['fraud_status']);
                    $responseCurrency = strtolower($_POST['list_currency']);
                    $responseAmount = $_POST['invoice_list_amount'];

					if ($StringToHash == $_POST['md5_hash']) {
						if ($responseCurrency == "usd") {
							if ($responseStatus == 'pass') {
								if ($payment->amount <= $responseAmount) {
									$hash = PaymentHash::findOne(['hash' => $hashOrder]);
			            			if ($hash === null) {

                                        if ($payment->complete()) {

                                            $paymentHashModel = new PaymentHash();
                                            $paymentHashModel->load(array('PaymentHash' => array(
                                                'hash' => $_POST['sale_id'],
                                            )));
                                            $paymentHashModel->save();

                                            // Send email notification
                                            $mail = new TwoCheckoutPass([
                                                'payment' => $payment,
                                                'customer' => $invoice->customer
                                            ]);
                                            $mail->send();
                                        }

										echo 'Ok';
	                  					exit;

			            			} else {
			            				$this->Errorlogging("bad hash", "2Checkout", $paymentSignature);
			            			}
								} else {
									$this->Errorlogging("bad amount: " . $responseAmount, "2Checkout", $paymentSignature);
								}
							} else {
								if ($responseStatus == 'wait') {
                                    $payment->status = Payments::STATUS_WAIT;
                                    $payment->update(false);

                                    // Send email notification
                                    $mail = new TwoCheckoutReview([
                                        'payment' => $payment,
                                        'customer' => $invoice->customer
                                    ]);
                                    $mail->send();

								} elseif ($responseStatus == 'fail') {
                                    $payment->status = Payments::STATUS_FAIL;
                                    $payment->update(false);

                                    $payment->makeNotActive();

                                    // Send email notification
						            $mail = new TwoCheckoutFailed([
						                'payment' => $payment,
                                        'customer' => $invoice->customer
                                    ]);
                                    $mail->send();
								} else {
                                    $payment->status = Payments::STATUS_PENDING;
                                    $payment->update(false);
                                }

								$this->Errorlogging("no final status: " . $responseStatus, "2Checkout", $paymentSignature);
							}
						} else {
							$this->Errorlogging("bad currency: " . $responseCurrency, "2Checkout", $paymentSignature);
						}
					} else {
						$this->Errorlogging("bad signature", "2Checkout", $paymentSignature);
					}

                } else {
                	$this->Errorlogging("dublicate response", "2Checkout", $paymentSignature);
                }
            } else {
            	$this->Errorlogging("no invoice", "2Checkout", $paymentSignature);
            }
		} catch(\Exception $e) {
			$this->Errorlogging($e->getMessage(), "2Checkout", $paymentSignature);
		}
    }

    /**
     * @throws \Throwable
     * @throws \yii\db\Exception
     * @throws \yii\db\StaleObjectException
     */
    public function actionCoinpayments()
    {
        $paymentSignature = md5(rand().rand().time().rand().rand());

        $this->logging(array("POST" => $_POST, "GET" => $_GET, "SERVER" => $_SERVER), 'CoinPayments', $paymentSignature);

        $ipn = [
            'hmac_signature' => ArrayHelper::getValue($_SERVER, 'HTTP_HMAC',null),
            'transaction_id' => ArrayHelper::getValue($_POST, 'txn_id', null),
            'ipn_mode' => ArrayHelper::getValue($_POST, 'ipn_mode', null),
            'merchant_id' => ArrayHelper::getValue($_POST, 'merchant', null),
            'ipn_status' => ArrayHelper::getValue($_POST, 'status', null),
            'payment_currency' => ArrayHelper::getValue($_POST, 'currency1', null),
            'payment_amount' => ArrayHelper::getValue($_POST, 'amount1', null),
            'payment_email' => ArrayHelper::getValue($_POST, 'email', null),
            'my_payment_id' => ArrayHelper::getValue($_POST, 'custom', null),
        ];

        if (in_array(null, $ipn, true)) {
            $this->Errorlogging("no data", "CoinPayments", $paymentSignature);
            exit;
        }

        $payment = Payments::findActual($ipn['my_payment_id'], Params::CODE_COINPAYMENTS);

        if ($payment === null) {
            $this->Errorlogging("no invoice", "CoinPayments", $paymentSignature);
            exit;
        }

        $this->paymentLog($_POST, $payment->id);

        $payments = Payments::findOne(['id' => $payment->id]);
        $payments->date_update = time();
        $payments->response = 1;
        $payments->update(false);

        $invoice = Invoices::findOne(['id' => $payment->iid]);

        if ((int)$invoice->status !== $invoice::STATUS_UNPAID or ((int)$payment->status !== $payment::STATUS_PENDING and (int)$payment->status !== $payment::STATUS_WAIT)) {
            $this->Errorlogging("duplicate response", "CoinPayments", $paymentSignature);
            exit;
        }

        $pgMerchantId = ArrayHelper::getValue(Params::get(Params::CATEGORY_PAYMENT, Params::CODE_COINPAYMENTS), ['credentials', 'merchant_id']);
        $pgIpnSecret = ArrayHelper::getValue(Params::get(Params::CATEGORY_PAYMENT, Params::CODE_COINPAYMENTS), ['credentials', 'secret']);

        $requestRawBody = http_build_query($_POST);

        $hmac = hash_hmac("sha512", $requestRawBody, trim($pgIpnSecret));

        if (!hash_equals($hmac, $ipn['hmac_signature'])) {
            $this->Errorlogging("bad signature", "CoinPayments", $paymentSignature);
            exit;
        }

        if (strcasecmp($ipn['payment_currency'], 'usd') !== 0) {
            $this->Errorlogging("bad currency", "CoinPayments", $paymentSignature);
            exit;
        }

        if ((float)$payments->amount !== (float)$ipn['payment_amount']) {
            $this->Errorlogging("bad amount", "CoinPayments", $paymentSignature);
            exit;
        }

        $ipnStatus = (int)$ipn['ipn_status'];

        $payments->refresh();
        $payments->comment = $ipn['payment_email'] . '; ' . $ipn['transaction_id'];
        $payments->transaction_id = $ipn['transaction_id'];

        // Mark invoice paid
        if (in_array($ipnStatus, [100, 2])) {

            $invoice->paid(Params::CODE_COINPAYMENTS);

            $payments->status = Payments::STATUS_COMPLETED;
            $payments->update(false);

            $paymentHashModel = new PaymentHash();
            $paymentHashModel->setAttribute('hash', $ipn['hmac_signature']);
            $paymentHashModel->save();

            echo 'Ok';
            exit;
        } elseif ($ipnStatus === 1) {
            $payments->status = Payments::STATUS_WAIT;
            $payments->update(false);
        } else {
            $this->Errorlogging("no final status", "CoinPayments", $paymentSignature);
        }
    }

    /**
     * @return \yii\web\Response
     */
    public function actionIndex()
    {
    	return $this->redirect('/signin',403);
    }

    /**
     * @param $invoice
     * @return \yii\web\Response
     */
    private function _redirectWithInvoice($invoice) {
        $redirectUrl = '/invoices';

        if (!empty($invoice) && $invoice instanceof Invoices) {
            $redirectUrl .= '/' . $invoice->code;
        }

        return $this->redirect($redirectUrl,302);
    }

    /**
     * @param $response
     * @param int $pid
     */
    private function paymentLog($response, $pid = -1) {
		$paymentsLogModel = new PaymentsLog();
		$paymentsLogModel->load(array('PaymentsLog' => array(
			'pid' => $pid,
			'response' => json_encode($response),
			'logs' => json_encode(array_merge($_SERVER, $_POST, $_GET)),
			'date' => time(),
			'ip' => $_SERVER['REMOTE_ADDR'],
		)));
		$paymentsLogModel->save();
    }

    /**
     * @param $array
     * @param $logname
     * @param $signStamp
     */
    private function logging($array, $logname, $signStamp) {
      
      $path = Yii::getAlias('@runtime/payments/');

      $output = $_SERVER['HTTP_HOST']."\n".date("Y-m-d H:i:s", time()+\Yii::$app->params['time']+10803)."\n\n".$logname."-".$signStamp."\n\n";
      $output .= json_encode($array, JSON_PRETTY_PRINT)."\n\n\n";
      $fp = fopen($path.$logname.".log", "a+");
      fputs ($fp, $output);
      fclose ($fp);
    }

    /**
     * @param $comment
     * @param $logname
     * @param $signStamp
     */
    private function Errorlogging($comment, $logname, $signStamp) {
    	$path = Yii::getAlias('@runtime/payments/');
		$output = $_SERVER['HTTP_HOST']."\n".date("Y-m-d H:i:s", time()+\Yii::$app->params['time']+10803)."\n\n".$logname."-".$signStamp."\n\n";
		$output .= $comment."\n\n\n";
		$fp = fopen($path.$logname.".log", "a+");
		fputs ($fp, $output);
		fclose ($fp);
    }
}