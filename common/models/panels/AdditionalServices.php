<?php

namespace common\models\panels;

use my\helpers\DomainsHelper;
use Yii;
use yii\db\ActiveRecord;
use common\models\panels\queries\AdditionalServicesQuery;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "additional_services".
 *
 * @property int $id
 * @property string $name
 * @property int $res
 * @property int $provider_id
 * @property string $apihelp
 * @property string $content
 * @property string $name_script
 * @property string $currency
 * @property int $type
 * @property int $store
 * @property int $status
 * @property int $search
 * @property string $username
 * @property string $password
 * @property string $skype
 * @property string $type_name
 * @property int $sc
 * @property int $start_count
 * @property int $callback
 * @property int $refill
 * @property int $cancel
 * @property int $auto_services
 * @property int $auto_order
 * @property int $processing
 * @property int $show_id
 * @property int $input_type
 * @property string $proxy
 * @property int $string_type
 * @property int $string_name
 * @property string $params
 * @property string $services_params
 * @property string $sender_params
 * @property string $getstatus_params
 * @property string $type_services
 * @property string $date
 * @property int $default_order_status
 * @property int $manual_order_status
 * @property int $send_method
 * @property int $service_auto_min
 * @property int $service_auto_max
 * @property int $provider_rate
 * @property int $service_view
 * @property string $service_options
 * @property int $provider_service_id_label
 * @property string $provider_service_settings
 * @property string $provider_service_api_error
 * @property int $import
 * @property int $service_description
 * @property int $service_auto_rate
 * @property int $service_count
 * @property int $service_inuse_count
 *
 * @property UserServices[] $userServices
 */
class AdditionalServices extends ActiveRecord
{
    const TYPE_EXTERNAL = 0;
    const TYPE_INTERNAL = 1;

    const STATUS_ACTIVE = 0;
    const STATUS_FROZEN = 1;
    const STATUS_PROCESSING = 2;
    const STATUS_NOT_UPDATED = 3;

    const AUTO_SERVICE_NOT_AUTO_LIST = 0;
    const AUTO_SERVICE_AUTO_LIST = 1;
    const AUTO_SERVICE_CUSTOM = 2;

    const REFILL_NO = 0;
    const REFILL_YES = 1;
    const REFILL_YES_SECOND = 2;

    const CANCEL_NO = 0;
    const CANCEL_YES = 1;
    const CANCEL_YES_SECOND = 2;

    const START_COUNT_NO = 0;
    const START_COUNT_YES = 1;

    const SEND_METHOD_SIMPLE = 1;
    const SEND_METHOD_PERFECTPANEL = 0;
    const SEND_METHOD_MULTI = 2;
    const SEND_METHOD_MASS = 4;

    const SERVICE_VIEW_SIMPLE = 0;
    const SERVICE_VIEW_PERFECTPANEL = 1;
    const SERVICE_VIEW_MULTI = 2;
    const SERVICE_VIEW_UNIQUE = 3;

