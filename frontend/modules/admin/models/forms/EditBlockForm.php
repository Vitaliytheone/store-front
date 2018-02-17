<?php
namespace frontend\modules\admin\models\forms;

use common\models\store\Blocks;
use Yii;
use yii\base\Model;

/**
 * Class EditBlockForm
 * @package app\modules\superadmin\models\forms
 */
class EditBlockForm extends Model {

    public $content;

    /**
     * @var Blocks
     */
    private $_block;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['content'], 'safe']
        ];
    }

    /**
     * Set block
     * @param Blocks $block
     */
    public function setBlock($block)
    {
        $this->_block = $block;
    }

    /**
     * Save block content
     * @return bool
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $this->_block->content = json_encode($this->content, true);

        if (!$this->_block->save(false)) {
            $this->addErrors($this->_block->getErrors());
            return false;
        }

        return true;
    }
}