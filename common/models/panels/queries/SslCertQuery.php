<?php

namespace common\models\panels\queries;

/**
 * This is the ActiveQuery class for [[\common\models\panels\SslCert]].
 *
 * @see \common\models\panels\SslCert
 */
class SslCertQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\panels\SslCert[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\panels\SslCert|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
