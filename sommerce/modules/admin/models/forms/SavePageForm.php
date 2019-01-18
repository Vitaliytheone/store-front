<?php

namespace sommerce\modules\admin\models\forms;

use common\models\store\Pages;
use common\models\stores\Stores;
use yii\base\Model;

/**
 * Class SavePageForm
 * @package sommerce\modules\admin\models\forms
 */
class SavePageForm extends Model
{
    public $json;

    public $styles;

    public $twig;

    public $json_dev;

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
    protected $_dev = false;

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
     * Set dev page mode
     * @param bool $dev
     */
    public function setDev(bool $dev)
    {
        $this->_dev = $dev;
    }

    /**
     * Get dev mode
     * @return bool
     */
    public function getDev()
    {
       return $this->_dev;
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
            [['twig', 'styles'], 'safe'],
            [['json', 'json_dev'], function($attribute, $params){

                $json = trim($this->$attribute);
                @json_decode($json);

                if (json_last_error()) {
                    $this->addError($attribute, 'Incorrect json format!');
                    return false;
                }

                return true;
            }],
        ];
    }

    /**
     * Update page
     * @return bool
     */
    public function save()
    {
        $page = $this->getPage();

        if (!$this->validate()) {
            return false;
        }

        if ($this->getDev()) {
            $page->json_dev = $this->json_dev;
        } else {
            $page->json = $this->json;
            $page->json_dev = $this->json_dev;
            $page->styles = $this->styles;
            $page->twig = $this->twig;
        }

        if (!$page->save(false)) {
            return false;
        }

        return true;
    }
}