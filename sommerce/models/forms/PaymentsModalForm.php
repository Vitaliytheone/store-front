<?php

namespace sommerce\models\forms;

use common\models\sommerce\Checkouts;
use common\models\sommerce\Packages;
use common\models\sommerce\Payments;
use common\models\sommerces\Stores;
use sommerce\helpers\PriceHelper;
use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

/**
 * Class PaymentsModalForm
 * @package sommerce\models\forms
 */
class PaymentsModalForm
{
    const FAILED_MODAL = 'payment_fail';
    const SUCCESS_MODAL = 'payment_success';
    const AWAITING_MODAL = 'payment_awaiting';

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
     * Return modal data
     * @param Payments $payment
     * @return array
     */
    public function getModalData(Payments $payment)
    {
        $data = [
            'url' => '/' . explode('/', Yii::$app->getRequest()->getUrl())[1],
        ];

        switch ($payment->status) {
            case Payments::STATUS_COMPLETED:
                $data += static::getSuccessModal($payment);
                break;
            case Payments::STATUS_AWAITING:
                $data += static::getAwaitingModal();
                break;
            case Payments::STATUS_FAILED:
                $data += static::getFailedModal();
                break;
        }

        return $data;
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
     * @param Payments $payment
     * @return array
     * @throws
     */
    protected function getSuccessModal(Payments $payment)
    {
        $checkout = Checkouts::findOne(['id' => $payment->checkout_id]);

        if (!$checkout) {
            throw new Exception('Checkout not found!');
        }

        $details = $checkout->getDetails();

        $package = Packages::find()
            ->where(['id' => $details['package_id']])
            ->one();

        if (!$package) {
            throw new Exception('Package not found!');
        }

        return [
            'type' => self::SUCCESS_MODAL,
            'data' => [
                'status' => ArrayHelper::getValue(Payments::getStatuses(), $payment->status),
                'price' => PriceHelper::getPrice($checkout->price, $this->getStore()->currency),
                'details' => ArrayHelper::getValue($details, 'link'),
                'package' => $package->name,
                'order_id' => $payment->order_id,
            ],
        ];
    }
}