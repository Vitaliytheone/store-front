<?php

namespace sommerce\modules\admin\models\forms;

use common\models\store\ActivityLog;
use common\models\stores\StoreAdminAuth;
use common\models\store\Products;
use common\models\store\PagesOld;
use yii\base\Model;
use yii\db\Query;
use yii\web\NotFoundHttpException;
use yii\web\User;

class EditPageForm extends Model
{
    public $title;
    public $content;
    public $visibility;
    public $url;
    public $seo_title;
    public $seo_description;
    public $seo_keywords;

    /**
     * Current page
     * @var PagesOld|null
     */
    protected $_page =  null;

    /**
     * Current User
     * @var User|null
     */
    protected $_user;

    /**
     * @inheritdoc
     */
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
     * @return User|null
     */
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * Set page
     * @param PagesOld $page
     */
    public function setPage(PagesOld $page)
    {
        $this->_page = $page;
    }

    /**
     * Get page
     */
    public function getPage()
    {
        return $this->_page;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'visibility', 'url'], 'required'],
            [['title', 'seo_title'], 'string', 'max' => 255],
            [['visibility'], 'integer'],
            [['content'], 'string'],
            [['title', 'seo_title', 'seo_description', 'url',], 'trim'],
            [['seo_description', 'seo_keywords'], 'string', 'max' => 2000],

            ['url', 'match', 'pattern' => '/^[a-z0-9-_]+$/i'],
            ['url', 'unique', 'targetClass' => Products::class, 'targetAttribute' => ['url' => 'url']],
            ['url', 'unique', 'targetClass' => PagesOld::class, 'targetAttribute' => ['url' => 'url'], 'filter' => function(Query $query) {
                $query->andWhere(['deleted' => PagesOld::DELETED_NO]);
                $pageId = $this->getPage()->id;
                if ($pageId) {
                    $query->andWhere('id <> :pageId', [':pageId' => $pageId]);
                }
            }]
        ];
    }

    /**
     * Create or Update page.
     * If page $id is exist will try to update exiting page, else try to create new page
     * @param $postData
     * @param $id
     * @return bool
     * @throws NotFoundHttpException
     */
    public function edit($postData, $id)
    {
        $pageModel = empty($id) ? new PagesOld() : PagesOld::findOne($id);

        if (!empty($id) && empty($pageModel)) {
            throw new NotFoundHttpException();
        }

        $this->setPage($pageModel);

        if (!$this->load($postData) || !$this->validate()) {
            return false;
        }

        $pageModel->attributes = $this->attributes;

        $this->_filterUrl();

        if (!$pageModel->save(false)) {
            return false;
        };

        /** @var StoreAdminAuth $identity */
        $identity = $this->getUser()->getIdentity(false);

        if ($pageModel->isNewRecord) {
            ActivityLog::log($identity, ActivityLog::E_SETTINGS_PAGES_PAGE_ADDED, $pageModel->id, $pageModel->id);
        } else {
            ActivityLog::log($identity, ActivityLog::E_SETTINGS_PAGES_PAGE_UPDATED, $pageModel->id, $pageModel->id);
        }

        return true;
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

        $url = PagesOld::NEW_PAGE_URL_PREFIX . $this->id;

        $_url = $url;
        $postfix = 1;

        while (Products::findOne(['url' => $_url])) {
            $_url = $url . '-' . $postfix;
            $postfix++;
        };

        $this->url = $_url;
    }
}