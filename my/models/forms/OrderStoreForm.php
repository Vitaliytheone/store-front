<?php
namespace my\models\forms;

use common\models\panels\InvoiceDetails;
use common\models\panels\Invoices;
use common\models\panels\MyActivityLog;
use common\models\panels\Orders;
use common\models\stores\StoreAdminAuth;
use common\models\stores\StoreDomains;
use my\helpers\UserHelper;
use sommerce\helpers\ConfigHelper;
use Yii;
use common\models\panels\Auth;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use my\components\validators\OrderDomainValidator;
use yii\base\Exception;

/**
 * Class OrderStoreForm
 * @package my\models\forms
 */
class OrderStoreForm extends DomainForm
{
    public $store_currency;
    public $admin_email;
    public $admin_username;
    public $admin_password;
    public $confirm_password;

    /** @var string Generated invoice code */
    private $_invoiceCode;

    /** @var string */
    protected $_ip;

    /** @var string */
    protected $storeDomain;

    const SCENARIO_CREATE_STORE = 'store';

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return array_merge(
            parent::rules(), [
            [['admin_username', 'admin_email'], 'trim'],
            [['domain', 'store_currency', 'admin_username', 'admin_password', 'confirm_password'], 'required', 'except' => static::SCENARIO_CREATE_DOMAIN],
            ['store_currency', 'in', 'range' => array_keys($this->getCurrencies()), 'message' => Yii::t('app', 'error.store.bad_currency')],
            ['admin_email', 'email'],
            [['domain'], OrderDomainValidator::class, 'store' => true],
            ['admin_username', 'string', 'max' => 255],
            ['admin_password', 'string', 'min' => 5],
            ['admin_password', 'compare', 'compareAttribute' => 'confirm_password'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(
            parent::attributeLabels(), [
            'domain' => Yii::t('app', 'stores.order.form.label.store_domain'),
            'store_currency' => Yii::t('app', 'stores.order.form.label.store_currency'),
            'admin_email' => Yii::t('app', 'stores.order.form.label.admin_email'),
            'admin_username' => Yii::t('app', 'stores.order.form.label.admin_username'),
            'admin_password' => Yii::t('app', 'stores.order.form.label.admin_password'),
            'confirm_password' => Yii::t('app', 'stores.order.form.label.confirm_password'),
        ]);
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

        asort($currencies);

        $usd = ArrayHelper::getValue($currencies, 'USD');
        
        if ($usd) {
            unset($currencies['USD']);
            $currencies = array_merge(['USD' => $usd], $currencies);
        }

        return $currencies;
    }

    /**
     * @return bool
     * @throws Exception
     * @throws \yii\db\Exception
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();

        $invoiceModel = new Invoices();
        $invoiceModel->total = 0;
        $invoiceModel->cid = $this->_user->id;
        $invoiceModel->generateCode();
        $invoiceModel->daysExpired(Yii::$app->params['invoice.storeDuration']);

        if (!$invoiceModel->save()) {
            return false;
        }

        if (static::HAS_NOT_DOMAIN == $this->has_domain) {

            if (!$this->orderDomain($invoiceModel)) {
                $this->addError('domain', Yii::t('app', 'error.store.can_not_order_domain'));
                return false;
            }
        } else {
            $this->preparedDomain = $this->domain;
        }

        $this->generateSubdomain();

        $result = $this->orderStore($invoiceModel);

        if (!$result) {
            return false;
        }

        $invoiceModel->save(false);

        $transaction->commit();

        $this->_invoiceCode = $invoiceModel->code;

        return true;
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

        $subdomain = str_replace(' ', '-', strtolower(trim($this->domain)));
        $subdomain = preg_replace('/\.(\w+)$/', '', $subdomain);

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
            ->from(StoreDomains::tableName())
            ->column();

        $exitingDomains = array_merge($pendingOrders, $exitingStores);

        $subdomainPostfix = 2;

        $checkingSubdomain = $subdomain;

        // Check if store with same domain already exist
        do {
            $checkingDomain = $checkingSubdomain . '.' . $domain;

            $domainExist = in_array($checkingDomain, $exitingDomains);

            if ($domainExist) {
                $checkingSubdomain = $subdomain . $subdomainPostfix;
                $subdomainPostfix++;
            }

        } while ($domainExist);

        $this->storeDomain = $checkingDomain;

        return $this->storeDomain;
    }

    /**
     * Order store
     * @param $invoiceModel
     * @return bool
     * @throws \yii\db\Exception
     */
    public function orderStore(&$invoiceModel)
    {
        $this->scenario = static::SCENARIO_CREATE_STORE;

        $transaction = Yii::$app->db->beginTransaction();

        $model = new Orders();
        $model->cid = $this->_user->id;
        $model->item = Orders::ITEM_BUY_STORE;
        $model->domain = $this->storeDomain;
        $model->ip = $this->_ip;
        $model->setDetails([
            'username' => $this->admin_username,
            'password' => StoreAdminAuth::hashPassword($this->admin_password),
            'domain' => $this->storeDomain,
            'currency' => $this->store_currency,
            'admin_email' => $this->admin_email,
            'name' => $this->domain,
        ]);

        if ($model->save()) {
            $invoiceDetailsModel = new InvoiceDetails();
            $invoiceDetailsModel->invoice_id = $invoiceModel->id;
            $invoiceDetailsModel->item_id = $model->id;
            $invoiceDetailsModel->amount = Yii::$app->params['storeDeployPrice'];
            $invoiceDetailsModel->item = InvoiceDetails::ITEM_BUY_STORE;

            if (!$invoiceDetailsModel->save()) {
                $this->addError('domain', Yii::t('app', 'error.store.can_not_order_store'));
                return false;
            }
        } else {
            $this->addErrors($model->getErrors());
            return false;
        }

        $invoiceModel->total += $invoiceDetailsModel->amount;

        $transaction->commit();

        MyActivityLog::log(MyActivityLog::E_ORDERS_CREATE_STORE_ORDER, $model->id, $model->id, UserHelper::getHash());

        return true;
    }
}
