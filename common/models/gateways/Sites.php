<?php

namespace common\models\gateways;


use common\helpers\DbHelper;
use common\helpers\DnsHelper;
use common\helpers\NginxHelper;
use common\models\common\ProjectInterface;
use common\models\panels\Customers;
use common\models\panels\InvoiceDetails;
use common\models\panels\Invoices;
use common\models\panels\Logs;
use gateway\components\behaviors\SiteBehavior;
use gateway\helpers\GatewayHelper;
use my\helpers\DomainsHelper;
use my\helpers\ExpiryHelper;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\gateways\queries\SitesQuery;
use yii\helpers\ArrayHelper;
use yii\base\Exception;

/**
 * This is the model class for table "{{%sites}}".
 *
 * @property int $id
 * @property int $customer_id
 * @property int $status
 * @property string $domain
 * @property int $subdomain
 * @property int $ssl
 * @property string $db_name
 * @property string $seo_title
 * @property string $seo_keywords
 * @property string $seo_description
 * @property string $folder
 * @property string $folder_content
 * @property string $theme_name
 * @property string $theme_folder
 * @property string $whois_lookup
 * @property string $nameservers
 * @property int $dns_status dns-check result: null-неизвестно, 0-не наши ns, 1-наш ns
 * @property int $dns_checked_at
 * @property int $expired_at
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Admins[] $admins
 * @property SitePaymentMethods[] $sitePaymentMethods
 * @property Customers $customer
 */
class Sites extends ActiveRecord implements ProjectInterface
{
    const GATEWAY_DB_NAME_PREFIX = 'gateway_';

    const STATUS_ACTIVE = 1;
    const STATUS_FROZEN = 2;
    const STATUS_TERMINATED = 3;

