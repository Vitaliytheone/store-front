<?php

namespace my\modules\superadmin\models\search;

use common\components\traits\UnixTimeFormatTrait;
use common\models\panels\PaymentGateway;
use common\models\panels\Payments;
use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class ReportsPaymentsSearch
 * @package my\modules\superadmin\models\search
 */
class ReportsPaymentsSearch
{
    use UnixTimeFormatTrait;

    const FILTER_YEAR = 'year';
    const FILTER_PAYMENT_GATEWAY = 'gateway';

    private $_defaultFilters = [];
    private $_currentFilters = [];

    /** @var int Time offset value according settings-params, ms */
    private $_timeOffset;

    /** @var array current searched models set */
    private $_models = [];

    private $_paymentsTable;
    private $_gatewaysTable;

    public function __construct()
    {
        $this->_timeOffset = ArrayHelper::getValue(Yii::$app->params, 'time', 0);

        $this->_paymentsTable= Payments::tableName();
        $this->_gatewaysTable = PaymentGateway::tableName();

        // Set default filters values
        $this->_defaultFilters = [
            self::FILTER_YEAR  => date("Y"),
            self::FILTER_PAYMENT_GATEWAY => null,
        ];
    }

    /**
     * @param $filters
     * @return array
     */
    public function setFilters($filters)
    {
        if (!is_array($filters)) {
            $this->_currentFilters = $this->_defaultFilters;
            return $this->_currentFilters;
        }

        // Check Year filters
        $yearFilter = ArrayHelper::getValue($filters, self::FILTER_YEAR);

        if (!$yearFilter || !ctype_digit($yearFilter)) {
            $yearFilter = $this->_defaultFilters[self::FILTER_YEAR];
        }

        // Check Gateway filter
        $gatewayFilter = (int)ArrayHelper::getValue($filters, self::FILTER_PAYMENT_GATEWAY, null);
        $allowedGatewaysIds = array_keys(PaymentGateway::getMethods());

        if (!in_array($gatewayFilter, $allowedGatewaysIds)) {
            $gatewayFilter = $this->_defaultFilters[self::FILTER_PAYMENT_GATEWAY];
        }

        $this->_currentFilters = array_merge($this->_defaultFilters, $filters, [
            self::FILTER_YEAR => $yearFilter,
            self::FILTER_PAYMENT_GATEWAY => $gatewayFilter,
        ]);

        return  $this->_currentFilters;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return $this->_currentFilters;
    }

    /**
     * Populate base query by filters
     * @param $query \yii\db\Query()
     */
    private function _applyFilters(&$query)
    {
        $filters = $this->_currentFilters;

        $yearFilter = ArrayHelper::getValue($filters, self::FILTER_YEAR, null);
        $gatewayFilter = ArrayHelper::getValue($filters, self::FILTER_PAYMENT_GATEWAY, null);

        $startYearTS = mktime(0, 0, 0, 01, 01, $yearFilter) - $this->_timeOffset;
        $endYearTS = mktime(23, 59, 59, 12, 31, $yearFilter) - $this->_timeOffset;

        $query->andWhere('`date` BETWEEN :startTS AND :endTS', [':startTS' => $startYearTS, ':endTS' => $endYearTS]);

        if ($gatewayFilter) {
            $type = ($gatewayFilter === -1 ? 0 : $gatewayFilter);
            $query->andWhere(['type' => $type ]);
        }
    }

    /**
     * Search payments according filters
     * @param array $filters
     * @return array
     */
    public function search($filters = [])
    {
        $this->setFilters($filters);

        $query = (new Query())
            ->select([
                'date' => 'DATE(FROM_UNIXTIME(`date`+('.$this->_timeOffset.')))',
                'amount' => 'sum(amount)',
                'count' => 'count(*)',
            ])
            ->from($this->_paymentsTable)
            ->andWhere(['status' => Payments::STATUS_COMPLETED])
            ->groupBy('DATE(FROM_UNIXTIME(`date`+('.$this->_timeOffset.')))')
            ->indexBy('date');

        $this->_applyFilters($query);

        $this->_models = $query->all();

        return $this->_models;
    }

    /**
     * Return months-days year report table
     * @return array
     */
    public function getYearReportTable()
    {
        $days = $this->_models;

        $reportTable = [];
        $reportViewTable = [];

        // Fill months-days report table
        foreach ($days as $day) {
            $tsDate = strtotime($day['date']);
            $dayNo = (int)date('d', $tsDate);
            $monthNo = (int)date('m', $tsDate);

            $reportTable[$monthNo][$dayNo] = [
                'amount' => $day['amount'],
                'count' => $day['count']
            ];
        }

        // Fill completed year table for view
        for ($month = 1; $month <= 12; $month++) {
            for ($day = 1; $day <= 31; $day++) {

                $reportViewTable[$month][$day] = [
                    'amount' => ArrayHelper::getValue($reportTable, "$month.$day.amount", 0),
                    'count' => ArrayHelper::getValue($reportTable, "$month.$day.count", 0)
                ];
            }
            $reportViewTable[$month]['month_total'] = [
                'amount' => array_sum(array_column($reportViewTable[$month], 'amount')),
                'count' =>  array_sum(array_column($reportViewTable[$month], 'count')),
            ];
        }

        return $reportViewTable;
    }

    /**
     * Return payment systems gateways data
     * @return array
     */
    public function getPaymentGateways()
    {
        $query = (new Query)
            ->select(['pgid', 'name', 'position'])
            ->from($this->_gatewaysTable)
            ->where(['pid' => '-1'])
            ->orderBy(['position' => SORT_DESC])
            ->all();

        return $query;
    }

    /**
     * Return payment gateways for view
     * @return array
     */
    public function getPaymentGatewaysForView()
    {
        $gateways = $this->getPaymentGateways();
        $currentGateway = ArrayHelper::getValue($this->_currentFilters, self::FILTER_PAYMENT_GATEWAY, null);

        foreach ($gateways as &$gateway) {
            $gateway['active'] = (int)$currentGateway === (int)ArrayHelper::getValue($gateway, 'pgid');
        }

        // Past 'All' item
        array_unshift($gateways, [
            'name' => 'All',
            'pgid' => null,
            'active' => !isset($this->_currentFilters[self::FILTER_PAYMENT_GATEWAY]),
        ]);

        array_push($gateways, [
            'name' => PaymentGateway::getMethodNameById(-1),
            'pgid' => -1,
            'active' => (int)$currentGateway === (-1)
        ]);

        return $gateways;
    }

    /**
     * Return reports years list
     * @return array
     */
    public function getYears()
    {
        $years = (new Query())
            ->select([
                'year' => 'YEAR(FROM_UNIXTIME(`date`))',
            ])
            ->from($this->_paymentsTable)
            ->groupBy('YEAR(FROM_UNIXTIME(`date`))')
            ->orderBy(['year' => SORT_ASC])
            ->all();

        return $years;
    }

    /**
     * Return years arrays for view
     * @return array
     */
    public function getYearsForView()
    {
        $years = $this->getYears();
        $currentYear = ArrayHelper::getValue($this->_currentFilters, self::FILTER_YEAR, null);

        foreach ($years as &$year) {
            $year['active'] = (int)$currentYear === (int)$year['year'];
        }

        return $years;
    }
}