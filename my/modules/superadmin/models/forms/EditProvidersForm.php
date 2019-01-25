<?php
namespace superadmin\models\forms;

use common\models\panels\AdditionalServices;
use common\models\panel\Services;
use common\models\panels\UserServices;
use Yii;
use common\models\panels\Project;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class EditProvidersForm
 * @package superadmin\models\forms
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
     * @throws \yii\db\Exception
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $currentProviders = ArrayHelper::index($this->_project->userServices, 'provider_id');
        $this->providers = (array)ArrayHelper::getValue($this, 'providers', []);
        $this->providers = array_filter($this->providers);

        foreach ($this->providers as $provider) {
            if (!empty($currentProviders[$provider])) {
                unset($currentProviders[$provider]);
                continue;
            }

            $userService = new UserServices();
            $userService->attributes = [
                'panel_id' => $this->_project->id,
                'provider_id' => $provider,
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
                'provider_id' => Yii::$app->params['manualProviderId'],
                'mode' => Services::MODE_DISABLED,
            ], 'provider_id IN (' . implode(",", $resIds) . ')')->execute();
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
            $providers[$provider->provider_id] = [
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