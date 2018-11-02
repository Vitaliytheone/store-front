<?php
namespace my\models\forms;

use my\components\domains\Ahnames;
use my\components\validators\OrderLimitValidator;
use my\components\validators\PanelDomainValidator;
use my\helpers\ChildHelper;
use my\helpers\CurlHelper;
use my\helpers\DomainsHelper;
use my\helpers\UserHelper;
use common\models\panels\Auth;
use common\models\panels\DomainZones;
use common\models\panels\InvoiceDetails;
use common\models\panels\Invoices;
use common\models\panels\MyActivityLog;
use common\models\panels\OrderLogs;
use common\models\panels\Orders;
use common\models\panels\Project;
use common\models\panels\ProjectAdmin;
use Yii;
use yii\base\Model;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class CreateChildForm
 * @package my\models\forms
 */
class CreateChildForm extends Model
{
    public $has_domain = 1;

    public $domain;
    public $currency;
    public $provider;
    public $username;
    public $password;
    public $password_confirm;

    public $code;
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

    /**
     * @var Auth
     */
    protected $_user;

    /**
     * @var string - user IP address
     */
    protected $_ip;

    /**
     * @var array
     */
    protected $_providers;

    const HAS_DOMAIN = 1;
    const HAS_NOT_DOMAIN = 2;

