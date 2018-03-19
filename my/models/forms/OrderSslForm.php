<?php
namespace my\models\forms;

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
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class OrderSslForm
 * @package my\models\forms
 */
class OrderSslForm extends Model
{
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
    protected $_availableProjects;

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
            [['item_id'], 'exist', 'skipOnError' => true, 'targetClass' => SslCertItem::className(), 'targetAttribute' => ['item_id' => 'id']],
            [['pid'], 'uniqCertValidation'],
        ];
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

        $certItem = SslCertItem::findOne($this->item_id);
        $project = Project::findOne($this->pid);

        $model = new Orders();
        $model->date = time();
        $model->ip = Yii::$app->request->userIP;
        $model->cid = $this->_customer->id;
        $model->item = Orders::ITEM_BUY_SSL;
        $model->domain = $project->site;
        $model->ip = $this->_ip;
        $model->setDetails([
            'pid' => $this->pid,
            'domain' => $project->site,
            'item_id' => $this->item_id,
            'details' => $this->getAttributes($this->getDetails()),
        ]);

        $transaction = Yii::$app->db->beginTransaction();

        if ($model->save()) {
            $invoiceModel = new Invoices();
            $invoiceModel->total = $certItem->price;
            $invoiceModel->cid = $model->cid;
            $invoiceModel->generateCode();
            $invoiceModel->daysExpired(Yii::$app->params['invoice.sslDuration']);

            if (!$invoiceModel->save()) {
                $this->addError('pid', Yii::t('app', 'error.ssl.can_not_order_ssl'));
                return false;
            }

            $invoiceDetailsModel = new InvoiceDetails();
            $invoiceDetailsModel->invoice_id = $invoiceModel->id;
            $invoiceDetailsModel->item_id = $model->id;
            $invoiceDetailsModel->amount = $certItem->price;
            $invoiceDetailsModel->item = InvoiceDetails::ITEM_BUY_SSL;

            if (!$invoiceDetailsModel->save()) {
                $this->addError('pid', Yii::t('app', 'error.ssl.can_not_order_ssl'));
                return false;
            }
        } else {
            $this->addErrors($model->getErrors());
            return false;
        }

        $transaction->commit();

        $this->code = $invoiceModel->code;

        MyActivityLog::log(MyActivityLog::E_ORDERS_CREATE_SSL_ORDER, $model->id, $model->id, UserHelper::getHash());

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
     * Get available projects
     * @param boolean $group
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getProjects($group = false)
    {
        if (!$this->_availableProjects) {
            $this->_availableProjects = Project::find()
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

        $availableProjects = $panels = $child = [];

        /**
         * @var $availableProject Project
         */
        foreach ($this->_availableProjects as $availableProject) {
            if ($group) {
                if ($availableProject->child_panel) {
                    $child[$availableProject->id] = $availableProject->getSite();
                } else {
                    $panels[$availableProject->id] = $availableProject->getSite();
                }
            } else {
                $availableProjects[$availableProject->id] = $availableProject->getSite();
            }
        }

        if ($group) {
            $availableProjects = [];

            if (!empty($panels)) {
                $availableProjects[Yii::t('app', 'form.order_ssl.panels_group')] = $panels;
            }

            if (!empty($child)) {
                $availableProjects[Yii::t('app', 'form.order_ssl.child_group')] = $child;
            }
        }

        return $availableProjects;
    }

    /**
     * Get ssl certifications available items
     * @return array
     */
    public function getSslItems()
    {
        $products = [];

        foreach (SslCertItem::find()->all() as $item) {
            $products[$item->id] = Yii::t('app', 'form.order_ssl.ssl_item', [
                'price' => $item->price,
                'name' => $item->name
            ]);
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
     * Check selected panel
     * @param $attribute
     * @return bool
     */
    public function uniqCertValidation($attribute)
    {
        if ($this->hasErrors($attribute)) {
            return false;
        }

        $availableProjects = $this->getProjects();

        $project = Project::findOne($this->pid);

        if (!in_array($project->getSite(), $availableProjects)) {
            $this->addError($attribute, Yii::t('app', 'error.ssl.panel_is_not_available'));
            return false;
        }
    }
}
