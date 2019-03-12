<?php

namespace sommerce\helpers;


use common\models\sommerce\Checkouts;
use common\models\sommerce\Packages;
use common\models\sommerce\Payments;
use common\models\sommerces\Stores;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Cookie;

/**
 * Class PaymentsModalHelper
 * @package sommerce\helpers
 */
class PaymentsModalHelper
{
    const FAILED_MODAL = 'payment_fail';
    const SUCCESS_MODAL = 'payment_success';
    const AWAITING_MODAL = 'payment_awaiting';

    protected static function getConfig()
    {
        return [
            self::FAILED_MODAL => 'getFailedModal',
            self::SUCCESS_MODAL => 'getSuccessModal',
            self::AWAITING_MODAL => 'getAwaitingModal',
        ];
    }

    /**
     * @var Stores
     */
    protected $store;

    /**
     * @return Stores
     */
    public function getStore(): Stores
    {
        return $this->store;
    }

    /**
     * @param Stores $store
     */
    public function setStore(Stores $store)
    {
        $this->store = $store;
    }

    /**
     * @param Checkouts $checkout
     * @return bool|array
     */
    protected function getSuccessDetails(Checkouts $checkout)
    {
        $detail = ArrayHelper::getValue($checkout->getDetails(), 0);
        $order = $checkout->order;

        $paymentStatus = Payments::find()
            ->select('status')
            ->where(['checkout_id' => $checkout->id])
            ->scalar();

        if (!$paymentStatus || !$detail || !$order)  {
            return false;
        }

        $package = Packages::find()
            ->select('name')
            ->where([
                'id' => ArrayHelper::getValue($detail, 'package_id')
            ])
            ->scalar();



        return [
            'status' => ArrayHelper::getValue(Payments::getStatuses(), $paymentStatus),
            'price' => PriceHelper::getPrice($checkout->price, $this->getStore()->currency),
            'details' => ArrayHelper::getValue($detail, 'link'),
            'package' => $package,
            'order_id' => $order->id
        ];
    }

    /**
     * @param $type
     * @param Checkouts $checkout
     * @return bool
     */
    public function addModal($type, Checkouts $checkout  = null)
    {
        $action = ArrayHelper::getValue($this->getConfig(), $type);
        if (!$action) {
            return false;
        }

        $data = $checkout ? $this->$action($checkout) : $this->$action();
        $cookies = Yii::$app->response->cookies;

        $cookies->add(new Cookie([
            'name' => 'modal',
            'value' => $data

        ]));

        return true;
    }


    /**
     * @return array
     */
    protected function getFailedModal() {
        return [
            'type' => self::FAILED_MODAL,
            'data' => []
        ];
    }

    /**
     * @return array
     */
    protected function getAwaitingModal() {
        return [
            'type' => self::AWAITING_MODAL,
            'data' => []
        ];
    }

    /**
     * @param $checkout
     * @return array
     */
    protected function getSuccessModal(Checkouts $checkout) {
        return [
            'type' => self::SUCCESS_MODAL,
            'data' => $this->getSuccessDetails($checkout)
        ];
    }

}