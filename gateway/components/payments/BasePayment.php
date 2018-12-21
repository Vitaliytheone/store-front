<?php

namespace payments;

use common\models\gateways\Sites;
use payments\exceptions\ValidationException;
use Yii;
use common\models\gateway\Payments;
use common\models\gateway\PaymentsLog;
use common\models\gateways\PaymentMethods;
use common\models\gateways\SitePaymentMethods;
use yii\base\Component;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;

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
    abstract function checkout($payment);

    /**
     * Validate payment service response
     * @return mixed
     */
    abstract function processing();

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
     * Get formatted description
     * @return string
     */
    protected function getDescription()
    {
        return Yii::t('app', 'addfunds.payment.description') . ' (' . $this->getUser()->login . ')';
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
        $this->_log->pid = $payment->id;
        $this->_log->setResponse(is_string($data) ? $data : var_export($data, true));
        $this->_log->save(false);
    }

    /**
     * Get payment by payment id
     * @param integer $paymentId
     * @return Payments|null
     * @throws ValidationException
     */
    public function getPayment($paymentId)
    {
        if (empty($paymentId)
            || !($this->_payment = Payments::findOne([
                'id' => $paymentId,
                'type' => $this->_method_id
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
                $this->_paymentMethod = ArrayHelper::getValue(PaymentMethodHelper::getPanelPaymentMethods($this->_panel), $methodId);
                if ($this->_paymentMethod) {
                    break;
                }
            }
        }

        if (empty($this->_paymentMethod['visibility'])) {
            throw new ValidationException('Bad payment method');
        }

        return $this->_paymentMethod;
    }

    /**
     * @param array $paymentMethod
     */
    public function setPaymentMethod(array $paymentMethod)
    {
        $this->_paymentMethod = $paymentMethod;
    }

    /**
     * @param Project $panel
     */
    public function setPanel(?Project $panel)
    {
        $this->_panel = $panel;
    }

    /**
     * @return Project
     */
    public function getPanel(): Project
    {
        return $this->_panel;
    }

    /**
     * @param Users $user
     */
    public function setUser(?Users $user)
    {
        $this->_user = $user;
    }

    /**
     * @return Users
     */
    public function getUser(): Users
    {
        return $this->_user;
    }

    /**
     * @return string
     */
    public function getNotifyUrl(): string
    {
        return SiteHelper::hostUrl($this->getPanel()->ssl) . '/' . ArrayHelper::getValue(PaymentMethodHelper::getPaymentMethods($this->_panel), [
            $this->getPaymentMethod()['method_id'],
            'url'
        ]);
    }

    /**
     * @return string
     */
    public function getReturnUrl(): string
    {
        return SiteHelper::hostUrl($this->getPanel()->ssl) . '/addfunds';
    }

    /**
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->getPanel()->getCurrencyCode();
    }
}