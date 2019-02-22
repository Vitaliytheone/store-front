<?php

namespace common\models\panels;

use common\components\behaviors\CustomersCountersBehavior;
use common\helpers\CurrencyHelper;
use common\helpers\NginxHelper;
use common\models\common\ProjectInterface;
use common\models\panels\services\GetParentPanelService;
use common\helpers\DnsHelper;
use my\helpers\DomainsHelper;
use my\helpers\ExpiryHelper;
use common\helpers\DbHelper;
use common\helpers\SuperTaskHelper;
use my\mail\mailers\CreatedProject;
use my\mail\mailers\PanelFrozen;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\components\traits\UnixTimeFormatTrait;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%project}}".
 *
 * @property int $id
 * @property int $cid
 * @property string $site
 * @property string $name
 * @property int $subdomain
 * @property string $skype
 * @property int $expired
 * @property int $date
 * @property int $act 0 - frozen; 1 - active; 2 - terminated; 3 - pending; 4 - canceled
 * @property int $hide
 * @property int $child_panel
 * @property int $provider_id
 * @property string $folder
 * @property string $folder_content
 * @property int $theme
 * @property string $theme_custom
 * @property string $theme_default
 * @property int $ssl
 * @property string $theme_path
 * @property int $rtl
 * @property int $utc
 * @property string $db
 * @property string $apikey
 * @property int $orders
 * @property int $plan
 * @property int $tariff
 * @property int $last_count
 * @property int $current_count
 * @property int $forecast_count
 * @property int $paypal
 * @property int $type
 * @property string $lang
 * @property int $language_id
 * @property int $currency
 * @property int $seo
 * @property int $comments
 * @property int $mentions
 * @property int $mentions_wo_hashtag
 * @property int $mentions_custom
 * @property int $mentions_hashtag
 * @property int $mentions_follower
 * @property int $mentions_likes
 * @property int $writing
 * @property int $userpass
 * @property int $validation
 * @property int $start_count
 * @property int $getstatus
 * @property int $custom
 * @property string $custom_header
 * @property string $custom_footer
 * @property int $hash_method (0 - md5, 1 - bcrypt)
 * @property string $seo_title
 * @property string $seo_desc
 * @property string $seo_key
 * @property int $package
 * @property int $captcha 0 - on, 1 - off
 * @property string $logo
 * @property string $favicon
 * @property int $public_service_list
 * @property int $ticket_system
 * @property int $registration_page
 * @property int $terms_checkbox
 * @property int $skype_field
 * @property int $service_description
 * @property int $service_categories
 * @property int $last_payment
 * @property int $ticket_per_user
 * @property int $auto_order
 * @property int $drip_feed 0 - on, 1 - off
 * @property int $currency_format
 * @property string $currency_code
 * @property int $tasks
 * @property int $name_fields
 * @property int $name_modal
 * @property string $notification_email
 * @property int $forgot_password 0 - disabled, 1 - enabled
 * @property int $no_invoice 0 - disabled, 1 - enabled
 * @property int $js_error_tracking 0 - disabled, 1 - enabled
 * @property int $refiller 0    0 - not supported, 1 - supported
 * @property string $whois_lookup Json domain data
 * @property string $nameservers Json domain nameservers data
 * @property int $dns_checked_at Last dns-check timestamp
 * @property int $dns_status dns-check result: null-неизвестно, 0-не наши ns, 1-наш ns
 * @property int $no_referral 0 - disabled, 1 - enabled
 * @property string $paypal_fraud_settings Panel PayPal payments fraud system settings
 * @property string $affiliate_minimum_payout
 * @property string $affiliate_commission_rate
 * @property int $affiliate_approve_payouts 0 - manual, 1 - auto
 * @property int $affiliate_system 0 - off, 1 - active
 *
 * @property PanelDomains[] $panelDomains
 * @property SslValidation[] $sslValidations
 * @property Tariff $tariffDetails
 * @property Tariff $newTariffDetails
 * @property Customers $customer
 * @property UserServices[] $userServices
 * @property string $domain
 */
class Project extends ActiveRecord implements ProjectInterface
{
    const STATUS_FROZEN = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_TERMINATED = 2;
    const STATUS_PENDING = 3;
    const STATUS_CANCELED = 4;

