<?php

namespace admin\models\search;

use common\models\gateways\SitePaymentMethods;
use Yii;
use yii\db\Query;
use common\models\gateways\PaymentMethods;
use yii\helpers\ArrayHelper;

/**
 * Class PaymentMethodsSearch
 * @package admin\models\searches
 */
class PaymentMethodsSearch extends BaseSearch
{
    protected $_paymentMethods;

    /**
     * @return SitePaymentMethods[]
     */
    public function getSitePaymentMethods()
    {
        return SitePaymentMethods::find()
            ->andWhere([
                'site_id' => $this->_gateway->id
            ])
            ->indexBy('method_id')
            ->all();
    }

    /**
     * @return array
     */
    public function search()
    {
        $sitePaymentMethods = $this->getSitePaymentMethods();
        $paymentMethods = $this->getPaymentMethods();

        $returnPaymentMethods = [];

        /**
         * @var PaymentMethods $paymentMethod
         */
        foreach ($paymentMethods as $methodId => $paymentMethod) {
            $returnPaymentMethods[$methodId] = [
                'method' => $paymentMethod->id,
                'title' => $paymentMethod->method_name,
                'icon' => $paymentMethod->icon,
                'icon_style' => $paymentMethod->iconStyle,
                'active' => (int)ArrayHelper::getValue($sitePaymentMethods, [$methodId, 'visibility']),
            ];
        }

        return $returnPaymentMethods;
    }

    /**
     * @return PaymentMethods[]
     */
    public function getPaymentMethods()
    {
        if (null === $this->_paymentMethods) {
            $this->_paymentMethods = PaymentMethods::find()->indexBy('id')->all();
        }

        return $this->_paymentMethods;
    }
}