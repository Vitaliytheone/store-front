<?php

namespace common\models\store;

use common\models\stores\DefaultThemes;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\store\queries\CustomThemesQuery;

/**
 * This is the model class for table "{{%custom_themes}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $folder
 * @property integer $created_at
 * @property integer $updated_at
 */
class CustomThemes extends ActiveRecord
{
    const THEME_PREFIX = 'custom_';
    const THEME_THUMBNAIL_URL = 'https://sommerce.myjetbrains.com/youtrack/_persistent/no_image.jpg?file=6-8&c=true&rw=622&rh=415&u=1513753769196';
    const NEW_THEME_TEMPLATE_NAME = 'classic';

    public static function getDb()
    {
        return Yii::$app->storeDb;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%custom_themes}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'folder'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['name', 'folder'], 'string', 'max' => 300],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'folder' => Yii::t('app', 'Folder'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     * @return CustomThemesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CustomThemesQuery(get_called_class());
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => function() {
                    return time();
                },
            ],
        ];
    }

    /**
     * Return custom themes folder path
     * @return string
     */
    public static function getThemesPath()
    {
        return Yii::getAlias('@frontend') .  '/views/themes/custom/' . Yii::$app->store->getInstance()->id;
    }

    /**
     * Return new custom theme template skeleton path
     * @return string
     */
    public static function getTemplatePath()
    {
        return DefaultThemes::getThemesPath() . '/' . self::NEW_THEME_TEMPLATE_NAME;
    }

    /**
     * Return theme folder full path
     * @return string
     */
    public function getThemePath()
    {
        return $this->folder ? static::getThemesPath() . '/' . $this->folder : null;
    }

}
