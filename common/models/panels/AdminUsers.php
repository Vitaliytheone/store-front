<?php

namespace common\models\panels;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "admin_users".
 *
 * @property integer $id
 * @property string $login
 * @property string $passwd
 * @property string $first_name
 * @property string $last_name
 * @property integer $last_login
 * @property string $last_ip
 */
class AdminUsers extends ActiveRecord
{
    const DEFAULT_ADMIN = 10000000;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'admin_users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['login', 'passwd', 'first_name', 'last_name', 'last_login', 'last_ip'], 'required'],
            [['last_login'], 'integer'],
            [['login', 'passwd', 'first_name', 'last_name'], 'string', 'max' => 300],
            [['last_ip'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'login' => 'Login',
            'passwd' => 'Passwd',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'last_login' => 'Last Login',
            'last_ip' => 'Last Ip',
        ];
    }

    /**
     * Get full customer name
     * @return string
     */
    public function getFullName()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
