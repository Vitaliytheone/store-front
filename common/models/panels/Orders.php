<?php

namespace common\models\panels;

use my\components\behaviors\IpBehavior;
use common\components\traits\UnixTimeFormatTrait;
use my\helpers\DomainsHelper;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * This is the model class for table "{{%orders}}".
 *
 * @property integer $id
 * @property integer $cid
 * @property integer $status
 * @property integer $hide
 * @property integer $processing
 * @property integer $date
 * @property string $ip
 * @property string $domain
 * @property string $details
 * @property integer $item
 * @property integer $item_id
 *
 * @property Customers $customer
 * @property Invoices $invoice
 */
class Orders extends ActiveRecord
{
    const STATUS_PENDING = 0;
    const STATUS_PAID = 1;
    const STATUS_ADDED = 2;
    const STATUS_ERROR = 3;
    const STATUS_CANCELED = 4;

    const PROCESSING_YES = 1;
    const PROCESSING_NO = 0;

    const HIDDEN_ON = 1;
    const HIDDEN_OFF = 0;

    const ITEM_BUY_PANEL = 1;
    const ITEM_BUY_DOMAIN = 2;
    const ITEM_BUY_SSL = 3;
    const ITEM_BUY_CHILD_PANEL = 4;
    const ITEM_BUY_STORE = 5;
    const ITEM_PROLONGATION_SSL = 6;
    const ITEM_PROLONGATION_DOMAIN = 7;
    const ITEM_BUY_TRIAL_STORE = 8;
    const ITEM_FREE_SSL = 9;
    const ITEM_PROLONGATION_FREE_SSL = 10;

