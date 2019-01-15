<?php

namespace sommerce\components\payments;

use common\events\Events;
use common\helpers\SiteHelper;
use common\models\store\Carts;
use common\models\store\Checkouts;
use common\models\store\Orders;
use common\models\store\Packages;
use common\models\store\Payments;
use common\models\store\Suborders;
use common\models\stores\PaymentMethodsCurrency;
use common\models\stores\StorePaymentMethods;
use common\models\stores\Stores;
use common\models\stores\StoresSendOrders;
use Yii;
use yii\base\Component;
use yii\db\Exception;
use yii\helpers\ArrayHelper;

/**
 * Class BasePayment
 * @package app\components\payments
 */
abstract class BasePayment extends Component
{
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
     * @var Checkouts
     */
    protected $_checkout;

    /**
     * @var Payments
     */
    protected $_payment;

    /**
     * @var bool - is redirect after check payment result
     */
    public $redirectProcessing = false;

    /**
     * Is shown processing & checkout errors messages
     * @var bool show errors (default False)
     */
    public $showErrors = false;

    /**
     * BasePayment constructor.
     * @param array $config
     * @throws \ReflectionException
     */
    public function __construct(array $config = [])
    {
        $this->_method = strtolower((new \ReflectionClass($this))->getShortName());

        parent::__construct($config);
    }

    public function init()
    {
        $this->_token = bin2hex(random_bytes(32));

        return parent::init();
    }

    /**
     * Checkout method
     * @param Checkouts $checkout
     * @param Stores $store
     * @param string $email
     * @param StorePaymentMethods $details
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
    abstract function checkout($checkout, $store, $email, $details);

    /**
     * Validate payment service response
     * @param Stores $store
     * @return mixed
     */
    abstract function processing($store);

    /**
     * Validate payment service response
     * @param Stores $store
     * @return mixed
     */
    public function process($store)
    {
        $this->log(json_encode([
            'POST' => $_POST,
            'GET' => $_GET,
            'SERVER' => $_SERVER,
        ], JSON_PRETTY_PRINT));

        $result = $this->processing($store);
        $this->_checkout && $this->_checkout->save(false);
        $this->log(var_export($result, true));
        try {
            static::success($this->_payment, $result, $store);
        } catch (Exception $e) {
            $this->log('Error '. $e->getMessage());
        }


        return $result;
    }