    const CAN_DASHBOARD = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return DB_GATEWAYS . '.sites';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customer_id', 'domain', 'db_name', 'created_at'], 'required'],
            [['customer_id', 'dns_checked_at', 'expired_at', 'created_at', 'updated_at', 'status'], 'integer'],
            [['whois_lookup', 'nameservers'], 'string'],
            [['domain', 'db_name', 'seo_title', 'folder', 'folder_content', 'theme_name', 'theme_folder'], 'string', 'max' => 255],
            [['subdomain', 'ssl', 'dns_status'], 'string', 'max' => 1],
            [['seo_keywords', 'seo_description'], 'string', 'max' => 2000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'customer_id' => Yii::t('app', 'Customer ID'),
            'status' => Yii::t('app', 'Status'),
            'domain' => Yii::t('app', 'Domain'),
            'subdomain' => Yii::t('app', 'Subdomain'),
            'ssl' => Yii::t('app', 'Ssl'),
            'db_name' => Yii::t('app', 'Db Name'),
            'seo_title' => Yii::t('app', 'Seo Title'),
            'seo_keywords' => Yii::t('app', 'Seo Keywords'),
            'seo_description' => Yii::t('app', 'Seo Description'),
            'folder' => Yii::t('app', 'Folder'),
            'folder_content' => Yii::t('app', 'Folder Content'),
            'theme_name' => Yii::t('app', 'Theme Name'),
            'theme_folder' => Yii::t('app', 'Theme Folder'),
            'whois_lookup' => Yii::t('app', 'Whois Lookup'),
            'nameservers' => Yii::t('app', 'Nameservers'),
            'dns_status' => Yii::t('app', 'Dns status'),
            'dns_checked_at' => Yii::t('app', 'Dns Checked At'),
            'expired_at' => Yii::t('app', 'Expired At'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'timestamp' => TimestampBehavior::class,
        ]);
    }

    /**
     * Get statuses labels
     * @return array
     */
    public static function getStatuses(): array
    {
        return [
            static::STATUS_ACTIVE => Yii::t('app', 'sites.status.active'),
            static::STATUS_FROZEN => Yii::t('app', 'sites.status.frozen'),
            static::STATUS_TERMINATED => Yii::t('app', 'sites.status.terminated'),
        ];
    }

    /**
     * Get status string name
     * @param int $status
     * @return string
     */
    public static function getStatusName(int $status): string
    {
        return ArrayHelper::getValue(static::getStatuses(), $status, '');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customers::class, ['id' => 'customer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdmins()
    {
        return $this->hasMany(Admins::class, ['site_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSitePaymentMethods()
    {
        return $this->hasMany(SitePaymentMethods::class, ['site_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return SitesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SitesQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function getBaseDomain()
    {
        return DomainsHelper::idnToUtf8($this->domain);
    }

    /**
     * @inheritdoc
     */
    public function getBaseSite()
    {
        return ($this->ssl == ProjectInterface::SSL_MODE_ON ? 'https://' : 'http://') . $this->getBaseDomain();
    }

    /**
     * @inheritdoc
     */
    public function setSslMode($isActive)
    {
        $this->ssl = $isActive;
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
        return json_decode($this->whois_lookup, true);
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
     * Get gateway folder
     * @return string
     */
    public function getThemeFolder()
    {
        return $this->theme_folder;
    }

    /**
     * Get folder content decoded data
     * @return array|mixed
     */
    public function getFolderContentData()
    {
        if (empty($this->folder_content)) {
            return [];
        }

        return json_decode($this->folder_content, true);
    }

    /**
     * Set folder content decoded data
     * @param mixed $content
     * @return bool
     */
    public function setFolderContentData($content)
    {
        $this->folder_content = json_encode($content);

        return $this->save(false);
    }

    /**
     * Generate unique folder name
     * @return bool
     */
    public function generateFolderName()
    {
        for ($i = 1; $i < 100; $i++) {
            $this->folder = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 6);
            if (!static::findOne([
                'folder' => $this->folder
            ])) {
                break;
            }
        }
    }

    /**
     * Get gateway folder
     * @return string
     */
    public function getFolder()
    {
        $assetsPath = GatewayHelper::getAssetsPath();
        if (empty($this->folder) || !is_dir($assetsPath . $this->folder)) {
            $this->generateFolderName();
            $this->save(false);
            GatewayHelper::generateAssets($this->id);
        }

        return $this->folder;
    }

    /**
     * Return is gateway inactive
     * @return bool
     */
    public function isInactive()
    {
        if ($this->checkExpired()) {
            return true;
        }

        return $this->status == static::STATUS_ACTIVE ? false : true;
    }

    /**
     * Return if gateway expired
     * @return bool
     */
    public function isExpired()
    {
        return $this->expired_at ? time() > $this->expired_at : false;
    }

    /**
     * Check if gateway is expired and update gateway status
     * @return bool
     */
    public function checkExpired()
    {
        if ($this->isExpired() && $this->status != self::STATUS_FROZEN) {
            return true;
        }
        return false;
    }

    /**
     * Rename database
     */
    public function renameDb()
    {
        $oldDbName = $this->db_name;
        $this->generateDbName();
        DbHelper::renameDatabase($oldDbName, $this->db_name);
    }

    /**
     * Create gateway db name
     */
    public function generateDbName()
    {
        $domain = Yii::$app->params['gatewayDomain'];

        $baseDbName = static::GATEWAY_DB_NAME_PREFIX . $this->id . "_" . strtolower(str_replace([$domain, '.', '-'], '', DomainsHelper::idnToAscii($this->domain)));

        $postfix = null;

        do {
            $dbName = $baseDbName .  ($postfix ? '_' . $postfix : '');
            $postfix ++;
        } while(DbHelper::existDatabase($dbName));

        $this->db_name = $dbName;
    }

    /**
     * Generate gateway expired datetime
     * @param bool $isTrial is gateway trial
     */
    public function generateExpired($isTrial = false)
    {
        $this->expired_at = $isTrial ? ExpiryHelper::days(14, time()) : ExpiryHelper::month(time());
    }

    /**
     * Update expired
     * @return bool
     */
    public function updateExpired()
    {
        if (!$this->isExpired()) {
            $time = $this->expired_at;
        } else {
            $time = time();
        }

        $this->expired_at = ExpiryHelper::month($time);

        return $this->save(false);
    }

    /**
     * Check gateway access some actions
     * @param Sites|array $site
     * @param integer $code
     * @param array $options
     * @return bool
     * @throws Exception
     */
    public static function hasAccess($site, $code, array $options = [])
    {
        if ($site instanceof Sites) {
            $status = $site->status;
        } else {
            $status = ArrayHelper::getValue($site, 'status');
        }

        if (!in_array($status, array_keys(static::getStatuses()))) {
            throw new Exception('Unknown site status ' . "[$status]");
        }

        switch ($code) {
            case self::CAN_DASHBOARD:
                return self::STATUS_ACTIVE == $status;
                break;
        }

        return false;
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
     * Terminate gateway
     * @return bool
     * @throws Exception
     */
    public function terminate()
    {
        // Cancel unpaid invoices
        $invoices = Invoices::find()
            ->innerJoin('invoice_details', 'invoice_details.invoice_id = invoices.id AND invoice_details.item = :item', [
                ':item' => InvoiceDetails::ITEM_PROLONGATION_GATEWAY,
            ])
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
     * @inheritdoc
     */
    public function hasManualPaymentMethods()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function restore()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public static function getProjectType()
    {
        return ProjectInterface::PROJECT_TYPE_GATEWAY;
    }

    /**
     * Change gateway status
     * @param int $status
     * @return bool
     * @throws Exception
     */
    public function changeStatus(int $status)
    {
        switch ($status) {
            case static::STATUS_ACTIVE:
                if (static::STATUS_FROZEN == $this->status) {
                    $this->status = static::STATUS_ACTIVE;
                }
                break;

            case static::STATUS_FROZEN:
                if (static::STATUS_ACTIVE == $this->status) {
                    $this->status = static::STATUS_FROZEN;
                }
                break;

            case static::STATUS_TERMINATED:
                if (static::STATUS_FROZEN == $this->status) {
                    $this->status = static::STATUS_TERMINATED;
                    $this->terminate();
                }
                break;
        }

        return $this->save(false);
    }

    /**
     * Enable gateway domain
     * @return bool
     */
    public function enableDomain()
    {
        if (!$this->enableMainDomain()) {
            return false;
        }

        return true;
    }

    /**
     * Enable main domain
     * @return bool
     */
    public function enableMainDomain()
    {
        if (!$this->subdomain) {
            if (!DnsHelper::addMainDns($this)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Disable domain
     * @return bool
     */
    public function disableDomain()
    {
        if (!DnsHelper::removeDns($this)) {
            return false;
        }

        return true;
    }
}
