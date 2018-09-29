<?php

namespace my\modules\superadmin\models\search;

use common\models\panels\Params;
use yii\db\ActiveQuery;

/**
 * Class PaymentGatewaySearch
 * @package my\modules\superadmin\models
 */
class PaymentGatewaySearch extends Params
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
        $query = static::find()
        ->andFilterWhere([
            'or',
            ['like', 'options', '"pid":-1'],
            ['like', 'options', '"pid":"-1"'],
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
            $options = $value->getOptions();
            $name = $options['name'];
            $visibility = $options['visibility'];

            $returnData[] = [
                'name' => $name,
                'visibility' => $visibility,
                'visibility_string' =>
                    array_key_exists($visibility, Params::getVisibilityList()) ?
                        Params::getVisibilityList()[$visibility] :
                        Params::getVisibilityList()[Params::VISIBILITY_DISABLED],
                'id' => $value->id,
                'code' => $value->code,
                'options' => $options,
                'updated_at' => $value->updated_at,
                'position' => $value->position,
            ];
        }

        return $returnData;
    }

    /**
     * Search payment gateway
     * @return array
     */
    public function search(): array
    {
        $query = clone $this->buildQuery();

        $models = $query->orderBy([
                'position' => SORT_ASC
            ]);

        return $this->prepareData($models->all());
    }
}