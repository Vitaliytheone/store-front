<?php

namespace superadmin\models\search;

use Yii;
use common\models\panels\ExchangeRates;
use yii\helpers\ArrayHelper;

/**
 * Class CurrencyRatesSearch
 * @package admin\models\search
 */
class CurrencyRatesSearch extends BaseSearch
{
    static $ratesMapping = [];

    /**
     * @return array
     */
    public function search()
    {
        $excludedCurrencies = [
            'IRR',
            'IQD',
            'VND',
        ];

        $newestRateTime = ExchangeRates::find()->max('created_at');

        $rates = ExchangeRates::find()
            ->select(['id', 'currency', 'source_currency', 'currency', 'rate'])
            ->andWhere(['created_at' => $newestRateTime])
            ->andWhere(['NOT IN', 'currency', $excludedCurrencies])
            ->groupBy('currency')
            ->asArray()
            ->indexBy('currency')
            ->all();

        foreach ($rates as $currencyCode => $rate) {
            static::$ratesMapping[$rate['source_currency']] = [
                'id' => $rate['id'],
                'rate' => 1,
            ];
            static::$ratesMapping[$rate['currency']] = [
                'id' => $rate['id'],
                'rate' => $rate['rate'],
            ];
        }

        $ratesForView = [];

        foreach ($rates as $currencyCode => $rateData) {

            $incomingCurrencyRate = ArrayHelper::getValue(static::$ratesMapping, [ExchangeRates::SOURCE_CURRENCY_USD, 'rate']);
            $resultCurrencyRate = ArrayHelper::getValue(static::$ratesMapping, [$currencyCode, 'rate']);

            if (
                empty($incomingCurrencyRate) ||
                empty($resultCurrencyRate)
            ) {
                continue;
            }

            $convertedAmount = (1 / $incomingCurrencyRate * $resultCurrencyRate);

            $ratesForView[$currencyCode] = [
                'currency' => $currencyCode,
                'exchange_rate' => round($convertedAmount, 5),
            ];

        }

        return $ratesForView;
    }
}