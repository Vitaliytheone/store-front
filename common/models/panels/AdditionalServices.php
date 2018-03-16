<?php

namespace common\models\panels;

use app\helpers\DomainsHelper;
use Yii;
use yii\db\ActiveRecord;
use common\models\panels\queries\AdditionalServicesQuery;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%additional_services}}".
 *
 * @property integer $id
 * @property string $name
 * @property integer $res
 * @property string $apihelp
 * @property string $content
 * @property integer $type
 * @property integer $status
 * @property integer $search
 * @property string $username
 * @property string $password
 * @property string $skype
 * @property string $type_name
 * @property integer $sc
 * @property integer $refill
 * @property integer $cancel
 * @property integer $auto_services
 * @property integer $auto_order
 * @property integer $processing
 * @property integer $show_id
 * @property integer $input_type
 * @property string $proxy
 * @property integer $string_type
 * @property integer $string_name
 * @property string $params
 * @property string $type_services
 * @property string $date
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

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%additional_services}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'apihelp', 'type', 'status'], 'required'],
            [['res', 'type', 'status', 'search', 'sc', 'refill', 'cancel', 'auto_services', 'auto_order', 'processing', 'show_id', 'input_type', 'string_type', 'string_name'], 'integer'],            [['content'], 'string'],
            [['date'], 'safe'],
            [['name'], 'string', 'max' => 32],
            [['apihelp'], 'string', 'max' => 2000],
            [['username', 'password', 'skype', 'type_name'], 'string', 'max' => 300],
            [['proxy'], 'string', 'max' => 1000],
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
            'sc' => Yii::t('app', 'Sc'),
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
        return $this->hasMany(UserServices::className(), ['aid' => 'id']);
    }

    /**
     * Get available statuses values
     * @return array
     */
    public static function getStatuses()
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
    public static function getTypes()
    {
        return [
            static::TYPE_EXTERNAL => Yii::t('app', 'additional_service.type.external'),
            static::TYPE_INTERNAL => Yii::t('app', 'additional_service.type.internal'),
        ];
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