    const DEFAULT_NO = 0;
    const DEFAULT_YES = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return DB_PANELS . '.additional_services';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'apihelp', 'type', 'status', 'service_view', 'store'], 'required'],
            [['res', 'provider_id', 'type', 'status', 'search', 'sc', 'auto_services', 'auto_order', 'processing', 'show_id', 'input_type', 'string_type', 'string_name', 'provider_service_id_label', 'service_count', 'service_inuse_count'], 'integer'],
            [['content', 'name_script', 'getstatus_params'], 'string'],
            [['date'], 'safe'],
            [['name'], 'string', 'max' => 32],
            [['apihelp'], 'string', 'max' => 2000],
            [['name_script', 'proxy'], 'string', 'max' => 1000],
            [['currency'], 'string', 'max' => 10],
            [['proxy'], 'string', 'max' => 1000],
            [['service_view'], 'string', 'max' => 3],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'res' => Yii::t('app', 'Res'),
            'apihelp' => Yii::t('app', 'Apihelp'),
            'content' => Yii::t('app', 'Content'),
            'type' => Yii::t('app', 'Type'),
            'status' => Yii::t('app', 'Status'),
            'search' => Yii::t('app', 'Search'),
            'username' => Yii::t('app', 'Username'),
            'password' => Yii::t('app', 'Password'),
            'skype' => Yii::t('app', 'Skype'),
            'type_name' => Yii::t('app', 'Type Name'),
            'start_count' => Yii::t('app', 'Start Count'),
            'refill' => Yii::t('app', 'Refill'),
            'cancel' => Yii::t('app', 'Cancel'),
            'auto_services' => Yii::t('app', 'Auto Services'),
            'auto_order' => Yii::t('app', 'Auto Order'),
            'processing' => Yii::t('app', 'Processing'),
            'show_id' => Yii::t('app', 'Show ID'),
            'input_type' => Yii::t('app', 'Input Type'),
            'proxy' => Yii::t('app', 'Proxy'),
            'string_type' => Yii::t('app', 'String Type'),
            'string_name' => Yii::t('app', 'String name'),
            'params' => Yii::t('app', 'Params'),
            'type_services' => Yii::t('app', 'Type Services'),
            'date' => Yii::t('app', 'Date'),
            'provider_service_settings' => Yii::t('app', 'Provider Service Settings'),
            'service_view' => Yii::t('app', 'Service View'),
            'provider_service_error' => Yii::t('app', 'Provider Service error'),
            'service_options' => Yii::t('app', 'Service option'),
            'provider_service_id_label' => Yii::t('app', 'Provider Service Id Label'),
            'store' => Yii::t('app', 'Store'),
            'service_description' => Yii::t('app', 'Service Description'),
            'import' => Yii::t('app', 'Import'),
            'currency' => Yii::t('app', 'Currency'),
            'provider_id' => Yii::t('app', 'Provider ID'),
            'name_script' => Yii::t('app', 'Script Name'),
            'sender_params' => Yii::t('app', 'Sender Params'),
            'send_method' => Yii::t('app', 'Send Method'),
            'provider_service_api_error' => Yii::t('app', 'Provider Service API Error'),
            'service_auto_min' => Yii::t('app', 'Service Auto Min'),
            'service_auto_max' => Yii::t('app', 'Service Auto Max'),
            'provider_rate' => Yii::t('app', 'Provider Rate'),
            'service_auto_rate' => Yii::t('app', 'Service Auto Rate'),
            'getstatus_params' => Yii::t('app', 'Getstatus Params'),
        ];
    }

    /**
     * @inheritdoc
     * @return AdditionalServicesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new AdditionalServicesQuery(get_called_class());
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            // Auto increment res value
            $maxRes = static::find()->max('res');
            $this->res = (int)$maxRes + 1;
            $this->provider_id = $this->res;
        }

        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    /**
     * Generate api help url
     * @param $domain
     */
    public function generateApiHelp($domain)
    {
        $this->apihelp = 'http://' . $domain . '/api';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserServices()
    {
        return $this->hasMany(UserServices::class, ['aid' => 'id']);
    }

    /**
     * Get available statuses values
     * @return array
     */
    public static function getStatuses(): array
    {
        return [
            static::STATUS_ACTIVE => Yii::t('app', 'additional_service.status.ok'),
            static::STATUS_FROZEN => Yii::t('app', 'additional_service.status.broken'),
            static::STATUS_PROCESSING => Yii::t('app', 'additional_service.status.send_only'),
            static::STATUS_NOT_UPDATED => Yii::t('app', 'additional_service.status.not_updated'),
        ];
    }

    /**
     * Get available types values
     * @return array
     */
    public static function getTypes(): array
    {
        return [
            static::TYPE_EXTERNAL => Yii::t('app', 'additional_service.type.external'),
            static::TYPE_INTERNAL => Yii::t('app', 'additional_service.type.internal'),
        ];
    }

    /**
     * Get auto_service values
     * @return array
     */
    public static function getAutoServices(): array
    {
        return [
            static::AUTO_SERVICE_NOT_AUTO_LIST => Yii::t('app/superadmin', 'providers.services_list.auto_services_manual'),
            static::AUTO_SERVICE_AUTO_LIST => Yii::t('app/superadmin', 'providers.services_list.auto_services_auto'),
            static::AUTO_SERVICE_CUSTOM => Yii::t('app/superadmin', 'providers.services_list.auto_services_custom'),
        ];
    }

    /**
     * Get auto_orders values
     * @return array
     */
    public static function getAutoOrders(): array
    {
        return [
            static::SEND_METHOD_PERFECTPANEL => Yii::t('app/superadmin', 'providers.auto_order.perfectpanel'),
            static::SEND_METHOD_SIMPLE => Yii::t('app/superadmin', 'providers.auto_order.simple'),
            static::SEND_METHOD_MULTI => Yii::t('app/superadmin', 'providers.auto_order.multi'),
            static::SEND_METHOD_MASS => Yii::t('app/superadmin', 'providers.auto_order.mass'),
        ];
    }

    /**
     * Get auto_orders values
     * @return array
     */
    public static function getSendMethods(): array
    {
        return [
            static::SEND_METHOD_PERFECTPANEL => Yii::t('app/superadmin', 'providers.auto_order.perfectpanel'),
            static::SEND_METHOD_SIMPLE => Yii::t('app/superadmin', 'providers.auto_order.simple'),
            static::SEND_METHOD_MULTI => Yii::t('app/superadmin', 'providers.auto_order.multi'),
            static::SEND_METHOD_MASS => Yii::t('app/superadmin', 'providers.auto_order.mass'),
        ];
    }

    /**
     * Get start_count values
     * @return array
     */
    public static function getStartCounts(): array
    {
        return [
            static::START_COUNT_NO => Yii::t('app/superadmin', 'providers.list.no'),
            static::START_COUNT_YES => Yii::t('app/superadmin', 'providers.list.yes'),
        ];
    }

    /**
     * Get refill values
     * @return array
     */
    public static function getRefill(): array
    {
        return [
            static::REFILL_NO => Yii::t('app/superadmin', 'providers.list.no'),
            static::REFILL_YES => Yii::t('app/superadmin', 'providers.list.yes'),
            static::REFILL_YES_SECOND => Yii::t('app/superadmin', 'providers.list.yes'),
        ];
    }

    /**
     * Get cancel values
     * @return array
     */
    public static function getCancel(): array
    {
        return [
            static::CANCEL_NO => Yii::t('app/superadmin', 'providers.list.no'),
            static::CANCEL_YES => Yii::t('app/superadmin', 'providers.list.yes'),
            static::CANCEL_YES_SECOND => Yii::t('app/superadmin', 'providers.list.yes'),
        ];
    }

    /**
     * Get service_view value
     * @return array
     */
    public static function getServiceView(): array
    {
        return [
            static::SERVICE_VIEW_SIMPLE => Yii::t('app/superadmin', 'providers.service_view.simple'),
            static::SERVICE_VIEW_PERFECTPANEL => Yii::t('app/superadmin', 'providers.service_view.perfectpanel'),
            static::SERVICE_VIEW_MULTI => Yii::t('app/superadmin', 'providers.service_view.multi'),
            static::SERVICE_VIEW_UNIQUE => Yii::t('app/superadmin', 'providers.service_view.unique'),
        ];
    }

    /**
     * Get default yes/no value
     * @return array
     */
    public static function getDefaultBool(): array
    {
        return [
            static::DEFAULT_NO => Yii::t('app/superadmin', 'providers.list.no'),
            static::DEFAULT_YES => Yii::t('app/superadmin', 'providers.list.yes'),
        ];
    }

    /**
     * Get service_view string name
     * @param $serviceView integer
     * @return string
     */
    public static function getServiceViewName($serviceView): string
    {
        return ArrayHelper::getValue(static::getServiceView(), $serviceView, Yii::t('app/superadmin', 'providers.service_view.unique'));
    }

    /**
     * Get start_count string name
     * @param start_count
     * @return mixed
     */
    public static function getStartCountName($sc)
    {
        return ArrayHelper::getValue(static::getStartCounts(), $sc, '');
    }

    /**
     * Get refill string name
     * @param $refill
     * @return mixed
     */
    public static function getRefillName($refill)
    {
        return ArrayHelper::getValue(static::getRefill(), $refill, '');
    }

    public static function getCancelName($cancel)
    {
        return ArrayHelper::getValue(static::getCancel(), $cancel, '');
    }

    /**
     * Get auto_order string name
     * @param $autoOrder
     * @return mixed
     */
    public static function getAutoOrderName($autoOrder)
    {
        return ArrayHelper::getValue(static::getAutoOrders(), $autoOrder, '');
    }

    /**
     * Get send_method string name
     * @param $autoOrder
     * @return string
     */
    public static function getSendMethodName($autoOrder): string
    {
        return ArrayHelper::getValue(static::getSendMethods(), $autoOrder, '');
    }

    /**
     * Get auto_service string name
     * @param $autoService
     * @return mixed
     */
    public static function getAutoServiceName($autoService)
    {
        return ArrayHelper::getValue(static::getAutoServices(), $autoService, '');
    }

    /**
     * Get status string name
     * @return string
     */
    public function getStatusName()
    {
        return static::getStatusNameString($this->status);
    }

    /**
     * Get status string name by status
     * @param $status
     * @return mixed
     */
    public static function getStatusNameString($status)
    {
        return ArrayHelper::getValue(static::getStatuses(), $status, '');
    }

    /**
     * Get type string name
     * @return string
     */
    public function getTypeName()
    {
        return static::getTypeNameString($this->type);
    }

    /**
     * Get type string name by type
     * @param $type
     * @return mixed
     */
    public static function getTypeNameString($type)
    {
        return ArrayHelper::getValue(static::getTypes(), $type, '');
    }

    /**
     * Get count assigned projects
     * @return array
     */
    public function getProjects()
    {
        $projects = [];

        foreach ($this->userServices as $service) {
            $projects[] = $service->project;
        }
        return $projects;
    }

    /**
     * Get count assigned projects with uses this provider
     * @return array
     */
    public function getUseProjects()
    {
        $projects = $this->getProjects();
        $usesProjects = [];

        /**
         * @var $project Project
         */
        foreach ($projects as $project) {
            if (Project::STATUS_ACTIVE != $project->act || empty($project->db)) {
                continue;
            }

            $connection = $project->getDbConnection();

            if (!$connection) {
                continue;
            }

            $exist = (new Query())
                ->from('services')
                ->andWhere([
                    'res' => $this->res,
                    'act' => 1
                ])
                ->scalar($connection);

            if (!$exist) {
                continue;
            }

            $usesProjects[] = $project;
        }

        return $usesProjects;
    }

    /**
     * Change status
     * @param int $status
     * @return bool
     */
    public function changeStatus($status)
    {
        if (!in_array($status, array_keys(static::getStatuses()))) {
            return false;
        }

        $this->status = $status;

        return $this->save(false);
    }

    /**
     * Get name
     * @return string
     */
    public function getName()
    {
        return DomainsHelper::idnToUtf8($this->name);
    }
}
