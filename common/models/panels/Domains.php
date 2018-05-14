<?php

namespace common\models\panels;

use common\components\traits\UnixTimeFormatTrait;
use my\mail\mailers\CreatedDomain;
use my\helpers\DomainsHelper;
use my\mail\mailers\RenewedDomain;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\panels\queries\DomainsQuery;
use yii\helpers\Json;

/**
 * This is the model class for table "{{%domains}}".
 *
 * @property integer $id
 * @property integer $customer_id
 * @property integer $zone_id
 * @property string $contact_id
 * @property integer $status
 * @property string $domain
 * @property string $password
 * @property integer $created_at
 * @property integer $expiry
 * @property integer $privacy_protection
 * @property integer $transfer_protection
 * @property string $details
 *
 * @property Customers $customer
 * @property DomainZones $zone
 */
class Domains extends ActiveRecord
{
    const STATUS_OK = 1;
    const STATUS_EXPIRED = 2;

    use UnixTimeFormatTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return DB_PANELS . '.domains';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customer_id', 'zone_id', 'contact_id', 'status', 'domain'], 'required'],
            [['customer_id', 'zone_id', 'status', 'created_at', 'expiry', 'privacy_protection', 'transfer_protection'], 'integer'],
            [['details'], 'string'],
            [['contact_id', 'domain', 'password'], 'string', 'max' => 250],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customers::class, 'targetAttribute' => ['customer_id' => 'id']],
            [['zone_id'], 'exist', 'skipOnError' => true, 'targetClass' => DomainZones::class, 'targetAttribute' => ['zone_id' => 'id']],
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
            'zone_id' => Yii::t('app', 'Zone ID'),
            'contact_id' => Yii::t('app', 'Contact ID'),
            'status' => Yii::t('app', 'Status'),
            'domain' => Yii::t('app', 'Domain'),
            'password' => Yii::t('app', 'Password'),
            'created_at' => Yii::t('app', 'Created At'),
            'expiry' => Yii::t('app', 'Expiry'),
            'privacy_protection' => Yii::t('app', 'Privacy Protection'),
            'transfer_protection' => Yii::t('app', 'Transfer Protection'),
            'details' => Yii::t('app', 'Details'),
        ];
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
    public function getZone()
    {
        return $this->hasOne(DomainZones::class, ['id' => 'zone_id']);
    }

    /**
     * @inheritdoc
     * @return DomainsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new DomainsQuery(get_called_class());
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
                ],
                'value' => function() {
                    return time();
                },
            ],
        ];
    }

    /**
     * Get statuses
     * @return array
     */
    public static function getStatuses()
    {
        return [
            static::STATUS_OK => Yii::t('app', 'domains.status.ok'),
            static::STATUS_EXPIRED => Yii::t('app', 'domains.status.expired'),
            static::STATUS_ERROR => Yii::t('app', 'domains.status.error'),
        ];
    }

    /**
     * Get status name
     * @return string
     */
    public function getStatusName()
    {
        return static::getStatuses()[$this->status];
    }

    /**
     * Set details
     * @param $details
     */
    public function setDetails($details)
    {
        $this->details = Json::encode($details);
    }

    /**
     * Get details
     * @return array
     */
    public function getDetails()
    {
        return !empty($this->details) ? Json::decode($this->details) : [];
    }

    /**
     * Set item details
     * @param array $orderDetails
     * @param string $item
     */
    public function setItemDetails($orderDetails, $item)
    {
        $details = $this->getDetails();

        if (empty($details)) {
            $details = [];
        }

        $details[$item] = $orderDetails;

        return $this->setDetails($details);
    }

    /**
     * Send created notification
     */
    public function createdNotice()
    {
        $mailer = new CreatedDomain([
            'domain' => $this
        ]);
        $mailer->send();
    }

    /**
     * Send prolonged notification
     */
    public function prolongedNotice()
    {
        $mailer = new RenewedDomain([
            'domain' => $this
        ]);
        $mailer->send();
    }

    /**
     * Get domain
     * @return string
     */
    public function getDomain()
    {
        return DomainsHelper::idnToUtf8($this->domain);
    }
}
