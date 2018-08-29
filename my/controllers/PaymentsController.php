<?php

namespace my\controllers;

use my\components\bitcoin\Bitcoin;
use my\components\payments\BasePayment;
use my\helpers\PaymentsHelper;
use my\mail\mailers\PaypalFailed;
use my\mail\mailers\PaypalPassed;
use my\mail\mailers\PaypalReviewed;
use my\mail\mailers\PaypalVerificationNeeded;
use my\mail\mailers\TwoCheckoutFailed;
use my\mail\mailers\TwoCheckoutPass;
use my\mail\mailers\TwoCheckoutReview;
use Yii;
use common\models\panels\Invoices;
use common\models\panels\PaymentGateway;
use common\models\panels\Payments;
use common\models\panel\PaymentsLog;
use common\models\panels\PaymentHash;
use my\components\payments\Paypal;
use yii\helpers\ArrayHelper;

class PaymentsController extends CustomController
{
	public $enableDomainValidation = false;
	
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
        $invoice = null;
    	$paymentSignature = md5(rand().rand().time().rand().rand());

		$this->logging(array("POST" => $_POST, "GET" => $_GET, "SERVER" => $_SERVER), 'Paypalexpress', $paymentSignature);

		if( isset($_GET['token']) && !empty($_GET['token']) ) {
			$paypal = new Paypal;

	        $checkoutDetails = $paypal->get(
	            $paypal->request('GetExpressCheckoutDetails', array('TOKEN' => $_GET['token'])),
                ['PAYMENTREQUEST_0_AMT', 'AMT']
            );

	        if (!$checkoutDetails) {
                $this->Errorlogging("no data", "Paypalexpress", $paymentSignature);
                return $this->_redirectWithInvoice($invoice);
            }
	        
	        $requestParams = array(
	           'PAYMENTREQUEST_0_PAYMENTACTION' => 'Sale',
	           'PAYERID' => $_GET['PayerID'],
	           'TOKEN' => $_GET['token'],
	           'PAYMENTREQUEST_0_AMT' => $checkoutDetails['PAYMENTREQUEST_0_AMT'],
	        );

	        $response = $paypal->request('DoExpressCheckoutPayment', $requestParams);

	        if (!$response) {
                $this->Errorlogging("no data", "Paypalexpress", $paymentSignature);
                return $this->_redirectWithInvoice($invoice);
            }

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

			            $GetTransactionDetails = $paypal->get(
			                $paypal->request('GetTransactionDetails', [
                                'TRANSACTIONID' => $response['PAYMENTINFO_0_TRANSACTIONID']
			                ]),
                            ['FEEAMT', 'CURRENCYCODE', 'EMAIL', 'PAYERID']
                        );

			            $this->logging(array("POST" => $_POST, "GET" => $_GET, "SERVER" => $_SERVER, 'response' => $response, 'GetTransactionDetails' => $GetTransactionDetails), 'Paypalexpress', $paymentSignature);

			            $this->paymentLog([
			                'GetTransactionDetails' => $GetTransactionDetails
                        ], $payment->id);

			            if (!$GetTransactionDetails) {
                            $this->Errorlogging("no data", "Paypalexpress", $paymentSignature);
                            return $this->_redirectWithInvoice($invoice);
                        }

                        $payments = Payments::findOne(['id' => $payment->id]);
                        $payments->comment = $GetTransactionDetails['EMAIL'].'; '.$response['PAYMENTINFO_0_TRANSACTIONID'];
                        $payments->transaction_id = $response['PAYMENTINFO_0_TRANSACTIONID'];
                        $payments->fee = ArrayHelper::getValue($GetTransactionDetails, 'FEEAMT');
                        $getTransactionDetailsStatus = ArrayHelper::getValue($GetTransactionDetails, 'PAYMENTSTATUS', '');
                        $doExpressCheckoutPaymentStatus = ArrayHelper::getValue($response, 'PAYMENTINFO_0_PAYMENTSTATUS', $getTransactionDetailsStatus);
                        $getTransactionDetailsStatus = strtolower($getTransactionDetailsStatus);
                        $doExpressCheckoutPaymentStatus = strtolower($doExpressCheckoutPaymentStatus);

			            if ($getTransactionDetailsStatus == 'completed' && $getTransactionDetailsStatus == $doExpressCheckoutPaymentStatus) {
			            	$hash = PaymentHash::findOne(['hash' => $response['PAYMENTINFO_0_TRANSACTIONID']]);
			            	if ($hash === null) {
                                if ($checkoutDetails['AMT'] == $payments->amount) {

                                    if ($GetTransactionDetails['CURRENCYCODE'] == 'USD') {

	                				    $payerId = $GetTransactionDetails['PAYERID'];
	                				    $payerEmail = $GetTransactionDetails['EMAIL'];

                                        if (PaymentsHelper::validatePaypalPayment($payments, $payerId, $payerEmail)) {

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

        return $this->_redirectWithInvoice($invoice);
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
		    $payment = new BasePayment();

		    $response = $payment->get($_POST, [
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

			if ($response) {
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
                        $response['LMI_PAYMENT_AMOUNT'].
                        $response['LMI_PAYMENT_NO'].
                        $response['LMI_MODE'].
                        $response['LMI_SYS_INVS_NO'].
                        $response['LMI_SYS_TRANS_NO'].
                        $response['LMI_SYS_TRANS_DATE'].
            			$secret_key.
                        $response['LMI_PAYER_PURSE'].
                        $response['LMI_PAYER_WM'];

            			$signature = strtoupper(hash('sha256', $common_string));

            			if($signature == $response['LMI_HASH']) {
            				if ($payments->amount == $response['LMI_PAYMENT_AMOUNT']) {
            					$hash = PaymentHash::findOne(['hash' => $response['LMI_HASH']]);
		            			if ($hash === null) {

                                    // Mark invoice paid
                                    $invoice->paid(PaymentGateway::METHOD_WEBMONEY);

					                $payments = Payments::findOne(['id' => $payment->id]);
                                    $payments->transaction_id = $response['LMI_PAYER_PURSE'];
						            $payments->comment = $response['LMI_PAYER_PURSE'];
                                    $payments->status = Payments::STATUS_COMPLETED;
						            $payments->update();

                					$paymentHashModel = new PaymentHash();
									$paymentHashModel->load(array('PaymentHash' => array(
										'hash' => $response['LMI_HASH'],
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
        $paymentComponent = new BasePayment();
        $response = $paymentComponent->get($_POST, [
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

		if ($response) {
			$payment = Payments::findOne(['id' => $response['PAYMENT_ID']]);

	        if ($payment !== null) {

	        	$this->paymentLog($response, $payment->id);

	        	$payments = Payments::findOne(['id' => $response['PAYMENT_ID']]);
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


	            	$string =   $response['PAYMENT_ID'].':'.$response['PAYEE_ACCOUNT'].':'.$response['PAYMENT_AMOUNT'].':'.$response['PAYMENT_UNITS'].':'.$response['PAYMENT_BATCH_NUM'].':'.$response['PAYER_ACCOUNT'].':'.$passphrase.':'.$response['TIMESTAMPGMT'];

                    $signature=strtoupper(md5($string));

          			if ($signature == $_POST['V2_HASH']){ 
          				if($_POST['PAYMENT_UNITS'] == 'USD'){
              				if ($payments->amount == $response['PAYMENT_AMOUNT']) {
              					$hash = PaymentHash::findOne(['hash' => $response['V2_HASH']]);
		            			if ($hash === null) {

                                    // Mark invoice paid
                                    $invoice->paid(PaymentGateway::METHOD_PERFECT_MONEY);

					                $payments = Payments::findOne(['id' => $payment->id]);
                                    $payments->transaction_id = $response['PAYER_ACCOUNT'];
						            $payments->comment = $response['PAYER_ACCOUNT'];
                                    $payments->status = Payments::STATUS_COMPLETED;
						            $payments->update();

                					$paymentHashModel = new PaymentHash();
									$paymentHashModel->load(array('PaymentHash' => array(
										'hash' => $response['V2_HASH'],
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

        $paymentComponent = new BasePayment();
        $response = $paymentComponent->get($_GET, [
            'callback_data',
            'status',
            'address',
            'tid'
        ]);

      	if ($response) {
      		$payment = Payments::findOne(['id' => $response['callback_data']]);

	        if ($payment !== null) {

	        	$this->paymentLog($response, $payment->id);

	        	$payments = Payments::findOne(['id' => $response['callback_data']]);
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
                    $payments->comment = $response['address'];
                    $payments->transaction_id = $response['tid'];

					if ($signature == $_SERVER['HTTP_X_SIGNATURE']) {
                        $amountPaid = ArrayHelper::getValue($_GET, 'amount_paid_in_btc', 0);
                        $amount = ArrayHelper::getValue($_GET, 'amount_in_btc', 0);

                        if ($amountPaid >= $amount) {
                            if (in_array($response['status'], [2, 4])) {
                                $hash = PaymentHash::findOne(['hash' => $response['tid']]);
                                if ($hash === null) {

                                    // Mark invoice paid
                                    $invoice->paid(PaymentGateway::METHOD_BITCOIN);

                                    $payments->status = Payments::STATUS_COMPLETED;
                                    $payments->update();


                                    $paymentHashModel = new PaymentHash();
                                    $paymentHashModel->load(array('PaymentHash' => array(
                                        'hash' => $response['tid'],
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

        $paymentComponent = new BasePayment();

        $response = $paymentComponent->get($_POST, [
            'sale_id',
            'invoice_id',
            'item_id_1',
            'fraud_status',
            'list_currency',
            'hash',
            'invoice_list_amount'
        ]);

		if ($response) {
			$payment = Payments::findOne(['id' => $response['item_id_1']]);

	        if ($payment !== null) {

	        	$this->paymentLog($response, $payment->id);

	        	$payments = Payments::findOne(['id' => $response['item_id_1']]);
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
					$hashOrder = $response['sale_id'];
					$hashInvoice = $response['invoice_id'];
					$StringToHash = strtoupper(md5($hashOrder . $hashSid . $hashInvoice . $secret_word));

                    $payments = Payments::findOne(['id' => $payment->id]);
                    $payments->comment = $hashOrder . '; ' . $hashInvoice;
                    $payments->transaction_id = $hashOrder;

					if ($StringToHash == $response['md5_hash']) {
						if (strtolower($response['list_currency']) == "usd") {
							if (strtolower($response['fraud_status']) == 'pass') {
								if ($payments->amount == $response['invoice_list_amount']) {
									$hash = PaymentHash::findOne(['hash' => $hashOrder]);
			            			if ($hash === null) {

                                        $payments->complete();

	                					$paymentHashModel = new PaymentHash();
										$paymentHashModel->load(array('PaymentHash' => array(
											'hash' => $response['sale_id'],
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

    private function logging($array, $logname, $signStamp) {
      
      $path = Yii::getAlias('@runtime/payments/');

      $output = $_SERVER['HTTP_HOST']."\n".date("Y-m-d H:i:s", time()+\Yii::$app->params['time']+10803)."\n\n".$logname."-".$signStamp."\n\n";
      $output .= json_encode($array, JSON_PRETTY_PRINT)."\n\n\n";
      $fp = fopen($path.$logname.".log", "a+");
      fputs ($fp, $output);
      fclose ($fp);
    }

    private function Errorlogging($comment, $logname, $signStamp) {
    	$path = Yii::getAlias('@runtime/payments/');
		$output = $_SERVER['HTTP_HOST']."\n".date("Y-m-d H:i:s", time()+\Yii::$app->params['time']+10803)."\n\n".$logname."-".$signStamp."\n\n";
		$output .= $comment."\n\n\n";
		$fp = fopen($path.$logname.".log", "a+");
		fputs ($fp, $output);
		fclose ($fp);
    }
}