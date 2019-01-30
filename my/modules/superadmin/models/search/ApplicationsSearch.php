<?php

namespace superadmin\models\search;

use common\models\panels\Params;
use yii\db\ActiveQuery;

/**
 * Class ApplicationsSearch
 * @package superadmin\models
 */
class ApplicationsSearch extends Params
{
    private $params;

    public $rows;

    /**
     * Set search parameters
     * @param array $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * Build main search query
     * @return ActiveQuery the newly created [[ActiveQuery]] instance.
     */
    private function buildQuery()
    {
        $query = static::find();

        return $query;
    }

    /**
     * Search contents
     * @return array
     */
    public function search()
    {
        $query = clone $this->buildQuery();

        $models = $query
            ->where(['code' => [
                Params::CODE_WHOISXML,
                Params::CODE_SOCIALSAPI,
                Params::CODE_WHOISXMLAPI,
                Params::CODE_AHNAMES,
                Params::CODE_GOGETSSL,
                Params::CODE_DNSLYTICS,
                Params::CODE_NAMESILO
            ]])
            ->orderBy(['id' => SORT_ASC])
            ->all();

        return $models;
    }
}