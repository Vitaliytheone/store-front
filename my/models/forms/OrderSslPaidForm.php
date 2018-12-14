<?php
namespace my\models\forms;

use common\models\stores\StoreDomains;
use common\models\stores\Stores;
use my\helpers\UserHelper;
use common\models\panels\Customers;
use common\models\panels\InvoiceDetails;
use common\models\panels\Invoices;
use common\models\panels\MyActivityLog;
use common\models\panels\Orders;
use common\models\panels\Project;
use common\models\panels\SslCert;
use common\models\panels\SslCertItem;
use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class OrderSslForm
 * @package my\models\forms
 */
class OrderSslPaidForm extends Model
{
    const PROJECT_STORE_PREFIX = 's#';
    const PROJECT_PANEL_PREFIX = 'p#';

    public $pid;
    public $item_id;

    // Details
    public $admin_firstname;
    public $admin_lastname;
    public $admin_addressline1;
    public $admin_phone;
    public $admin_email;
    public $admin_city;
    public $admin_country;
    public $admin_postalcode;
    public $admin_region;
    public $admin_job;
    public $admin_organization;

    public $code;

    /**
     * @var Customers
     */
    public $_customer;

    /**
     * @var string - user IP address
     */
    protected $_ip;

    /**
     * @var Project[]
     */
    protected $_availablePanels;