    /**
     * After success payment
     * @param Payments $payment
     * @param array $result
     * @param Stores $store
     * @return void
     */
    public static function success($payment, $result, $store)
    {
        if (1 != $result['result']) {
            if ($payment) {
                if (!$payment->status) {
                    $payment->status = Payments::STATUS_FAILED;
                }

                $payment->save(false);

            }

            return;
        }
        $checkout = Checkouts::findOne($result['checkout_id']);

        if (Checkouts::STATUS_PAID == $checkout->status) {
            $obj = new static();
            $obj->log('$checkout->status = ' .  Checkouts::STATUS_PAID);
            return;
        }

        $checkout->status = Checkouts::STATUS_PAID;
        $checkout->save(false);
        $order = new Orders();
        $order->checkout_id = $checkout->id;
        $order->customer = $checkout->customer;
        $order->save(false);

        $items = $checkout->getDetails();
        $packages = ArrayHelper::index(Packages::find()->andWhere([
            'id' => ArrayHelper::getColumn($items, 'package_id')
        ])->all(), 'id');
        foreach ($items as $item) {
            /**
             * @var Packages $package
             */
            $package = $packages[$item['package_id']];

            $orderItem = new Suborders();
            $orderItem->checkout_id = $checkout->id;
            $orderItem->order_id = $order->id;
            $orderItem->link = $item['link'];
            $orderItem->quantity = $package->quantity;
            $orderItem->overflow_quantity = $package->quantity + floor($package->quantity * $package->overflow / 100);
            $orderItem->package_id = $package->id;
            $orderItem->currency = $checkout->currency;
            $orderItem->amount = $package->price;
            $orderItem->mode = $package->mode;
            $orderItem->provider_id = $package->provider_id;
            $orderItem->provider_service = $package->provider_service;

            $orderItem->status = Suborders::STATUS_PENDING;

            if (Packages::MODE_AUTO == $package->mode) {
                $orderItem->status = Suborders::STATUS_AWAITING;
                $orderItem->send = Suborders::SEND_STATUS_AWAITING;
            }

            $orderItem->save(false);

            // Make queue for sender
            if (Suborders::MODE_AUTO == $orderItem->mode) {
                $sendOrder = new StoresSendOrders();
                $sendOrder->store_id = $store->id;
                $sendOrder->store_db = $store->db_name;
                $sendOrder->provider_id = $package->provider_id;
                $sendOrder->suborder_id = $orderItem->id;
                $sendOrder->save(false);
            }

            // Remove paid items from cart
            Carts::removeItemByKey($item['cart_key']);
        }

        $payment->refresh();
        $payment->checkout_id = $checkout->id;
        $payment->amount = $checkout->price;
        $payment->customer = $checkout->customer;
        $payment->status = Payments::STATUS_COMPLETED;
        $payment->currency = $checkout->currency;

        if (empty($payment->transaction_id)) {
            $payment->transaction_id = $result['transaction_id'];
        }

        if (empty($payment->memo)) {
            $payment->memo = $result['transaction_id'];
        }

        $payment->save(false);

        // Event confirm
        Events::add(Events::EVENT_STORE_ORDER_CONFIRM, [
            'order' => $order,
            'store' => $store
        ]);

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
     * Return checkout error
     * @param array|null $result
     * @return array
     */
    protected static function returnError($result = null)
    {
        if (empty($result)) {
            return [
                'result' => 3,
            ];
        }

        return $result;
    }

    /**
     * Get formatted description
     * @param string $orderId
     * @return string
     */
    protected static function getDescription($orderId)
    {
        return Yii::t('app', 'cart.payment_description', ['order_id' => $orderId]);
    }

    /**
     * Return payments result by Checkout Id
     * Uses for payments result page
     * @param $checkoutId
     * @return array
     */
    public static function getPaymentResult($checkoutId)
    {
        $paymentsResult = [
            'id' => $checkoutId,
        ];

        if (!$checkoutId ||
            !$payment = Payments::findOne(['checkout_id' => $checkoutId])
        ) {
            $paymentsResult['failed'] = true;
        } else {
            $paymentsResult['failed'] = in_array($payment->status, [Payments::STATUS_FAILED]);
            $paymentsResult['awaiting'] = in_array($payment->status, [Payments::STATUS_AWAITING]);
            $paymentsResult['completed'] = in_array($payment->status, [Payments::STATUS_COMPLETED]);
        }

        return $paymentsResult;
    }

    /**
     * Log to file
     * @param $data
     */
    protected function log($data)
    {
        $filePath = Yii::getAlias('@paymentsLog') . '/' . $this->_method . '.log';

        $data = implode("\r\n", [
            SiteHelper::host(),
            date('Y-m-d H:i:s'),
            '',
            $this->_method . '-' . $this->_token,
            '',
            is_string($data) ? $data : var_export($data, true)
        ]);
        @file_put_contents($filePath, $data . "\r\n\r\n", FILE_APPEND);
    }

    /**
     * Get js payment environment
     * @param Stores $store
     * @param string $email
     * @param StorePaymentMethods $details
     * @return array
     */
    public function getJsEnvironments($store, $email, $details)
    {
        return [];
    }

    /**
     * @param Stores $store
     * @param string $name
     * @param string|mixed $value
     * @return bool
     */
    public function validate($store, $name, $value)
    {
        return true;
    }

    /**
     * @return array
     */
    public function fields()
    {
        return [];
    }

    /**
     * Get visible store pay method
     * @param Stores $store
     * @param int $methodId - payment_methods.id
     * @return StorePaymentMethods|bool
     */
    public function getPaymentMethod(Stores $store, int $methodId)
    {
        $storeMethod = StorePaymentMethods::findOne([
            'method_id' => $methodId,
            'store_id' => $store->id,
            'visibility' => StorePaymentMethods::VISIBILITY_ENABLED,
        ]);

        $currencyMethod = PaymentMethodsCurrency::findOne($storeMethod->currency_id);

        if ($currencyMethod->currency !== $store->currency) {
            return false;
        }

        return $storeMethod;
    }
}