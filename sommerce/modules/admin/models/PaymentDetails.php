<?php

namespace sommerce\modules\admin\models;

use common\helpers\CustomHtmlHelper;
use common\models\store\PaymentsLog;
use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use common\models\store\Payments;

/**
 * Class Payment
 * @package sommerce\modules\admin\forms
 */
class PaymentDetails extends Payments
{
    private $_storeDb;
    private $_paymentsLogTable;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->_storeDb = yii::$app->store->getInstance()->db_name;
        $this->_paymentsLogTable = $this->_storeDb . "." . PaymentsLog::tableName();

        parent::init();
    }

    /**
     * Return Payment Details data
     * @return array|null
     */
    public function details()
    {
        $paymentLogs = (new Query())
            ->select(['id', 'checkout_id', 'result', 'ip', 'created_at'])
            ->from($this->_paymentsLogTable)
            ->where(['checkout_id' => $this->checkout_id])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        $formatter = Yii::$app->formatter;

        $paymentDetails = [];

        foreach ($paymentLogs as $log) {
            $detail = CustomHtmlHelper::responseFormatter(ArrayHelper::getValue($log, 'result', []));
            $created = ArrayHelper::getValue($log, 'created_at');

            $paymentDetails[] = [
                'time' => $formatter->asDatetime($created,'yyyy-MM-dd HH:mm:ss'),
                'data' => $detail,
            ];
        }

        return $paymentDetails;
    }
}