    /**
     * @var Stores[]
     */
    protected $_availableStores;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['pid', 'item_id'], 'required'],
            [[
                'admin_firstname',
                'admin_lastname',
                'admin_phone',
                'admin_job',
                'admin_addressline1',
                'admin_city',
                'admin_region',
                'admin_postalcode',
                'admin_country',
                'admin_email'
            ], 'required'],
            [$this->getDetails(), 'filter', 'filter' => 'trim'],
            [['admin_email'], 'filter', 'filter' => function($value) {
                return trim(strtolower($value));
            }],
            [['admin_country'], 'in', 'range' => array_keys($this->getCountries())],
            [['admin_email'], 'email'],
            [['admin_phone'], 'integer'],
            [['item_id'], 'exist', 'skipOnError' => true, 'targetClass' => SslCertItem::class, 'targetAttribute' => ['item_id' => 'id']],
            ['item_id', 'checkAllowSslCertItem', 'skipOnError' => true,],
            [['pid'], 'isDomainAllowedValidator'],
        ];
    }

    /**
     * Check is passed cert item id in allowed list
     * @param $attribute
     * @param $params
     * @return bool
     */
    public function checkAllowSslCertItem($attribute, $params)
    {
        /** @var SslCertItem $sslCertItem */
        $sslCertItemId = $this->$attribute;

        $allowedSslItems = array_keys($this->getSslItems());

        if (!in_array($sslCertItemId, $allowedSslItems)) {
            $this->addError($attribute, Yii::t('app', 'error.ssl.can_not_order_ssl'));

            return false;
        }

        return true;
    }


    /**
     * Set customer
     * @param Customers $customer
     */
    public function setCustomer(Customers $customer)
    {
        $this->_customer = $customer;

        // В формах заказа нового домена/ssl убираем временно автозаполнение формы включая выбор страны
        /*$this->_ip = $this->_ip ? $this->_ip : Yii::$app->request->userIP;
        if ($this->_ip) {
            $geoIp = Yii::$app->geoip->ip($this->_ip);

            if ($geoIp && $geoIp->isoCode) {
                $this->admin_country = $geoIp->isoCode;
            }
        }

        $this->initLastSslDetails();*/
    }


    /**
     * Return current customer
     * @return Customers|null
     */
    public function getCustomer()
    {
        return $this->_customer;
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
     * Init previous order ssl details
     */
    protected function initLastSslDetails()
    {
        if (empty($this->_customer)) {
            return ;
        }

        $lastOrder = Orders::find()->andWhere([
            'cid' => $this->_customer->id,
            'item' => Orders::ITEM_BUY_SSL
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
     * Save method
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $project = $this->_getProject();

        if (empty($project)) {
            $this->addError('pid', Yii::t('app', 'error.ssl.can_not_order_ssl'));
            return false;
        }

        $certItem = SslCertItem::findOne($this->item_id);

        $orderModel = new Orders();
        $orderModel->date = time();
        $orderModel->cid = $this->_customer->id;
        $orderModel->item = Orders::ITEM_BUY_SSL;
        $orderModel->domain = $project->domain;
        $orderModel->ip = $this->_ip;
        $orderModel->setDetails([
            'pid' => $project->id,
            'project_type' => $project::getProjectType(),
            'domain' => $project->domain,
            'item_id' => $this->item_id,
            'details' => $this->getAttributes($this->getDetails()),
        ]);

        $transaction = Yii::$app->db->beginTransaction();

        if ($orderModel->save()) {
            $invoiceModel = new Invoices();
            $invoiceModel->total = $certItem->price;
            $invoiceModel->cid = $orderModel->cid;
            $invoiceModel->generateCode();
            $invoiceModel->daysExpired(Yii::$app->params['invoice.sslDuration']);

            if (!$invoiceModel->save()) {
                $this->addError('pid', Yii::t('app', 'error.ssl.can_not_order_ssl'));
                return false;
            }

            $invoiceDetailsModel = new InvoiceDetails();
            $invoiceDetailsModel->invoice_id = $invoiceModel->id;
            $invoiceDetailsModel->item_id = $orderModel->id;
            $invoiceDetailsModel->amount = $certItem->price;
            $invoiceDetailsModel->item = InvoiceDetails::ITEM_BUY_SSL;

            if (!$invoiceDetailsModel->save()) {
                $this->addError('pid', Yii::t('app', 'error.ssl.can_not_order_ssl'));
                return false;
            }
        } else {
            $this->addErrors($orderModel->getErrors());
            return false;
        }

        $transaction->commit();

        $this->code = $invoiceModel->code;

        MyActivityLog::log(MyActivityLog::E_ORDERS_CREATE_SSL_ORDER, $orderModel->id, $orderModel->id, UserHelper::getHash());

        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pid' => Yii::t('app', 'form.order_ssl.pid'),
            'item_id' => Yii::t('app', 'form.order_ssl.item_id'),
            'admin_firstname' => Yii::t('app', 'form.order_ssl.admin_firstname'),
            'admin_lastname' => Yii::t('app', 'form.order_ssl.admin_lastname'),
            'admin_addressline1' => Yii::t('app', 'form.order_ssl.admin_addressline1'),
            'admin_phone' => Yii::t('app', 'form.order_ssl.admin_phone'),
            'admin_email' => Yii::t('app', 'form.order_ssl.admin_email'),
            'admin_city' => Yii::t('app', 'form.order_ssl.admin_city'),
            'admin_country' => Yii::t('app', 'form.order_ssl.admin_country'),
            'admin_postalcode' => Yii::t('app', 'form.order_ssl.admin_postalcode'),
            'admin_region' => Yii::t('app', 'form.order_ssl.admin_region'),
            'admin_job' => Yii::t('app', 'form.order_ssl.admin_job'),
            'admin_organization' => Yii::t('app', 'form.order_ssl.admin_organization')
        ];
    }

    /**
     * Return `project` (Store or Project) by encoded type from form `pid`
     * @return null|Stores|Project
     * @throws Exception
     */
    private function _getProject()
    {
        if (empty($this->pid)) {
            return null;
        }

        if (strpos($this->pid, self::PROJECT_STORE_PREFIX) !== false) {
            $type = self::PROJECT_STORE_PREFIX;
        } elseif (strpos($this->pid, self::PROJECT_PANEL_PREFIX) !== false) {
            $type = self::PROJECT_PANEL_PREFIX;
        } else {
            throw new Exception('Unknown project type!');
        }

        $pid = str_replace([self::PROJECT_STORE_PREFIX, self::PROJECT_PANEL_PREFIX], '', $this->pid);

        $project = null;

        switch ($type) {
            case self::PROJECT_STORE_PREFIX :
                $project = Stores::findOne($pid);
            break;

            case self::PROJECT_PANEL_PREFIX :
                $project = Project::findOne($pid);
            break;
        }

        return $project;
    }

    /**
     * Get available panels
     * @return Project[]|[]
     */
    private function _getPanels()
    {
        if (!$this->_availablePanels) {
            $this->_availablePanels = Project::find()
                ->leftJoin('ssl_cert sc', 'project.site = sc.domain AND sc.status <> :status', [
                    ':status' => SslCert::STATUS_CANCELED
                ])
                ->leftJoin('orders o', 'project.site = o.domain AND o.status <> :orderStatus AND o.item = :orderItem', [
                    ':orderStatus' => Orders::STATUS_CANCELED,
                    ':orderItem' => Orders::ITEM_BUY_SSL
                ])
                ->andWhere([
                    'project.cid' => $this->_customer->id,
                    'project.act' => Project::STATUS_ACTIVE
                ])
                ->groupBy('project.id')
                ->having('COUNT(sc.id) = 0 AND COUNT(o.id) = 0')
                ->all();
        }

        return $this->_availablePanels;
    }

    /**
     * Return available stores domains
     * @return Stores[]|[]
     */
    private function _getStores()
    {
        if (!$this->_availableStores) {

            $allowedDomains = (new Query())
                ->select(['sd.domain'])
                ->from(['sd' => StoreDomains::tableName()])
                ->rightJoin(['s' => Stores::tableName()], 's.id = sd.store_id')
                ->andWhere(['s.customer_id' => $this->_customer->id])
                ->andWhere(['sd.type' => [
                        StoreDomains::DOMAIN_TYPE_DEFAULT,
                        StoreDomains::DOMAIN_TYPE_SUBDOMAIN,
                        StoreDomains::DOMAIN_TYPE_SOMMERCE
                    ]
                ])
                ->column();

            $this->_availableStores = Stores::find()
                ->leftJoin('ssl_cert sc', 'stores.domain = sc.domain AND sc.status <> :status', [
                    ':status' => SslCert::STATUS_CANCELED
                ])
                ->leftJoin('orders o', 'stores.domain = o.domain AND o.status <> :orderStatus AND o.item = :orderItem', [
                    ':orderStatus' => Orders::STATUS_CANCELED,
                    ':orderItem' => Orders::ITEM_BUY_SSL
                ])
                ->andWhere([
                    'stores.customer_id' => $this->_customer->id,
                    'stores.status' => Stores::STATUS_ACTIVE,
                    'stores.domain' => $allowedDomains,
                ])
                ->groupBy('stores.id')
                ->having('COUNT(sc.id) = 0 AND COUNT(o.id) = 0')
                ->all();
        }

        return $this->_availableStores;
    }

    /**
     * Return Stores & Panels & Child panels domains list
     * @param $group bool Is needs group projects domains by groups (stores domains, panels domains)
     * @return array;
     */
    public function getAllProjectsDomains($group = false)
    {
        $stores = $this->_getStores();
        $panels = $this->_getPanels();

        $storesDomains = $panelsDomains = $childPanelsDomains = [];

        /** @var Stores $store */
        foreach ($stores as $store) {
            if ($group) {
                $storesDomains[Yii::t('app', 'form.order_ssl.stores_group')][self::PROJECT_STORE_PREFIX.$store->id] = $store->getBaseDomain();
            } else {
                $storesDomains[$store->id] = $store->getBaseDomain();
            }
        }

        /** @var $panel Project */
        foreach ($panels as $panel) {
            if ($group) {
                if ($panel->child_panel) {
                    $childPanelsDomains[Yii::t('app', 'form.order_ssl.child_group')][self::PROJECT_PANEL_PREFIX.$panel->id] = $panel->getBaseDomain();
                } else {
                    $panelsDomains[Yii::t('app', 'form.order_ssl.panels_group')][self::PROJECT_PANEL_PREFIX.$panel->id] = $panel->getBaseDomain();
                }
            } else {
                $panelsDomains[$panel->id] = $panel->getBaseDomain();
            }
        }

        return array_merge($storesDomains, $panelsDomains, $childPanelsDomains);
    }

    /**
     * Get ssl certifications available items
     * @return array
     */
    public function getSslItems()
    {
        $products = [];

        foreach (SslCertItem::find()->all() as $item) {

            $allowIdList = $item->getAllow();

            if (empty($allowIdList) || (is_array($allowIdList) and in_array($this->getCustomer()->id, $allowIdList))) {
                $products[$item->id] = Yii::t('app', 'form.order_ssl.ssl_item', [
                    'price' => $item->price,
                    'name' => $item->name
                ]);
            }
        }

        return $products;
    }

    /**
     * Get details fields
     * @return array
     */
    public function getDetails()
    {
        return [
            'admin_firstname',
            'admin_lastname',
            'admin_phone',
            'admin_job',
            'admin_organization',
            'admin_addressline1',
            'admin_city',
            'admin_region',
            'admin_postalcode',
            'admin_country',
            'admin_email'
        ];
    }

    /**
     * Get countries
     * @return array
     */
    public function getCountries()
    {
        $countries = Yii::$app->params['countries'];

        unset($countries['IR']); // в my из создания ssl уберите Iran - thirteen
        
        return $countries;
    }

    /**
     * Check if the passed domain is allowed to be registered
     * @param $attribute
     * @return bool
     */
    public function isDomainAllowedValidator($attribute)
    {
        if ($this->hasErrors($attribute)) {
            return false;
        }

        $availableDomains = $this->getAllProjectsDomains();

        $project = $this->_getProject();

        if (!in_array($project->getBaseDomain(), $availableDomains)) {
            $this->addError($attribute, Yii::t('app', 'error.ssl.panel_is_not_available'));
            return false;
        }
    }
}
