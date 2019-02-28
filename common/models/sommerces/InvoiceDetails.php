<?php

namespace common\models\sommerces;

use my\helpers\DomainsHelper;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\sommerces\queries\InvoiceDetailsQuery;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%invoice_details}}".
 *
 * @property integer $id
 * @property integer $invoice_id
 * @property integer $item_id
 * @property string $description
 * @property string $amount
 * @property integer $item
 * @property integer $created_at
 *
 * @property Invoices $invoice
 * @property Orders $order
 * @property Customers $customer
 */
class InvoiceDetails extends ActiveRecord
{
    const ITEM_BUY_STORE = 1;
    const ITEM_BUY_DOMAIN = 2;
    const ITEM_PROLONGATION_DOMAIN = 3;
    const ITEM_BUY_SSL = 4;
    const ITEM_PROLONGATION_SSL = 5;
    const ITEM_CUSTOM_CUSTOMER = 6;
    const ITEM_BUY_TRIAL_STORE = 7;
    const ITEM_PROLONGATION_STORE = 8;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return DB_SOMMERCES . '.invoice_details';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['invoice_id', 'item_id', 'amount', 'item'], 'required'],
            [['invoice_id', 'item_id', 'item', 'created_at'], 'integer'],
            [['description'], 'string'],
            [['amount'], 'number'],
            [['invoice_id'], 'exist', 'skipOnError' => true, 'targetClass' => Invoices::class, 'targetAttribute' => ['invoice_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'invoice_id' => Yii::t('app', 'Invoice ID'),
            'item_id' => Yii::t('app', 'Item ID'),
            'description' => Yii::t('app', 'Description'),
            'amount' => Yii::t('app', 'Amount'),
            'item' => Yii::t('app', 'Item'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
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
            ]
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customers::class, ['id' => 'cid'])->via('invoice');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoice()
    {
        return $this->hasOne(Invoices::class, ['id' => 'invoice_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Orders::class, ['id' => 'item_id']);
    }

    /**
     * @inheritdoc
     * @return InvoiceDetailsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new InvoiceDetailsQuery(get_called_class());
    }

    /**
     * Get item labels
     * @return array
     */
    public static function getItemsLabels()
    {
        return [
            static::ITEM_BUY_DOMAIN => Yii::t('app', 'invoice_details.item.buy_domain'),
            static::ITEM_PROLONGATION_DOMAIN => Yii::t('app', 'invoice_details.item.prolongation_domain'),
            static::ITEM_BUY_SSL => Yii::t('app', 'invoice_details.item.buy_ssl'),
            static::ITEM_PROLONGATION_SSL => Yii::t('app', 'invoice_details.item.prolongation_ssl'),
            static::ITEM_CUSTOM_CUSTOMER => Yii::t('app', 'invoice_details.item.custom'),
            static::ITEM_BUY_STORE => Yii::t('app', 'invoice_details.item.buy_store'),
            static::ITEM_BUY_TRIAL_STORE => Yii::t('app', 'invoice_details.item.buy_trial_store'),
            static::ITEM_PROLONGATION_STORE => Yii::t('app', 'invoice_details.item.prolongation_store'),
        ];
    }

    /**
     * @return array
     */
    public static function getOrdersItem(): array
    {
        return [
            static::ITEM_BUY_SSL,
            static::ITEM_BUY_DOMAIN,
            static::ITEM_BUY_STORE,
            static::ITEM_BUY_TRIAL_STORE,
            static::ITEM_PROLONGATION_SSL,
            static::ITEM_PROLONGATION_DOMAIN,
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if ($insert) {
            switch ($this->item) {

                case static::ITEM_BUY_DOMAIN:
                    $order = Orders::findOne($this->item_id);
                    $details = $order->getDetails();
                    $zone_id = ArrayHelper::getValue($details, 'zone');
                    if ($zone_id) {
                        $zone = DomainZones::findOne($zone_id);

                        if ($zone) {
                            $this->description = Yii::t('app', 'invoice_details.description.buy_domain', [
                                'zone' => $zone->zone
                            ]);
                        }
                    }

                    break;

                case static::ITEM_PROLONGATION_DOMAIN:
                    $order = Orders::findOne($this->item_id);
                    $domain = Domains::findOne($order->item_id);

                    $this->description = Yii::t('app', 'invoice_details.description.prolongation_domain', [
                        'zone' => $domain->zone->zone,
                        'domain' => $domain->getDomain(),
                    ]);

                    break;

                case static::ITEM_BUY_SSL:
                    $order = Orders::findOne($this->item_id);
                    $details = $order->getDetails();
                    $item = SslCertItem::findOne($details['item_id']);
                    $this->description = Yii::t('app', 'invoice_details.description.buy_ssl', [
                        'name' => $item->name,
                        'domain' => $order->domain
                    ]);
                    break;

                case static::ITEM_PROLONGATION_SSL:
                    $order = Orders::findOne($this->item_id);
                    $ssl = SslCert::findOne($order->item_id);

                    $this->description = Yii::t('app', 'invoice_details.description.prolongation_ssl', [
                        'name' => $ssl->item->name,
                        'domain' => $ssl->project->site
                    ]);
                    break;

                case static::ITEM_CUSTOM_CUSTOMER:

                case static::ITEM_BUY_STORE:
                    $order = Orders::findOne($this->item_id);
                    $this->description = Yii::t('app', 'invoice_details.description.buy_store', [
                        'domain' => $order->domain
                    ]);
                    break;

                case static::ITEM_BUY_TRIAL_STORE:
                    $order = Orders::findOne($this->item_id);
                    $this->description = Yii::t('app', 'invoice_details.description.buy_trial_store', [
                        'domain' => $order->domain
                    ]);
                    break;

                case static::ITEM_PROLONGATION_STORE:
                    $store = Stores::findOne($this->item_id);
                    $this->description = Yii::t('app', 'invoice_details.description.prolongation_store', [
                        'domain' => $store->domain
                    ]);
                break;
            }
        }
        return parent::beforeSave($insert);
    }

    /**
     * Get domain name
     * @return null|string
     */
    public function getDomain()
    {
        switch ($this->item) {
            case static::ITEM_BUY_DOMAIN:
            case static::ITEM_BUY_SSL:
            case static::ITEM_BUY_STORE:
            case static::ITEM_BUY_TRIAL_STORE:
                $order = Orders::findOne($this->item_id);
                return $order ? $order->getDomain() : '';
            break;

            case static::ITEM_PROLONGATION_STORE:
                $store = Stores::findOne($this->item_id);
                return $store ? $store->getBaseDomain() : '';
            break;

            case static::ITEM_PROLONGATION_SSL:
                $sslCert = SslCert::findOne($this->item_id);
                if (empty($sslCert->project)) {
                    return null;
                }
                $project = $sslCert->project;
                return $project ? $project->getSite() : '';
            break;

            case static::ITEM_CUSTOM_CUSTOMER:
                $customer = $this->customer;
                return $customer ? $customer->email : '';
            break;
        }

        return null;
    }

    /**
     * Get prepared invoice details description
     * @return bool|string
     */
    public function getDescription()
    {
        return $this->description ? DomainsHelper::idnToUtf8($this->description) : null;
    }

    /**
     * Get panel or order
     * @return null|string
     */
    public function getPanel()
    {
        switch ($this->item) {
            case static::ITEM_BUY_SSL:
            case static::ITEM_BUY_DOMAIN:
            case static::ITEM_BUY_STORE:
            case static::ITEM_BUY_TRIAL_STORE:
            case static::ITEM_PROLONGATION_SSL:
            case static::ITEM_PROLONGATION_DOMAIN:
                $order = Orders::findOne($this->item_id);
                return $order;
            break;

            case static::ITEM_PROLONGATION_STORE:
                $store = Stores::findOne($this->item_id);
                return $store;
            break;

            case static::ITEM_CUSTOM_CUSTOMER:
                $customer = Customers::findOne($this->item_id);
                return $customer;
            break;
        }

        return null;
    }

    /**
     * Mark invoice details paid
     * @param string $method
     * @return bool
     */
    public function paid($method)
    {
        switch ($this->item) {
            case static::ITEM_BUY_SSL:
            case static::ITEM_BUY_DOMAIN:
            case static::ITEM_BUY_STORE:
            case static::ITEM_PROLONGATION_SSL:
            case static::ITEM_PROLONGATION_DOMAIN:
                $order = Orders::findOne($this->item_id);
                $order->status = Orders::STATUS_PAID;
                return $order->save(false);

            break;

            case static::ITEM_PROLONGATION_STORE:
                $store = Stores::findOne($this->item_id);
                $lastExpired = $store->expired;

                if (!$store->updateExpired()) {
                    ThirdPartyLog::log(ThirdPartyLog::ITEM_PROLONGATION_STORE, $store->id, $store->getErrors(), 'paid.invoice_details.expired');
                    return false;
                }

                if ($store->trial == Stores::TRIAL_MODE_ON && !$store->activateFullMode()) {
                    ThirdPartyLog::log(ThirdPartyLog::ITEM_PROLONGATION_STORE, $store->id, $store->getErrors(), 'paid.invoice_details.trial_off');
                    return false;
                }

                $ExpiredLogModel = new ExpiredLog();
                $ExpiredLogModel->attributes = [
                    'pid' => $store->id,
                    'expired_last' => $lastExpired,
                    'expired' => $store->expired,
                    'created_at' => time(),
                    'type' => ExpiredLog::getTypeByCode($method)
                ];
                $ExpiredLogModel->save(false);

            return true;

            case static::ITEM_CUSTOM_CUSTOMER:
                return true;
        }

        return false;
    }
}