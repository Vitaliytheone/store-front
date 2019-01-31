<?php
namespace admin\models\forms;

use common\models\gateway\Files;
use common\models\gateways\Sites;
use Yii;
use yii\base\Model;

/**
 * Class RenameFileForm
 * @package admin\models\forms
 */
class RenameFileForm extends Model
{
    public $name;

    /**
     * @var Files
     */
    protected $_file;

    /**
     * @var Sites
     */
    protected $_gateway;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string'],
        ];
    }

    /**
     * @param Sites $gateway
     */
    public function setGateway(Sites $gateway)
    {
        $this->_gateway = $gateway;
    }

    /**
     * @param Files $file
     */
    public function setFile(Files $file)
    {
        $this->_file = $file;
        $this->name = $file->name;
    }

    /**
     * @return bool
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $this->_file->name = $this->name;

        if (!$this->_file->save(true, ['name'])) {
            $this->addError('name', Yii::t('admin', "settings.message_settings_error"));
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
            'name' => Yii::t('admin', 'settings.files.rename_file.field.name'),
        ];
    }
}