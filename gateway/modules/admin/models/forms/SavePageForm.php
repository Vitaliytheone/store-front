<?php

namespace admin\models\forms;
use common\models\store\Pages;

class SavePageForm extends EditPageForm
{

    public $template;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge (parent::rules(), [
            ['template', 'default', 'value' => Pages::TEMPLATE_PAGE],
            [['template'], 'string'],
        ]);
    }
}