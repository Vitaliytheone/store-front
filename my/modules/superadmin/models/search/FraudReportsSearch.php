<?php

namespace my\modules\superadmin\models\search;

use yii\base\Model;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use \Yii;
use common\models\panels\PaypalFraudReports;

/**
 * Class FraudReportsSearch
 * @package my\modules\superadmin\models\search
 */
class FraudReportsSearch extends Model
{

    use SearchTrait;

    /**
     * Get query params
     * @return array
     */
    public function getParams(): array
    {
        return [
            'status' => ArrayHelper::getValue($this->params, 'status', 'all'),
        ];
    }

    /**
     * @param null|integer $status
     * @return Query
     */
    private function buildQuery($status = null): Query
    {
        $status = $status === 'all' ? null : $status;

        $query = (new Query())
            ->select('*')
            ->from(DB_PANELS . '.paypal_fraud_reports');

        if ($status !== null) {
            $query->andWhere([
                'status' => $status
            ]);
        }

        return $query;
    }

    /**
     * Get reports
     * @return array
     */
    public function search(): array
    {
        $status = ArrayHelper::getValue($this->params, 'status', 'all');

        $reports = $this->buildQuery($status);
        $reports->select([
            'project.site as panel',
            'project.child_panel as child_panel',
            'paypal_fraud_reports.*'
        ]);
        $reports->leftJoin(DB_PANELS . '.project', 'project.id = paypal_fraud_reports.panel_id');
        $reports->orderBy(['paypal_fraud_reports.id' => SORT_DESC]);

        return $this->prepareData($reports->all());
    }

    /**
     * @param $data
     * @return array
     */
    private function prepareData($data): array
    {
        $resultArray = [];

        foreach ($data as $key => $value) {
            $resultArray[$key] = $value;
            $resultArray[$key]['created_at'] = $value['created_at'] != 0 ? date('Y-m-d', $value['created_at']) : '';
            $resultArray[$key]['updated_at'] = $value['updated_at'] != 0 ? date('Y-m-d', $value['updated_at']) : '';
        }

        return $resultArray;
    }

    /**
     * @return array
     */
    public function navs(): array
    {
        return [
            'all' => Yii::t('app/superadmin', 'fraud_reports.nav.all', [
                'count' => $this->buildQuery()->count(),
            ]),
            PaypalFraudReports::STATUS_PENDING => Yii::t('app/superadmin', 'fraud_reports.nav.pending', [
                'count' => $this->buildQuery(PaypalFraudReports::STATUS_PENDING)->count(),
            ]),
            PaypalFraudReports::STATUS_ACCEPTED => Yii::t('app/superadmin', 'fraud_reports.nav.accepted', [
                'count' => $this->buildQuery(PaypalFraudReports::STATUS_ACCEPTED)->count(),
            ]),
            PaypalFraudReports::STATUS_REJECTED => Yii::t('app/superadmin', 'fraud_reports.nav.rejected', [
                'count' => $this->buildQuery(PaypalFraudReports::STATUS_REJECTED)->count(),
            ]),
        ];
    }
}
