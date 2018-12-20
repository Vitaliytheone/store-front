<?php

namespace admin\models\forms;

use common\models\gateway\Pages;
use common\models\gateways\Admins;
use yii\base\Model;
use yii\db\Query;

/**
 * Class EditPageForm
 * @package admin\models\forms
 */
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
     * @var Pages
     */
    protected $_page =  null;

    /**
     * Current Admins
     * @var Admins
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
     * Set current admin
     * @param Admins $admin
     */
    public function setUser($admin)
    {
        $this->_user = $admin;
    }

    /**
     * Get current admin
     * @return Admins|null
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
            [['title', 'visibility', 'url'], 'required'],
            [['title', 'seo_title'], 'string', 'max' => 255],
            [['visibility'], 'integer'],
            [['content'], 'string'],
            [['title', 'seo_title', 'seo_description', 'url',], 'trim'],
            [['seo_description', 'seo_keywords'], 'string', 'max' => 2000],

            ['url', 'match', 'pattern' => '/^[a-z0-9-_]+$/i'],
            ['url', 'unique', 'targetClass' => Pages::class, 'targetAttribute' => ['url' => 'url'], 'filter' => function(Query $query) {
                $query->andWhere(['deleted' => Pages::DELETED_NO]);
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
     * @return bool
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        if (empty($this->_page)) {
            $this->_page = new Pages();
        }

        $this->_page->attributes = $this->attributes;

        if (!$this->_page->save(false)) {
            $this->addErrors($this->_page->getErrors());
            return false;
        };

        return true;
    }
}