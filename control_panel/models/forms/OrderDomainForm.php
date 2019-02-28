<?php

namespace control_panel\models\forms;

use control_panel\components\validators\OrderDomainValidator;
use control_panel\helpers\DomainsHelper;
use control_panel\helpers\UserHelper;
use common\models\sommerces\Auth;
use common\models\sommerces\DomainZones;
use common\models\sommerces\InvoiceDetails;
use common\models\sommerces\Invoices;
use common\models\sommerces\MyActivityLog;
use common\models\sommerces\Orders;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class OrderDomainForm
 * @package control_panel\models\forms
 */
class OrderDomainForm extends Model
{
    public $domain;
    public $currency;
    public $username;
    public $password;
    public $password_confirm;

    public $code;
    public $preparedDomain;

    public $search_domain;
    public $domain_zone;

    public $domain_name;
    public $domain_protection;

    /**
     * @var Auth
     */
    protected $_user;

    /**
     * @var string - user IP address
     */
    protected $_ip;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['domain'], OrderDomainValidator::class],
            [['domain_zone'], 'integer'],
            [['search_domain'], 'string'],
            [['search_domain',], 'safe'],
        ];
    }

    /**
     * Set user IP
     * @param $ip
     */
    public function setIP($ip)
    {
        $this->_ip = $ip;
    }

    /**
     * Set user
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
     * Sign up method
     * @throws yii\base\UnknownClassException
     * @throws yii\db\Exception
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

        if (!$this->orderDomain($invoiceModel)) {
            $this->addError('domain', Yii::t('app', 'error.panel.can_not_order_domain'));
            return false;
        }

        $invoiceModel->save(false);

        $transaction->commit();

        $this->code = $invoiceModel->code;

        return true;
    }

    /**
     * Order domain
     * @param Invoices $invoiceModel
     * @return bool
     * @throws yii\base\UnknownClassException
     */
    protected function orderDomain(&$invoiceModel)
    {
        $model = new static();
        $model->attributes = $this->attributes;

        if (!$this->validate()) {
            return false;
        }

        $zone = DomainZones::findOne($this->domain_zone);

        if (!$zone) {
            return false;
        }

        $this->search_domain = trim($this->search_domain);

        if (false !== mb_strpos($this->search_domain, '.')) {
            $this->search_domain = explode('.', $this->search_domain)[0];
        }

        $this->domain = mb_strtolower($this->search_domain . $zone->zone);

        $this->preparedDomain = DomainsHelper::idnToAscii($this->domain);
        $contact_id = DomainsHelper::checkContactExist($zone->registrar, true);

        $model = new Orders();
        $model->cid = $this->_user->id;
        $model->item = Orders::ITEM_BUY_DOMAIN;
        $model->domain = $this->preparedDomain;
        $model->ip = $this->_ip;
        $model->setDetails([
            'zone' => $zone->id,
            'domain' => $this->domain,
            'domain_contact' => [
                'id' => $contact_id,
            ],
            'details' => [
                'domain_contact_id' => $contact_id,
                'domain_protection' => 1, // force domain privacy protect
            ]
        ]);

        if ($model->save()) {
            $invoiceDetailsModel = new InvoiceDetails();
            $invoiceDetailsModel->invoice_id = $invoiceModel->id;
            $invoiceDetailsModel->item_id = $model->id;
            $invoiceDetailsModel->amount = $zone->price_register;
            $invoiceDetailsModel->item = InvoiceDetails::ITEM_BUY_DOMAIN;

            if (!$invoiceDetailsModel->save()) {
                return false;
            }
        } else {
            $this->addErrors($model->getErrors());
            return false;
        }

        $invoiceModel->total += $invoiceDetailsModel->amount;

        MyActivityLog::log(MyActivityLog::E_ORDERS_CREATE_DOMAIN_ORDER, $model->id, $model->id, UserHelper::getHash());

        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'domain' => Yii::t('app', 'form.order_panel.domain'),
            'search_domain' => Yii::t('app', 'form.order_panel.search_domain'),
            'domain_lastname' => Yii::t('app', 'form.order_panel.domain_lastname'),
            'domain_firstname' => Yii::t('app', 'form.order_panel.domain_firstname'),
            'domain_email' => Yii::t('app', 'form.order_panel.domain_email'),
            'domain_company' => Yii::t('app', 'form.order_panel.domain_company'),
            'domain_address' => Yii::t('app', 'form.order_panel.domain_address'),
            'domain_city' => Yii::t('app', 'form.order_panel.domain_city'),
            'domain_postalcode' => Yii::t('app', 'form.order_panel.domain_postalcode'),
            'domain_state' => Yii::t('app', 'form.order_panel.domain_state'),
            'domain_country' => Yii::t('app', 'form.order_panel.domain_country'),
            'domain_phone' => Yii::t('app', 'form.order_panel.domain_phone'),
            'domain_fax' => Yii::t('app', 'form.order_panel.domain_fax'),
            'domain_protection' => Yii::t('app', 'form.order_panel.domain_protection'),
        ];
    }

    /**
     * Get currencies
     * @return mixed
     */
    public function getCurrencies()
    {
        $currencies = [];

        foreach (Yii::$app->params['currencies'] as $code => $currency) {
            $currencies[$code] = $currency['name'] . ' (' . $code . ')';
        }
        return $currencies;
    }

    /**
     * Get countries
     * @return array
     */
    public function getCountries()
    {
        return Yii::$app->params['countries'];
    }

    /**
     * Get domain zones
     * @param bool $registrar
     * @return array
     */
    public function getDomainZones($registrar = false): array
    {
        $zones = [];

        if ($registrar) {
            foreach (DomainZones::find()->all() as $zone) {
                $zones[$zone->id] = ['data-value'  => (int)DomainsHelper::checkContactExist($zone->registrar)];
            }
            return $zones;
        }

        foreach (DomainZones::find()->all() as $zone) {
            $zones[$zone->id] = $zone->zone . ' — $' . $zone->price_register;
        }

        return $zones;
    }

    /**
     * Init previous order order details
     */
    protected function initLastOrderDetails()
    {
        /**
         * @var Orders $lastOrder
         */
        $lastOrder = Orders::find()->andWhere([
            'cid' => $this->_user->id,
            'item' => Orders::ITEM_BUY_DOMAIN
        ])->orderBy([
            'id' => SORT_DESC
        ])->one();

        if (!$lastOrder) {
            return ;
        }

        $details = ArrayHelper::getValue($lastOrder->getDetails(), 'details', []);

        $this->setAttributes($details);
    }

    /**
     * Get domain value
     * @return string
     */
    public function getDomain(): string
    {
        return DomainsHelper::idnToUtf8($this->domain);
    }
}
