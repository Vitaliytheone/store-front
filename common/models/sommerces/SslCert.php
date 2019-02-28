<?php

namespace common\models\sommerces;

use control_panel\components\behaviors\CustomersCountersBehavior;
use common\components\traits\UnixTimeFormatTrait;
use common\models\common\ProjectInterface;
use common\models\stores\Stores;
use control_panel\helpers\DomainsHelper;
use control_panel\mail\mailers\RenewedSSL;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\sommerces\queries\SslCertQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use control_panel\mail\mailers\CreatedSSL;

/**
 * This is the model class for table "{{%ssl_cert}}".
 *
 * @property integer $id
 * @property integer $cid
 * @property integer $project_type
 * @property integer $pid
 * @property integer $item_id
 * @property integer $status
 * @property integer $checked
 * @property string $domain
 * @property string $csr_code
 * @property string $csr_key
 * @property string $csr_files
 * @property string $details
 * @property string $expiry
 * @property string $expiry_at_timestamp
 * @property integer $created_at
 *
 * @property Customers $customer
 * @property SslCertItem $item
 */
class SslCert extends ActiveRecord
{
    const PROJECT_TYPE_PANEL = 1;
    const PROJECT_TYPE_STORE = 2;

    const STATUS_PENDING = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_PROCESSING = 2;
    const STATUS_PAYMENT_NEEDED = 3;
    const STATUS_CANCELED = 4;
    const STATUS_INCOMPLETE = 5;
    const STATUS_EXPIRED = 6;
    const STATUS_ERROR = 7;
    const STATUS_RENEWED = 8;

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
        return DB_SOMMERCES . '.ssl_cert';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pid', 'cid', 'item_id', 'details'], 'required'],
            [['pid', 'cid', 'item_id', 'status', 'created_at', 'checked', 'project_type'], 'integer'],
            [['details', 'expiry', 'csr_code', 'csr_key'], 'string'],
            [['domain'], 'string', 'max' => 255],
            [['checked'], 'default', 'value' => static::CHECKED_YES],
            [['status'], 'default', 'value' => static::STATUS_PENDING],
            [['cid'], 'exist', 'skipOnError' => true, 'targetClass' => Customers::class, 'targetAttribute' => ['cid' => 'id']],
            [['item_id'], 'exist', 'skipOnError' => true, 'targetClass' => SslCertItem::class, 'targetAttribute' => ['item_id' => 'id']],
            ['csr_files', 'string'],
            ['expiry_at_timestamp', 'integer'],
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
            'project_type' => Yii::t('app', 'Project type'),
            'pid' => Yii::t('app', 'Pid'),
            'item_id' => Yii::t('app', 'Item ID'),
            'status' => Yii::t('app', 'Status'),
            'domain' => Yii::t('app', 'Domain'),
            'checked' => Yii::t('app', 'Is Checked'),
            'csr_code' => Yii::t('app', 'CSR code'),
            'csr_key' => Yii::t('app', 'CSR key'),
            'csr_files' => Yii::t('app', 'CSR files'),
            'details' => Yii::t('app', 'Details'),
            'expiry' => Yii::t('app', 'Expiry'),
            'expiry_at_timestamp' => Yii::t('app', 'Expiry at'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        switch ($this->project_type) {
            case self::PROJECT_TYPE_STORE:
                return $this->hasOne(Stores::class, ['id' => 'pid']);
                break;
            default:
                return $this->hasOne(Stores::class, ['id' => 'pid']);
                break;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(SslCertItem::class, ['id' => 'item_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customers::class, ['id' => 'cid']);
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
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
                ],
                'value' => function() {
                    return time();
                },
            ],
            [
                'class' => CustomersCountersBehavior::class,
                'column' => 'ssl_certs',
                'customerId' => 'cid',
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
            static::STATUS_PENDING => Yii::t('app', 'ssl_cert.status.pending'),
            static::STATUS_ACTIVE => Yii::t('app', 'ssl_cert.status.active'),
            static::STATUS_PROCESSING => Yii::t('app', 'ssl_cert.status.processing'),
            static::STATUS_PAYMENT_NEEDED => Yii::t('app', 'ssl_cert.status.payment_needed'),
            static::STATUS_CANCELED => Yii::t('app', 'ssl_cert.status.canceled'),
            static::STATUS_INCOMPLETE => Yii::t('app', 'ssl_cert.status.incomplete'),
            static::STATUS_EXPIRED => Yii::t('app', 'ssl_cert.status.expired'),
            static::STATUS_ERROR => Yii::t('app', 'ssl_cert.status.ddos_error')
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
            static::STATUS_ERROR,
        ])) {
            $orderDetails = $this->getOrderStatusDetails();
            $this->expiry = ArrayHelper::getValue($orderDetails, 'valid_till');
            $this->checked = static::CHECKED_YES;

            $project = $this->project;

            $project->setSslMode(ProjectInterface::SSL_MODE_ON);
            $project->save(false);

            switch ($project::getProjectType()) {
                case ProjectInterface::PROJECT_TYPE_PANEL:
                    $messagePrefix = 'my';
                    break;
                case ProjectInterface::PROJECT_TYPE_STORE:
                    $messagePrefix = 'sommerce';
                    break;
                default:
                    $messagePrefix = 'my';
                    break;
            }

            $isProlonged = Orders::find()->andWhere(['item' => Orders::ITEM_PROLONGATION_SSL, 'item_id' => $this->id])->exists();

            // Create new unreaded ticket after activate ssl cert.
            // Not for SSL prolongation
            if(!$isProlonged && $project->hasManualPaymentMethods()) {
                $ticket = new Tickets();
                $ticket->customer_id = $this->cid;
                $ticket->is_admin = 1;
                $ticket->subject = Yii::t('app', "ssl.$messagePrefix.created.ticket_subject");
                if ($ticket->save(false)) {
                    $ticketMessage = new TicketMessages();
                    $ticketMessage->ticket_id = $ticket->id;
                    $ticketMessage->admin_id = SuperAdmin::DEFAULT_ADMIN;
                    $ticketMessage->created_at = time();
                    $ticketMessage->message = Yii::t('app', "ssl.$messagePrefix.created.ticket_message", [
                        'domain' => $this->project->getBaseDomain()
                    ]);
                    $ticketMessage->ip = ' ';
                    $ticketMessage->user_agent = ' ';
                    $ticketMessage->save(false);
                }
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

    /**
     * Send prolonged notification
     */
    public function prolongedNotice()
    {
        $mailer = new RenewedSSL([
            'ssl' => $this,
        ]);
        $mailer->send();
    }

    /**
     * Return ssl approver email by passed domain name
     * @param $domain string
     * @return string;
     */
    public static function approverEmailByDomain($domain)
    {
        return "ssladmin@" . $domain;
    }

    /**
     * Return ssl approver email
     * @return string
     */
    public function getApproverEmail()
    {
        return static::approverEmailByDomain($this->getDomain());
    }
}