    const HIDDEN_ON = 1;
    const HIDDEN_OFF = 0;

    const DRIP_FEED_ON = 1;
    const DRIP_FEED_OFF = 0;

    const DEFAULT_CHILD_TARIFF = -1;
    const DEFAULT_TARIFF = 1;

    const HASH_METHOD_MD5 = 0;
    const HASH_METHOD_BCRYPT = 1;

    const FORGOT_PASSWORD_ENABLED = 1;
    const FORGOT_PASSWORD_DISABLED = 0;

    const NO_INVOICE_ENABLED = 1;
    const NO_INVOICE_DISABLED = 0;

    const CAN_ACCEPT_PAYPAL_FRAUD_LEVEL_HIGH = 'accept_high';
    const CAN_ACCEPT_PAYPAL_FRAUD_LEVEL_CRITICAL = 'accept_critical';
    
    const AFFILIATE_SYSTEM_ENABLED = 1;
    const AFFILIATE_SYSTEM_DISABLED = 0;

    /** @var bool */
    private $isForeignSubdomain = false;
    
    use UnixTimeFormatTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return DB_PANELS . '.project';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cid', 'site'], 'required'],
            [[
                'cid', 'subdomain', 'expired', 'date', 'act', 'hide', 'theme', 'ssl', 'utc', 'plan', 'tariff', 'last_count', 'current_count',
                'forecast_count', 'paypal', 'type', 'currency', 'seo', 'comments', 'mentions', 'mentions_wo_hashtag', 'mentions_custom',
                'mentions_hashtag', 'mentions_follower', 'mentions_likes', 'writing', 'validation', 'start_count', 'getstatus', 'custom',
                'package', 'captcha', 'public_service_list', 'ticket_system', 'registration_page', 'terms_checkbox', 'skype_field', 'service_description',
                'service_categories', 'last_payment', 'ticket_per_user', 'auto_order', 'drip_feed', 'child_panel', 'provider_id', 'hash_method', 'forgot_password',
                'name_fields', 'name_modal', 'no_invoice', 'no_referral', 'rtl', 'orders', 'language_id', 'currency_format', 'tasks', 'js_error_tracking', 'refiller',
                'affiliate_approve_payouts', 'affiliate_system'
            ], 'integer'],
            [['affiliate_minimum_payout', 'affiliate_commission_rate'], 'number'],
            [['site', 'name', 'skype'], 'string', 'max' => 1000],
            [['theme_custom', 'theme_default', 'db', 'logo', 'favicon', 'notification_email'], 'string', 'max' => 300],
            [['theme_path'], 'string', 'max' => 500],
            [['apikey'], 'string', 'max' => 64],
            [['lang'], 'string', 'max' => 32],
            [['folder'], 'string', 'max' => 6],
            [['currency_code'], 'string', 'max' => 3],
            [['folder_content', 'paypal_fraud_settings'], 'string'],
            [['custom_header', 'custom_footer', 'seo_title', 'seo_desc', 'seo_key'], 'string', 'max' => 3000],
            [['drip_feed'], 'default', 'value' => static::DRIP_FEED_OFF],
            [['notification_email'], 'default', 'value' => ' '],
            [['whois_lookup', 'nameservers'], 'string'],
            [['dns_checked_at', 'dns_status'], 'integer'],
            ['paypal_fraud_settings', 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'cid' => Yii::t('app', 'Cid'),
            'site' => Yii::t('app', 'Site'),
            'name' => Yii::t('app', 'Name'),
            'subdomain' => Yii::t('app', 'Subdomain'),
            'skype' => Yii::t('app', 'Skype'),
            'expired' => Yii::t('app', 'Expired'),
            'date' => Yii::t('app', 'Date'),
            'act' => Yii::t('app', 'Status'),
            'hide' => Yii::t('app', 'Hidden'),
            'child_panel' => Yii::t('app', 'Child Panel'),
            'provider_id' => Yii::t('app', 'Provider ID'),
            'folder' => Yii::t('app', 'Folder'),
            'folder_content' => Yii::t('app', 'Folder Content'),
            'theme' => Yii::t('app', 'Theme'),
            'theme_custom' => Yii::t('app', 'Theme Custom'),
            'theme_default' => Yii::t('app', 'Theme Default'),
            'theme_path' => Yii::t('app', 'Theme Path'),
            'rtl' => Yii::t('app', 'Rtl'),
            'utc' => Yii::t('app', 'Utc'),
            'db' => Yii::t('app', 'Db'),
            'apikey' => Yii::t('app', 'Apikey'),
            'orders' => Yii::t('app', 'Orders'),
            'plan' => Yii::t('app', 'Plan'),
            'tariff' => Yii::t('app', 'Tariff'),
            'last_count' => Yii::t('app', 'Last Count'),
            'current_count' => Yii::t('app', 'Current Count'),
            'forecast_count' => Yii::t('app', 'Forecast Count'),
            'paypal' => Yii::t('app', 'Paypal'),
            'type' => Yii::t('app', 'Type'),
            'lang' => Yii::t('app', 'Lang'),
            'language_id' => Yii::t('app', 'Language ID'),
            'currency' => Yii::t('app', 'Currency'),
            'seo' => Yii::t('app', 'Seo'),
            'comments' => Yii::t('app', 'Comments'),
            'mentions' => Yii::t('app', 'Mentions'),
            'mentions_wo_hashtag' => Yii::t('app', 'Mentions Wo Hashtag'),
            'mentions_custom' => Yii::t('app', 'Mentions Custom'),
            'mentions_hashtag' => Yii::t('app', 'Mentions Hashtag'),
            'mentions_follower' => Yii::t('app', 'Mentions Follower'),
            'mentions_likes' => Yii::t('app', 'Mentions Likes'),
            'writing' => Yii::t('app', 'Writing'),
            'validation' => Yii::t('app', 'Validation'),
            'start_count' => Yii::t('app', 'Start Count'),
            'getstatus' => Yii::t('app', 'Getstatus'),
            'custom' => Yii::t('app', 'Custom'),
            'custom_header' => Yii::t('app', 'Custom Header'),
            'custom_footer' => Yii::t('app', 'Custom Footer'),
            'hash_method' => Yii::t('app', 'Hash Method'),
            'seo_title' => Yii::t('app', 'Seo Title'),
            'seo_desc' => Yii::t('app', 'Seo Desc'),
            'seo_key' => Yii::t('app', 'Seo Key'),
            'package' => Yii::t('app', 'Package'),
            'captcha' => Yii::t('app', 'Captcha'),
            'logo' => Yii::t('app', 'Logo'),
            'favicon' => Yii::t('app', 'Favicon'),
            'public_service_list' => Yii::t('app', 'Public Service List'),
            'ticket_system' => Yii::t('app', 'Ticket System'),
            'registration_page' => Yii::t('app', 'Registration Page'),
            'terms_checkbox' => Yii::t('app', 'Terms Checkbox'),
            'skype_field' => Yii::t('app', 'Skype Field'),
            'service_description' => Yii::t('app', 'Service Description'),
            'service_categories' => Yii::t('app', 'Service Categories'),
            'last_payment' => Yii::t('app', 'Last Payment'),
            'ticket_per_user' => Yii::t('app', 'Ticket Per User'),
            'auto_order' => Yii::t('app', 'Auto Order'),
            'ssl' => Yii::t('app', 'Ssl'),
            'drip_feed' => Yii::t('app', 'Drip Feed'),
            'name_fields' => Yii::t('app', 'Name fields'),
            'name_modal' => Yii::t('app', 'Name modal'),
            'notification_email' => Yii::t('app', 'Notification email'),
            'forgot_password' => Yii::t('app', 'Forgot password'),
            'no_invoice' => Yii::t('app', 'No Invoice'),
            'currency_format' => Yii::t('app', 'Currency Format'),
            'currency_code' => Yii::t('app', 'Currency code'),
            'tasks' => Yii::t('app', 'Tasks'),
            'js_error_tracking' => Yii::t('app', 'JS Error Tracking'),
            'no_referral' => Yii::t('app', 'No Referral'),
            'paypal_fraud_settings' => Yii::t('app', 'PayPal Fraud Settings'),
            'refiller' => Yii::t('app', 'Refiller'),
            'whois_lookup' => Yii::t('app', 'Who is'),
            'nameservers' => Yii::t('app', 'Nameservers'),
            'dns_checked_at' => Yii::t('app', 'Dns checked at'),
            'dns_status' => Yii::t('app', 'Dns status'),
        ];
    }

    /**
     * @param bool $isForeign
     */
    public function setForeignSubdomain(bool $isForeign)
    {
        $this->isForeignSubdomain = $isForeign;
    }

    /**
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function getParent()
    {
        return Yii::$container->get(GetParentPanelService::class, [$this->provider_id])->get();
    }
    
    /**
     * Get statuses labels
     * @return array
     */
    public static function getStatuses()
    {
        return [
            static::STATUS_FROZEN => Yii::t('app', 'project.status.frozen'),
            static::STATUS_ACTIVE => Yii::t('app', 'project.status.active'),
            static::STATUS_TERMINATED => Yii::t('app', 'project.status.terminated'),
            static::STATUS_PENDING => Yii::t('app', 'project.status.pending'),
            static::STATUS_CANCELED => Yii::t('app', 'project.status.canceled')
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getProjectType()
    {
        return ProjectInterface::PROJECT_TYPE_PANEL;
    }

    /**
     * @inheritdoc
     */
    public function getDomain()
    {
        return $this->site;
    }

    /**
     * @inheritdoc
     */
    public function getBaseDomain()
    {
        return $this->getSite();
    }

    /**
     * @inheritdoc
     */
    public function setSslMode($isActive)
    {
        $this->ssl = $isActive;
    }

    /**
     * @inheritdoc
     */
    public function getBaseSite()
    {
        return ($this->ssl == ProjectInterface::SSL_MODE_ON ? 'https://' : 'http://') . $this->getBaseDomain();
    }

    /**
     * Get act status name
     * @return string
     */
    public function getActName()
    {
        return static::getActNameString($this->act);
    }

    /**
     * Get status string name by status
     * @param int $act
     * @return mixed
     */
    public static function getActNameString($act)
    {
        return ArrayHelper::getValue(static::getStatuses(), $act, '');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPanelDomains()
    {
        return $this->hasMany(PanelDomains::class, ['panel_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSslValidations()
    {
        return $this->hasMany(SslValidation::class, ['pid' => 'id', 'ptype' => static::getProjectType()]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserServices()
    {
        return $this->hasMany(UserServices::class, ['panel_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTariffDetails()
    {
        return $this->hasOne(Tariff::class, ['id' => 'plan']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNewTariffDetails()
    {
        return $this->hasOne(Tariff::class, ['id' => 'tariff']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customers::class, ['id' => 'cid']);
    }

    /**
     * Create panel db name
     */
    public function generateDbName()
    {
        $dbName = "panel_" . strtolower(str_replace(['.', '-'], '', $this->site));

        if (!DbHelper::existDatabase($dbName)) {
            $this->db = $dbName;
            return;
        }

        $dbName = $dbName . '_';
        for ($i = 1; $i < 100; $i++) {
            $this->db = $dbName . $i;
            if (!DbHelper::existDatabase($this->db)) {
                return;
            }
        }
    }

    /**
     * Generate expired
     * @return bool
     */
    public function generateExpired()
    {
        $this->expired = ExpiryHelper::month(time());
    }

    /**
     * Update expired
     * @return bool
     */
    public function updateExpired()
    {
        if ($this->act == static::STATUS_ACTIVE) {
            $time = $this->expired;
        } else {
            $time = time();
        }

        $this->act = static::STATUS_ACTIVE;
        $this->expired = ExpiryHelper::month($time);

        return $this->save(false);
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        $this->currency = CurrencyHelper::getCurrencyIdByCode($this->currency_code);

        return true;
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'date',
                ],
                'value' => function() {
                    return time();
                },
            ],
            [
                'class' => CustomersCountersBehavior::class,
                'column' => function() {
                    return (bool)$this->child_panel ? 'child_panels' : 'panels';
                },
                'customerId' => function() {
                    return $this->cid;
                },
            ],
        ];
    }

    /**
     * Get currency code
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->currency_code;
    }

    /**
     * Change project status
     * @param int $status
     * @return bool
     * @throws \yii\base\Exception
     */
    public function changeStatus($status)
    {
        switch ($status) {
            case static::STATUS_ACTIVE:
                if (static::STATUS_FROZEN == $this->act) {
                    $this->act = static::STATUS_ACTIVE;
                }

            break;

            case static::STATUS_FROZEN:
                if (static::STATUS_ACTIVE == $this->act) {
                    $this->act = static::STATUS_FROZEN;

                    $mail = new PanelFrozen([
                        'project' => $this
                    ]);
                    $mail->send();

                } else if (static::STATUS_TERMINATED == $this->act) {
                    if ($this->restore()) {
                        $this->act = static::STATUS_FROZEN;

                        $mail = new PanelFrozen([
                            'project' => $this
                        ]);
                        $mail->send();
                    }
                }
            break;

            case static::STATUS_TERMINATED:
                if (static::STATUS_FROZEN == $this->act) {
                    $this->act = static::STATUS_TERMINATED;
                    $this->terminate(true);
                }
            break;
        }

        return $this->save(false);
    }

    /**
     * Restore project
     * @return boolean
     * @throws \yii\base\Exception
     */
    public function restore()
    {
        if (!$this->subdomain) {
            // Добавляем основной домен
            $this->enableMainDomain();
        }

        SuperTaskHelper::setTasksNginx($this);

        $invoiceModel = new Invoices();
        $invoiceModel->total = $this->child_panel == 1 ? Yii::$app->params['childPanelDeployPrice'] : Yii::$app->params['panelDeployPrice'];
        $invoiceModel->cid = $this->cid;
        $invoiceModel->generateCode();
        $invoiceModel->daysExpired(Yii::$app->params['invoice.domainDuration']);

        if (!$invoiceModel->save(false)) {
            ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_PANEL, $this->id, $invoiceModel->getErrors(), 'project.restore.invoice');
            return false;
        }

        $invoiceDetailsModel = new InvoiceDetails();
        $invoiceDetailsModel->invoice_id = $invoiceModel->id;
        $invoiceDetailsModel->item_id = $this->id;
        $invoiceDetailsModel->amount = $invoiceModel->total;
        $invoiceDetailsModel->item = InvoiceDetails::ITEM_PROLONGATION_PANEL;
        if ($this->child_panel) {
            $invoiceDetailsModel->item = InvoiceDetails::ITEM_PROLONGATION_CHILD_PANEL;
        }


        if (!$invoiceDetailsModel->save(false)) {
            ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_PANEL, $this->id, $invoiceDetailsModel->getErrors(), 'project.restore.invoice_details');
            return false;
        }

        Logs::log($this, Logs::TYPE_RESTORED);

        return true;
    }

    /**
     * Terminate project
     * @param bool $check
     * @return bool
     * @throws \yii\base\Exception
     */
    public function terminate($check = false)
    {
        if (!$this->subdomain) {
            // Удаляем главный домен
            $this->disableMainDomain($check);
        }

        $item = $this->child_panel ? InvoiceDetails::ITEM_PROLONGATION_CHILD_PANEL : InvoiceDetails::ITEM_PROLONGATION_PANEL;

        // Cancel all unpaid invoices
        $invoices = Invoices::find()
            ->innerJoin('invoice_details', 'invoice_details.invoice_id = invoices.id AND invoice_details.item = ' . $item)
            ->andWhere([
                'invoices.status' => Invoices::STATUS_UNPAID,
                'invoice_details.item_id' => $this->id
            ])
            ->all();

        /**
         * @var Invoices $invoice
         */
        foreach ($invoices as $invoice) {
            $invoice->status = Invoices::STATUS_CANCELED;
            $invoice->save(false);
        }

        Logs::log($this, Logs::TYPE_TERMINATED);

        return true;
    }

    /**
     * Enable domain (create panel domains and add domain to dns servers)
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function enableDomain()
    {
        $domain = $this->site;

        // Если включен режим субдомена, не выполняем действий с доменом
        $panelDomain = PanelDomains::findOne([
            'domain' => $domain,
        ]);

        if ($panelDomain) {
            $panel = $panelDomain->panel;

            if (Project::STATUS_TERMINATED !== $panel->act) {
                return false;
            }

            $panelDomain->delete();
        }

        $result = true;

        if (!$this->enableSubDomain()) {
            $result = false;
        }

        if (!$this->enableMainDomain()) {
            $result = false;
        }

        return $result;
    }

    /**
     * Enable sub domain
     * @return bool
     */
    public function enableSubDomain()
    {
        $domain = $this->site;
        $subPrefix = str_replace('.', '-', $domain);
        $panelDomainName = Yii::$app->params['panelDomain'];
        $subDomain = $subPrefix . '.' . $panelDomainName;

        $panelDomain = PanelDomains::findOne([
            'domain' => $subDomain,
        ]);

        if (!$panelDomain) {
            $panelDomain = new PanelDomains();
            $panelDomain->type = PanelDomains::TYPE_SUBDOMAIN;
            $panelDomain->panel_id = $this->id;
            $panelDomain->domain = $subDomain;

            if (!$panelDomain->save(false)) {
                ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_PANEL, $this->id, $panelDomain->getErrors(), 'project.restore.subdomain');
                return false;
            }
        } else {
            if ($panelDomain->panel_id != $this->id) {
                $panelDomain->panel_id = $this->id;
                $panelDomain->save(false);
            }
        }

        return true;
    }

    /**
     * Enable main domain
     * @return bool
     */
    public function enableMainDomain()
    {
        $domain = $this->site;

        if (!PanelDomains::findOne([
            'type' => [PanelDomains::TYPE_STANDARD, PanelDomains::TYPE_FOREIGN_SUBDOMAIN],
            'panel_id' => $this->id
        ])) {
            $panelDomain = new PanelDomains();
            $panelDomain->type = !$this->isForeignSubdomain ? PanelDomains::TYPE_STANDARD : PanelDomains::TYPE_FOREIGN_SUBDOMAIN;
            $panelDomain->panel_id = $this->id;
            $panelDomain->domain = $domain;

            if (!$panelDomain->save(false)) {
                ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_PANEL, $this->id, $panelDomain->getErrors(), 'project.restore.domain');
                return false;
            }

            if (!$this->subdomain && !$this->isForeignSubdomain) {
                if (!DnsHelper::addMainDns($this)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Disable main domain (remove panel domains and remove domain from dns servers)
     * @param bool $check
     * @return bool
     */
    public function disableMainDomain($check = false)
    {
        // Remove all subdomains and domains
        PanelDomains::deleteAll([
            'type' => [
                PanelDomains::TYPE_STANDARD,
                PanelDomains::TYPE_FOREIGN_SUBDOMAIN,
            ],
            'panel_id' => $this->id
        ]);

        $domain = Domains::findOne(['domain' => $this->domain]);

        if (!$check || !isset($domain)) {
            DnsHelper::removeMainDns($this);
        }

        return true;
    }

    /**
     * Disable domain (remove panel domains and remove domain from dns servers)
     * @param bool $check
     * @return bool
     */
    public function disableDomain($check = false)
    {
        // Remove all subdomains and domains
        PanelDomains::deleteAll([
            'type' => [
                PanelDomains::TYPE_SUBDOMAIN,
                PanelDomains::TYPE_STANDARD,
                PanelDomains::TYPE_FOREIGN_SUBDOMAIN,
            ],
            'panel_id' => $this->id
        ]);

        $domain = Domains::findOne(['domain' => $this->site]);

        if (!$check || !isset($domain)) {
            DnsHelper::removeDns($this);
        }

        return true;
    }

    /**
     * Create nginx config
     * @return bool
     * @throws \Exception
     */
    public function createNginxConfig()
    {
       return NginxHelper::create($this);
    }

    /**
     * Remove nginx config
     * @return bool
     * @throws \Exception
     */
    public function deleteNginxConfig()
    {
        return NginxHelper::delete($this);
    }

    /**
     * Send created notification
     */
    public function createdNotice()
    {
        $mailer = new CreatedProject([
            'project' => $this
        ]);
        $mailer->send();
    }

    /**
     * Get panel db connection
     * @return null|\yii\db\Connection
     */
    public function getDbConnection()
    {
        if (empty($this->db) || !DbHelper::existDatabase($this->db)) {
            return null;
        }

        return DbHelper::getDbConnection($this->db);
    }

    /**
     * Rename database
     */
    public function renameDb()
    {
        $oldDbName = $this->db;
        $this->generateDbName();
        DbHelper::renameDatabase($oldDbName, $this->db);
    }

    /**
     * Get site
     * @return string
     */
    public function getSite()
    {
        return DomainsHelper::idnToUtf8($this->site);
    }

    /**
     * Get site url
     * @return string
     */
    public function getSiteUrl()
    {
        return ($this->ssl ? 'https://' : 'http://') . $this->getSite();
    }

    /**
     * Check panel access to some actions
     * @param mixed $panel
     * @param string $code
     * @return bool
     */
    public static function hasAccess($panel, $code)
    {
        if ($panel instanceof Project) {
            $status = $panel->act;
        } else {
            $status = ArrayHelper::getValue($panel, 'act', ArrayHelper::getValue($panel, 'status'));
        }

        switch ($code) {
            case 'canDowngrade':
                return !$panel->child_panel && (1 < static::find()->andWhere([
                    'cid' => $panel->cid
                ])->count());
            break;

            case 'canUpgrade':
                return $panel->child_panel;
            break;

            case 'canEdit':
                return static::STATUS_ACTIVE == $status;
            break;


            case 'canActivityLog':
                $db = ArrayHelper::getValue($panel, 'db');
                if (in_array($status, [
                        Project::STATUS_ACTIVE,
                        Project::STATUS_FROZEN,
                    ]) && !empty($db)) {
                    return true;
                }

            break;

            case 'canCreateStaff':
                if ($panel->child_panel) {
                    return 2 > ProjectAdmin::find()->andWhere([
                        'pid' => $panel->id
                    ])->count();
                }

                return true;
            break;
        }

        return false;
    }

    /**
     * Downgrade panel
     * @return bool
     */
    public function downgrade()
    {
        if (!static::hasAccess($this, 'canDowngrade')) {
            return false;
        }

        $this->child_panel = 1;
        $this->plan = Project::DEFAULT_CHILD_TARIFF;
        $this->tariff = Project::DEFAULT_CHILD_TARIFF;
        $this->apikey = '';

        return $this->save(false);
    }

    /**
     * Upgrade panel
     * @return bool
     */
    public function upgrade()
    {
        if (!static::hasAccess($this, 'canUpgrade')) {
            return false;
        }

        $this->child_panel = 0;
        $this->plan = Project::DEFAULT_TARIFF;
        $this->tariff = Project::DEFAULT_TARIFF;

        return $this->save(false);
    }

    /**
     * Get array of Project-objects which are child panels
     * @return array|ActiveRecord[]
     */
    public function getChildPanels()
    {
        return Project::find()
            ->select('child_panel.*')
            ->leftJoin('additional_services', 'additional_services.name = project.site')
            ->leftJoin('project as child_panel', 'child_panel.provider_id = additional_services.provider_id')
            ->where(['project.site' => $this->site])
            ->all();
    }

    /**
     * @return bool
     */
    public function hasManualPaymentMethods()
    {
        return (new Query())
            ->from(['ppm' => PanelPaymentMethods::tableName()])
            ->innerJoin(['pm' => PaymentMethods::tableName()], 'pm.id = ppm.method_id AND pm.manual_callback_url = :manual_url', [':manual_url' => 1])
            ->andWhere([
                'ppm.panel_id' => $this->id,
                'ppm.visibility' => 1
            ])
            ->exists();
    }

    /**
     * Set whois_lookup
     * @param array|mixed $whoisLookupData
     */
    public function setWhoisLookup($whoisLookupData)
    {
        $this->whois_lookup = json_encode($whoisLookupData, JSON_PRETTY_PRINT);
    }

    /**
     * Get whois_lookup
     * @return array|mixed
     */
    public function getWhoisLookup()
    {
        return json_decode($this->whois_lookup,true);
    }

    /**
     * Set nameservers
     * @param array|mixed $nameserversList
     */
    public function setNameservers($nameserversList)
    {
        $this->nameservers = json_encode($nameserversList, JSON_PRETTY_PRINT);
    }

    /**
     * Get nameservers
     * @return array|mixed
     */
    public function getNameservers()
    {
        return json_decode($this->nameservers,true);
    }

    /**
     * Get paypal_fraud_settings
     * @return array
     */
    public function getPaypalFraudSettings()
    {
        return json_decode($this->paypal_fraud_settings, true);
    }

    /**
     * Set paypal_fraud_settings
     * @param array $settings
     */
    public function setPaypalFraudSettings(array $settings)
    {
        $this->paypal_fraud_settings = json_encode($settings);
    }

}
