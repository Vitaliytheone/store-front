<?php

namespace common\models\stores;

use frontend\helpers\StoreHelper;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\stores\queries\StoresQuery;

/**
 * This is the model class for table "{{%stores}}".
 *
 * @property integer $id
 * @property integer $customer_id
 * @property string $domain
 * @property string $name
 * @property integer $timezone
 * @property string $language
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
 * @property string $admin_email
 *
 * @property PaymentMethods[] $paymentMethods
 * @property StoreAdmins[] $storeAdmins
 * @property StoreDomains[] $storeDomains
 * @property StoreFiles[] $storeFiles
 * @property StoreProviders[] $storeProviders
 * @property Customers $customer
 */
class Stores extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%stores}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customer_id', 'timezone', 'expired', 'created_at', 'updated_at'], 'integer'],
            [['domain', 'name', 'db_name', 'logo', 'favicon', 'seo_title', 'theme_name', 'theme_folder', 'folder', 'folder_content'], 'string', 'max' => 255],
            [['currency', 'language'], 'string', 'max' => 10],
            [['seo_keywords', 'seo_description'], 'string', 'max' => 2000],
            [['admin_email'], 'string', 'max' => 300],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customers::className(), 'targetAttribute' => ['customer_id' => 'id']],
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
            'admin_email' => Yii::t('app', 'Admin e-mail'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentMethods()
    {
        return $this->hasMany(PaymentMethods::className(), ['store_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStoreAdmins()
    {
        return $this->hasMany(StoreAdmins::className(), ['store_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStoreDomains()
    {
        return $this->hasMany(StoreDomains::className(), ['store_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStoreFiles()
    {
        return $this->hasMany(StoreFiles::className(), ['store_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStoreProviders()
    {
        return $this->hasMany(StoreProviders::className(), ['store_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customers::className(), ['id' => 'customer_id']);
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
                'class' => TimestampBehavior::className(),
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
}
