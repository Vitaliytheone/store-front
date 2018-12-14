<?php

namespace common\models\gateways;

use Yii;
use yii\db\ActiveRecord;
use common\models\gateways\queries\AdminsQuery;

/**
 * This is the model class for table "{{%admins}}".
 *
 * @property int $id
 * @property int $site_id
 * @property string $username
 * @property string $password
 * @property string $auth_key
 * @property int $status 1 - active; 2 - suspended
 * @property string $ip
 * @property int $last_login
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Sites $site
 */
class Admins extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%admins}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['site_id', 'username', 'password', 'auth_key', 'created_at'], 'required'],
            [['site_id', 'last_login', 'created_at', 'updated_at'], 'integer'],
            [['username', 'password', 'auth_key', 'ip'], 'string', 'max' => 255],
            [['status'], 'string', 'max' => 1],
            [['site_id'], 'exist', 'skipOnError' => true, 'targetClass' => Sites::class, 'targetAttribute' => ['site_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'site_id' => Yii::t('app', 'Site ID'),
            'username' => Yii::t('app', 'Username'),
            'password' => Yii::t('app', 'Password'),
            'auth_key' => Yii::t('app', 'Auth Key'),
            'status' => Yii::t('app', 'Status'),
            'ip' => Yii::t('app', 'Ip'),
            'last_login' => Yii::t('app', 'Last Login'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSite()
    {
        return $this->hasOne(Sites::class, ['id' => 'site_id']);
    }

    /**
     * @inheritdoc
     * @return AdminsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new AdminsQuery(get_called_class());
    }
}