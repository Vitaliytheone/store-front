<?php

namespace payments;

use common\models\gateways\Sites;
use payments\exceptions\ValidationException;
use Yii;
use common\models\gateway\Payments;
use common\models\gateway\PaymentsLog;
use common\models\gateways\SitePaymentMethods;
use yii\base\Component;
use Exception;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use common\helpers\SiteHelper;

/**
 * Class BasePayment
 * @package payments
 */
abstract class BasePayment extends Component {

    /**
     * @var integer|array
     */
    protected $_method_id;

    /**
     * @var string - url action
     */
    public $action;

    /**
     * @var string - method POST, GET
     */
    public $method = 'POST';

    /**
     * @var string
     */
    public $charset = 'utf-8';

    /**
     * @var string
     */
    protected $_description;

    /**
     * @var array
     */
    protected $_data = [];

    /**
     * @var string
     */
    protected $_token;

    /**
     * @var string
     */
    protected $_method;

    /**
     * @var Payments
     */
    protected $_payment;

    /**
     * @var PaymentsLog
     */
    protected $_log;

    /**
     * @var bool - is redirect after check payment result
     */
    public $redirectProcessing = false;

    /**
     * @var bool - show errors
     */
    public $showErrors = false;

    /**
     * @var Sites
     */
    protected $_site;

    /**
     * @var array
     */
    protected $_paymentMethod;

    /**
     * @var array
     */
    protected $_user_details;

    public function init()
    {
        $this->_token = bin2hex(random_bytes(32));
        $this->_method = strtolower((new \ReflectionClass($this))->getShortName());

        return parent::init();
    }

    /**
     * Checkout method
     * @param Payments $payment
     * @return mixed -
     * [
     *  'result' => 1,
     *  'formData' => [
     *      'form' => [
     *          'action' => '',
     *          'method' => '',
     *          'charset' => '',
     *      ],
     *      'data' => [
     *          '<key>' => '<value>'
     *      ]
     *  ]
     * ]
     *
     * [
     *  'result' => 2,
     *  'redirect' => <redirect link>
     * ]
     */
    abstract function checkouting();

    /**
     * Validate payment service response
     * @return mixed
     */
    abstract function processing();

    /**
     * Checkout
     * @return mixed
     */
    public function checkout()
    {
        if (empty($this->_payment)) {
            throw new Exception();
        }

        try {
            return $this->checkouting();
        } catch (Exception $e) {
            $this->fileLog($e->getMessage() . $e->getTraceAsString());
            return static::returnError();
        }
    }

    /**
     * Validate payment service response
     * @return mixed
     */
    public function process()
    {
        $this->fileLog(json_encode([
            'POST' => $_POST,
            'GET' => $_GET,
            'SERVER' => $_SERVER,
        ], JSON_PRETTY_PRINT));

        try {
            $result = $this->processing();
        } catch (ValidationException $e) {
            $result = [
                'result' => 2,
                'content' => $e->getMessage()
            ];
        }

        $this->_payment && $this->_payment->save(false);

        $this->_log && $this->_log->setResult($result)->save(false);

        $this->fileLog(var_export($result, true));

        $this->success($result);

        return $result;
    }

    /**
     * After success payment
     * @param array $result
     * @return boolean
     */
    public function success($result)
    {
        if (1 != $result['result']) {
            return false;
        }

        // Если успешно прошла проверка - то добавляем сумму на счет и меняем статусы платежа
        $payment = Payments::findOne($result['payment_id']);

        if (Payments::STATUS_COMPLETED == $payment->status) {
            return true;
        }

        /**
         * @var $transaction Transaction
         */
        $transaction = Yii::$app->gatewayDb->beginTransaction();
        Yii::$app->db->beginTransaction();

        $payment->status = Payments::STATUS_COMPLETED;
        $payment->response = 1;

        if (empty($payment->transaction_id)) {
            $payment->transaction_id = $result['transaction_id'];
        }

        // Если платеж уже существует, то не добавляем/изменяем записи в базе
        if (Payments::find()->andWhere([
            'id' => $result['payment_id'],
            'status' => Payments::STATUS_COMPLETED
        ])->exists()) {
            $transaction->rollBack();
            return false;
        }

        $payment->save(false);

        $transaction->commit();
    }

    /**
     * Get js payment environment
     * @return array
     */
    public function getJsEnvironments()
    {
        return [];
    }

    /**
     * Get form
     * @return array
     */
    protected function getFrom()
    {
        return [
            'action' => $this->action,
            'method' => $this->method,
            'charset' => $this->charset,
        ];
    }

    /**
     * Return checkout form data
     * @param array $form
     * @param array $data
     * @return array
     */
    protected static function returnForm($form = [], $data = [])
    {
        return [
            'result' => 1,
            'formData' => [
                'form' => $form,
                'data' => $data
            ]
        ];
    }

