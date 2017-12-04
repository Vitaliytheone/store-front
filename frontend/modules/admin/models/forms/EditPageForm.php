<?php

namespace frontend\modules\admin\models\forms;

use Yii;
use common\models\store\Products;
use common\models\store\Pages;

class EditPageForm extends Pages
{

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
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'url', 'visibility'], 'required'],
            [['visibility'], 'integer'],
            [['content'], 'string'],
            [['name', 'url'], 'unique'],
            ['url', 'unique', 'targetClass' => Products::className(), 'targetAttribute' => ['url' => 'url']],
            ['url', 'match', 'pattern' => '/^[a-z0-9-_]+$/i'],
            [['name', 'seo_title', 'seo_description', 'url',], 'trim'],
            [['name', 'seo_title', 'url'], 'string', 'max' => 255],
            [['seo_description'], 'string', 'max' => 2000],
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

        if ($this->load($postData) && $this->save()) {
            return true;
        }

        return false;
    }

    /**
     * Save Page routine
     * @param $postData
     * @return bool
     */
    public function edit($postData)
    {
        if ($this->load($postData) && $this->save()) {
            return true;
        }

        return false;
    }

    /**
     * Set default model attributes
     */
    private function _setDefaults()
    {
        $this->setAttributes([
            'name' => '',
            'visibility' => self::VISIBILITY_YES,
            'content' => '',
            'seo_title' => Yii::t('admin', 'settings.pages_seo_page_default'),
            'seo_description' => Yii::t('admin', 'settings.pages_seo_meta_default'),
            'url' => Yii::t('admin', 'settings.pages_seo_url_default'),
        ], false);
    }

}