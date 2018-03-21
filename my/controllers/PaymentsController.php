<?php

namespace my\controllers;

use my\components\bitcoin\Bitcoin;
use my\mail\mailers\PaypalFailed;
use my\mail\mailers\PaypalPassed;
use my\mail\mailers\PaypalReviewed;
use my\mail\mailers\TwoCheckoutFailed;
use my\mail\mailers\TwoCheckoutPass;
use my\mail\mailers\TwoCheckoutReview;
use Yii;
use common\models\panels\Invoices;
use common\models\panels\PaymentGateway;
use common\models\panels\Payments;
use common\models\panel\PaymentsLog;
use common\models\panels\PaymentHash;
use my\components\Paypal;
use yii\helpers\ArrayHelper;

class PaymentsController extends CustomController
{
	public function init()
    {

    }

    public function beforeAction($action) {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionPaypalexpress()
    {
    	$paymentSignature = md5(rand().rand().time().rand().rand());

		$this->logging(array("POST" => $_POST, "GET" => $_GET, "SERVER" => $_SERVER), 'Paypalexpress', $paymentSignature);

		if( isset($_GET['token']) && !empty($_GET['token']) ) {
			$paypal = new Paypal;

	        $checkoutDetails = $paypal->request('GetExpressCheckoutDetails', array('TOKEN' => $_GET['token']));
	        
	        $requestParams = array(
	           'PAYMENTREQUEST_0_PAYMENTACTION' => 'Sale',
	           'PAYERID' => $_GET['PayerID'],
	           'TOKEN' => $_GET['token'],
	           'PAYMENTREQUEST_0_AMT' => $checkoutDetails['PAYMENTREQUEST_0_AMT'],
	        );

	        $response = $paypal->request('DoExpressCheckoutPayment', $requestParams);

	        $this->logging(array("POST" => $_POST, "GET" => $_GET, "SERVER" => $_SERVER, 'response' => $response), 'Paypalexpress', $paymentSignature);

	        $payment = Payments::findOne(['id' => $_GET['id']]);

	        if ($payment !== null) {

	        	$this->paymentLog([
	        	    'DoExpressCheckoutPayment' => $response
                ], $payment->id);

	        	$payments = Payments::findOne(['id' => $_GET['id']]);
                $payments->date_update = time();
                $payments->response = 1;
                $payments->update();

                $invoice = Invoices::findOne(['id' => $payment->iid]);

                if ($invoice->status == 0 and $payment->status == 0) {
                	if( is_array($response) && $response['ACK'] == 'Success' ) {

			            $GetTransactionDetails = $paypal->request('GetTransactionDetails', array(
			              'TRANSACTIONID' => $response['PAYMENTINFO_0_TRANSACTIONID']
			            ));

			            $this->logging(array("POST" => $_POST, "GET" => $_GET, "SERVER" => $_SERVER, 'response' => $response, 'GetTransactionDetails' => $GetTransactionDetails), 'Paypalexpress', $paymentSignature);

			            $this->paymentLog([
			                'GetTransactionDetails' => $GetTransactionDetails
                        ], $payment->id);

                        $payments = Payments::findOne(['id' => $payment->id]);
                        $payments->comment = $GetTransactionDetails['EMAIL'].'; '.$response['PAYMENTINFO_0_TRANSACTIONID'];
                        $payments->transaction_id = $response['PAYMENTINFO_0_TRANSACTIONID'];
                        $getTransactionDetailsStatus = ArrayHelper::getValue($GetTransactionDetails, 'PAYMENTSTATUS', '');
                        $doExpressCheckoutPaymentStatus = ArrayHelper::getValue($response, 'PAYMENTINFO_0_PAYMENTSTATUS', $getTransactionDetailsStatus);
                        $getTransactionDetailsStatus = strtolower($getTransactionDetailsStatus);
                        $doExpressCheckoutPaymentStatus = strtolower($doExpressCheckoutPaymentStatus);

                        if (empty($GetTransactionDetails['EMAIL'])) {
                            $GetTransactionDetails['EMAIL'] = '';
                        }

			            if ($getTransactionDetailsStatus == 'completed' && $getTransactionDetailsStatus == $doExpressCheckoutPaymentStatus) {
			            	$hash = PaymentHash::findOne(['hash' => $response['PAYMENTINFO_0_TRANSACTIONID']]);
			            	if ($hash === null) {
			            		if ($checkoutDetails['AMT'] == $payments->amount) {
	                				if ($GetTransactionDetails['CURRENCYCODE'] == 'USD') {

                                        $payments->complete();

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

	                				} else {
	                					$this->Errorlogging("bad currency", "Paypalexpress", $paymentSignature);
	                				}
	                			} else {
	                				$this->Errorlogging("bad amount", "Paypalexpress", $paymentSignature);
	                			}
			            	} else {
			            		$this->Errorlogging("dublicate response", "Paypalexpress", $paymentSignature);
			            	}
			            } else {
                            if ('pending' == $doExpressCheckoutPaymentStatus) {
                                $payments->status = Payments::STATUS_WAIT;

                                // Send email notification
                                $mail = new PaypalReviewed([
                                    'payment' => $payments,
                                    'customer' => $invoice->customer
                                ]);
                                $mail->send();

                            } elseif ('failed' == $doExpressCheckoutPaymentStatus) {
                                $payments->status = Payments::STATUS_FAIL;
                                $payments->makeNotActive();

                                // Send email notification
                                $mail = new PaypalFailed([
                                    'payment' => $payments,
                                    'customer' => $invoice->customer
                                ]);
                                $mail->send();
                            } else {
                                $payments->status = Payments::STATUS_PENDING;
                            }

                            $payments->update();

			            	$this->Errorlogging("no final status", "Paypalexpress", $paymentSignature);
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
		return $this->redirect('/invoices',302);
    }

    public function actionWebmoney()
    {
		$paymentSignature = md5(rand().rand().time().rand().rand());

		$this->logging(array("POST" => $_POST, "GET" => $_GET, "SERVER" => $_SERVER), 'Webmoney', $paymentSignature);

		$paypalInfo = PaymentGateway::findOne(['pgid' => 3, 'visibility' => 1, 'pid' => -1]);

		$purse = '';
		$secret_key = '';

		$paypalInfo = json_decode($paypalInfo->options);

		if (!empty($paypalInfo->purse)) {
			$purse = $paypalInfo->purse;
		}

		if (!empty($paypalInfo->secret_key)) {
			$secret_key = $paypalInfo->secret_key;
		}

		if(!empty($_POST['LMI_PREREQUEST'])) {
			if(trim($_POST['LMI_PAYEE_PURSE']) != $purse) {
				echo "ERR: НЕВЕРНЫЙ КОШЕЛЕК ПОЛУЧАТЕЛЯ ".$_POST['LMI_PAYEE_PURSE'];
				exit;
			} else {
				echo "YES";
			}
		} else {
			if (!empty($_POST['id'])) {
				$payment = Payments::findOne(['id' => $_POST['id']]);

		        if ($payment !== null) {

		        	$this->paymentLog($_POST, $payment->id);

		        	$payments = Payments::findOne(['id' => $_POST['id']]);
	                $payments->date_update = time();
	                $payments->response = 1;
	                $payments->update();

	                $invoice = Invoices::findOne(['id' => $payment->iid]);

	                if ($invoice->status == 0 and $payment->status == 0) {
	                	$common_string = $_POST['LMI_PAYEE_PURSE'].
	                	$_POST['LMI_PAYMENT_AMOUNT'].
	                	$_POST['LMI_PAYMENT_NO'].
            			$_POST['LMI_MODE'].
            			$_POST['LMI_SYS_INVS_NO'].
            			$_POST['LMI_SYS_TRANS_NO'].
            			$_POST['LMI_SYS_TRANS_DATE'].
            			$secret_key.
            			$_POST['LMI_PAYER_PURSE'].
            			$_POST['LMI_PAYER_WM'];

            			$signature = strtoupper(hash('sha256', $common_string));

            			if($signature == $_POST['LMI_HASH']) {
            				if ($payments->amount == $_POST['LMI_PAYMENT_AMOUNT']) {
            					$hash = PaymentHash::findOne(['hash' => $_POST['LMI_HASH']]);
		            			if ($hash === null) {

                                    // Mark invoice paid
                                    $invoice->paid(PaymentGateway::METHOD_WEBMONEY);

					                $payments = Payments::findOne(['id' => $payment->id]);
                                    $payments->transaction_id = $_POST['LMI_PAYER_PURSE'];
						            $payments->comment = $_POST['LMI_PAYER_PURSE'];
                                    $payments->status = Payments::STATUS_COMPLETED;
						            $payments->update();

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
			} else {
				$this->Errorlogging("no data", "Webmoney", $paymentSignature);	
			}
		}
    }

    public function actionPerfectmoney()
    {
    	$paymentSignature = md5(rand().rand().time().rand().rand());

		$this->logging(array("POST" => $_POST, "GET" => $_GET, "SERVER" => $_SERVER), 'Perfectmoney', $paymentSignature);

		if (
			!empty($_POST['PAYMENT_ID']) and 
			!empty($_POST['PAYEE_ACCOUNT']) and 
			!empty($_POST['PAYMENT_AMOUNT']) and 
			!empty($_POST['PAYMENT_UNITS']) and 
			!empty($_POST['PAYMENT_BATCH_NUM']) and 
			!empty($_POST['PAYER_ACCOUNT']) and 
			!empty($_POST['TIMESTAMPGMT']) and 
			!empty($_POST['V2_HASH']) and 
			!empty($_POST['PAYER_ACCOUNT']) and 
			!empty($_POST['PAYMENT_AMOUNT'])
		) {
			$payment = Payments::findOne(['id' => $_POST['PAYMENT_ID']]);

	        if ($payment !== null) {

	        	$this->paymentLog($_POST, $payment->id);

	        	$payments = Payments::findOne(['id' => $_POST['PAYMENT_ID']]);
	            $payments->date_update = time();
	            $payments->response = 1;
	            $payments->update();

	            $invoice = Invoices::findOne(['id' => $payment->iid]);

	            if ($invoice->status == 0 and $payment->status == 0) {

	            	$paypalInfo = PaymentGateway::findOne(['pgid' => 2, 'visibility' => 1, 'pid' => -1]);

					$account = '';
					$passphrase = '';

					$paypalInfo = json_decode($paypalInfo->options);

					if (!empty($paypalInfo->account)) {
						$account = $paypalInfo->account;
					}

					if (!empty($paypalInfo->passphrase)) {
						$passphrase = strtoupper(md5($paypalInfo->passphrase));
					}


	            	$string =   $_POST['PAYMENT_ID'].':'.$_POST['PAYEE_ACCOUNT'].':'.$_POST['PAYMENT_AMOUNT'].':'.$_POST['PAYMENT_UNITS'].':'.$_POST['PAYMENT_BATCH_NUM'].':'.$_POST['PAYER_ACCOUNT'].':'.$passphrase.':'.$_POST['TIMESTAMPGMT'];

                    $signature=strtoupper(md5($string));

          			if ($signature == $_POST['V2_HASH']){ 
          				if($_POST['PAYMENT_UNITS'] == 'USD'){
              				if ($payments->amount == $_POST['PAYMENT_AMOUNT']) {
              					$hash = PaymentHash::findOne(['hash' => $_POST['V2_HASH']]);
		            			if ($hash === null) {

                                    // Mark invoice paid
                                    $invoice->paid(PaymentGateway::METHOD_PERFECT_MONEY);

					                $payments = Payments::findOne(['id' => $payment->id]);
                                    $payments->transaction_id = $_POST['PAYER_ACCOUNT'];
						            $payments->comment = $_POST['PAYER_ACCOUNT'];
                                    $payments->status = Payments::STATUS_COMPLETED;
						            $payments->update();

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
		} else {
			$this->Errorlogging("no data", "Perfectmoney", $paymentSignature);
		}
    }

    public function actionBitcoin()
    {
    	$paymentSignature = md5(rand().rand().time().rand().rand());

    	$this->logging(array("POST" => $_POST, "GET" => $_GET, "SERVER" => $_SERVER), 'Bitcoin', $paymentSignature);	

      	if (!empty($_GET['status']) and !empty($_GET['callback_data'])) {
      		$payment = Payments::findOne(['id' => $_GET['callback_data']]);

	        if ($payment !== null) {

	        	$this->paymentLog($_GET, $payment->id);

	        	$payments = Payments::findOne(['id' => $_GET['callback_data']]);
	            $payments->date_update = time();
	            $payments->response = 1;
	            $payments->update();

	            $invoice = Invoices::findOne(['id' => $payment->iid]);

	            if ($invoice->status == 0 && in_array($payment->status, [0, 2])) {

	            	$paypalInfo = PaymentGateway::findOne(['pgid' => 4, 'visibility' => 1, 'pid' => -1]);

					$paypalInfo = json_decode($paypalInfo->options);

                    $id = ArrayHelper::getValue($paypalInfo, 'id');
                    $secret = ArrayHelper::getValue($paypalInfo, 'secret');

                    $signature = Bitcoin::generateSignature($_SERVER['REQUEST_URI'], $secret);

                    $payments = Payments::findOne(['id' => $payment->id]);
                    $payments->comment = $_GET['address'];
                    $payments->transaction_id = $_GET['tid'];

					if ($signature == $_SERVER['HTTP_X_SIGNATURE']) {
                        $amountPaid = ArrayHelper::getValue($_GET, 'amount_paid_in_btc', 0);
                        $amount = ArrayHelper::getValue($_GET, 'amount_in_btc', 0);

                        if ($amountPaid >= $amount) {
                            if (in_array($_GET['status'], [2, 4])) {
                                $hash = PaymentHash::findOne(['hash' => $_GET['tid']]);
                                if ($hash === null) {

                                    // Mark invoice paid
                                    $invoice->paid(PaymentGateway::METHOD_BITCOIN);

                                    $payments->status = Payments::STATUS_COMPLETED;
                                    $payments->update();


                                    $paymentHashModel = new PaymentHash();
                                    $paymentHashModel->load(array('PaymentHash' => array(
                                        'hash' => $_GET['tid'],
                                    )));
                                    $paymentHashModel->save();

                                    echo 'Ok';
                                    exit;

                                } else {
                                    $this->Errorlogging("bad hash", "Perfectmoney", $paymentSignature);
                                }
                            } else {

                                $payments->status = Payments::STATUS_PENDING;
                                $payments->update();

                                $this->Errorlogging("no final status", "Bitcoin", $paymentSignature);
                            }
                        } else {
                            $this->Errorlogging("bad amount", "Bitcoin", $paymentSignature);
                        }
					} else {
						$this->Errorlogging("bad signature", "Bitcoin", $paymentSignature);
					}
				} else {
					$this->Errorlogging("dublicate response", "Bitcoin", $paymentSignature);
				}
			} else {
				$this->Errorlogging("no invoice", "Bitcoin", $paymentSignature);
			}
      	} else {
      		$this->Errorlogging("no data", "Bitcoin", $paymentSignature);
      	}
    }

    public function action2checkout()
    {
    	$paymentSignature = md5(rand().rand().time().rand().rand());

		$this->logging(array("POST" => $_POST, "GET" => $_GET, "SERVER" => $_SERVER), '2Checkout', $paymentSignature);

		if (!empty($_POST['sale_id']) and !empty($_POST['invoice_id'])) {
			$payment = Payments::findOne(['id' => $_POST['item_id_1']]);

	        if ($payment !== null) {

	        	$this->paymentLog($_POST, $payment->id);

	        	$payments = Payments::findOne(['id' => $_POST['item_id_1']]);
                $payments->date_update = time();
                $payments->response = 1;
                $payments->update();

                $invoice = Invoices::findOne(['id' => $payment->iid]);

                if ($invoice->status == 0 and $payment->status != 1) {
                	
                	$paypalInfo = PaymentGateway::findOne(['pgid' => 5, 'visibility' => 1, 'pid' => -1]);

					$account_number = '';
					$secret_word = '';

					$paypalInfo = json_decode($paypalInfo->options);

					if (!empty($paypalInfo->account_number)) {
						$account_number = $paypalInfo->account_number;
					}

					if (!empty($paypalInfo->secret_word)) {
						$secret_word = $paypalInfo->secret_word;
					}

					$hashSid = $account_number; #Input your seller ID (2Checkout account number)
					$hashOrder = $_POST['sale_id'];
					$hashInvoice = $_POST['invoice_id'];
					$StringToHash = strtoupper(md5($hashOrder . $hashSid . $hashInvoice . $secret_word));

                    $payments = Payments::findOne(['id' => $payment->id]);
                    $payments->comment = $hashOrder . '; ' . $hashInvoice;
                    $payments->transaction_id = $hashOrder;

					if ($StringToHash == $_POST['md5_hash']) {
						if (strtolower($_POST['list_currency']) == "usd") {
							if (strtolower($_POST['fraud_status']) == 'pass') {
								if ($payments->amount == $_POST['invoice_list_amount']) {
									$hash = PaymentHash::findOne(['hash' => $hashOrder]);
			            			if ($hash === null) {

                                        $payments->complete();

	                					$paymentHashModel = new PaymentHash();
										$paymentHashModel->load(array('PaymentHash' => array(
											'hash' => $_POST['sale_id'],
										)));
										$paymentHashModel->save();

										// Send email notification
                                        $mail = new TwoCheckoutPass([
                                            'payment' => $payments,
                                            'customer' => $invoice->customer
                                        ]);
                                        $mail->send();

										echo 'Ok';
	                  					exit;

			            			} else {
			            				$this->Errorlogging("bad hash", "2Checkout", $paymentSignature);
			            			}
								} else {
									$this->Errorlogging("bad amount", "2Checkout", $paymentSignature);
								}
							} else {
								if (strtolower($_POST['fraud_status']) == 'wait') {
						            $payments->status = Payments::STATUS_WAIT;

                                    // Send email notification
                                    $mail = new TwoCheckoutReview([
                                        'payment' => $payments,
                                        'customer' => $invoice->customer
                                    ]);
                                    $mail->send();

								} elseif (strtolower($_POST['fraud_status']) == 'fail') {
                                    $payments->status = Payments::STATUS_FAIL;
						            $payments->makeNotActive();

                                    // Send email notification
						            $mail = new TwoCheckoutFailed([
						                'payment' => $payments,
                                        'customer' => $invoice->customer
                                    ]);
                                    $mail->send();
								} else {
                                    $payments->status = Payments::STATUS_PENDING;
                                }

                                $payments->update();

								$this->Errorlogging("no final status", "2Checkout", $paymentSignature);
							}
						} else {
							$this->Errorlogging("bad currency", "2Checkout", $paymentSignature);
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
		} else {
			$this->Errorlogging("no data", "2Checkout", $paymentSignature);
		}
    }

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

        $payment = Payments::findOne(['id' => $ipn['my_payment_id']]);

        if ($payment === null) {
            $this->Errorlogging("no invoice", "CoinPayments", $paymentSignature);
            exit;
        }

        $this->paymentLog($_POST, $payment->id);

        $payments = Payments::findOne(['id' => $payment->id]);
        $payments->date_update = time();
        $payments->response = 1;
        $payments->update();

        $invoice = Invoices::findOne(['id' => $payment->iid]);

        if ((int)$invoice->status !== $invoice::STATUS_UNPAID or ((int)$payment->status !== $payment::STATUS_PENDING and (int)$payment->status !== $payment::STATUS_WAIT)) {
            $this->Errorlogging("duplicate response", "CoinPayments", $paymentSignature);
            exit;
        }

        $pg = PaymentGateway::findOne(['pgid' => 6, 'visibility' => 1, 'pid' => -1]);

        $pgData = json_decode($pg->options);
        $pgMerchantId = ArrayHelper::getValue($pgData,'merchant_id', null);
        $pgIpnSecret = ArrayHelper::getValue($pgData,'secret', null);

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

        $payments = Payments::findOne(['id' => $payment->id]);
        $payments->comment = $ipn['payment_email'] . '; ' . $ipn['transaction_id'];
        $payments->transaction_id = $ipn['transaction_id'];

        // Mark invoice paid
        if (in_array($ipnStatus, [100, 2])) {

            $invoice->paid(PaymentGateway::METHOD_COINPAYMENTS);

            $payments->status = Payments::STATUS_COMPLETED;
            $payments->update();

            $paymentHashModel = new PaymentHash();
            $paymentHashModel->setAttribute('hash', $ipn['hmac_signature']);
            $paymentHashModel->save();

            echo 'Ok';
            exit;
        } elseif ($ipnStatus === 1) {
            $payments->status = Payments::STATUS_WAIT;
            $payments->update();
        } else {
            $this->Errorlogging("no final status", "CoinPayments", $paymentSignature);
        }
    }

    public function actionIndex()
    {
    	return $this->redirect('/signin',403);
    }

    private  function paymentLog($response, $pid = -1) {
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

    private  function logging($array, $logname, $signStamp) {
      
      $path = Yii::getAlias('@runtime/payments/');

      $output = $_SERVER['HTTP_HOST']."\n".date("Y-m-d H:i:s", time()+\Yii::$app->params['time']+10803)."\n\n".$logname."-".$signStamp."\n\n";
      $output .= json_encode($array, JSON_PRETTY_PRINT)."\n\n\n";
      $fp = fopen($path.$logname.".log", "a+");
      fputs ($fp, $output);
      fclose ($fp);
    }

    private  function Errorlogging($comment, $logname, $signStamp) {
    	$path = Yii::getAlias('@runtime/payments/');
		$output = $_SERVER['HTTP_HOST']."\n".date("Y-m-d H:i:s", time()+\Yii::$app->params['time']+10803)."\n\n".$logname."-".$signStamp."\n\n";
		$output .= $comment."\n\n\n";
		$fp = fopen($path.$logname.".log", "a+");
		fputs ($fp, $output);
		fclose ($fp);
    }
}