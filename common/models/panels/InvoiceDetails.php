<?php

namespace common\models\panels;

use app\helpers\DomainsHelper;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\panels\queries\InvoiceDetailsQuery;
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
 * @property Project $project
 * @property Customers $customer
 */
class InvoiceDetails extends ActiveRecord
{
    const ITEM_BUY_PANEL = 1;
    const ITEM_PROLONGATION_PANEL = 2;
    const ITEM_BUY_DOMAIN = 3;
    const ITEM_PROLONGATION_DOMAIN = 4;
    const ITEM_BUY_SSL = 5;
    const ITEM_PROLONGATION_SSL = 6;
    const ITEM_BUY_CHILD_PANEL = 7;
    const ITEM_PROLONGATION_CHILD_PANEL = 8;
    const ITEM_CUSTOM_CUSTOMER = 9;
    const ITEM_CUSTOM_PANEL = 10;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%invoice_details}}';
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
            [['invoice_id'], 'exist', 'skipOnError' => true, 'targetClass' => Invoices::className(), 'targetAttribute' => ['invoice_id' => 'id']],
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
                'class' => TimestampBehavior::className(),
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
        return $this->hasOne(Customers::className(), ['id' => 'cid'])->via('invoice');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoice()
    {
        return $this->hasOne(Invoices::className(), ['id' => 'invoice_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Orders::className(), ['id' => 'item_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::className(), ['id' => 'item_id']);
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
            static::ITEM_BUY_PANEL => Yii::t('app', 'invoice_details.item.buy_panel'),
            static::ITEM_PROLONGATION_PANEL => Yii::t('app', 'invoice_details.item.prolongation_panel'),
            static::ITEM_BUY_DOMAIN => Yii::t('app', 'invoice_details.item.buy_domain'),
            static::ITEM_PROLONGATION_DOMAIN => Yii::t('app', 'invoice_details.item.prolongation_domain'),
            static::ITEM_BUY_SSL => Yii::t('app', 'invoice_details.item.buy_ssl'),
            static::ITEM_PROLONGATION_SSL => Yii::t('app', 'invoice_details.item.prolongation_ssl'),
            static::ITEM_BUY_CHILD_PANEL => Yii::t('app', 'invoice_details.item.buy_child_panel'),
            static::ITEM_CUSTOM_CUSTOMER => Yii::t('app', 'invoice_details.item.custom'),
            static::ITEM_CUSTOM_PANEL => Yii::t('app', 'invoice_details.item.custom'),
        ];
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            switch ($this->item) {
                case static::ITEM_BUY_PANEL:
                    $order = Orders::findOne($this->item_id);
                    $this->description = Yii::t('app', 'invoice_details.description.buy_panel', [
                        'domain' => $order->domain
                    ]);
                    break;

                case static::ITEM_PROLONGATION_PANEL:
                    $project = Project::findOne($this->item_id);
                    $this->description = Yii::t('app', 'invoice_details.description.prolongation_panel', [
                        'domain' => $project->site
                    ]);
                    break;

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
                    $domain = Domains::findOne($this->item_id);
                    $this->description = $domain->zone->zone . ' domain renewal for 1 year';
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
                    $ssl = SslCert::findOne($this->item_id);
                    $this->description = Yii::t('app', 'invoice_details.description.prolongation_ssl', [
                        'name' => $ssl->item->name,
                        'domain' => $ssl->project->site
                    ]);
                    break;

                case static::ITEM_BUY_CHILD_PANEL:
                    $order = Orders::findOne($this->item_id);
                    $this->description = Yii::t('app', 'invoice_details.description.buy_child_panel', [
                        'domain' => $order->domain
                    ]);
                break;

                case static::ITEM_PROLONGATION_CHILD_PANEL:
                    $project = Project::findOne($this->item_id);
                    $this->description = Yii::t('app', 'invoice_details.description.prolongation_child_panel', [
                        'domain' => $project->site
                    ]);
                break;

                case static::ITEM_CUSTOM_CUSTOMER:
                case static::ITEM_CUSTOM_PANEL:
                    $this->description = !empty($this->description) ? $this->description : Yii::t('app', 'invoice_details.description.custom');
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
            case static::ITEM_BUY_PANEL:
            case static::ITEM_BUY_SSL:
            case static::ITEM_BUY_CHILD_PANEL:
                $order = Orders::findOne($this->item_id);
                return $order ? $order->getDomain() : '';
            break;

            case static::ITEM_PROLONGATION_PANEL:
            case static::ITEM_PROLONGATION_CHILD_PANEL:
            case static::ITEM_CUSTOM_PANEL:
                $project = Project::findOne($this->item_id);
                return $project ? $project->getSite() : '';
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
            case static::ITEM_BUY_PANEL:
            case static::ITEM_BUY_SSL:
            case static::ITEM_BUY_DOMAIN:
            case static::ITEM_BUY_CHILD_PANEL:
                $order = Orders::findOne($this->item_id);
                return $order;
                break;

            case static::ITEM_PROLONGATION_PANEL:
            case static::ITEM_PROLONGATION_CHILD_PANEL:
            case static::ITEM_CUSTOM_PANEL:
                $project = Project::findOne($this->item_id);
                return $project;
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
     * @param integer $method
     * @return bool
     */
    public function paid($method)
    {
        switch ($this->item) {
            case static::ITEM_BUY_PANEL:
            case static::ITEM_BUY_SSL:
            case static::ITEM_BUY_DOMAIN:
            case static::ITEM_BUY_CHILD_PANEL:
                $order = Orders::findOne($this->item_id);
                $order->status = Orders::STATUS_PAID;
                return $order->save(false);

            break;

            case static::ITEM_PROLONGATION_PANEL:
            case static::ITEM_PROLONGATION_CHILD_PANEL:
                $project = Project::findOne($this->item_id);
                $lastExpired = $project->expired;

                if (!$project->updateExpired()) {
                    ThirdPartyLog::log(ThirdPartyLog::ITEM_PROLONGATION_PANEL, $project->id, $project->getErrors(), 'paid.invoice_details.expired');
                    return false;
                }

                $ExpiredLogModel = new ExpiredLog();
                $ExpiredLogModel->attributes = [
                    'pid' => $project->id,
                    'expired_last' => $lastExpired,
                    'expired' => $project->expired,
                    'created_at' => time(),
                    'type' => ExpiredLog::getTypeByGateway($method)
                ];
                $ExpiredLogModel->save(false);

            return true;

            case static::ITEM_CUSTOM_CUSTOMER:
            case static::ITEM_CUSTOM_PANEL:
                return true;
        }

        return false;
    }
}
