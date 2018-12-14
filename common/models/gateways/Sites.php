<?php

namespace common\models\gateways;

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
}