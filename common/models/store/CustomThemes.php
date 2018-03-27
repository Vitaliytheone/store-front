<?php

namespace common\models\store;

use common\models\stores\DefaultThemes;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\store\queries\CustomThemesQuery;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

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
    const THEME_TYPE = 1; // Custom

    const THEME_PREFIX = 'custom_';
    const THEME_THUMBNAIL_URL = '/img/custom_theme_thumbnail.jpg';

    private $_store;

    public function init()
    {
        parent::init();

        $this->_store = Yii::$app->store->getInstance();
    }

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
                'class' => TimestampBehavior::class,
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
        return Yii::getAlias('@sommerce') .  '/views/themes/custom/' . Yii::$app->store->getInstance()->id;
    }

    /**
     * Return default theme path
     * @return string
     * @throws Exception
     */
    public static function getDefaultThemePath()
    {
        $defaultTheme = ArrayHelper::getValue(Yii::$app->params, 'defaultTheme', null);
        if (!$defaultTheme) {
            throw new Exception('Default theme does not configured!');
        }

        return DefaultThemes::getThemesPath() . '/' . $defaultTheme;
    }

    /**
     * Return theme folder full path
     * @return string
     */
    public function getThemePath()
    {
        return $this->folder ? static::getThemesPath() . '/' . $this->folder : null;
    }

    /**
     * Return is theme active
     * @return bool
     */
    public function isActive()
    {
        return $this->folder === $this->_store->theme_folder;
    }

}
