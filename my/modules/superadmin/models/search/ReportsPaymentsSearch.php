<?php

namespace superadmin\models\search;

use common\components\traits\UnixTimeFormatTrait;
use common\models\panels\Params;
use common\models\panels\Payments;
use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class ReportsPaymentsSearch
 * @package superadmin\models\search
 */
class ReportsPaymentsSearch
{
    use UnixTimeFormatTrait;

    const FILTER_YEAR = 'year';
    const FILTER_PAYMENT_PARAMS = 'params';

    private $_defaultFilters = [];
    private $_currentFilters = [];

    /** @var int Time offset value according settings-params, ms */
    private $_timeOffset;

    /** @var array current searched models set */
    private $_models = [];

    private $_paymentsTable;
    private $_paramsTable;

    public function __construct()
    {
        $this->_timeOffset = ArrayHelper::getValue(Yii::$app->params, 'time', 0);

        $this->_paymentsTable= Payments::tableName();
        $this->_paramsTable = Params::tableName();

        // Set default filters values
        $this->_defaultFilters = [
            self::FILTER_YEAR  => date("Y"),
            self::FILTER_PAYMENT_PARAMS => null,
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

        // Check Params filter
        $paramsFilter = ArrayHelper::getValue($filters, self::FILTER_PAYMENT_PARAMS, null);
        $allowedParamsCodes = array_keys(Params::getPayments());

        if (!in_array($paramsFilter, $allowedParamsCodes)) {
            $paramsFilter = $this->_defaultFilters[self::FILTER_PAYMENT_PARAMS];
        }

        $this->_currentFilters = array_merge($this->_defaultFilters, $filters, [
            self::FILTER_YEAR => $yearFilter,
            self::FILTER_PAYMENT_PARAMS => $paramsFilter,
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
        $paramsFilter = ArrayHelper::getValue($filters, self::FILTER_PAYMENT_PARAMS, null);

        $startYearTS = mktime(0, 0, 0, 01, 01, $yearFilter) - $this->_timeOffset;
        $endYearTS = mktime(23, 59, 59, 12, 31, $yearFilter) - $this->_timeOffset;

        $query->andWhere('`date` BETWEEN :startTS AND :endTS', [':startTS' => $startYearTS, ':endTS' => $endYearTS]);

        if ($paramsFilter) {
            $method = $paramsFilter == Params::CODE_OTHER ? null : $paramsFilter;
            $query->andWhere(['payment_method' => $method]);
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
     * Return payment systems params data
     * @return array
     */
    public function getPaymentParams()
    {
        $query = (new Query)
            ->select(['code', 'position'])
            ->from($this->_paramsTable)
            ->where(['category' => Params::CATEGORY_PAYMENT])
            ->orderBy(['position' => SORT_DESC])
            ->all();

        return $query;
    }

    /**
     * Return payment params for view
     * @return array
     */
    public function getPaymentParamsForView()
    {
        $params = $this->getPaymentParams();
        $currentParams = ArrayHelper::getValue($this->_currentFilters, self::FILTER_PAYMENT_PARAMS, null);

        foreach ($params as &$param) {
            $param['active'] = $currentParams === ArrayHelper::getValue($param, 'code');
            $param['name'] = Params::getPaymentName($param['code']);
        }

        // Past 'All' item
        array_unshift($params, [
            'code' => null,
            'name' => 'All',
            'active' => !isset($this->_currentFilters[self::FILTER_PAYMENT_PARAMS]),
        ]);

        array_push($params, [
            'code' => Params::CODE_OTHER,
            'name' => Params::getPaymentName(Params::CODE_OTHER),
            'active' => $currentParams === Params::CODE_OTHER,
        ]);

        return $params;
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