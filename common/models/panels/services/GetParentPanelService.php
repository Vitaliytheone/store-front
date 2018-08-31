<?php
namespace common\models\panels\services;

use common\models\panels\Project;
use yii\db\Query;

/**
 * Class GetParentPanelService
 * @package common\models\panels\services
 */
class GetParentPanelService
{
    private $provider_id;

    /**
     * Owner constructor.
     * @param $provider_id
     */
    public function __construct($provider_id)
    {
        $this->provider_id = $provider_id;
    }

    /**
     * @return null|Project
     */
    public function get()
    {
        if (empty($this->provider_id)) {
            return null;
        }

        $owner = (new Query())
            ->select(['additional_services.name'])
            ->from('additional_services')
            ->andWhere(['res' =>  $this->provider_id])
            ->one()['name'];

        if (empty($owner)) {
            return null;
        }

        return Project::findOne(['site' => $owner]);
    }
}