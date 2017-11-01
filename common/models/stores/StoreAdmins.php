<?php

namespace common\models\stores;

use Yii;

/**
 * This is the model class for table "{{%store_admins}}".
 *
 * @property integer $id
 * @property integer $store_id
 * @property string $username
 * @property string $password
 * @property string $auth_hash
 * @property string $first_name
 * @property string $last_name
 * @property integer $status
 * @property string $ip
 * @property integer $last_login
 * @property string $rules
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Stores $store
 */
class StoreAdmins extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%store_admins}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['store_id', 'status', 'last_login', 'created_at', 'updated_at'], 'integer'],
            [['username', 'first_name', 'last_name', 'ip'], 'string', 'max' => 255],
            [['password', 'auth_hash'], 'string', 'max' => 64],
            [['rules'], 'string', 'max' => 1000],
            [['store_id'], 'exist', 'skipOnError' => true, 'targetClass' => Stores::className(), 'targetAttribute' => ['store_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'store_id' => Yii::t('app', 'Store ID'),
            'username' => Yii::t('app', 'Username'),
            'password' => Yii::t('app', 'Password'),
            'auth_hash' => Yii::t('app', 'Auth Hash'),
            'first_name' => Yii::t('app', 'First Name'),
            'last_name' => Yii::t('app', 'Last Name'),
            'status' => Yii::t('app', '1 - active
2 - suspended'),
            'ip' => Yii::t('app', 'Ip'),
            'last_login' => Yii::t('app', 'Last Login'),
            'rules' => Yii::t('app', 'Rules'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStore()
    {
        return $this->hasOne(Stores::className(), ['id' => 'store_id']);
    }

    /**
     * @inheritdoc
     * @return \common\models\stores\queries\StoreAdminsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\stores\queries\StoreAdminsQuery(get_called_class());
    }
}
