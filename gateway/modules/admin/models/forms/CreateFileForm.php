<?php
namespace admin\models\forms;

use common\models\gateway\Files;
use common\models\gateways\Sites;
use Yii;
use yii\base\Model;

/**
 * Class CreateFileForm
 * @package admin\models\forms
 */
class CreateFileForm extends Model
{
    public $name;
    public $type;

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
            [['name', 'type'], 'required'],
            [['name'], 'string'],
            [['type'], 'in', 'range' => [
                Files::FILE_TYPE_JS,
                Files::FILE_TYPE_CSS,
                Files::FILE_TYPE_PAGE,
                Files::FILE_TYPE_SNIPPET,
            ]],
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

        $this->_file->content = $this->content;

        if (!$this->_file->save(true, ['content'])) {
            $this->addError('content', Yii::t('admin', "settings.message_settings_error"));
            return false;
        }

        return true;
    }
}