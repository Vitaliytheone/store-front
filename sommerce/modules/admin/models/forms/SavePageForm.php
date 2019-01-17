<?php

namespace sommerce\modules\admin\models\forms;
use common\models\store\PagesOld;

class SavePageForm extends EditPageForm
{

    public $template;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge (parent::rules(), [
            ['template', 'default', 'value' => PagesOld::TEMPLATE_PAGE],
            [['template'], 'string'],
        ]);
    }
}