<?php

namespace common\models\store;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use common\models\store\queries\NavigationsQuery;
use yii\db\Query;

/**
 * This is the model class for table "navigation".
 *
 * @property integer $id
 * @property integer $parent_id
 * @property string $name
 * @property integer $link
 * @property integer $link_id
 * @property integer $position
 * @property string $url
 * @property integer $deleted
 */
class Navigations extends ActiveRecord
{
    const LINK_HOME_PAGE = 1;
    const LINK_PRODUCT = 2;
    const LINK_PAGE = 3;
    const LINK_WEB_ADDRESS = 4;

    const DELETED_NO = 0;
    const DELETED_YES = 1;

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
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
        return '{{%navigation}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'link', 'link_id', 'position', 'deleted'], 'integer'],
            [['name'], 'string', 'max' => 300],
            [['url'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'parent_id' => Yii::t('app', 'Parent ID'),
            'name' => Yii::t('app', 'Name'),
            'link' => Yii::t('app', 'Link'),
            'link_id' => Yii::t('app', 'Link ID'),
            'position' => Yii::t('app', 'Position'),
            'url' => Yii::t('app', 'Url'),
            'deleted' => Yii::t('app', 'Deleted'),
        ];
    }

    /**
     * Return human readable link names list
     * @return array
     */
    public static function getLinkNames()
    {
        return [
            self::LINK_HOME_PAGE => Yii::t('admin', 'settings.nav_link_home_page'),
            self::LINK_PRODUCT => Yii::t('admin', 'settings.nav_link_product'),
            self::LINK_PAGE => Yii::t('admin', 'settings.nav_link_page'),
            self::LINK_WEB_ADDRESS => Yii::t('admin', 'settings.nav_link_web_address'),
        ];
    }

    /**
     * Return human readable link name by link type
     * @param $link
     * @return mixed
     */
    public static function getLinkName($link)
    {
        return ArrayHelper::getValue(static::getLinkNames(), $link, $link);
    }

    /**
     * @inheritdoc
     * @return \common\models\store\queries\NavigationsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new NavigationsQuery(get_called_class());
    }

    /**
     * Get Max position of current navigation items
     * @return array|bool
     */
    public function getMaxPosition()
    {
        $db = yii::$app->store->getInstance()->db_name;
        $query = (new Query())
            ->select(['MAX(position) position'])
            ->from($db.'.'.static::tableName())
            ->where(['parent_id' => $this->parent_id])
            ->one();

        return $query['position'];
    }

    /**
     * Calculate `position` for new navigation record
     * @return int
     */
    public function getNewPosition()
    {
        $maxPosition = $this->getMaxPosition();
        $position = is_null($maxPosition) ? 0 : $maxPosition + 1;
        return $position;
    }

}
