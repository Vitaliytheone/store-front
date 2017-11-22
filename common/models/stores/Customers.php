<?php

namespace common\models\stores;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\stores\queries\CustomersQuery;

/**
 * This is the model class for table "{{%customers}}".
 *
 * @property integer $id
 * @property string $email
 * @property string $password
 * @property string $first_name
 * @property string $last_name
 * @property string $access_token
 * @property string $token
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $last_login
 * @property integer $timezone
 * @property string $auth_token
 *
 * @property Stores[] $stores
 */
class Customers extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%customers}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'created_at', 'updated_at', 'timezone'], 'integer'],
            [['email', 'first_name', 'last_name', 'last_login'], 'string', 'max' => 255],
            [['password', 'access_token', 'token', 'auth_token'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'email' => Yii::t('app', 'Email'),
            'password' => Yii::t('app', 'Password'),
            'first_name' => Yii::t('app', 'First Name'),
            'last_name' => Yii::t('app', 'Last Name'),
            'access_token' => Yii::t('app', 'Access Token'),
            'token' => Yii::t('app', 'Token'),
            'status' => Yii::t('app', '1 - active, 2 - suspended'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'last_login' => Yii::t('app', 'Last Login'),
            'timezone' => Yii::t('app', 'Timezone'),
            'auth_token' => Yii::t('app', 'Auth Token'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStores()
    {
        return $this->hasMany(Stores::className(), ['customer_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return CustomersQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CustomersQuery(get_called_class());
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => [
                        'created_at',
                        'updated_at'
                    ],
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
                'value' => function() {
                    return time();
                },
            ],
        ];
    }
}
