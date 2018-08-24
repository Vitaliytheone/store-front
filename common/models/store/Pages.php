<?php

namespace common\models\store;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\store\queries\PagesQuery;

/**
 * This is the model class for table "{{%pages}}".
 *
 * @property integer $id
 * @property string $title
 * @property string $template
 * @property integer $visibility
 * @property string $content
 * @property string $seo_title
 * @property string $seo_description
 * @property string $seo_keywords
 * @property string $url
 * @property bool $deleted
 * @property integer $created_at
 * @property integer $updated_at
 */
class Pages extends ActiveRecord
{
    const VISIBILITY_YES = 1;
    const VISIBILITY_NO = 0;

    const DELETED_YES = 1;
    const DELETED_NO = 0;

    const TEMPLATE_INDEX = 'index';
    const TEMPLATE_PRODUCT = 'product';
    const TEMPLATE_ORDER = 'order';
    const TEMPLATE_PAGE = 'page';
    const TEMPLATE_CART = 'cart';
    const TEMPLATE_404 = '404';
    const TEMPLATE_CONTACT = 'contact';

    const NEW_PAGE_URL_PREFIX = 'page-';

    /**
     * @return mixed
     */
    public static function getDb()
    {
        return Yii::$app->storeDb;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%pages}}';
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
                    self::EVENT_BEFORE_INSERT => [
                        'created_at',
                        'updated_at'
                    ],
                    self::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
                'value' => function() {
                    return time();
                },
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        if (parent::afterSave($insert, $changedAttributes)) {
            return true;
        }

        // Update Nav URL if Page URL updated
        if (array_key_exists('url', $changedAttributes)) {

            $navModels = Navigation::findAll([
                'link' => Navigation::LINK_PAGE,
                'link_id' => $this->id,
                'deleted' => Navigation::DELETED_NO,
            ]);

            foreach ($navModels as $navModel) {
                $navModel->setAttribute('url', $this->url);
                $navModel->save(false);
            }
        }

        // Update Nav URL if Page Deleted or set Invisible
        $setInvisible = array_key_exists('visibility', $changedAttributes) && ($this->visibility == self::VISIBILITY_NO);
        $setDeleted = array_key_exists('deleted', $changedAttributes) && ($this->deleted == self::DELETED_YES);
        if ($setInvisible || $setDeleted) {

            $navModels = Navigation::findAll([
                'link' => Navigation::LINK_PAGE,
                'link_id' => $this->id,
                'deleted' => Navigation::DELETED_NO,
            ]);

            foreach ($navModels as $navModel) {
                $navModel->setAttributes([
                    'url' => $this->url,
                    'link' => Navigation::LINK_WEB_ADDRESS,
                    'link_id' => null,
                ]);
                $navModel->save();
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['visibility', 'deleted', 'created_at', 'updated_at'], 'integer'],
            [['content', 'template'], 'string'],
            [['title', 'seo_title', 'url'], 'string', 'max' => 255],
            [['seo_description', 'seo_keywords'], 'string', 'max' => 2000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Title'),
            'template' => Yii::t('app', 'Template'),
            'visibility' => Yii::t('app', 'Visibility'),
            'content' => Yii::t('app', 'Content'),
            'seo_title' => Yii::t('app', 'Seo Title'),
            'seo_description' => Yii::t('app', 'Seo Description'),
            'seo_keywords' => Yii::t('app', 'Seo Keywords'),
            'url' => Yii::t('app', 'Url'),
            'deleted' => Yii::t('app', 'Deleted'),
        ];
    }

    /**
     * @inheritdoc
     * @return PagesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PagesQuery(get_called_class());
    }

    /**
     * Get available templates
     * @return array
     */
    public static function getTemplates()
    {
        return [
            static::TEMPLATE_ORDER,
            static::TEMPLATE_CONTACT,
            static::TEMPLATE_404,
            static::TEMPLATE_CART,
            static::TEMPLATE_INDEX,
            static::TEMPLATE_PAGE,
            static::TEMPLATE_PRODUCT,
        ];
    }

    /**
     * @param array|static $page
     * @return bool
     */
    public static function canDelete($page)
    {
        if ($page['template'] == Pages::TEMPLATE_PAGE) {
            return true;
        }

        return false;
    }

    /**
     * Virtual deleting page
     * @return bool
     */
    public function deleteVirtual()
    {
        if ($this->deleted == self::DELETED_YES) {
            return false;
        }

        $this->setAttribute('deleted', self::DELETED_YES);

        return $this->save(false);
    }

}
