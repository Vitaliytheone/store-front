<?php

namespace common\models\panels;

use my\components\traits\UnixTimeFormatTrait;
use my\helpers\DomainsHelper;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\panels\queries\SslCertQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use app\mail\mailers\CreatedSSL;

/**
 * This is the model class for table "{{%ssl_cert}}".
 *
 * @property integer $id
 * @property integer $cid
 * @property integer $pid
 * @property integer $item_id
 * @property integer $status
 * @property integer $checked
 * @property string $domain
 * @property string $details
 * @property string $expiry
 * @property integer $created_at
 *
 * @property Project $project
 * @property Customers $customer
 * @property SslCertItem $item
 */
class SslCert extends ActiveRecord
{
    const STATUS_PENDING = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_PROCESSING = 2;
    const STATUS_PAYMENT_NEEDED = 3;
    const STATUS_CANCELED = 4;
    const STATUS_INCOMPLETE = 5;
    const STATUS_EXPIRED = 6;
    const STATUS_DDOS_ERROR = 7;

    const CHECKED_NO = 0;
    const CHECKED_YES = 1;

    const SSL_CERT_PERIOD = 12;

    const DETAILS_ORDER = 'order';
    const DETAILS_ORDER_STATUS = 'order_status';
    const DETAILS_CSR = 'csr';

    use UnixTimeFormatTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%ssl_cert}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pid', 'cid', 'item_id', 'details'], 'required'],
            [['pid', 'cid', 'item_id', 'status', 'created_at', 'checked'], 'integer'],
            [['details', 'expiry'], 'string'],
            [['domain'], 'string', 'max' => 255],
            [['checked'], 'default', 'value' => static::CHECKED_YES],
            [['status'], 'default', 'value' => static::STATUS_PENDING],
            [['cid'], 'exist', 'skipOnError' => true, 'targetClass' => Customers::className(), 'targetAttribute' => ['cid' => 'id']],
            [['item_id'], 'exist', 'skipOnError' => true, 'targetClass' => SslCertItem::className(), 'targetAttribute' => ['item_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'cid' => Yii::t('app', 'Customer'),
            'pid' => Yii::t('app', 'Pid'),
            'item_id' => Yii::t('app', 'Item ID'),
            'status' => Yii::t('app', 'Status'),
            'domain' => Yii::t('app', 'Domain'),
            'checked' => Yii::t('app', 'Is Checked'),
            'details' => Yii::t('app', 'Details'),
            'expiry' => Yii::t('app', 'Expiry'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::className(), ['id' => 'pid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(SslCertItem::className(), ['id' => 'item_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customers::className(), ['id' => 'cid']);
    }

    /**
     * @inheritdoc
     * @return SslCertQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SslCertQuery(get_called_class());
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
     * Get statuses
     * @return array
     */
    public static function getStatuses()
    {
        return [
            static::STATUS_PENDING => Yii::t('app', 'ssl_cert.status.pending'),
            static::STATUS_ACTIVE => Yii::t('app', 'ssl_cert.status.active'),
            static::STATUS_PROCESSING => Yii::t('app', 'ssl_cert.status.processing'),
            static::STATUS_PAYMENT_NEEDED => Yii::t('app', 'ssl_cert.status.payment_needed'),
            static::STATUS_CANCELED => Yii::t('app', 'ssl_cert.status.canceled'),
            static::STATUS_INCOMPLETE => Yii::t('app', 'ssl_cert.status.incomplete'),
            static::STATUS_EXPIRED => Yii::t('app', 'ssl_cert.status.expired'),
            static::STATUS_DDOS_ERROR => Yii::t('app', 'ssl_cert.status.ddos_error')
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
     * @param array $details
     */
    public function setDetails($details)
    {
        $this->details = Json::encode($details);
    }

    /**
     * Get details
     * @return mixed
     */
    public function getDetails()
    {
        return Json::decode($this->details);
    }

    /**
     * Change status
     * @param int $status
     * @return bool
     */
    public function changeStatus($status)
    {
        $this->status = $status;

        if (in_array($status, [
            static::STATUS_ACTIVE,
            static::STATUS_DDOS_ERROR,
        ])) {
            $orderDetails = $this->getOrderStatusDetails();
            $this->expiry = ArrayHelper::getValue($orderDetails, 'valid_till');
            $this->checked = static::CHECKED_YES;

            $this->project->ssl = 1;
            $this->project->save(false);
            
            // Create new unreaded ticket after activate ssl cert
            $ticket = new Tickets();
            $ticket->cid = $this->cid;
            $ticket->admin = 1;
            $ticket->subject = Yii::t('app', 'ssl.created.ticket_subject');
            if ($ticket->save(false)) {
                $ticketMessage = new TicketMessages();
                $ticketMessage->tid = $ticket->id;
                $ticketMessage->uid = SuperAdmin::DEFAULT_ADMIN;
                $ticketMessage->date = time();
                $ticketMessage->message = Yii::t('app', 'ssl.created.ticket_message', [
                    'domain' => $this->project->getSite()
                ]);
                $ticketMessage->save(false);
            }
        }

        return $this->save(false);
    }

    /**
     * Get order details
     * @return mixed
     */
    public function getOrderDetails()
    {
        return ArrayHelper::getValue($this->getDetails(), static::DETAILS_ORDER);
    }

    /**
     * Get csr details
     * @return mixed
     */
    public function getCsrDetails()
    {
        return ArrayHelper::getValue($this->getDetails(), static::DETAILS_CSR);
    }

    /**
     * Get order status details
     * @return mixed
     */
    public function getOrderStatusDetails()
    {
        return ArrayHelper::getValue($this->getDetails(), static::DETAILS_ORDER_STATUS);
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
     * Set order details
     * @param array $details
     */
    public function setOrderDetails($details)
    {
        return $this->setItemDetails($details, static::DETAILS_ORDER);
    }

    /**
     * Set order status details
     * @param array $details
     */
    public function setOrderStatusDetails($details)
    {
        return $this->setItemDetails($details, static::DETAILS_ORDER_STATUS);
    }

    /**
     * Set scr details
     * @param array $details
     */
    public function setCsrDetails($details)
    {
        return $this->setItemDetails($details, static::DETAILS_CSR);
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
     * Send created notification
     */
    public function createdNotice()
    {
        $mailer = new CreatedSSL([
            'ssl' => $this
        ]);
        $mailer->send();
    }
}
