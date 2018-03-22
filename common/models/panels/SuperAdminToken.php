<?php

namespace common\models\panels;

use common\models\panels\queries\SuperAdminTokenQuery;
use my\components\traits\UnixTimeFormatTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%super_admin_token}}".
 *
 * @property int $id
 * @property int $super_admin_id
 * @property int $item_id
 * @property int $item 0 - panels, 1 - my, 2 - sommerce admin
 * @property string $token
 * @property int $expiry_at
 */
class SuperAdminToken extends ActiveRecord
{
    const ITEM_PANELS = 0;
    const ITEM_MY = 1;
    const ITEM_SOMMERCE = 2;

    use UnixTimeFormatTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%super_admin_token}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['super_admin_id', 'item_id', 'token'], 'required'],
            [['super_admin_id', 'item_id', 'expiry_at'], 'integer'],
            [['item'], 'string', 'max' => 4],
            [['token'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'super_admin_id' => Yii::t('app', 'Super Admin ID'),
            'item_id' => Yii::t('app', 'Item ID'),
            'item' => Yii::t('app', 'Item'),
            'token' => Yii::t('app', 'Token'),
            'expiry_at' => Yii::t('app', 'Expiry At'),
        ];
    }

    /**
     * @inheritdoc
     * @return SuperAdminTokenQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SuperAdminTokenQuery(get_called_class());
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'expiry_at',
                ],
                'value' => function() {
                    return time() + 60;
                },
            ]
        ];
    }

    /**
     * Generate unique token
     */
    public function generateToken()
    {
        $token = substr(md5(microtime()), 0, 32);
        $result = static::findOne(['token' => $token]);
        if($result !== null) {
            $this->generateToken();
        } else {
            $this->token = $token;
        }
    }

    /**
     * Get token
     * @param int $superAdminId
     * @param int $item
     * @param int $itemId
     * @return string
     */
    public static function getToken(int $superAdminId, int $item, int $itemId): string
    {
        $model = new static();
        $model->attributes = [
            'item' => $item,
            'item_id' => $itemId,
            'super_admin_id' => $superAdminId
        ];
        $model->generateToken();
        $model->save(false);

        return $model->token;
    }
}
