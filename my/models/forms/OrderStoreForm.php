<?php
namespace my\models\forms;

use common\models\panels\InvoiceDetails;
use common\models\panels\Invoices;
use common\models\panels\MyActivityLog;
use common\models\panels\Orders;
use common\models\stores\StoreAdminAuth;
use common\models\stores\Stores;
use my\helpers\OrderHelper;
use my\helpers\UserHelper;
use sommerce\helpers\ConfigHelper;
use Yii;
use yii\base\Exception;
use yii\base\Model;
use common\models\panels\Auth;
use yii\db\Query;

/**
 * Class OrderStoreForm
 * @package my\models\forms
 */
class OrderStoreForm extends Model
{
    public $store_name;
    public $store_currency;
    public $admin_username;
    public $admin_password;
    public $confirm_password;

    /** @var string */
    public $storeDomain;

    /** @var string Generated invoice code */
    private $_invoiceCode;

    /** @var Auth */
    private $_user;

    /** @var string */
    private $_ip;

    /** @var  bool Is order trial store */
    private $_isTrial;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['store_name', 'store_currency', 'admin_username', 'admin_password', 'confirm_password'], 'required'],
            [['store_name', 'admin_username',], 'trim'],
            ['store_name', 'string', 'max' => 255,],
            ['store_name', 'match', 'pattern' => '/^[a-zA-Z0-9 \-\s]+$/', 'message' => Yii::t('app', 'error.store.bad_name')],
            ['store_currency', 'in', 'range' => array_keys($this->getCurrencies()), 'message' => Yii::t('app', 'error.store.bad_currency')],
            ['admin_username', 'string', 'max' => 255],
            ['admin_password', 'string', 'min' => 5],
            ['admin_password', 'compare', 'compareAttribute' => 'confirm_password'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'store_name' => Yii::t('app', 'stores.order.form.label.store_name'),
            'store_currency' => Yii::t('app', 'stores.order.form.label.store_currency'),
            'admin_username' => Yii::t('app', 'stores.order.form.label.admin_username'),
            'admin_password' => Yii::t('app', 'stores.order.form.label.admin_password'),
            'confirm_password' => Yii::t('app', 'stores.order.form.label.confirm_password'),
        ];
    }

    /**
     * Set current user
     * @param Auth $user
     */
    public function setUser(Auth $user)
    {
        $this->_user = $user;
    }

    /**
     * Get current user
     * @return Auth
     */
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * Set user ip
     * @param $ip
     */
    public function setIp($ip)
    {
        $this->_ip = $ip;
    }

    /**
     * Get user ip
     * @return string
     */
    public function getIp()
    {
        return $this->_ip;
    }

    /**
     * Set is orders trial store
     * @param bool $isTrial
     */
    public function setTrial(bool $isTrial)
    {
        $this->_isTrial = $isTrial;
    }

    /**
     * Get is order trial
     * @return bool
     */
    public function getTrial()
    {
        return $this->_isTrial;
    }

    /**
     * Get invoice code
     * @return string
     */
    public function getInvoiceCode()
    {
        return $this->_invoiceCode;
    }

    /**
     * Return store currencies list
     * @return array
     */
    public function getCurrencies()
    {
        $currencies = ConfigHelper::getParam('currencies');

        array_walk($currencies, function(&$value, $key){
            $value = $value['name'] . " ($key)";
        });

        return $currencies;
    }

    /**
     * Return generated subdomain from store name
     * @return string
     * @throws Exception
     */
    protected function generateSubdomain()
    {
        $domain = Yii::$app->params['storeDomain'];

        if (empty($domain)) {
            throw new Exception('Bad config-params: store_domain not configured yet!');
        }

        $subdomain = str_replace(' ', '-', strtolower(trim($this->store_name)));

        $pendingOrders = (new Query())
            ->select('domain')
            ->from(Orders::tableName())
            ->andWhere(['status' => [
                Orders::STATUS_PENDING,
                Orders::STATUS_PAID
            ]])
            ->column();

        $exitingStores = (new Query())
            ->select('domain')
            ->from(Stores::tableName())
            ->column();

        $exitingDomains = array_merge($pendingOrders, $exitingStores);

        $subdomainPostfix = 2;

        $checkingSubdomain = $subdomain;

        // Check if store with same domain already exist
        do {
            $chekingDomain = $checkingSubdomain . '.' . $domain;

            $domainExist = in_array($chekingDomain, $exitingDomains);

            if ($domainExist) {
                $checkingSubdomain = $subdomain . $subdomainPostfix;
                $subdomainPostfix++;
            }

        } while ($domainExist);

        $this->storeDomain = $chekingDomain;

        return $this->storeDomain;
    }

    /**
     * Create order
     * @return bool|Orders
     */
    protected function createOrder()
    {
        $order = new Orders();
        $order->cid = $this->getUser()->id;
        $order->item = Orders::ITEM_BUY_STORE;
        $order->domain = $this->storeDomain;
        $order->ip = $this->getIp();
        $order->status = $this->getTrial() ? Orders::STATUS_PAID : Orders::STATUS_PENDING;
        $order->setDetails([
            'trial' => $this->getTrial() ? 1 : 0,
            'name' => $this->store_name,
            'domain' => $this->storeDomain,
            'currency' => $this->store_currency,
            'username' => $this->admin_username,
            'password' => StoreAdminAuth::hashPassword($this->admin_password),
        ]);

        if (!$order->save()) {
            return false;
        }

        if (!OrderHelper::store($order)) {
            return false;
        }

        return $order;
    }

    /**
     * Create invoice
     * @param Orders $order
     * @return bool|Invoices
     */
    protected function createInvoice(Orders $order)
    {
        $isTrial = $this->getTrial();

        // Make invoice
        $invoice = new Invoices();
        $invoice->cid = $this->_user->id;
        $invoice->generateCode();
        $invoice->daysExpired(Yii::$app->params['invoice.storeDuration']);
        $invoice->total = $isTrial ? 0 : Yii::$app->params['storeDeployPrice'];
        $invoice->status = $isTrial ? Invoices::STATUS_PAID : Invoices::STATUS_UNPAID;

        if (!$invoice->save()) {
            return false;
        }

        // Make invoice details
        $invoiceDetails = new InvoiceDetails();
        $invoiceDetails->invoice_id = $invoice->id;
        $invoiceDetails->item_id = $order->id;
        $invoiceDetails->item = $isTrial ? InvoiceDetails::ITEM_BUY_TRIAL_STORE : InvoiceDetails::ITEM_BUY_STORE;
        $invoiceDetails->amount = $invoice->total;

        if (!$invoiceDetails->save()) {
            return false;
        }

        return $invoice;
    }

    /**
     * Order store
     * @return bool
     */
    public function orderStore()
    {
        if (!$this->validate()) {
            return false;
        }

        $this->generateSubdomain();

        $transaction = Yii::$app->db->beginTransaction();

        $order = $this->createOrder();

        if (!$order) {
            $this->addError('domain', Yii::t('app', 'error.store.can_not_order_store'));
            return false;
        }

        $invoice = $this->createInvoice($order);

        if (!$invoice) {
            $this->addError('domain', Yii::t('app', 'error.store.can_not_order_store'));
            return false;
        }

        $this->_invoiceCode = $invoice->code;

        $transaction->commit();

        MyActivityLog::log(MyActivityLog::E_ORDERS_CREATE_STORE_ORDER, $order->id, $order->id, UserHelper::getHash());

        return true;
    }
}
