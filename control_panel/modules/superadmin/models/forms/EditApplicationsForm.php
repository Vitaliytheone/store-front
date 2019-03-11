<?php

namespace superadmin\models\forms;

use common\models\sommerces\Params;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * EditApplicationsForm
 *
 * @property \common\models\sommerces\Params $params
 */
class EditApplicationsForm extends Model
{
    /** @var array */
    public $options = [];

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
        ];
    }

    /**
     * Set settings
     * @param Params $params
     */
    public function setParams(Params $params)
    {
        $this->_params = $params;
        $this->options = $params->getOptions();

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

        $applicationsSettings = $this->_params->getOptions();

        $cleanOptions = [];

        foreach ($applicationsSettings as $key => $details) {
            $data = ArrayHelper::getValue($this->options, $key);
            if ($data !== null){
                $cleanOptions[$key] = (string)$data;
            } else {
                $cleanOptions[$key] = $details;
            }
        }

        $this->_params->setOptions($cleanOptions);

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
