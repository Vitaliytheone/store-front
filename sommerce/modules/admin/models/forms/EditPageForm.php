<?php

namespace sommerce\modules\admin\models\forms;

use common\models\sommerce\ActivityLog;
use common\models\sommerce\Pages;
use common\models\sommerce\Products;
use common\models\sommerces\StoreAdminAuth;
use sommerce\modules\admin\components\CustomUser;
use sommerce\modules\admin\models\search\PagesSearch;
use Yii;
use yii\base\Model;

/**
 * Class EditPageForm
 * @package sommerce\modules\admin\models\forms
 */
class EditPageForm extends Model
{
    public $name;
    public $title;
    public $description;
    public $keywords;
    public $url;
    public $visibility;

    /**
     * @var CustomUser
     */
    protected $user;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'url'], 'required'],
            [['title', 'keywords', 'url', 'name'], 'string'],
            [['visibility'], 'integer'],
            [['url', 'title'], 'string', 'max' => 70],
            [['description'], 'string', 'max' => 160],
            ['url', 'match', 'pattern' => '/^[a-z0-9-_]+$/i'],
            ['url', 'unique', 'targetClass' => Products::class, 'targetAttribute' => ['url' => 'url']],
            ['url', 'unique', 'targetClass' => Pages::class, 'targetAttribute' => ['url' => 'url'],
                'when' => function ($model) {
                    if (empty($this->page->url)) {
                        return true;
                    }

                    return $model->url !== $this->page->url;
                }
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'mame' => Yii::t('admin', 'pages.name'),
            'url' => Yii::t('admin', 'pages.url'),
            'title' => Yii::t('admin', 'pages.title'),
            'description' => Yii::t('admin', 'pages.description'),
            'keywords' => Yii::t('admin', 'pages.keywords'),
        ];
    }

    /**
     * @return CustomUser
     */
    public function getUser(): CustomUser
    {
        return $this->user;
    }

    /**
     * @param CustomUser $user
     */
    public function setUser(CustomUser $user)
    {
        $this->user = $user;
    }
    /**
     * @var $page Pages
     */
    protected $page;

    /**
     * @return Pages
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param mixed Pages
     */
    public function setPage($page)
    {
        $this->page = $page;
    }

    /**
     * @return bool|int
     */
    public function edit()
    {
        if (!$this->validate()){
            return false;
        }

        $transaction = Pages::getDb()->beginTransaction();
        try {
            $page = $this->getPage();

            $page->attributes = [
                'seo_title' => $this->title,
                'name' => $this->name,
                'seo_keywords' => $this->keywords,
                'seo_description' => $this->description,
                'visibility' => intval($this->visibility),
                'url' => $this->url
            ];

            $page->save(false);

            /** @var StoreAdminAuth $identity */
            $identity = $this->getUser()->getIdentity(false);

            ActivityLog::log($identity, ActivityLog::E_SETTINGS_PAGES_PAGE_UPDATED, $page->id, $page->id);

            $transaction->commit();

        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->addError('', $e->getMessage());
            return false;
        }

        return true;
    }


    /**
     * Add new page
     *
     * @return bool|int
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function add()
    {
        if (!$this->validate()){
            return false;
        }

        $transaction = Pages::getDb()->beginTransaction();
        try {
            $page = new Pages();

            $page->attributes = [
                'seo_title' => $this->title,
                'name' => $this->name,
                'seo_keywords' => $this->keywords,
                'seo_description' => $this->description,
                'visibility' => (int)$this->visibility,
                'is_draft' => 0,
                'url' => $this->url
            ];

            $page->save(false);

            /** @var StoreAdminAuth $identity */
            $identity = $this->getUser()->getIdentity(false);

            ActivityLog::log($identity, ActivityLog::E_SETTINGS_PAGES_PAGE_ADDED, $page->id, $page->id);

            $transaction->commit();

        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->addError('', $e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * @return bool|false|int
     */
    public function delete()
    {
        $page = $this->getPage();
        if (!PagesSearch::canDelete($page)) {
            return false;
        }
        $transaction = Pages::getDb()->beginTransaction();
        try {
            $page->delete();
            /** @var StoreAdminAuth $identity */
            $identity = $this->getUser()->getIdentity(false);
            ActivityLog::log($identity, ActivityLog::E_SETTINGS_PAGES_PAGE_DELETED, $page->id, $page->id);
            $transaction->commit();
        }
        catch (\Exception $e) {
            $transaction->rollBack();
            $this->addError('', $e->getMessage());
            return false;
        }
        return true;
    }

    /**
     * @param string $url
     * @return bool|false|int
     */
    public function duplicate($url) {

        $this->url = $url;
        if (!$this->validate('url')) {
            return false;
        }

        $page = $this->getPage();
        $newPage = new Pages();

        $newPage->attributes = $page->attributes;
        $newPage->created_at = null;
        $newPage->updated_at = null;
        $newPage->visibility = 0;
        $newPage->publish_at = null;
        $newPage->url = $url;
        return $newPage->save(false);


    }
}