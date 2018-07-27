<?php
namespace my\modules\superadmin\models\forms;

use common\models\panels\ExpiredLog;
use Yii;
use common\models\panels\Project;
use yii\base\Model;

/**
 * Class EditExpityForm
 * @package my\modules\superadmin\models\forms
 */
class EditExpiryForm extends Model {

    public $expired;

    /**
     * @var Project
     */
    private $_project;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['expired'], 'required'],
            [['expired'], 'date', 'format' => 'php:Y-m-d H:i:s']
        ];
    }

    /**
     * Set project
     * @param Project $project
     */
    public function setProject(Project $project)
    {
        $this->_project = $project;
    }

    /**
     * Save expied
     * @return bool
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $lastExpired = $this->_project->expired;
        $this->_project->expired = strtotime($this->expired) - Yii::$app->params['time'];

        if (!$this->_project->save(false)) {
            $this->addError('expiry', 'Can not edit expired');
            return false;
        }

        $ExpiredLogModel = new ExpiredLog();
        $ExpiredLogModel->attributes = [
            'pid' => $this->_project->id,
            'expired_last' => $lastExpired,
            'expired' => $this->_project->expired,
            'created_at' => time(),
            'type' => ExpiredLog::TYPE_CHANGE_EXPIRY
        ];
        $ExpiredLogModel->save(false);

        return true;
    }


}