<?php

namespace common\models\stores;

use common\helpers\DbHelper;
use common\models\panels\Customers;
use common\components\traits\UnixTimeFormatTrait;
use common\helpers\NginxHelper;
use common\models\panels\ThirdPartyLog;
use common\models\store\Blocks;
use my\helpers\ExpiryHelper;
use sommerce\helpers\DnsHelper;
use sommerce\helpers\StoreHelper;
use Yii;
use yii\base\Exception;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\stores\queries\StoresQuery;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%stores}}".
 *
 * @property integer $id
 * @property integer $customer_id
 * @property string $domain
 * @property integer $subdomain
 * @property string $name
 * @property integer $timezone
 * @property string $language
 * @property integer $status
 * @property string $db_name
 * @property integer $expired
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $logo
 * @property string $favicon
 * @property string $currency
 * @property string $seo_title
 * @property string $seo_keywords
 * @property string $seo_description
 * @property string $folder
 * @property string $folder_content
 * @property string $theme_name
 * @property string $theme_folder
 * @property string $block_slider
 * @property string $block_features
 * @property string $block_reviews
 * @property string $block_process
 * @property string $admin_email
 *
 * @property PaymentMethods[] $paymentMethods
 * @property StoreAdmins[] $storeAdmins
 * @property StoreDomains[] $storeDomains
 * @property StoreProviders[] $storeProviders
 * @property Customers $customer
 */
