<?php

namespace common\models\panels;

use Yii;
use yii\db\ActiveRecord;
use common\models\panels\queries\UserServicesQuery;

/**
 * This is the model class for table "{{%user_services}}".
 *
/**
 * This is the model class for table "{{%user_services}}".
 *
 * @property int $id
 * @property int $pid
 * @property int $panel_id - Дублирует поле pid
 * @property int $aid
 * @property int $provider_id - Дублирует поле aid
 * @property string $login
 * @property string $passwd
 * @property string $apikey
 *
 * @property Project $project
 * @property AdditionalServices $additionalService
 */
class UserServices extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return DB_PANELS . '.user_services';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pid', 'panel_id', 'aid', 'provider_id'], 'integer'],
            [['login', 'passwd', 'apikey'], 'string', 'max' => 300],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'pid' => Yii::t('app', 'Panel ID'),
            'panel_id' => Yii::t('app', 'Panel ID'),
            'aid' => Yii::t('app', 'Provider ID'),
            'provider_id' => Yii::t('app', 'Provider ID'),
            'login' => Yii::t('app', 'Login'),
            'passwd' => Yii::t('app', 'Password'),
            'apikey' => Yii::t('app', 'API Key'),
        ];
    }

    /**
     * @inheritdoc
     * @return UserServicesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserServicesQuery(get_called_class());
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::class, ['id' => 'panel_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdditionalService()
    {
        return $this->hasOne(AdditionalServices::class, ['id' => 'provider_id']);
    }
}
