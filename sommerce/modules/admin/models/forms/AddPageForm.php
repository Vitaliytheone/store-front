<?php

namespace sommerce\modules\admin\models\forms;


use common\models\store\Pages;
use yii\base\Model;

class AddPageForm extends Model
{
   public $name;
    public $title;
    public $description;
    public $keywords;
    public $url;
    public $visibility;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['title', 'keywords', 'url', 'name'], 'string'],
            [['visibility'], 'integer'],
            [['url', 'title'], 'string', 'max' => 300]
        ];
    }



    /**
     * @return bool|int
     */
    public function save()
    {
        if (!$this->validate()){
            return false;
        }

        $page = new Pages();

        $page->attributes = [
            'seo_title' => $this->title,
            'name' => '',
            'seo_keywords' => $this->keywords,
            'seo_description' => $this->description,
            'visibility' => $this->visibility,
            'is_draft' => 1,
            'url' => ''

        ];

        return true;
    }



}