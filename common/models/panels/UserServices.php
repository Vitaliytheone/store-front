<?php

namespace common\models\panels;

use Yii;
use yii\db\ActiveRecord;
use common\models\panels\queries\UserServicesQuery;

/**
 * This is the model class for table "{{%user_services}}".
 *
 * @property integer $id
 * @property integer $pid
 * @property integer $aid
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
        return '{{%user_services}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pid', 'aid'], 'required'],
            [['pid', 'aid'], 'integer'],
            [['login', 'passwd', 'apikey'], 'default', 'value' => null],
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
            'pid' => Yii::t('app', 'Pid'),
            'aid' => Yii::t('app', 'Aid'),
            'login' => Yii::t('app', 'Login'),
            'passwd' => Yii::t('app', 'Passwd'),
            'apikey' => Yii::t('app', 'Apikey'),
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
        return $this->hasOne(Project::className(), ['id' => 'pid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdditionalService()
    {
        return $this->hasOne(AdditionalServices::className(), ['id' => 'aid']);
    }
}
