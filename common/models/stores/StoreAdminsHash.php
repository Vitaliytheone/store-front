<?php
namespace common\models\stores;

use common\components\behaviors\IpBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;
use \yii\db\ActiveRecord;
use \common\models\stores\queries\StoreAdminsHashQuery;

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
        $hashModel->save();
    }

    /**
     * Update freshness of the admin hash records
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
     * Delete hash records by user id
     * @param $adminId
     * @param int $super
     */
    public static function deleteByUser($adminId, $super = StoreAdmins::SUPER_USER_MODE_OFF)
    {
        static::deleteAll([
            'admin_id' => $adminId,
            'super_user' => $super
        ]);
    }

    /**
     * Delete all records older than $time
     * @param $time
     */
    public static function deleteOld($time = 30 * 24 * 60 * 60)
    {
        static::deleteAll([
            '<',
            'updated_at', time() - $time,
        ]);
    }
}
