<?php

namespace common\models\panels\queries;


use yii\db\ActiveQuery;
use common\models\panels\PaypalFraudAccounts;

/**
 * Class PaypalFraudAccountsQuery
 * @package common\models\panels\queries
 */
class PaypalFraudAccountsQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return PaypalFraudAccounts[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return PaypalFraudAccounts|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}