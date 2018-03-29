<?php

namespace my\modules\superadmin\models\search;

use common\components\traits\UnixTimeFormatTrait;
use common\models\panels\AdditionalServices;
use common\models\panels\Customers;
use common\models\panels\PanelDomains;
use common\models\panels\ProjectAdmin;
use common\models\panels\Project;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * Class ApiKeysLogsSearch
 *
 * ID       panel_providers_log.id
 * Panel	project.site
 * Account	project_admin.login
 * Provider	additional_services.name
 * Key		panel_providers_log.apiKey, panel_providers_log.login, panel_providers_log.passwd
 * In use	[project.site, …]
 * Date		panel_providers_log.created_at
 *
 * @package my\modules\superadmin\models\search
 */
class ApiKeysLogsSearch
{
    use UnixTimeFormatTrait;

    const PAGE_SIZE = 100;

    private $_panelProvidersLogTable;
    private $_panelDomainsTable;
    private $_customersTable;
    private $_projectTable;
    private $_providersTable;
    private $_projectAdminTable;

    /** @var $_dataProvider ActiveDataProvider */
    private $_dataProvider;

    public function __construct()
    {
        $this->_panelProvidersLogTable = '{{%panel_providers_log}}';
        $this->_panelDomainsTable= PanelDomains::tableName();
        $this->_projectTable= Project::tableName();
        $this->_projectAdminTable= ProjectAdmin::tableName();
        $this->_customersTable= Customers::tableName();
        $this->_providersTable = AdditionalServices::tableName();
    }

    /**
     * Search
     * @return ActiveDataProvider
     */
    public function search()
    {
        $query = (new Query())
            ->select([
                'lt.id id', 'lt.panel_id panel_id', 'lt.admin_id admin_id', 'lt.provider_id provider_id',
                'lt.login login', 'lt.passwd passwd','lt.apikey apiKey', 'lt.matched matched', 'lt.report report', 'lt.created_at created_at',
                'pt.site',
                'ct.id customer_id', 'ct.email',
                'pat.id admin_id', 'pat.login admin_login',
                'prvt.name provider',
            ])
            ->from(['lt' => $this->_panelProvidersLogTable])
            ->leftJoin(['pt' => $this->_projectTable], 'pt.id = lt.panel_id')
            ->leftJoin(['pat' => $this->_projectAdminTable], 'pat.id = lt.admin_id')
            ->leftJoin(['ct' => $this->_customersTable], 'ct.id = pt.cid')
            ->leftJoin(['prvt' => (new Query())
                ->select(['res', 'name'])
                ->from($this->_providersTable)
                ->groupBy('res')
            ],'prvt.res = lt.provider_id')
            ->andWhere(['not', ['matched' => null]])
            ->orderBy([
                'id' => SORT_DESC,
            ])
            ->indexBy('id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => static::PAGE_SIZE,
            ],
        ]);

        $this->_dataProvider = $dataProvider;

        return $this->_dataProvider;
    }

    /**
     * Populate fetched models by `in use projects` data
     * @param $models
     */
    private function _populateByInUseProjects(&$models)
    {
        /** @var array $projectsIds collection of project ids of each models */
        $projectsIds = [];

        // Get in use projects ids from json & collect all projects ids
        foreach ($models as &$model) {
            if (!isset($model['matched'])) {
                continue;
            }
            $projectIds = json_decode($model['matched'], true);
            if (!is_array($projectIds)) {
                continue;
            }
            $model['matched'] = $projectIds;

            $projectsIds = array_merge($projectsIds, $projectIds);
        }

        // Fetch project data for each panel.
        $projects = (new Query())
            ->select([
                'id',
                'cid',
                'site',
                'name',
            ])
            ->from($this->_projectTable)
            ->where(['in', 'id', $projectsIds])
            ->indexBy('id')
            ->all();

        // Populate each model by projects in use data
        foreach ($models as &$model) {

            $matchedProjects = [];
            $currentModelProjectsIds = $model['matched'];

            foreach ($projects as $projectId => &$project) {
                // Add `in use` projects
                if (in_array($projectId, $currentModelProjectsIds)) {
                    // Check current panel and `in use panels` owners
                    $project['common_customer'] = $project['cid'] === $model['customer_id'];
                    array_push($matchedProjects, $project);
                }
            }

            // Add additional data
            $model['matched_projects'] = $matchedProjects;
            $model['date'] = static::formatDate($model['created_at']);
        }
    }

    /**
     * Return found models with additional data for view
     * @return array
     */
    public function getModelsForView()
    {
        if (!$this->_dataProvider instanceof ActiveDataProvider) {
            return [];
        }

        $models = $this->_dataProvider->getModels();
        // Populate matched by panel names
        $this->_populateByInUseProjects($models);

        return $models;
    }

}