    /**
     * Return checkout redirect
     * @param string $redirect
     * @return array
     */
    protected static function returnRedirect($redirect)
    {
        return [
            'result' => 2,
            'redirect' => $redirect
        ];
    }

    /**
     * Return checkout data
     * @param array $options
     * @return array
     */
    protected static function returnSuccess($options = [])
    {
        return [
            'result' => 3,
            'options' => $options
        ];
    }
    

    /**
     * Return checkout error
     * @return array
     */
    protected static function returnError()
    {
        return [
            'result' => 4,
        ];
    }

    /**
     * Set formatted description
     * @param string $description
     * @return string
     */
    public function setDescription($description)
    {
        $this->_description = $description;
    }

    /**
     * Get formatted description
     * @return string
     */
    public function getDescription()
    {
        if (null !== $this->_description) {
            return $this->_description;
        }
        $this->_description = ArrayHelper::getValue($this->getPaymentMethod(), ['options', 'description']);
        
        return $this->_description;
    }

    /**
     * Log to file
     * @param mixed $data
     */
    protected function fileLog($data)
    {
        if (!is_string($data)) {
            $data = json_encode($data, JSON_PRETTY_PRINT);
        }
        
        $filePath = Yii::getAlias('@paymentsLog') . '/' . $this->_method . '.log';
        $data = implode("\r\n", [
            SiteHelper::host(),
            date('Y-m-d H:i:s'),
            '',
            $this->_method . '-' . $this->_token,
            '',
            $data
        ]);
        @file_put_contents($filePath, $data . "\r\n\r\n", FILE_APPEND);
    }

    /**
     * Log to db table
     *
     * @param Payments $payment
     * @param mixed $data
     */
    protected function dbLog($payment, $data)
    {
        // заносим запись в таблицу payments_log
        $this->_log = new PaymentsLog();
        $this->_log->payment_id = $payment->id;
        $this->_log->setResponse(is_string($data) ? $data : var_export($data, true));
        $this->_log->save(false);
    }

    /**
     * @param Payments $payment
     * @return BasePayment
     */
    public function setPayment(Payments $payment)
    {
        $this->_payment = $payment;

        return $this;
    }

    /**
     * @return Payments
     */
    public function getPayment()
    {
        return $this->_payment;
    }

    /**
     * Get payment by payment id
     * @param integer $paymentId
     * @return Payments|null
     * @throws ValidationException
     */
    public function getPaymentById($paymentId)
    {
        if (empty($paymentId)
            || !($this->_payment = Payments::findOne([
                'id' => $paymentId,
                'method_id' => $this->_method_id
            ]))
            || in_array($this->_payment->status, [1, 2])) {
            // no invoice

            throw new ValidationException('No invoice');
        }

        $this->_payment->response = 1;
        $this->_payment->updated_at = time();

        return $this->_payment;
    }

    /**
     * @return array
     * @throws ValidationException
     */
    public function getPaymentMethod(): array
    {
        if (!$this->_paymentMethod) {
            foreach ((array)$this->_method_id as $methodId) {
                $sitePaymentMethod = SitePaymentMethods::find()
                    ->innerJoinWith(['method'])
                    ->andWhere([
                        'method_id' => $methodId,
                        'site_id' => $this->_site->id,
                        'visibility' => 1
                    ])
                    ->one();

                if ($sitePaymentMethod) {
                    $this->_paymentMethod = [
                        'id' => $sitePaymentMethod->id,
                        'method_id' => $sitePaymentMethod->method_id,
                        'name' => $sitePaymentMethod->method->method_name,
                        'url' => $sitePaymentMethod->method->url,
                        'options' => $sitePaymentMethod->getOptionsDetails(),
                    ];

                    break;
                }

            }
        }

        return $this->_paymentMethod;
    }

    /**
     * @param array $paymentMethod
     * @return BasePayment
     */
    public function setPaymentMethod(array $paymentMethod)
    {
        $this->_paymentMethod = $paymentMethod;

        return $this;
    }

    /**
     * @param Sites $site
     * @return BasePayment
     */
    public function setGateway(?Sites $site)
    {
        $this->_site = $site;

        return $this;
    }

    /**
     * @return Sites
     */
    public function getGateway(): Sites
    {
        return $this->_site;
    }

    /**
     * @return string
     */
    public function getNotifyUrl(): string
    {
        return SiteHelper::hostUrl($this->getGateway()->ssl) . '/' . ArrayHelper::getValue($this->getPaymentMethod(), 'url');
    }

    /**
     * @return string
     */
    public function getReturnUrl(): string
    {
        if (($payment = $this->getPayment())) {
            return $payment->return_url;
        }

        return SiteHelper::hostUrl($this->getGateway()->ssl);
    }

    /**
     * @param $data
     * @return bool
     */
    public function validateUserDetails($data)
    {
        return true;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return [];
    }

    /**
     * @return array
     */
    public function getScripts()
    {
        return [];
    }
}