<?php

namespace common\models\stores;

use common\components\traits\UnixTimeFormatTrait;
use common\models\store\Blocks;
use sommerce\helpers\StoreHelper;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\stores\queries\StoresQuery;
use common\models\store\Files;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%stores}}".
 *
 * @property integer $id
 * @property integer $customer_id
 * @property string $domain
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

    use UnixTimeFormatTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return DB_STORES . '.{{%stores}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'customer_id', 'timezone', 'status', 'expired', 'created_at', 'updated_at',
                'block_slider', 'block_features', 'block_reviews', 'block_process',
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
}
