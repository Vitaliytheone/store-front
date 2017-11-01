<?php

namespace common\models\stores;

use Yii;

/**
 * This is the model class for table "{{%stores}}".
 *
 * @property integer $id
 * @property integer $customer_id
 * @property string $domain
 * @property string $name
 * @property integer $timezone
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
 * @property string $theme_name
 *
 * @property PaymentMethods[] $paymentMethods
 * @property StoreAdmins[] $storeAdmins
 * @property StoreDomains[] $storeDomains
 * @property StoreFiles[] $storeFiles
 * @property StoreProviders[] $storeProviders
 * @property Customers $customer
 */
class Stores extends \yii\db\ActiveRecord
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
            [['domain', 'name', 'db_name', 'logo', 'favicon', 'seo_title', 'theme_name'], 'string', 'max' => 255],
            [['currency'], 'string', 'max' => 10],
            [['seo_keywords', 'seo_description'], 'string', 'max' => 2000],
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
            'theme_name' => Yii::t('app', 'Theme Name'),
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
     * @return \common\models\stores\queries\StoresQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\stores\queries\StoresQuery(get_called_class());
    }
}
