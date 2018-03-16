<?php
namespace my\modules\superadmin\models\forms;

use my\helpers\ChildHelper;
use common\models\panels\AdditionalServices;
use common\models\panels\UserServices;
use Yii;
use common\models\panels\Project;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\ArrayHelper;

/**
 * Class DowngradePanelForm
 * @package my\modules\superadmin\models\forms
 */
class DowngradePanelForm extends Model {

    public $provider;

    /**
     * @var Project
     */
    private $_project;

    /**
     * @var AdditionalServices
     */
    private $_providers;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['provider'], 'required'],
            [['provider'], 'integer'],
            [['provider'], 'in', 'range' => array_keys($this->getProviders())],
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

        /**
         * @var UserServices $currentProviders
         */
        $currentProviders = ArrayHelper::index($this->_project->userServices, 'aid');

        $transaction = Yii::$app->db->beginTransaction();
        
        try {
            if (!$this->_project->downgrade()) {
                $this->addError('mode', 'Can not downgrade.');
                return false;
            }

            if (!empty($currentProviders[$this->provider])) {
                unset($currentProviders[$this->provider]);
            } else {
                $userService = new UserServices();
                $userService->attributes = [
                    'pid' => $this->_project->id,
                    'aid' => $this->provider,
                ];

                $userService->save(false);
            }

            $projectDbConnection = $this->_project->getDbConnection();
            $resIds = [];

            foreach ($currentProviders as $res => $provider) {
                $resIds[] = $res;
                $provider->delete();
            }

            if ($projectDbConnection && !empty($resIds)) {
                $projectDbConnection->createCommand()->update('services', [
                    'res' => Yii::$app->params['manualProviderId']
                ], 'res IN (' . implode(",", $resIds) . ')')->execute();
            }

        } catch (Exception $exception) {
            $transaction->rollBack();

            Yii::error($exception->getMessage() . $exception->getTraceAsString());

            $this->addError('mode', 'Can not downgrade.');
            return false;
        }

        $transaction->commit();

        return true;
    }

    /**
     * Get providers
     * @return mixed
     */
    public function getProviders()
    {
        if (null !== $this->_providers) {
            return $this->_providers;
        }

        $this->_providers = ChildHelper::getProviders($this->_project->cid, [
            Project::STATUS_ACTIVE,
            Project::STATUS_FROZEN
        ]);

        if (($key = array_search($this->_project->site, $this->_providers))) {
            unset($this->_providers[$key]);
        }

        return $this->_providers;
    }
}