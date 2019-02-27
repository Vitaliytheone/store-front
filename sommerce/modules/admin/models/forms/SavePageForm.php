<?php

namespace sommerce\modules\admin\models\forms;

use common\models\sommerce\PageFiles;
use Yii;
use common\models\sommerce\Pages;
use common\models\sommerces\Stores;
use yii\base\Model;
use yii\db\Transaction;

/**
 * Class SavePageForm
 * @package sommerce\modules\admin\models\forms
 */
class SavePageForm extends Model
{
    /**
     * @var array
     */
    public $styles;
    /**
     * @var array
     */
    public $header;
    /**
     * @var array
     */
    public $footer;
    /**
     * @var array
     */
    public $page;

    /**
     * @var Stores
     */
    protected $_store;

    /**
     * @var Pages
     */
    protected $_page;

    /**
     * @var boolean
     */
    protected $_draft = true;

    /**
     * Set store
     * @param Stores $store
     */
    public function setStore(Stores $store)
    {
        $this->_store = $store;
    }

    /**
     * Return store
     * @return Stores
     */
    public function getStore()
    {
        return $this->_store;
    }

    /**
     * Set page
     * @param Pages $page
     */
    public function setPage(Pages $page) {
        $this->_page = $page;
    }

    /**
     * Get page
     * @return Pages
     */
    public function getPage()
    {
        return $this->_page;
    }

    /**
     * Get is draft
     * @return bool
     */
    public function getIsDraft()
    {
        return $this->_draft;
    }

    /**
     * Set is draft
     * @param bool $draft
     */
    public function setIsDraft(bool $draft)
    {
        $this->_draft = $draft;
    }

    /**
     * @return string
     */
    public function formName()
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['styles', 'header', 'footer', 'page'],'required'],
            [['styles', 'header', 'footer', 'page'], function($attribute, $params) {

                $data = $this->$attribute;
                
                if (!is_array($data)) {
                    $this->addError($attribute, 'Bad (' . $attribute . ') format!');

                    return false;
                }

                // Check draft and publish
                if (!array_key_exists('json', $data)) {
                    $this->addError($attribute, 'Missed (' . $attribute . ') json field!');

                    return false;
                }

                // Check publish
                if (!$this->getIsDraft() && !array_key_exists('content', $data) ) {
                    $this->addError($attribute, 'Missed (' . $attribute . ') content field!');

                    return false;
                }

                return true;
            }],
        ];
    }

    /**
     * Create or update draft|publish
     * @return bool
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->storeDb->beginTransaction();

        // Page routine
        $page = $this->getPage();

        if (!$page) {
            $page = new Pages();
        }

        $page->setJsonDraft($this->page['json']);

        if ($this->getIsDraft()) {
            $page->is_draft = Pages::IS_DRAFT_ON;
            $page->visibility = Pages::VISIBILITY_OFF;
        } else {
            // Publish
            $page->is_draft = Pages::IS_DRAFT_OFF;
            $page->publish_at = time();
            $page->visibility = Pages::VISIBILITY_ON;
            $page->twig = $this->page['content'];
            $page->setJson($this->page['json']);
        }

        if (!$page->save(false)) {
            $this->addError('page', 'Cannot save page!');
            $transaction->rollBack();

            return false;
        }

        $this->setPage($page);

        // Page files routine
        $files = [
            PageFiles::NAME_HEADER => [
                'type' => PageFiles::FILE_TYPE_TWIG,
                'data' => $this->header
            ],
            PageFiles::NAME_FOOTER => [
                'type' =>  PageFiles::FILE_TYPE_TWIG,
                'data' => $this->footer,
            ],
            PageFiles::NAME_STYLES => [
                'type' => PageFiles::FILE_TYPE_STYLE,
                'data' => $this->styles,
            ],
        ];

        foreach ($files as $name => $file) {

            $pageFile = PageFiles::findOne([
                'name_react' => $name,
                'file_type' => $file['type']
            ]);

            if (!$pageFile) {
                $pageFile = new PageFiles();
                $pageFile->name_react = $name;
                $pageFile->file_type = $file['type'];
            }

            $pageFile->setJsonDraft($file['data']['json']);

            if ($this->getIsDraft()) {
                // Were there any changes in the file
                if ($pageFile->isDraftHasChanges()) {
                    $pageFile->is_draft = PageFiles::IS_DRAFT_ON;
                }
            } else {
                // Publish
                $pageFile->is_draft = PageFiles::IS_DRAFT_OFF;
                $pageFile->publish_at = time();
                $pageFile->content = $file['data']['content'];
                $pageFile->setJson($file['data']['json']);
            }

            if (!$pageFile->save(false)) {
                $this->addError('page_file', 'Cannot save page file!');
                $transaction->rollBack();

                return false;
            }
        }

        $transaction->commit();

        return true;
    }
}