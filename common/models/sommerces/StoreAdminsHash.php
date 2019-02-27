<?php
namespace common\models\sommerces;

use common\components\behaviors\IpBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;
use \yii\db\ActiveRecord;
use \common\models\sommerces\queries\StoreAdminsHashQuery;

/**
 * This is the model class for table "{{%store_admins_hash}}".
 *
 * @property integer $id
 * @property integer $admin_id
 * @property string $hash
 * @property string $ip
 * @property integer $super_user
 * @property integer $updated_at
 * @property integer $created_at
 */
class StoreAdminsHash extends ActiveRecord
{
    const MODE_SUPERADMIN_ON = 1;
    const MODE_SUPERADMIN_OFF = 0;

    /**
     * @inheritdoc
     */
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
     * @inheritdoc
     */
    public static function tableName()
    {
        return DB_STORES . '.store_admins_hash';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
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
            'super_user' => Yii::t('app', '0 - admin, 1 - superadmin'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * @inheritdoc
     * @return StoreAdminsHashQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new StoreAdminsHashQuery(get_called_class());
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
     * Update freshness of the admin hash record
     * @param $adminId
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
     * Update hash freshness of the current admin
     * @return bool
     */
    public static function updateFreshnessCurrentAdmin()
    {
        /** @var StoreAdminAuth $identity */
        $identity = Yii::$app->user->getIdentity();

        if (!$identity) {
            return false;
        }


        $hash = $identity::getHash($identity->getId());

        if (!$hash || !$hash instanceof StoreAdminsHash) {
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
            'super_user' => StoreAdmins::SUPER_USER_MODE_OFF,
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
     * Delete all records older than $time for $adminMode
     * @param $adminMode integer { Superadmin (1) || Admin (0) }
     * @param $time integer Time in seconds. Example $time = 30 * 24 * 60 * 60 (30 days)
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
