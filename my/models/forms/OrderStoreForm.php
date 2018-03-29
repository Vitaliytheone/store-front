<?php
namespace my\models\forms;

use common\models\panels\Invoices;
use common\models\panels\Orders;
use common\models\stores\Stores;
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
    public $subdomain;

    /** @var string Generated invoice code */
    public $code;

    /** @var Auth */
    private $_user;

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
    private function generateSubdomain()
    {
        $domain = Yii::$app->params['stores_domain'];

        if (empty($domain)) {
            throw new Exception('Bad config-params: store_domain not configured yet!');
        }

        $subdomain = str_replace(' ', '-', strtolower(trim($this->store_name)));

        $pendingOrders = (new Query())
            ->select('domain')
            ->from(Orders::tableName())
            ->andWhere(['status' => Orders::STATUS_PENDING])
            ->column();

        $exitingStores = (new Query())
            ->select('domain')
            ->from(Stores::tableName())
            ->column();

        $exitingDomains = array_merge($pendingOrders, $exitingStores);

        $domainExist = false;

        $subdomainPostfix = 1;

        // Check if store with same domain already exist
        do {
            $chekingDomain = $subdomain . '.' . $domain;

            $domainExist = in_array($chekingDomain, $exitingDomains);

            if ($domainExist) {
                $subdomain = $subdomain . $subdomainPostfix;
                $subdomainPostfix++;
            }

        } while ($domainExist);

        $this->subdomain = $subdomain . '.' . $domain;

        return $this->subdomain;
    }

    public function orderStore()
    {
        //  Далее создаем заказ, если у кастомера ранее не было магазина то,
        //  в orders.details добавляем параметр trial = 1
        //  и ставим orders.status = 1 и делаем редирект на список магазинов

        // Если магазин у кастомера уже есть то
        // ставим trial = 0, и создаем инвойс на создания магазина 35$ (надо занести сумму в конфиг)
        //  и делаем редирект на ранее созданый инвойс


    }

    /**
     * Create store invoice && order
     * @return bool|string
     */
    public function createOrder()
    {
        if (!$this->validate()) {
            return false;
        }

        $this->generateSubdomain();

        $transaction = Yii::$app->db->beginTransaction();

        $invoiceModel = new Invoices();
        $invoiceModel->total = 0;
        $invoiceModel->cid = $this->_user->id;
        $invoiceModel->generateCode();
        $invoiceModel->daysExpired(Yii::$app->params['invoice.storeDuration']);

        if (!$invoiceModel->save()) {
            return false;
        }

        if (!$this->orderStore($invoiceModel)) {
            $this->addError('domain', Yii::t('app', 'error.store.can_not_order_store'));
            return false;
        }

        $transaction->commit();
        $this->code = $invoiceModel->code;

        return $this->code;
    }



}