class Stores extends ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_FROZEN = 2;
    const STATUS_TERMINATED = 3;

    const CAN_DASHBOARD = 2;
    const CAN_PROLONG = 3;
    const CAN_ACTIVITY_LOG = 4;
    const CAN_DOMAIN_CONNECT = 5;

    const STORE_DB_NAME_PREFIX = 'store_';

    use UnixTimeFormatTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return DB_STORES . '.stores';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'customer_id', 'timezone', 'status', 'expired', 'created_at', 'updated_at',
                'block_slider', 'block_features', 'block_reviews', 'block_process', 'subdomain'
            ], 'integer'],
            [[
                'block_slider', 'block_features', 'block_reviews', 'block_process',
            ], 'default', 'value' => 0],
            [['domain', 'name', 'db_name', 'logo', 'favicon', 'seo_title', 'theme_name', 'theme_folder', 'folder', 'folder_content'], 'string', 'max' => 255],
            [['currency', 'language'], 'string', 'max' => 10],
            [['seo_keywords', 'seo_description'], 'string', 'max' => 2000],
            [['admin_email'], 'string', 'max' => 300],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customers::class, 'targetAttribute' => ['customer_id' => 'id']],
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
            'name' => Yii::t('app', 'Name'),
            'timezone' => Yii::t('app', 'Timezone'),
            'language' => Yii::t('app', 'Language'),
            'status' => Yii::t('app', 'Status'),
            'db_name' => Yii::t('app', 'Db Name'),
            'expired' => Yii::t('app', 'Expired'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'logo' => Yii::t('app', 'Logo'),
            'favicon' => Yii::t('app', 'Favicon'),
            'currency' => Yii::t('app', 'Currency'),
            'seo_title' => Yii::t('app', 'Seo Title'),
            'seo_keywords' => Yii::t('app', 'Seo Keywords'),
            'seo_description' => Yii::t('app', 'Seo Description'),
            'folder' => Yii::t('app', 'Folder'),
            'folder_content' => Yii::t('app', 'Folder Content'),
            'theme_name' => Yii::t('app', 'Theme Name'),
            'theme_folder' => Yii::t('app', 'Theme Folder'),
            'block_slider' => Yii::t('app', 'Block Slider'),
            'block_features' => Yii::t('app', 'Block Features'),
            'block_reviews' => Yii::t('app', 'Block Reviews'),
            'block_process' => Yii::t('app', 'Block Process'),
            'admin_email' => Yii::t('app', 'Admin e-mail'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentMethods()
    {
        return $this->hasMany(PaymentMethods::class, ['store_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStoreAdmins()
    {
        return $this->hasMany(StoreAdmins::class, ['store_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStoreDomains()
    {
        return $this->hasMany(StoreDomains::class, ['store_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStoreProviders()
    {
        return $this->hasMany(StoreProviders::class, ['store_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customers::class, ['id' => 'customer_id']);
    }

    /**
     * @inheritdoc
     * @return StoresQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new StoresQuery(get_called_class());
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => [
                        'created_at',
                        'updated_at'
                    ],
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
                'value' => function() {
                    return time();
                },
            ],
        ];
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
                'folder' => $this->folder
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
        $assetsPath = StoreHelper::getAssetsPath();
        if (empty($this->folder) || !is_dir($assetsPath . $this->folder)) {
            $this->generateFolderName();
            $this->save(false);
            StoreHelper::generateAssets($this->id);
        }

        return $this->folder;
    }

    /**
     * @return array
     */
    public function getBlocks()
    {
        return [
            Blocks::CODE_SLIDER => Yii::t('app', 'Slider'),
            Blocks::CODE_FEATURES => Yii::t('app', 'Features'),
            Blocks::CODE_REVIEW => Yii::t('app', 'Reviews'),
            Blocks::CODE_PROCESS => Yii::t('app', 'Process'),
        ];
    }

    /**
     * Check is enable blocks by code
     * @param string $code
     * @return bool
     */
    public function isEnableBlock($code)
    {
        $fieldName = 'block_' . $code;

        return $this->hasAttribute($fieldName) && $this->getAttribute($fieldName);
    }

    /**
     * Return is store inactive
     * @return bool
     */
    public function isInactive()
    {
        return in_array($this->status, [self::STATUS_FROZEN, self::STATUS_TERMINATED]);
    }

    /**
     * Return if store expired
     * @return bool
     */
    public function isExpired()
    {
        return $this->expired ? time() > $this->expired : false;
    }

    /**
     * Check if store is expired and update store status
     */
    public function checkExpired()
    {
        if ($this->isExpired() && $this->status != self::STATUS_FROZEN) {
            $this->status = self::STATUS_FROZEN;
            $this->save(false);
        }
    }

    /**
     * Return store site url
     * @return string
     */
    public function getSite()
    {
        /** @var StoreDomains $domain */
        $domain = $this->getStoreDomains()
            ->andWhere([
                'type' => [
                    StoreDomains::DOMAIN_TYPE_DEFAULT,
                    StoreDomains::DOMAIN_TYPE_SOMMERCE,
                ]
            ])
            ->orderBy(['type' => SORT_DESC])
            ->one();

        return ((bool)$domain->ssl ? 'https' : 'http') . '://' . $domain->domain;
    }

    /**
     * Get statuses
     * @return array
     */
    public static function getStatuses()
    {
        return [
            static::STATUS_ACTIVE => Yii::t('app', 'stores.status.active'),
            static::STATUS_FROZEN => Yii::t('app', 'stores.status.frozen'),
            static::STATUS_TERMINATED => Yii::t('app', 'stores.status.terminated'),
        ];
    }

    /**
     * Get status string name by status
     * @param int $status
     * @return mixed
     */
    public static function getActNameString($status)
    {
        return ArrayHelper::getValue(static::getStatuses(), $status, '');
    }

    /**
     * Change store status
     * @param int $status
     * @return bool
     */
    public function changeStatus($status)
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
        }

        return $this->save(false);
    }

    /**
     * Check store access some actions
     * @param Stores|array $store
     * @param string $code
     * @param array $options
     * @return bool
     * @throws Exception
     */
    public static function hasAccess($store, $code, $options = [])
    {
        if ($store instanceof Stores) {
            $customerId = $store->customer_id;
            $status = $store->status;
        } else {
            $customerId = ArrayHelper::getValue($store, 'customer_id');
            $status = ArrayHelper::getValue($store, 'status');
        }

        if (!in_array($status, array_keys(static::getStatuses()))) {
            throw new Exception('Unknown store status ' . "[$status]");
        }

        /**
         * @var Customers $customer
         */
        $customer = ArrayHelper::getValue($options, 'customer');

        switch ($code) {
            case self::CAN_DASHBOARD:
                return self::STATUS_ACTIVE == $status;
            break;

            case self::CAN_PROLONG:
                return in_array($status, [
                    static::STATUS_ACTIVE,
                    static::STATUS_FROZEN,
                ]);
            break;

            case self::CAN_DOMAIN_CONNECT:
                if (static::STATUS_ACTIVE != (int)$status) {
                    return false;
                }

                if ($customer && $customer->id != $customerId) {
                    return false;
                }

                $updatedAt = ArrayHelper::getValue($options, 'last_update');
                if ($updatedAt && ($updatedAt > (time() - (Yii::$app->params['storeChangeDomainDuration'])))) {
                    return false;
                }

                return true;
            break;

            case self::CAN_ACTIVITY_LOG:
                return true;
            break;
        }

        return false;
    }

    /**
     * Create nginx config
     * @return bool
     */
    public function createNginxConfig()
    {
        return NginxHelper::create($this);
    }

    /**
     * Remove nginx config
     * @return bool
     */
    public function deleteNginxConfig()
    {
        return NginxHelper::delete($this);
    }

    /**
     * Disable domain (remove store domains and remove domain from dns servers)
     * @return bool
     */
    public function disableDomain()
    {
        // Remove all subdomains and domains
        StoreDomains::deleteAll([
            'type' => [
                StoreDomains::DOMAIN_TYPE_DEFAULT,
                StoreDomains::DOMAIN_TYPE_SUBDOMAIN
            ],
            'store_id' => $this->id
        ]);

        DnsHelper::removeDns($this);

        return true;
    }

    /**
     * Enable domain (create panel domains and add domain to dns servers)
     * @return bool
     */
    public function enableDomain()
    {
        $domain = $this->domain;

        // Если включен режим субдомена, не выполняем действий с доменом
        $storeDomain = StoreDomains::findOne([
            'domain' => $domain,
        ]);

        if ($storeDomain) {
            $panel = $storeDomain->store;

            if (static::STATUS_TERMINATED !== $panel->status) {
                return false;
            }

            $storeDomain->delete();
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
        $domain = $this->domain;
        $subPrefix = str_replace('.', '-', $domain);
        $storeDomainName = Yii::$app->params['storeDomain'];
        $subDomain = $subPrefix . '.' . $storeDomainName;

        $storeDomain = StoreDomains::findOne([
            'domain' => $subDomain,
        ]);

        if (!$storeDomain) {
            $storeDomain = new StoreDomains();
            $storeDomain->type = StoreDomains::DOMAIN_TYPE_SUBDOMAIN;
            $storeDomain->store_id = $this->id;
            $storeDomain->domain = $subDomain;

            if (!$storeDomain->save(false)) {
                ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_STORE, $this->id, $storeDomain->getErrors(), 'store.restore.subdomain');
                return false;
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
        $domain = $this->domain;

        if (!StoreDomains::findOne([
            'type' => StoreDomains::DOMAIN_TYPE_DEFAULT,
            'store_id' => $this->id
        ])) {
            $storeDomain = new StoreDomains();
            $storeDomain->type = StoreDomains::DOMAIN_TYPE_DEFAULT;
            $storeDomain->store_id = $this->id;
            $storeDomain->domain = $domain;

            if (!$storeDomain->save(false)) {
                ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_STORE, $this->id, $storeDomain->getErrors(), 'store.restore.domain');
                return false;
            }

            if (!$this->subdomain) {
                if (!DnsHelper::addMainDns($this)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Disable main domain (remove store domains and remove domain from dns servers)
     * @return bool
     */
    public function disableMainDomain()
    {
        // Remove all subdomains and domains
        StoreDomains::deleteAll([
            'type' => [
                StoreDomains::DOMAIN_TYPE_DEFAULT
            ],
            'store_id' => $this->id
        ]);

        DnsHelper::removeMainDns($this);

        return true;
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
     * Create panel db name
     */
    public function generateDbName()
    {
        $domain = Yii::$app->params['storeDomain'];

        $baseDbName = self::STORE_DB_NAME_PREFIX . $this->id . "_" . strtolower(str_replace([$domain, '.', '-'], '', $this->domain));

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
        $this->expired = $isTrial ? ExpiryHelper::days(14, time()) : ExpiryHelper::month(time());;
    }
}
