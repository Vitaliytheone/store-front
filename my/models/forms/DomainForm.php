<?php

namespace my\models\forms;

use my\components\validators\OrderDomainValidator;
use yii\base\Model;
use Yii;
use my\components\validators\OrderLimitValidator;
use common\models\panels\Invoices;
use common\models\panels\DomainZones;
use my\helpers\DomainsHelper;
use common\models\panels\Orders;
use common\models\panels\InvoiceDetails;
use common\models\panels\MyActivityLog;
use my\helpers\UserHelper;
use common\models\panels\Auth;
use yii\helpers\ArrayHelper;

/**
 * Class DomainForm
 * @package my\models\forms
 */
class DomainForm extends Model
{
    public $has_domain = 1;

    public $domain;
    public $preparedDomain;

    public $search_domain;
    public $domain_zone;

    public $domain_name;
    public $domain_firstname;
    public $domain_lastname;
    public $domain_email;
    public $domain_company;
    public $domain_address;
    public $domain_city;
    public $domain_postalcode;
    public $domain_state;
    public $domain_country;
    public $domain_phone;
    public $domain_fax;
    public $domain_protection;

    public const HAS_DOMAIN = 1;
    public const HAS_NOT_DOMAIN = 2;
    public const HAS_SUBDOMAIN = 3;

    const SCENARIO_CREATE_DOMAIN = 'domain';

    /**
     * @var Auth
     */
    protected $_user;

    public function rules()
    {
        return [
            [['domain'], OrderLimitValidator::class],
            [['domain'], OrderDomainValidator::class],
            [['domain_country'], 'in', 'range' => array_keys($this->getCountries()), 'message' => Yii::t('app', 'error.panel.bad_country')],
            [['domain_zone'], 'integer'],
            [['domain_email'], 'email'],
            ['has_domain', 'in', 'range' => array_keys($this->getHasDomainsLabels()), 'message' => Yii::t('app', 'error.panel.bad_domain')],
            [['domain_fax'], 'integer', 'message' => Yii::t('app', 'error.domain.bad_fax')],
            [['search_domain'], 'string'],
            [[
                'search_domain', 'domain_firstname', 'domain_lastname', 'domain_email', 'domain_company', 'domain_address', 'domain_city',
                'domain_postalcode', 'domain_state', 'domain_country', 'domain_phone', 'domain_protection',
            ], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
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
     * Set user
     * @param Auth $user
     */
    public function setUser(Auth $user)
    {
        $this->_user = $user;

        // В формах заказа нового домена/ssl убираем временно автозаполнение формы включая выбор страны
        /*$this->_ip = $this->_ip ? $this->_ip : Yii::$app->request->userIP;
        if ($this->_ip) {
            $geoIp = Yii::$app->geoip->ip($this->_ip);

            if ($geoIp && $geoIp->isoCode) {
                $this->domain_country = $geoIp->isoCode;
            }
        }

        $this->initLastOrderDetails();*/
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
     * Get countries
     * @return array
     */
    public function getCountries()
    {
        return Yii::$app->params['countries'];
    }

    /**
     * Get has domain labels
     * @return array
     */
    public function getHasDomainsLabels()
    {
        return [
            static::HAS_DOMAIN => Yii::t('app', 'form.order_panel.have_domain'),
            static::HAS_NOT_DOMAIN => Yii::t('app', 'form.order_panel.want_to_register_new_domain'),
            static::HAS_SUBDOMAIN => Yii::t('app', 'form.order_panel.want_use_on_subdomain'),
        ];
    }

    /**
     * Is validate domain
     * @return bool
     */
    public function isValidateDomain()
    {
        if (static::HAS_NOT_DOMAIN == $this->has_domain) {
            return false;
        }

        return true;
    }

    /**
     * Get domain value
     * @return string
     */
    public function getDomain(): string
    {
        return DomainsHelper::idnToUtf8($this->domain);
    }

    /**
     * Order domain
     * @param Invoices $invoiceModel
     * @return bool
     */
    protected function orderDomain(&$invoiceModel)
    {
        $model = new static();
        $model->scenario = static::SCENARIO_CREATE_DOMAIN;
        $model->attributes = $this->attributes;

        if (!$this->validate()) {
            return false;
        }

        $zone = DomainZones::findOne($this->domain_zone);

        if (!$zone) {
            return false;
        }

        if (!$this->validate()) {
            return false;
        }


        if (false !== mb_strpos($this->search_domain, '.')) {
            $this->search_domain = explode('.', $this->search_domain)[0];
        }

        $this->domain = mb_strtolower($this->search_domain . $zone->zone);

        $this->preparedDomain = DomainsHelper::idnToAscii($this->domain);

        $model = new Orders();
        $model->cid = $this->_user->id;
        $model->item = Orders::ITEM_BUY_DOMAIN;
        $model->domain = $this->preparedDomain;
        $model->ip = $this->_ip;
        $model->setDetails([
            'zone' => $zone->id,
            'domain' => $this->domain,
            'details' => [
                'domain_firstname' => $this->domain_firstname,
                'domain_lastname' => $this->domain_lastname,
                'domain_email' => $this->domain_email,
                'domain_company' => $this->domain_company,
                'domain_address' => $this->domain_address,
                'domain_city' => $this->domain_city,
                'domain_postalcode' => $this->domain_postalcode,
                'domain_state' => $this->domain_state,
                'domain_country' => $this->domain_country,
                'domain_phone' => $this->domain_phone,
                'domain_fax' => $this->domain_fax,
                'domain_protection' => 1, // force domain privacy protect - old -- $this->domain_protection,
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
}
