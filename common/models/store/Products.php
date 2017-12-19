<?php

namespace common\models\store;

use Yii;
use yii\db\Query;
use common\models\store\queries\ProductsQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%products}}".
 *
 * @property integer $id
 * @property string $name
 * @property integer $position
 * @property string $url
 * @property string $properties
 * @property string $description
 * @property integer $visibility
 * @property string $seo_title
 * @property string $seo_description
 *
 * @property Packages[] $packages
 */
class Products extends ActiveRecord
{
    const VISIBILITY_YES = 1;
    const VISIBILITY_NO = 0;

    public static function getDb()
    {
        return Yii::$app->storeDb;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%products}}';
    }


    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        if (parent::afterSave($insert, $changedAttributes)) {
            return true;
        }

        // Update Nav URL if Product URL updated
        if (array_key_exists('url', $changedAttributes)) {

            $navModels = Navigations::findAll([
                'link' => Navigations::LINK_PRODUCT,
                'link_id' => $this->id,
                'deleted' => Navigations::DELETED_NO,
            ]);

            foreach ($navModels as $navModel) {
                $navModel->setAttribute('url', $this->url);
                $navModel->save(false);
            }
        }

        // Update Nav URL if Product set invisible
        $setInvisible = array_key_exists('visibility', $changedAttributes) && ($this->visibility == self::VISIBILITY_NO);
        if ($setInvisible) {

            $navModels = Navigations::findAll([
                'link' => Navigations::LINK_PRODUCT,
                'link_id' => $this->id,
                'deleted' => Navigations::DELETED_NO,
            ]);

            foreach ($navModels as $navModel) {
                $navModel->setAttributes([
                    'url' => $this->url,
                    'link' => Navigations::LINK_WEB_ADDRESS,
                    'link_id' => null,
                ]);
                $navModel->save(false);
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
            [['id'], 'required'],
            [['id', 'position', 'visibility'], 'integer'],
            [['description'], 'string'],
            [['name', 'url'], 'string', 'max' => 255],
            [['properties'], 'string', 'max' => 1000],
            [['seo_title', 'seo_description'], 'string', 'max' => 45],
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
            'position' => Yii::t('app', 'Position'),
            'url' => Yii::t('app', 'Url'),
            'properties' => Yii::t('app', 'Properties'),
            'description' => Yii::t('app', 'Description'),
            'visibility' => Yii::t('app', 'Visibility'),
            'seo_title' => Yii::t('app', 'Seo Title'),
            'seo_description' => Yii::t('app', 'Seo Description'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPackages()
    {
        return $this->hasMany(Packages::className(), ['product_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return ProductsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ProductsQuery(get_called_class());
    }

    /**
     * Get Max position for new inserts
     * @return array|bool
     */
    public static function getMaxPosition()
    {
        $db = yii::$app->store->getInstance()->db_name;
        $query = (new Query())
            ->select(['MAX(position) position'])
            ->from("$db.products")
            ->one();

        return $query['position'];
    }
}
