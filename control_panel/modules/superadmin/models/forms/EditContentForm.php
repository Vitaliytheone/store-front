<?php
namespace superadmin\models\forms;

use common\models\sommerces\Content;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * EditContentForm is the model behind the Edit Content form.
 */
class EditContentForm extends Model
{
    public $name;
    public $text;

    /**
     * @var Content
     */
    protected $_content;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['text'], 'safe'],
            [['text'], 'trim'],
        ];
    }

    /**
     * Set content
     * @param Content $content
     */
    public function setContent(Content $content)
    {
        $this->_content = $content;
    }

    /**
     * Save admin settings
     * @return bool
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $this->_content->text = (string)$this->text;

        if (!$this->_content->save()) {
            $this->addErrors($this->_content->getErrors());
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'text' => Yii::t('app/superadmin', 'content.edit.column_text'),
            'name' => Yii::t('app/superadmin', 'content.edit.column_name'),
        ];
    }
}
