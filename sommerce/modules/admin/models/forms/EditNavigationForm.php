<?php

namespace sommerce\modules\admin\models\forms;

use common\models\store\ActivityLog;
use common\models\store\Pages;
use common\models\store\Products;
use common\models\stores\StoreAdminAuth;
use yii\behaviors\AttributeBehavior;
use common\models\store\Navigation;
use yii\web\User;

/**
 * Class EditNavigationForm
 * @package sommerce\modules\admin\models\forms
 */
class EditNavigationForm extends Navigation
{
    /** @var  User */
    protected $_user;

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

            ['link_id', 'filter', 'filter' => function($value) {
                return in_array($this->link, [self::LINK_PRODUCT, self::LINK_PAGE]) ? $value : null;
            }]
        ];
    }

    /**
     * Set current user
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->_user = $user;
    }

    /**
     * Return current user
     * @return User
     */
    public function getUser()
    {
        return $this->_user;
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

    /**
     * Create menu item
     * @param $postData
     * @return bool
     */
    public function create($postData)
    {
        if (!$this->load($postData) || !$this->save()) {
            return false;
        }

        /** @var StoreAdminAuth $identity */
        $identity = $this->getUser()->getIdentity();

        // TODO: uncomment this after API testing
        //ActivityLog::log($identity, ActivityLog::E_SETTINGS_NAVIGATION_MENU_ITEM_ADDED, $this->id, $this->name);

        return true;
    }

    /**
     * Update menu item
     * @param $postData
     * @return bool
     */
    public function updateNav($postData)
    {
        if (!$this->load($postData) || !$this->save()) {
            return false;
        }

        /** @var StoreAdminAuth $identity */
        $identity = $this->getUser()->getIdentity();

        // TODO: uncomment this after API testing
        //ActivityLog::log($identity, ActivityLog::E_SETTINGS_NAVIGATION_MENU_ITEM_UPDATED, $this->id, $this->name);

        return true;
    }

}