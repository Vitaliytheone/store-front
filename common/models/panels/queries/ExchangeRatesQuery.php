<?php

namespace common\models\panels\queries;

use yii\db\ActiveQuery;
use common\models\panels\ExchangeRates;

/**
 * This is the ActiveQuery class for [[ExchangeRates]].
 *
 * @see ExchangeRates
 */
class ExchangeRatesQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return ExchangeRates[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ExchangeRates|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}