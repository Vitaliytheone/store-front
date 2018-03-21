<?php
namespace common\models\panels;

use common\models\panels\queries\SuperToolsScannerQuery;
use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "super_tools_scanner".
 *
 * @property integer $id
 * @property integer $panel_id
 * @property integer $panel
 * @property string $domain
 * @property string $server_ip
 * @property integer $status
 * @property string $details
 * @property integer $created_at
 * @property integer $updated_at
 */
class SuperToolsScanner extends ActiveRecord
{
    // Panels
    const PANEL_LEVOPANEL = 1;
    const PANEL_PANELFIRE = 2;

    // Panel statuses
    const PANEL_STATUS_ACTIVE = 1;
    const PANEL_STATUS_DISABLED = 2;
    const PANEL_STATUS_PERFECTPANEL = 3;
    const PANEL_STATUS_MOVED = 5;

    /**
     * All possible panels
     * @var array
     */
    public static $panels = [
        self::PANEL_LEVOPANEL,
        self::PANEL_PANELFIRE,
    ];

    /**
     * All possible statuses
     * @var array
     */
    public static $statuses = [
        self::PANEL_STATUS_ACTIVE,
        self::PANEL_STATUS_DISABLED,
        self::PANEL_STATUS_PERFECTPANEL,
        self::PANEL_STATUS_MOVED,
    ];

    /**
     * Return statuses labels
     * @return array
     */
    public static function statusesLabels()
    {
        return [
            self::PANEL_STATUS_ACTIVE       => Yii::t('app/superadmin','tools.levopanel.status.active'),
            self::PANEL_STATUS_DISABLED     => Yii::t('app/superadmin', 'tools.levopanel.status.disabled'),
            self::PANEL_STATUS_PERFECTPANEL => Yii::t('app/superadmin', 'tools.levopanel.status.perfect'),
            self::PANEL_STATUS_MOVED        => Yii::t('app/superadmin', 'tools.levopanel.status.moved'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => [
                        'created_at',
                        'updated_at'
                    ],
                    self::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
                'value' => function() {
                    return time();
                },
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['panel_id', 'panel', 'status', 'created_at', 'updated_at'], 'integer'],
            [['domain'], 'string', 'max' => 255],
            [['server_ip'], 'string', 'max' => 50],
            [['details'], 'string', 'max' => 1000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'panel_id' => 'Panel ID',
            'panel' => 'Panel',
            'domain' => 'Domain',
            'server_ip' => 'Server Ip',
            'status' => 'Status',
            'details' => 'Details',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Return status name by status
     * @param $status
     * @return mixed
     */
    public static function getStatusName($status)
    {
        return ArrayHelper::getValue(static::statusesLabels(), $status, $status);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'super_tools_scanner';
    }

    /**
     * @inheritdoc
     * @return SuperToolsScannerQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SuperToolsScannerQuery(get_called_class());
    }

    /**
     * Return last panel id from db
     * @param $panel int
     * @return integer
     */
    public static function getLastPanelId($panel)
    {
        return (new Query())
            ->select('panel_id')
            ->from(static::tableName())
            ->andWhere(['panel' => $panel])
            ->max('panel_id');
    }

    /**
     * Return next panel id from db
     * @param $panel
     * @return int
     */
    public static function getNextPanelId($panel)
    {
        return static::getLastPanelId($panel) + 1;
    }
}
