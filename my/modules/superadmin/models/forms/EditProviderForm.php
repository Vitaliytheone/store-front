<?php
namespace my\modules\superadmin\models\forms;

use common\models\panels\AdditionalServices;
use Yii;
use yii\base\Model;

/**
 * Class EditProviderForm
 * @package my\modules\superadmin\models\forms
 */
class EditProviderForm extends Model
{
    public $name;
    public $skype;

    /**
     * @var AdditionalServices $_provider;
     */
    protected $_provider;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 32],
            [['skype'], 'string', 'max' => 300],
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $this->_provider->skype = $this->skype;
        $this->_provider->name = $this->name;

        if (!$this->_provider->save(false)) {
            $this->addError('message', Yii::t('app', 'error.provider.can_not_edit_provider'));
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
            'skype' => Yii::t('app/superadmin', 'providers.edit.column_skype'),
            'name' => Yii::t('app/superadmin', 'providers.edit.column_name'),
        ];
    }

    /**
     * Set provider
     * @param AdditionalServices $provider
     */
    public function setProvider($provider)
    {
        $this->_provider = $provider;
    }
}