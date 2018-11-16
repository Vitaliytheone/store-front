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
class OrderSslForm extends Model
{
    const PROJECT_STORE_PREFIX = 's#';
    const PROJECT_PANEL_PREFIX = 'p#';

    public $pid;
    public $item_id;

    /**
     * @var Customers
     */
    public $_customer;

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
            [['item_id'], 'exist', 'skipOnError' => true, 'targetClass' => SslCertItem::class, 'targetAttribute' => ['item_id' => 'id']],
            ['item_id', 'checkAllowSslCertItem', 'skipOnError' => true,],
            [['pid'], 'isDomainAllowedValidator'],
        ];
    }

    /**
     * Check is passed cert product item id in allowed list
     * @param $attribute
     * @param $params
     * @return bool
     */
    public function checkAllowSslCertItem($attribute, $params)
    {
        if (!in_array($this->$attribute,  array_keys($this->getSslItems()))) {
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

        if (!$certItem) {
            throw new Exception('Cannot find SSL cert item!');
        }

        $project->dns_status = Project::DNS_STATUS_2;

        if (!$project->save(false)) {
            throw new Exception('Cannot update Panel!');
        }

        $order = new Orders();
        $order->cid = $this->_customer->id;
        $order->status = Orders::STATUS_PAID;
        $order->hide = Orders::HIDDEN_OFF;
        $order->processing = Orders::PROCESSING_NO;
        $order->domain = $project->domain;
        $order->item = Orders::ITEM_OBTAIN_LE_SSL;
        $order->setDetails([
            'pid' => $project->id,
            'project_type' => $project::getProjectType(),
            'domain' => $project->domain,
            'ssl_cert_item_id' => $certItem->id
        ]);

        if (!$order->save()) {
            throw new Exception('Cannot create new Letsencrypt SSL order!');
        }

        MyActivityLog::log(MyActivityLog::E_ORDERS_CREATE_SSL_ORDER, $order->id, $order->id, UserHelper::getHash());

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
                    ':orderItem' => Orders::ITEM_OBTAIN_LE_SSL,
                ])
                ->andWhere([
                    'project.cid' => $this->_customer->id,
                    'project.act' => Project::STATUS_ACTIVE,
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
                    StoreDomains::DOMAIN_TYPE_SUBDOMAIN]
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
     * @param $panelsOnly boolean Return panel projects only if true
     * @param $group bool Is needs group projects domains by groups (stores domains, panels domains)
     * @return array;
     */
    public function getAllProjectsDomains($group = false, $panelsOnly = true)
    {
        $storesDomains = $panelsDomains = $childPanelsDomains = [];

        $panels = $this->_getPanels();

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

        if ($panelsOnly) {
            return array_merge($panelsDomains, $childPanelsDomains);
        }

        $stores = $this->_getStores();

        /** @var Stores $store */
        foreach ($stores as $store) {
            if ($group) {
                $storesDomains[Yii::t('app', 'form.order_ssl.stores_group')][self::PROJECT_STORE_PREFIX.$store->id] = $store->getBaseDomain();
            } else {
                $storesDomains[$store->id] = $store->getBaseDomain();
            }
        }

        return array_merge($storesDomains, $panelsDomains, $childPanelsDomains);
    }

    /**
     * Get ssl certifications available items
     * @param $freeOnly boolean Return only free Ssl products if true
     * @return array
     */
    public function getSslItems($freeOnly = true)
    {
        $products = [];

        $sslCertItems = SslCertItem::find();

        if ($freeOnly) {
            $sslCertItems->andWhere([
                'provider' => SslCertItem::PROVIDER_LETSENCRYPT,
                'product_id' => [SslCertItem::PRODUCT_ID_LETSENCRYPT_BASE],
            ]);
        }

        foreach ($sslCertItems->all() as $item) {

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
