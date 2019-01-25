<?php

namespace common\models\gateways;

use common\components\behaviors\IpBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "admins_hash".
 *
 * @property int $id
 * @property int $admin_id
 * @property string $hash
 * @property string $ip
 * @property int $super_user 0 - admin, 1 - superadmin
 * @property int $updated_at
 * @property int $created_at
 */
class AdminsHash extends ActiveRecord
{
    const MODE_SUPERADMIN_ON = 1;
    const MODE_SUPERADMIN_OFF = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return DB_GATEWAYS . '.admins_hash';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['admin_id', 'hash', 'ip'], 'required'],
            [['admin_id', 'super_user', 'updated_at', 'created_at'], 'integer'],
            [['hash'], 'string', 'max' => 64],
            [['ip'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'admin_id' => Yii::t('app', 'Admin ID'),
            'hash' => Yii::t('app', 'Hash'),
            'ip' => Yii::t('app', 'Ip'),
            'super_user' => Yii::t('app', 'Super User'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'timestamp' => TimestampBehavior::class,
            'ip' => [
                'class' => IpBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'ip',
                ]
            ],
        ]);
    }

    /**
     * Set store admin hash
     * @param $adminId
     * @param $hash
     * @param bool $super
     */
    public static function setHash($adminId, $hash, $super = false)
    {
        $hashModel = new static();
        $hashModel->admin_id = $adminId;
        $hashModel->hash = $hash;
        $hashModel->super_user = (int)$super;
        $hashModel->save(false);
    }

    /**
     * @param $adminId
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public static function updateFreshness($adminId)
    {
        $model = self::findOne(['admin_id' => $adminId]);
        if (!$model) {
            return;
        }

        $model->updated_at = time();

        $model->update(false);
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public static function updateFreshnessCurrentAdmin()
    {
        /** @var Admins $identity */
        $identity = Yii::$app->gateway->getIdentity();

        if (!$identity) {
            return false;
        }


        $hash = $identity::getHash($identity->getId());

        if (!$hash || !$hash instanceof AdminsHash) {
            return false;
        }

        $hash->updated_at = time();

        return $hash->save(false);
    }

    /**
     * Delete hash records by user id
     * @param $adminId
     */
    public static function deleteByUser($adminId)
    {
        static::deleteAll([
            'admin_id' => $adminId,
            'super_user' => static::MODE_SUPERADMIN_OFF,
        ]);
    }

    /**
     * Delete all hash records with same $hash
     * @param $hash
     */
    public static function deleteByHash($hash)
    {
        static::deleteAll([
            'hash' => $hash,
        ]);
    }

    /**
     * @param $adminMode
     * @param $time
     */
    public static function deleteOld($adminMode, $time)
    {
        static::deleteAll(
            'super_user = :adminMode AND updated_at < :time',
            [
                ':adminMode' => $adminMode,
                ':time' => time() - $time
            ]
        );
    }
}
