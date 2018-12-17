<?php

namespace common\models\gateways;

use common\helpers\DbHelper;
use common\models\common\ProjectInterface;
use gateway\helpers\GatewayHelper;
use my\helpers\DomainsHelper;
use my\helpers\ExpiryHelper;
use Yii;
use yii\db\ActiveRecord;
use common\models\gateways\queries\SitesQuery;

/**
 * This is the model class for table "{{%sites}}".
 *
 * @property int $id
 * @property int $customer_id
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
 */
class Sites extends ActiveRecord
{
    const GATEWAY_DB_NAME_PREFIX = 'gateway_';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sites}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customer_id', 'domain', 'db_name', 'created_at'], 'required'],
            [['customer_id', 'dns_checked_at', 'expired_at', 'created_at', 'updated_at'], 'integer'],
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
            'dns_status' => Yii::t('app', 'dns-check result: null-неизвестно, 0-не наши ns, 1-наш ns'),
            'dns_checked_at' => Yii::t('app', 'Dns Checked At'),
            'expired_at' => Yii::t('app', 'Expired At'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
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
     * Get store folder
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
                'folder' => $this->theme_folder
            ])) {
                break;
            }
        }
    }

    /**
     * Get store folder
     * @return string
     */
    public function getFolder()
    {
        $assetsPath = GatewayHelper::getAssetsPath();
        if (empty($this->theme_folder) || !is_dir($assetsPath . $this->theme_folder)) {
            $this->generateFolderName();
            $this->save(false);
            GatewayHelper::generateAssets($this->id);
        }

        return $this->folder;
    }

    /**
     * Return is store inactive
     * @return bool
     */
    public function isInactive()
    {
        return $this->isExpired();
    }

    /**
     * Return if store expired
     * @return bool
     */
    public function isExpired()
    {
        return $this->expired_at ? time() > $this->expired_at : false;
    }

    /**
     * Check if store is expired and update store status
     * @return bool
     */
    public function checkExpired()
    {
        if ($this->isExpired()) {
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
     * Create store db name
     */
    public function generateDbName()
    {
        $domain = Yii::$app->params['gatewayDomain'];

        $baseDbName = static::GATEWAY_DB_NAME_PREFIX . $this->id . "_" . strtolower(str_replace([$domain, '.', '-'], '', $this->domain));

        $postfix = null;

        do {
            $dbName = $baseDbName .  ($postfix ? '_' . $postfix : '');
            $postfix ++;
        } while(DbHelper::existDatabase($dbName));

        $this->db_name = $dbName;
    }

    /**
     * Generate store expired datetime
     * @param bool $isTrial is store trial
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
}