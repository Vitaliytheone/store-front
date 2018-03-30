<?php

namespace common\models\panels;

use my\components\behaviors\IpBehavior;
use my\helpers\UserHelper;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\panels\queries\MyCustomersHashQuery;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%my_customers_hash}}".
 *
 * @property integer $id
 * @property integer $customer_id
 * @property string $hash
 * @property string $ip
 * @property string $remember - 0 - not remember; 1 - remember
 * @property integer $super_user - 0 - user, 1 - super user
 * @property integer $updated_at
 * @property integer $created_at
 *
 * @property Auth $customer
 */
class MyCustomersHash extends ActiveRecord
{
    const TYPE_REMEMBER = 1;
    const TYPE_NOT_REMEMBER = 0;

    const TYPE_SUPER_USER = 1;
    const TYPE_NOT_SUPER_USER = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return DB_PANELS . '.my_customers_hash';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customer_id', 'hash'], 'required'],
            [['customer_id', 'super_user', 'updated_at', 'created_at', 'remember'], 'integer'],
            [['hash'], 'string', 'max' => 64],
            [['remember'], 'default', 'value' => 0],
            [['ip'], 'string', 'max' => 255],
            [['hash'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'customer_id' => Yii::t('app', 'Customer ID'),
            'hash' => Yii::t('app', 'Hash'),
            'ip' => Yii::t('app', 'Ip'),
            'remember' => Yii::t('app', 'Remember'),
            'super_user' => Yii::t('app', 'Super user'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Auth::class, ['id' => 'customer_id']);
    }

    /**
     * @inheritdoc
     * @return MyCustomersHashQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MyCustomersHashQuery(get_called_class());
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
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
            'ip' => [
                'class' => IpBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'ip',
                ]
            ],
        ];
    }

    /**
     * Set customer hash
     * @param Customers|Auth $customer
     * @param bool $remember
     * @param bool $super
     */
    public static function setHash($customer, $remember = false, $super = false)
    {
        // при авторизации удаляем все записи даного кастомера с super_user = 0
        static::clear($customer);

        $hash = $customer->generateAuthKey();

        UserHelper::setHash($hash, $remember);

        $hashModel = new static();
        $hashModel->customer_id = $customer->id;
        $hashModel->remember = (int)$remember;
        $hashModel->hash = $hash;
        $hashModel->super_user = (int)$super;
        $hashModel->save();
    }

    /**
     * Get customer hash
     * @param Customers|Auth $customer
     * @return null|string
     */
    public static function getHash($customer)
    {
        if (($model = static::findOne([
            'customer_id' => $customer->id
        ]))) {
            return $model->hash;
        }

        return null;
    }

    /**
     * Find customer by hash value
     * @param string $hash
     * @return Auth|null
     */
    public static function getCustomerByHash($hash)
    {
        if (($model = static::findOne([
            'hash' => $hash
        ]))) {
            return $model->customer;
        }

        return null;
    }

    /**
     * Get all hash values by customer
     * @param Customers|Auth $customer
     * @return array
     */
    public static function getHashValues($customer)
    {
        return ArrayHelper::getColumn(static::find()->andWhere([
            'customer_id' => $customer->id
        ])->all(), 'hash');
    }

    /**
     * Validate customer hash value
     * @param Customers|Auth $customer
     * @param string $hash
     * @return boolean
     */
    public static function validateHash($customer, $hash)
    {
        $userHash = static::findOne([
            'customer_id' => $customer->id,
            'hash' => $hash
        ]);

        if ($userHash) {
            $userHash->updated_at = time();
            $userHash->save(false);
            return true;
        }

        return false;
    }

    /**
     * Clear customer auth hash values
     * @param Auth $customer
     * @param int $superUser
     */
    public static function clear($customer, $superUser = 0)
    {
        static::deleteAll([
            'customer_id' => $customer->id,
            'super_user' => $superUser
        ]);
    }

    /**
     * Remove hash
     * @param string $hash
     */
    public static function remove($hash)
    {
        static::deleteAll([
            'hash' => $hash,
        ]);
    }
}
