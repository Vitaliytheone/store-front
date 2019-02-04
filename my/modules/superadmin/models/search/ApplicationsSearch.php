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

        $query->andWhere([
            'category' => static::CATEGORY_SERVICE,
        ]);
        $query->andWhere([
            'code' => [
                Params::CODE_WHOISXML,
                Params::CODE_SOCIALSAPI,
                Params::CODE_WHOISXMLAPI,
                Params::CODE_AHNAMES,
                Params::CODE_GOGETSSL,
                Params::CODE_DNSLYTICS,
                Params::CODE_NAMESILO,
            ]
        ]);

        return $query;
    }

    /**
     * @param array $data
     * @return array
     */
    private function prepareData(array $data): array
    {
        $returnData = [];

        foreach ($data as $key => $value) {

            $returnData[] = [
                'id' => $value['id'],
                'code' => $value['code'],
            ];
        }

        return $returnData;
    }

    /**
     * Search contents
     * @return array
     */
    public function search()
    {
        $query = clone $this->buildQuery();

        $models = $query
            ->orderBy([
                'id' => SORT_ASC,
                'position' => SORT_ASC
            ])
            ->asArray()
            ->all();

        return $this->prepareData($models);
    }
}