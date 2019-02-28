<?php

namespace store\modules\admin\models\forms;

use common\models\store\ActivityLog;
use common\models\stores\StoreAdminAuth;
use common\models\store\Pages;
use yii\base\Model;
use yii\web\NotFoundHttpException;
use yii\web\User;

class EditFilePageForm extends Model
{
    public $content;

    /**
     * Current page
     * @var Pages|null
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
     * @param Pages $page
     */
    public function setPage(Pages $page)
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
            [['content'], 'string'],
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
        $pageModel = empty($id) ? new Pages() : Pages::findOne($id);

        if (!empty($id) && empty($pageModel)) {
            throw new NotFoundHttpException();
        }

        $this->setPage($pageModel);

        if (!$this->load($postData) || !$this->validate()) {
            return false;
        }

        $pageModel->attributes = $this->attributes;

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
}