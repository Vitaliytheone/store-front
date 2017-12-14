<?php

namespace frontend\modules\admin\models\forms;

use common\models\store\Pages;
use common\models\store\Products;
use yii\behaviors\AttributeBehavior;
use common\models\store\Navigations;

/**
 * Class EditNavigationForm
 * @package frontend\modules\admin\models\forms
 */
class EditNavigationForm extends Navigations
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [

            'position' => [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => 'position',
                ],
                'value' => function ($event) {
                    return $this->getNewPosition();
                },
            ],

            'url' => [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_VALIDATE => 'url',
                ],
                'value' => function() {
                    $url = '';

                    switch ($this->link) {
                        case self::LINK_HOME_PAGE:
                            $url = '/';
                            break;
                        case self::LINK_PRODUCT:
                            $url = Products::findOne($this->link_id)->url;
                            break;
                        case self::LINK_PAGE:
                            $url = Pages::findOne($this->link_id)->url;
                            break;
                        case self::LINK_WEB_ADDRESS:
                            $url = $this->url;
                            break;
                        default:
                    }

                    return $url;
                },
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return 'NavForm';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'url'], 'required'],
            [['parent_id', 'link', 'link_id', 'position', 'deleted'], 'integer'],

            ['parent_id', 'default', 'value' => 0],

            [['name', 'url'], 'trim'],
            [['name'], 'string', 'max' => 300],
            [['url'], 'string', 'max' => 255],
        ];
    }

    /**
     * Return link types list for create/edit form menu
     * @return array
     */
    public static function linkTypes()
    {
        return [
            self::LINK_HOME_PAGE => [
                'name' => static::getLinkName(self::LINK_HOME_PAGE),
            ],
            self::LINK_PRODUCT => [
                'name' => static::getLinkName(self::LINK_PRODUCT),
                'select_id' => 23,
                'fetched',
            ],
            self::LINK_PAGE => [
                'name' => static::getLinkName(self::LINK_PAGE),
                'select_id' => 23,
                'fetched',
            ],
            self::LINK_WEB_ADDRESS => [
                'name' => static::getLinkName(self::LINK_WEB_ADDRESS),
                'select_id' => 4,
            ],
        ];
    }

}