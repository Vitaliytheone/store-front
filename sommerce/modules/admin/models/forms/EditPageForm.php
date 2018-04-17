<?php

namespace sommerce\modules\admin\models\forms;

use common\models\store\ActivityLog;
use common\models\stores\StoreAdminAuth;
use Yii;
use common\models\store\Products;
use common\models\store\Pages;
use yii\web\User;

class EditPageForm extends Pages
{

    /**
     * @var User
     */
    protected $_user;


    public function init()
    {
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return 'PageForm';
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
     * Get current user
     * @return User
     */
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'visibility'], 'required'],
            [['title', 'seo_title'], 'string', 'max' => 255],
            [['visibility'], 'integer'],
            [['content', 'template'], 'string'],
            [['title', 'seo_title', 'seo_description', 'url',], 'trim'],
            [['seo_description', 'seo_keywords'], 'string', 'max' => 2000],

            ['url', 'match', 'pattern' => '/^[a-z0-9-_]+$/i'],
            ['url', 'unique', 'filter' => ['deleted' => Pages::DELETED_NO]],
            ['url', 'unique', 'targetClass' => Products::class, 'targetAttribute' => ['url' => 'url']],

            ['template', 'default', 'value' => self::TEMPLATE_PAGE],
        ];
    }

    /**
     * Create Page routine
     * @param $postData
     * @return bool
     */
    public function create($postData)
    {
        $this->_setDefaults();

        if (!$this->load($postData) || !$this->save()) {
            return false;
        }

        $this->_filterUrl();

        /** @var StoreAdminAuth $identity */
        $identity = $this->getUser()->getIdentity(false);

        ActivityLog::log($identity, ActivityLog::E_SETTINGS_PAGES_PAGE_ADDED, $this->id, $this->id);

        return true;
    }

    /**
     * Save Page routine
     * @param $postData
     * @return bool
     */
    public function edit($postData)
    {
        if (!$this->load($postData) || !$this->save()) {
            return false;
        }

        $this->_filterUrl();

        /** @var StoreAdminAuth $identity */
        $identity = $this->getUser()->getIdentity(false);

        ActivityLog::log($identity, ActivityLog::E_SETTINGS_PAGES_PAGE_UPDATED, $this->id, $this->id);

        return true;
    }

    /**
     * Set default model attributes
     */
    private function _setDefaults()
    {
        $this->setAttributes([
            'title' => '',
            'visibility' => self::VISIBILITY_YES,
            'content' => '',
            'seo_title' => Yii::t('admin', 'settings.pages_seo_page_default'),
            'seo_description' => "", /* Yii::t('admin', 'settings.pages_seo_meta_default') */
            'url' => Yii::t('admin', 'settings.pages_seo_url_default'),
        ], false);
    }

    /**
     * Filter and generate URL for empty page url
     */
    private function _filterUrl()
    {
        $url = trim($this->url, ' ');
        $url = trim($url, '_');
        $url = trim($url, '-');

        if (!empty($url)) {
            return;
        }

        $url = Pages::NEW_PAGE_URL_PREFIX . $this->id;

        $_url = $url;
        $postfix = 1;

        while (Products::findOne(['url' => $_url])) {
            $_url = $url . '-' . $postfix;
            $postfix++;
        };

        $this->url = $_url;
        $this->save(false);
    }
}