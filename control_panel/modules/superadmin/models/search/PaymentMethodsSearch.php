<?php

namespace superadmin\models\search;

use common\models\sommerces\Params;
use yii\db\ActiveQuery;

/**
 * Class PaymentMethodsSearch
 * @package control_panel\modules\superadmin\models
 */
class PaymentMethodsSearch extends Params
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
            'category' => static::CATEGORY_PAYMENT
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
            $options = json_decode($value['options'], true);
            $visibility = isset($options['visibility']) ? $options['visibility'] : Params::VISIBILITY_DISABLED;

            $returnData[] = [
                'name' => $options['name'],
                'visibility' => $visibility,
                'visibility_string' =>
                    array_key_exists($visibility, Params::getVisibilityList()) ?
                        Params::getVisibilityList()[$visibility] :
                        Params::getVisibilityList()[Params::VISIBILITY_DISABLED],
                'id' => $value['id'],
                'code' => $value['code'],
                'category' => $value['category'],
                'options' => $options,
                'updated_at' => $value['updated_at'],
                'position' => $value['position'],
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
        ])->asArray()->all();

        return $this->prepareData($models);
    }
}