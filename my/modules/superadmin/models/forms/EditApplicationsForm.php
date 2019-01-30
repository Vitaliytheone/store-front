<?php

namespace superadmin\models\forms;

use common\models\panels\Params;
use Yii;
use yii\base\Model;

/**
 * EditApplicationsForm
 *
 * @property \common\models\panels\Params $params
 */
class EditApplicationsForm extends Model
{
    public $code;
//    public $options;

    /** @var Params */
    protected $_params;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['options'], 'safe'],
            [['options'], 'trim'],
            ['credentials', 'safe']
        ];
    }

    /**
     * Set content
     * @param Params $params
     */
    public function setParams(Params $params)
    {
        $this->_params = $params;
        $options = $params->getOptions();

        $this->code = $params->code;
        $this->attributes = $options;
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

        $this->_params->setOptions($this->attributes);

        if (!$this->_params->save()) {
            $this->addErrors($this->_params->getErrors());
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
            'code' => Yii::t('app/superadmin', 'applications.edit.column_code'),
            'options' => Yii::t('app/superadmin', 'applications.edit.column_options'),
        ];
    }
}
