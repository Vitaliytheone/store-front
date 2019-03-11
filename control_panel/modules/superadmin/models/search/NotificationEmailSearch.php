<?php

namespace superadmin\models\search;

use common\models\sommerces\NotificationEmail;
use yii\db\ActiveQuery;

/**
 * Class NotificationEmailSearch
 * @package superadmin\models
 */
class NotificationEmailSearch extends NotificationEmail
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
     * Search emails
     * @return array
     */
    public function search()
    {
        $query = clone $this->buildQuery();

        $models = $query->orderBy([
                'id' => SORT_DESC
            ])
            ->all();

        return $models;
    }
}