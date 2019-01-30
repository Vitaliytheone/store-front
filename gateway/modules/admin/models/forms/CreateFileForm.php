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
    public $file;
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
     * @return bool
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $model = new Files();
        $model->file_type = $this->type;
        $model->name = $this->name;

        if (!$model->save()) {
            $this->addErrors($model->getErrors());
            return false;
        }

        return true;
    }
}