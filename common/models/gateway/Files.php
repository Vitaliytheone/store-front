<?php

namespace common\models\gateway;

use common\components\traits\UnixTimeFormatTrait;
use gateway\components\behaviors\FilesBehavior;
use Yii;
use common\components\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\gateway\queries\FilesQuery;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%files}}".
 *
 * @property int $id
 * @property string $name
 * @property string $url
 * @property string $file_type
 * @property string $mime
 * @property string $content
 * @property int $is_default
 * @property int $is_deleted
 * @property int $created_at
 * @property int $updated_at
 */
class Files extends ActiveRecord
{
    public const FILE_TYPE_LAYOUT = 'layout';
    public const FILE_TYPE_PAGE = 'page';
    public const FILE_TYPE_SNIPPET = 'snippet';
    public const FILE_TYPE_JS = 'js';
    public const FILE_TYPE_CSS = 'css';
    public const FILE_TYPE_IMAGE = 'img';

    public const CAN_DELETE = 'delete';
    public const CAN_RENAME = 'rename';
    public const CAN_DOWNLOAD = 'download';
    public const CAN_UPDATE = 'update';
    public const CAN_PREVIEW = 'preview';

    public const TWIG_SIZE = (1024 * 500);
    public const CSS_SIZE = (1024 * 500);
    public const JS_SIZE = (1024 * 500);
    public const IMAGE_SIZE = (1024 * 500);

    public static $availableExtensions = [
        self::FILE_TYPE_LAYOUT => 'twig',
        self::FILE_TYPE_PAGE => 'twig',
        self::FILE_TYPE_SNIPPET => 'twig',
        self::FILE_TYPE_JS => 'js',
        self::FILE_TYPE_CSS => 'css',
        self::FILE_TYPE_IMAGE => [
            'png',
            'jpg',
            'jpeg',
            'gif',
        ],
    ];

    use UnixTimeFormatTrait;

    public static function getDb()
    {
        return Yii::$app->gatewayDb;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%files}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['created_at', 'updated_at', 'is_default', 'is_deleted'], 'integer'],
            [['content', 'url', 'file_type', 'mime'], 'string'],
            [['name'], 'string', 'max' => 300],
            [['name'], 'uniqueNameValidation'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'theme_id' => Yii::t('app', 'Theme ID'),
            'name' => Yii::t('app', 'Name'),
            'url' => Yii::t('app', 'Url'),
            'file_type' => Yii::t('app', 'File type'),
            'mime' => Yii::t('app', 'Mime'),
            'is_default' => Yii::t('app', 'Is default'),
            'is_deleted' => Yii::t('app', 'Is deleted'),
            'content' => Yii::t('app', 'Content'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     * @return FilesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return (new FilesQuery(get_called_class()))->active();
    }

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
            'assets' => FilesBehavior::class,
            'url' => [
                'class' => SluggableBehavior::class,
                'attribute' => function() {
                    return $this->getClearName();
                },
                'slugAttribute' => 'url',
            ],
        ];
    }

    /**
     * @return bool
     */
    public function delete()
    {
        $this->is_deleted = 1;
        return $this->save(false);
    }

    /**
     * @return array
     */
    public static function getFileTypes()
    {
        return [
            static::FILE_TYPE_LAYOUT,
            static::FILE_TYPE_PAGE,
            static::FILE_TYPE_JS,
            static::FILE_TYPE_CSS,
            static::FILE_TYPE_SNIPPET,
            static::FILE_TYPE_IMAGE,
        ];
    }

    /**
     * @param string $code
     * @param mixed $file
     * @param array $options
     * @return bool
     */
    public static function can($code, $file, $options = [])
    {
        switch ($code) {
            case static::CAN_DELETE:
            case static::CAN_RENAME:
                return !ArrayHelper::getValue($file, 'is_default') && !ArrayHelper::getValue($file, 'is_deleted');
            break;

            case static::CAN_UPDATE:
                return !ArrayHelper::getValue($file, 'is_deleted') && in_array(ArrayHelper::getValue($file, 'file_type'), [
                    static::FILE_TYPE_JS,
                    static::FILE_TYPE_LAYOUT,
                    static::FILE_TYPE_CSS,
                    static::FILE_TYPE_SNIPPET,
                    static::FILE_TYPE_PAGE,
                ]);
            break;

            case static::CAN_PREVIEW:
                return !ArrayHelper::getValue($file, 'is_deleted') && in_array(ArrayHelper::getValue($file, 'file_type'), [
                    static::FILE_TYPE_IMAGE,
                ]);
            break;

            case static::CAN_DOWNLOAD:
                return !ArrayHelper::getValue($file, 'is_deleted');
            break;
        }

        return false;
    }

    /**
     * @return mixed|null
     */
    public function getClearName()
    {
        return !empty($this->name) ? pathinfo($this->name, PATHINFO_FILENAME) : null;
    }

    /**
     * @return mixed|null
     */
    public function getExtension()
    {
        return !empty($this->name) ? pathinfo($this->name, PATHINFO_EXTENSION) : null;
    }

    /**
     * @return null|string
     */
    public function getUrl()
    {
        switch ($this->file_type) {
            case static::FILE_TYPE_PAGE:
                return '/' . $this->url;
            break;

            case static::FILE_TYPE_CSS:
                return '/css/' . $this->name;
            break;

            case static::FILE_TYPE_JS:
                return '/js/' . $this->name;
            break;

            case static::FILE_TYPE_IMAGE:
                return '/images/' . $this->name;
            break;
        }

        return null;
    }

    /**
     * @return null|string
     */
    public function getTitle()
    {
        $title = null;

        if (empty($this->url) || static::FILE_TYPE_PAGE !== $this->file_type) {
            return $title;
        }

        $title = ucfirst(str_replace(['_'], " ", $this->url));

        return $title;
    }

    /**
     * @param string $attribute
     * @param array $options
     * @return bool
     */
    public function uniqueNameValidation($attribute, $options = [])
    {
        if ($this->hasErrors()) {
            return false;
        }

        if ((static::find()->andWhere([
            'name' => $this->{$attribute},
            'file_type' => $this->file_type
        ])->active()->exists())) {
            $this->addError($attribute, Yii::t('admin', 'settings.files.field.name_unique_error'));
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function getMimeType()
    {
        if (!empty($this->mime)) {
            return $this->mime;
        }

        switch ($this->file_type) {
            case static::FILE_TYPE_LAYOUT;
            case static::FILE_TYPE_PAGE;
            case static::FILE_TYPE_SNIPPET;
                return 'text/html';
            break;

            case static::FILE_TYPE_CSS;
                return 'text/css';
            break;

            case static::FILE_TYPE_JS;
                return 'text/javascript';
            break;
        }

        return 'text/html';
    }
}