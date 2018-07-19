<?php
namespace my\modules\superadmin\models\forms;

use common\models\panels\AdditionalServices;
use common\models\panels\UserServices;
use Yii;
use common\models\panels\Project;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class EditProvidersForm
 * @package my\modules\superadmin\models\forms
 */
class EditProvidersForm extends Model {

    public $providers = [];

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
            [['providers'], 'safe'],
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

        $currentProviders = ArrayHelper::index($this->_project->userServices, 'aid');
        $this->providers = (array)ArrayHelper::getValue($this, 'providers', []);
        $this->providers = array_filter($this->providers);

        foreach ($this->providers as $provider) {
            if (!empty($currentProviders[$provider])) {
                unset($currentProviders[$provider]);
                continue;
            }

            $userService = new UserServices();
            $userService->attributes = [
                'pid' => $this->_project->id,
                'aid' => $provider,
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

        return true;
    }

    /**
     * Get providers
     * @return array
     */
    public function getProviders()
    {
        $providers = [];

        foreach ($this->_getProviders() as $provider) {
            $providers[$provider->res] = [
                'name' => $provider->name,
                'internal' => (AdditionalServices::TYPE_INTERNAL == $provider->type) ? true : false
            ];
        }

        return $providers;
    }

    /**
     * Get providers
     * @return AdditionalServices|AdditionalServices[]|array
     */
    private function _getProviders()
    {
        if (null == $this->_providers) {
            $this->_providers = AdditionalServices::find()->orderBy([
                'name' => SORT_ASC
            ])->all();
        }

        return $this->_providers;
    }
}