    const SCENARIO_CREATE_PROJECT = 'project';
    const SCENARIO_CREATE_DOMAIN = 'domain';

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['domain'], OrderLimitValidator::class],
            [['domain', 'currency', 'username', 'password', 'password_confirm', 'provider'], 'required', 'except' => static::SCENARIO_CREATE_DOMAIN],
            [['currency'], 'in', 'range' => array_keys($this->getCurrencies()), 'message' => Yii::t('app', 'error.panel.bad_currency')],
            [['provider'], 'in', 'range' => array_keys($this->getProviders()), 'message' => Yii::t('app', 'error.panel.bad_provider')],
            [['domain'], PanelDomainValidator::class, 'child_panel' => true],
            ['password', 'compare', 'compareAttribute' => 'password_confirm'],
            [['username'], 'safe'],

            ['has_domain', 'in', 'range' => array_keys($this->getHasDomainsLabels()), 'message' => Yii::t('app', 'error.child_panel.bad_domain')],

            [['domain_firstname', 'domain_lastname', 'domain_email', 'domain_address', 'domain_city', 'domain_postalcode', 'domain_state', 'domain_country', 'domain_phone', 'domain_protection'], 'required', 'on' => static::SCENARIO_CREATE_DOMAIN],
            [['domain_zone'], 'integer'],
            [['search_domain'], 'string'],
            [['domain_email'], 'email'],
            [['domain_country'], 'in', 'range' => array_keys($this->getCountries()), 'message' => Yii::t('app', 'error.panel.bad_ccountry')],
            [['search_domain',], 'safe'],

            [['domain_fax'], 'integer', 'message' => Yii::t('app', 'error.domain.bad_fax')],
            [[
                'search_domain', 'domain_firstname', 'domain_lastname', 'domain_email', 'domain_company', 'domain_address', 'domain_city',
                'domain_postalcode', 'domain_state', 'domain_country', 'domain_phone', 'domain_protection',
            ], 'safe'],
        ];
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
        }

        $result = $this->orderPanel($invoiceModel);

        if (!$result) {
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
     */
    protected function orderDomain(&$invoiceModel)
    {
        $model = new static();
        $model->setUser($this->getUser());

        $model->scenario = static::SCENARIO_CREATE_DOMAIN;
        $model->attributes = $this->attributes;

        if (!$this->validate()) {
            return false;
        }

        $zone = DomainZones::findOne($this->domain_zone);

        if (!$zone) {
            return false;
        }

        $this->search_domain = trim($this->search_domain);

        if (false !== strpos($this->search_domain, '.')) {
            $this->search_domain = explode(".", $this->search_domain)[0];
        }

        $this->preparedDomain = mb_strtolower($this->search_domain . $zone->zone);

        if (!$this->isDomainAvailable($this->domain)) {
            return false;
        }

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
                'domain_protection' => $this->domain_protection,
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

        MyActivityLog::log( MyActivityLog::E_ORDERS_CREATE_DOMAIN_ORDER, $model->id, $model->id, UserHelper::getHash());

        return true;
    }

    /**
     * Order panel
     * @param Invoices $invoiceModel
     * @return bool
     */
    private function orderPanel(&$invoiceModel)
    {
        $this->scenario = static::SCENARIO_CREATE_PROJECT;

        $model = new Orders();
        $model->cid = $this->_user->id;
        $model->item = Orders::ITEM_BUY_CHILD_PANEL;
        $model->domain = $this->preparedDomain;
        $model->ip = $this->_ip;
        $model->setDetails([
            'provider' => $this->provider,
            'username' => $this->username,
            'password' => ProjectAdmin::hashPassword($this->password),
            'domain' => $this->domain,
            'clean_domain' => $this->preparedDomain,
            'currency' => $this->currency
        ]);

        if ($model->save()) {
            $invoiceDetailsModel = new InvoiceDetails();
            $invoiceDetailsModel->invoice_id = $invoiceModel->id;
            $invoiceDetailsModel->item_id = $model->id;
            $invoiceDetailsModel->amount = Yii::$app->params['childPanelDeployPrice'];
            $invoiceDetailsModel->item = InvoiceDetails::ITEM_BUY_CHILD_PANEL;

            if (!$invoiceDetailsModel->save()) {
                $this->addError('domain', Yii::t('app', 'error.panel.can_not_order_panel'));
                return false;
            }
        } else {
            $this->addErrors($model->getErrors());
            return false;
        }

        $invoiceModel->total += $invoiceDetailsModel->amount;

        MyActivityLog::log(MyActivityLog::E_ORDERS_CREATE_CHILD_PANEL_ORDER, $model->id, $model->id, UserHelper::getHash());

        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'domain' => Yii::t('app', 'form.order_child_panel.domain'),
            'provider' => Yii::t('app', 'form.order_child_panel.provider'),
            'currency' => Yii::t('app', 'form.order_child_panel.currency'),
            'username' => Yii::t('app', 'form.order_child_panel.username'),
            'password' => Yii::t('app', 'form.order_child_panel.password'),
            'password_confirm' => Yii::t('app', 'form.order_child_panel.password_confirm'),
            'search_domain' => Yii::t('app', 'form.order_child_panel.search_domain'),
            'domain_lastname' => Yii::t('app', 'form.order_child_panel.domain_lastname'),
            'domain_firstname' => Yii::t('app', 'form.order_child_panel.domain_firstname'),
            'domain_email' => Yii::t('app', 'form.order_child_panel.domain_email'),
            'domain_company' => Yii::t('app', 'form.order_child_panel.domain_company'),
            'domain_address' => Yii::t('app', 'form.order_child_panel.domain_address'),
            'domain_city' => Yii::t('app', 'form.order_child_panel.domain_city'),
            'domain_postalcode' => Yii::t('app', 'form.order_child_panel.domain_postalcode'),
            'domain_state' => Yii::t('app', 'form.order_child_panel.domain_state'),
            'domain_country' => Yii::t('app', 'form.order_child_panel.domain_country'),
            'domain_phone' => Yii::t('app', 'form.order_child_panel.domain_phone'),
            'domain_fax' => Yii::t('app', 'form.order_child_panel.domain_fax'),
            'domain_protection' => Yii::t('app', 'form.order_child_panel.domain_protection'),
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
            $currencies[$code] = Yii::t('app', $currency['name']) . ' (' . $code . ')';
        }

        return $currencies;
    }

    /**
     * Get providers
     * @return mixed
     */
    public function getProviders()
    {
        if (null !== $this->_providers) {
            return $this->_providers;
        }

        $this->_providers = ChildHelper::getProviders($this->_user->id, [
            Project::STATUS_ACTIVE
        ]);

        return $this->_providers;
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
    public function getDomain()
    {
        return DomainsHelper::idnToUtf8($this->domain);
    }

    /**
     * Get has domain labels
     * @return array
     */
    public function getHasDomainsLabels()
    {
        return [
            static::HAS_DOMAIN => Yii::t('app', 'form.order_child_panel.have_domain'),
            static::HAS_NOT_DOMAIN => Yii::t('app', 'form.order_child_panel.want_to_register_new_domain')
        ];
    }

    /**
     * Get domain zones
     * @return array
     */
    public function getDomainZones()
    {
        $zones = [];

        foreach (DomainZones::find()->all() as $zone) {
            $zones[$zone->id] = $zone->zone . ' — $' . $zone->price_register;
        }

        return $zones;
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
     * Is domain available
     * @param string $domain
     * @return bool
     */
    public function isDomainAvailable($domain)
    {
        if (empty($domain)) {
            return false;
        }

        $domain = mb_strtolower(trim($domain));

        $result = Ahnames::domainsCheck($domain);

        if (empty($result[$domain])) {
            return false;
        }

        $existsDomain = Orders::find()->andWhere([
            'domain' => DomainsHelper::idnToAscii($domain),
            'item' => Orders::ITEM_BUY_DOMAIN,
            'status' => [
                Orders::STATUS_PENDING,
                Orders::STATUS_PAID,
                Orders::STATUS_ADDED,
                Orders::STATUS_ERROR
            ]
        ])->exists();

        if ($existsDomain) {
            return false;
        }

        return true;
    }
}
