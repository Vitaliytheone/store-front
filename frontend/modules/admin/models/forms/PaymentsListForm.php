<?php

namespace frontend\modules\admin\models\forms;

use yii;
use yii\helpers\ArrayHelper;
use yii\db\Query;
use \common\models\store\Payments;

/**
 * Class PaymentsListForm
 * @package frontend\modules\admin\models\forms
 */
class PaymentsListForm extends Payments
{
    private $_db;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->_db = yii::$app->store->getInstance()->db_name;
        parent::init();
    }

    /**
     * Return Payment formatted Details data & time
     * @return array
     */
    public function getDetails()
    {
        $paymentLogs = (new Query())
            ->select(['id', 'checkout_id', 'result', 'ip', 'created_at'])
            ->from("$this->_db.payments_log")
            ->where(['checkout_id' => $this->checkout_id])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        $formatter = Yii::$app->formatter;

        $paymentDetails = [];

        foreach ($paymentLogs as $log) {
            $result = ArrayHelper::getValue($log, 'result', []);
            $created = ArrayHelper::getValue($log, 'created_at');
            $detail = [
                $formatter->asDatetime($created,'yyyy-MM-dd HH:mm:ss'),
                json_decode($result, true),
            ];

            $paymentDetails[] = print_r($detail,1);
        }

        return $paymentDetails;
    }
}