    use UnixTimeFormatTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return DB_PANELS . '.orders';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cid'], 'required'],
            [['cid', 'status', 'hide', 'processing', 'date', 'item', 'item_id'], 'integer'],
            [['ip', 'domain'], 'string', 'max' => 300],
            [['status'], 'default', 'value' => static::STATUS_PENDING],
            [['item'], 'default', 'value' => static::ITEM_BUY_PANEL],
            [['details'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'cid' => Yii::t('app', 'Cid'),
            'status' => Yii::t('app', 'Status'),
            'hide' => Yii::t('app', 'Hidden'),
            'processing' => Yii::t('app', 'Processing'),
            'date' => Yii::t('app', 'Date'),
            'domain' => Yii::t('app', 'Domain'),
            'ip' => Yii::t('app', 'Ip'),
            'details' => Yii::t('app', 'Details'),
            'item' => Yii::t('app', 'Item'),
            'item_id' => Yii::t('app', 'Item ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customers::class, ['id' => 'cid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoice()
    {
        return $this->hasOne(Invoices::class, ['id' => 'invoice_id'])
            ->viaTable('invoice_details', ['item_id' => 'id'], function ($query) {
//                TODO:: Commented for support Orders without invoices in my/superadmin
//                $query->andWhere(['invoice_details.item' => [
//                    InvoiceDetails::ITEM_BUY_PANEL,
//                    InvoiceDetails::ITEM_BUY_DOMAIN,
//                    InvoiceDetails::ITEM_BUY_SSL,
//                    InvoiceDetails::ITEM_BUY_CHILD_PANEL,
//                    InvoiceDetails::ITEM_BUY_STORE,
//                    InvoiceDetails::ITEM_BUY_TRIAL_STORE,
//                ]]);
            });
    }

    /**
     * Get statuses
     * @return array
     */
    public static function getStatuses()
    {
        return [
            static::STATUS_PENDING => Yii::t('app', 'orders.status.pending'),
            static::STATUS_PAID => Yii::t('app', 'orders.status.paid'),
            static::STATUS_ADDED => Yii::t('app', 'orders.status.added'),
            static::STATUS_ERROR => Yii::t('app', 'orders.status.error'),
            static::STATUS_CANCELED => Yii::t('app', 'orders.status.canceled')
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
     * Get items
     * @return array
     */
    public static function getItems()
    {
        return [
            static::ITEM_BUY_PANEL => Yii::t('app', 'orders.item.buy_panel'),
            static::ITEM_BUY_DOMAIN => Yii::t('app', 'orders.item.buy_domain'),
            static::ITEM_BUY_SSL => Yii::t('app', 'orders.item.buy_ssl'),
            static::ITEM_BUY_CHILD_PANEL => Yii::t('app', 'orders.item.buy_child_panel'),
            static::ITEM_BUY_STORE => Yii::t('app', 'orders.item.buy_store'),
            static::ITEM_PROLONGATION_SSL => Yii::t('app', 'orders.item.prolongation_ssl'),
            static::ITEM_PROLONGATION_DOMAIN => Yii::t('app', 'orders.item.prolongation_domain'),
            static::ITEM_BUY_TRIAL_STORE => Yii::t('app', 'orders.item.trial_store'),
            static::ITEM_FREE_SSL => Yii::t('app', 'orders.item.free_ssl'),
            static::ITEM_PROLONGATION_FREE_SSL => Yii::t('app', 'orders.item.prolongation_free_ssl'),
        ];
    }

    /**
     * Get item name
     * @param int $item
     * @return string
     */
    public static function getItemName($item)
    {
        return ArrayHelper::getValue(static::getItems(), $item, '');
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

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'date',
                ],
                'value' => function() {
                    return time();
                },
            ],
            'ip' => [
                'class' => IpBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'ip',
                ]
            ],
        ];
    }

    /**
     * Get domain
     * @return int|null
     */
    public function getCurrency()
    {
        return ArrayHelper::getValue(Json::decode($this->details), 'currency');
    }

    /**
     * Mark processing 1
     */
    public function process()
    {
        $this->processing = 1;
        $this->save(false);
    }

    /**
     * Mark processing 0
     */
    public function finish()
    {
        $this->processing = 0;
        $this->save(false);
    }

    /**
     * Change status
     * @param int $status
     * @param int $invoice_id
     * @return bool
     */
    public function changeStatus($status)
    {
        switch ($status) {
            case static::STATUS_ADDED:
                $this->status = static::STATUS_ADDED;
            break;

            case static::STATUS_PAID:
                if (static::STATUS_ERROR == $this->status) {
                    $this->processing = 0;
                }
                $this->status = static::STATUS_PAID;
            break;

            case static::STATUS_CANCELED:
                $this->cancel();
                break;

        }

        return $this->save(false);
    }

    /**
     * Can create new order or not
     * Mark order error
     */
    public function makeError()
    {
        $this->status = static::STATUS_ERROR;
        $this->save(false);
    }

    /**
     * Can access
     * @param string $code
     * @param array $params
     * @return bool
     */
    public static function can($code, $params = [])
    {
        $customerId = ArrayHelper::getValue($params, 'customerId');

        switch ($code) {
            case 'create_panel':
                if (empty($customerId)) {
                    return false;
                }

                return Yii::$app->params['pending_orders'] > static::find()->andWhere([
                        'cid' => $customerId,
                        'status' => static::STATUS_PENDING,
                        'item' => static::ITEM_BUY_PANEL
                    ])->count();
            break;

            case 'create_child_panel':
                if (empty($customerId)) {
                    return false;
                }

                $flag = Project::find()->andWhere([
                        'cid' => $customerId,
                        'child_panel' => 0
                    ])->andWhere([
                        'act' => Project::STATUS_ACTIVE,
                    ])->exists();
                
                return $flag;
            break;

            // TODO:: Dummy rules. Populate it for real conditions.
            case 'create_store':
                if (empty($customerId)) {
                    return false;
                }

                return true;
        }

        return false;
    }

    /**
     * Get domain
     * @return string
     */
    public function getDomain()
    {
        return DomainsHelper::idnToUtf8($this->domain);
    }

    /**
     * Cancel order
     */
    public function cancel()
    {
        $transaction = Yii::$app->db->beginTransaction();

        $invoiceDetails = InvoiceDetails::find()
            ->where([
                'item_id' => $this->id
            ])
            ->orders()
        ->one();

        // При отмене инвойса проверить. Есть ли платежы в статусе wait, если есть инвойс не отменяем
        if (!empty($invoiceDetails)) {
            if (Payments::findOne([
                'iid' => $invoiceDetails->invoice_id,
                'status' => Payments::STATUS_WAIT
            ])) {
                return false;
            }
        }

        $this->status = static::STATUS_CANCELED;

        if (!$this->save(false)) {
            return false;
        }

        if (!empty($invoiceDetails)) {
            $invoice = $invoiceDetails->invoice;
            $invoice->status = Invoices::STATUS_CANCELED;
            if (!$invoice->save(false)) {
                $transaction->rollBack();
                return false;
            }
        }

        $transaction->commit();

        return true;
    }
}
