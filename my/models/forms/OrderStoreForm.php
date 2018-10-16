<?php
namespace my\models\forms;

use common\models\panels\InvoiceDetails;
use common\models\panels\Invoices;
use common\models\panels\MyActivityLog;
use common\models\panels\Orders;
use common\models\stores\StoreAdminAuth;
use common\models\stores\StoreAdmins;
use common\models\stores\StoreDomains;
use my\helpers\UserHelper;
use sommerce\helpers\ConfigHelper;
use Yii;
use yii\base\Exception;
use common\models\panels\Auth;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use my\components\validators\OrderDomainValidator;

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

    /** @var string */
    public $storeDomain;

    /** @var string Generated invoice code */
    private $_invoiceCode;

    /** @var Auth */
    protected $_user;

    /** @var string */
    private $_ip;

    const SCENARIO_CREATE_STORE = 'store';

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return array_merge(
            parent::rules(), [
            [['store_currency', 'admin_email', 'admin_username', 'admin_password', 'confirm_password'], 'required'],
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
            'domain' => Yii::t('app', 'stores.order.form.label.store_name'),
            'store_currency' => Yii::t('app', 'stores.order.form.label.store_currency'),
            'admin_email' => Yii::t('app', 'stores.order.form.label.admin_email'),
            'admin_username' => Yii::t('app', 'stores.order.form.label.admin_username'),
            'admin_password' => Yii::t('app', 'stores.order.form.label.admin_password'),
            'confirm_password' => Yii::t('app', 'stores.order.form.label.confirm_password'),
        ]);
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
        $invoiceModel->daysExpired(Yii::$app->params['invoice.domainDuration']);

        if (!$invoiceModel->save()) {
            return false;
        }

        if (static::HAS_NOT_DOMAIN == $this->has_domain) {

            if (!$this->orderDomain($invoiceModel)) {
                $this->addError('domain', Yii::t('app', 'error.panel.can_not_order_domain'));
                return false;
            }
        } else {
            $this->preparedDomain = $this->domain;
        }

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
     * Order store
     * @param Invoices $invoiceModel
     * @return bool
     */
    public function orderStore(&$invoiceModel)
    {
        $this->scenario = static::SCENARIO_CREATE_STORE;

        $model = new Orders();
        $model->cid = $this->_user->id;
        $model->item = Orders::ITEM_BUY_STORE;
        $model->domain = $this->preparedDomain;
        $model->ip = $this->_ip;
        $model->setDetails([
            'username' => $this->admin_username,
            'password' => StoreAdminAuth::hashPassword($this->admin_password),
            'domain' => $this->domain,
            'clean_domain' => $this->preparedDomain,
            'currency' => $this->store_currency,
            'admin_email' => $this->admin_email,
        ]);

        if ($model->save()) {
            $invoiceDetailsModel = new InvoiceDetails();
            $invoiceDetailsModel->invoice_id = $invoiceModel->id;
            $invoiceDetailsModel->item_id = $model->id;
            $invoiceDetailsModel->amount = Yii::$app->params['storeDeployPrice'];
            $invoiceDetailsModel->item = InvoiceDetails::ITEM_BUY_STORE;

            if (!$invoiceDetailsModel->save()) {
                $this->addError('domain', Yii::t('app', 'error.panel.can_not_order_panel'));
                return false;
            }
        } else {
            $this->addErrors($model->getErrors());
            return false;
        }

        $invoiceModel->total += $invoiceDetailsModel->amount;

        MyActivityLog::log(MyActivityLog::E_ORDERS_CREATE_STORE_ORDER, $model->id, $model->id, UserHelper::getHash());

        return true;
    }
}
