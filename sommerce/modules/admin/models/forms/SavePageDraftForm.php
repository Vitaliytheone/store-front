<?php

namespace sommerce\modules\admin\models\forms;

use common\models\store\PageFiles;
use Yii;
use common\models\store\Pages;
use common\models\stores\Stores;
use yii\base\Model;
use yii\db\Transaction;

/**
 * Class SavePageForm
 * @package sommerce\modules\admin\models\forms
 */
class SavePageDraftForm extends Model
{
    public $styles;
    public $layouts;
    public $json;

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
    protected $_draft = false;

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
            [['json', 'styles', 'layouts'], 'required'],
            [['json', 'styles'], 'safe'],
            ['layouts', function($attribute, $params) {
                $layouts = $this->$attribute;
                if (!is_array($layouts) || !isset($layouts['header'], $layouts['footer'])) {
                    $this->addError($attribute, 'Missed layouts data!');
                    return false;
                }
                return true;
            }],
        ];
    }

    /**
     * Create or update draft
     * @return bool
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->storeDb->beginTransaction();

        $page = $this->getPage();

        if (!$page) {
            $page = new Pages();
            $page->visibility = Pages::VISIBILITY_OFF;
        }

        $page->setJsonDraft($this->json);
        $page->is_draft = Pages::IS_DRAFT_ON;

        if (!$page->save(false)) {
            $transaction->rollBack();

            return false;
        }

        $this->setPage($page);

        // Styles routine
        if ($this->styles) {
            $styles = PageFiles::findOne([
                'name' => PageFiles::NAME_STYLES,
                'file_type' => PageFiles::FILE_TYPE_STYLE
            ]);

            if (!$styles) {
                $styles = new PageFiles();
                $styles->name = PageFiles::NAME_STYLES;
                $styles->file_type = PageFiles::FILE_TYPE_STYLE;
            }

            $styles->setJsonDraft($this->styles);

            if (!$styles->save(false)) {
                $transaction->rollBack();

                return false;
            }
        }

        // Layout routine
        if ($this->layouts) {
            foreach ($this->layouts as $name => $content) {

                $layout = PageFiles::findOne([
                    'name' => $name,
                    'file_type' => PageFiles::FILE_TYPE_TWIG,
                ]);

                if (!$layout) {
                    $layout = new PageFiles();
                    $layout->name = $name;
                    $layout->file_type = PageFiles::FILE_TYPE_TWIG;
                }

                $layout->setJsonDraft($content);

                if (!$layout->save(false)) {
                    $transaction->rollBack();

                    return false;
                }
            }
        }

        $transaction->commit();

        return true;
    }
}