<?php
namespace admin\models\forms;

use common\models\gateway\Files;
use common\models\gateways\Sites;
use Yii;
use yii\base\Model;

/**
 * Class EditFileForm
 * @package admin\models\forms
 */
class EditFileForm extends Model
{
    public $content;

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
            [['content'], 'safe'],
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
        $this->content = $file->content;
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