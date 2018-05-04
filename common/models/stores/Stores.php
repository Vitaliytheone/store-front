<?php

namespace common\models\stores;

use common\helpers\DbHelper;
use common\models\common\ProjectInterface;
use common\models\panels\Customers;
use common\components\traits\UnixTimeFormatTrait;
use common\helpers\NginxHelper;
use common\models\panels\InvoiceDetails;
use common\models\panels\Invoices;
use common\models\panels\ThirdPartyLog;
use common\models\store\Blocks;
use common\models\store\Languages;
use my\helpers\DomainsHelper;
use my\helpers\ExpiryHelper;
use my\mail\mailers\InvoiceCreated;
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
 * @property integer $ssl
 * @property string $name
 * @property integer $timezone
 * @property string $language
 * @property integer $status
 * @property integer $hide
 * @property string $db_name
 * @property integer $trial
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
 * @property string $custom_header
 * @property string $custom_footer
 *
 * @property PaymentMethods[] $paymentMethods
 * @property StoreAdmins[] $storeAdmins
 * @property StoreDomains[] $storeDomains
 * @property StoreProviders[] $storeProviders
 * @property array $languages
 * @property Customers $customer
 */
class Stores extends ActiveRecord implements ProjectInterface
{
    const STATUS_ACTIVE = 1;
    const STATUS_FROZEN = 2;
    const STATUS_TERMINATED = 3;

    const TRIAL_MODE_ON = 1;
    const TRIAL_MODE_OFF = 0;

    const HIDDEN_ON = 1;
    const HIDDEN_OFF = 0;

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
                'block_slider', 'block_features', 'block_reviews', 'block_process', 'subdomain', 'ssl',
                'trial', 'hide'
                ], 'integer'],
            [[
                'block_slider', 'block_features', 'block_reviews', 'block_process',
            ], 'default', 'value' => 0],
            [['domain', 'name', 'db_name', 'logo', 'favicon', 'seo_title', 'theme_name', 'theme_folder', 'folder', 'folder_content'], 'string', 'max' => 255],
            [['currency', 'language'], 'string', 'max' => 10],
            [['custom_header', 'custom_footer'], 'string', 'max' => 10000],
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
            'ssl' => Yii::t('app', 'SSL'),
            'name' => Yii::t('app', 'Name'),
            'timezone' => Yii::t('app', 'Timezone'),
            'language' => Yii::t('app', 'Language'),
            'status' => Yii::t('app', 'Status'),
            'hide' => Yii::t('app', 'Hidden'),
            'trial' => Yii::t('app', 'Trial'),
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
            'custom_header' => Yii::t('app', 'Custom header'),
            'custom_footer' => Yii::t('app', 'Custom footer'),
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
     * @inheritdoc
     */
    public static function getProjectType()
    {
        return ProjectInterface::PROJECT_TYPE_STORE;
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
     * Return store languages codes array
     * @return array
     */
    public function getLanguages()
    {
        return Languages::find()
            ->select(['code'])
            ->column();
    }

    /**
     * Return store default language code
     * @return mixed
     */
    public static function getDefaultLanguage()
    {
        return Yii::$app->params['store.defaults']['language'];
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
            Blocks::CODE_PROCESS => Yii::t('app', 'Process'),
            Blocks::CODE_REVIEW => Yii::t('app', 'Reviews'),
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
            $expired = $store->expired;
        } else {
            $customerId = ArrayHelper::getValue($store, 'customer_id');
            $status = ArrayHelper::getValue($store, 'status');
            $expired = ArrayHelper::getValue($store, 'expired');
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
                if (!in_array($status, [
                    static::STATUS_ACTIVE,
                    static::STATUS_FROZEN,
                ])) {
                  return false;
                }

                if ($customer && $customer->id != $customerId) {
                    return false;
                }

                if ($expired && ($expired > (time() + (Yii::$app->params['storeProlongMinDuration'])))) {
                    return false;
                }

                return true;

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

        $type = $this->subdomain ? StoreDomains::DOMAIN_TYPE_SUBDOMAIN : StoreDomains::DOMAIN_TYPE_DEFAULT;

        if (!StoreDomains::findOne([
            'type' => $type,
            'store_id' => $this->id
        ])) {
            $storeDomain = new StoreDomains();
            $storeDomain->type = $type;
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
        $this->expired = $isTrial ? ExpiryHelper::days(14, time()) : ExpiryHelper::month(time());
    }

    /**
     * Update expired
     * @return bool
     */
    public function updateExpired()
    {
        if ($this->status == static::STATUS_ACTIVE) {
            $time = $this->expired;
        } else {
            $time = time();
        }

        $this->status = static::STATUS_ACTIVE;
        $this->expired = ExpiryHelper::month($time);

        return $this->save(false);
    }

    /**
     * Return store Sommerce domain from store domain list
     * @return array|StoreDomains|null
     */
    public function getSommerceDomain()
    {
        $sommerceDomain = StoreDomains::find()
            ->andWhere([
                'store_id' => $this->id,
                'type' => StoreDomains::DOMAIN_TYPE_SOMMERCE,
            ])
            ->andFilterWhere([
                'AND',
                ['like', 'domain', Yii::$app->params['storeDomain']]
            ])
            ->one();

        return $sommerceDomain;
    }

    /**
     * Prolong store and create new invoice
     */
    public function prolong()
    {
        /**
         * @var Invoices $invoice
         */
        if (($invoice = Invoices::find()
            ->joinWith([
                'invoiceDetails'
            ])
            ->andWhere([
                'status' => Invoices::STATUS_UNPAID,
                'invoice_details.item' => InvoiceDetails::ITEM_PROLONGATION_STORE,
                'invoice_details.item_id' => $this->id,
            ])
            ->one())) {

            if ($invoice->expired > time()) {
                return $invoice->code;
            }

            $invoice->status = Invoices::STATUS_CANCELED;
            $invoice->save(false);
        }

        $transaction = Yii::$app->db->beginTransaction();

        $invoice = new Invoices();
        $invoice->cid = $this->customer_id;
        $invoice->total = Yii::$app->params['storeDeployPrice'];
        $invoice->generateCode();
        $invoice->daysExpired(7);

        if ($invoice->save()) {
            $invoiceDetailsModel = new InvoiceDetails();
            $invoiceDetailsModel->invoice_id = $invoice->id;
            $invoiceDetailsModel->item_id = $this->id;
            $invoiceDetailsModel->amount = $invoice->total;
            $invoiceDetailsModel->item = InvoiceDetails::ITEM_PROLONGATION_STORE;

            if (!$invoiceDetailsModel->save()) {
                $transaction->rollBack();
                return null;
            }

            $transaction->commit();

            $mail = new InvoiceCreated([
                'store' => $this
            ]);
            $mail->send();

            return $invoice->code;
        }

        return null;
    }
}
