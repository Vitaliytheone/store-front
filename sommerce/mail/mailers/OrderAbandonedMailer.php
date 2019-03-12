<?php
namespace sommerce\mail\mailers;

use common\models\sommerce\Packages;
use common\models\sommerce\Suborders;
use common\models\sommerce\Checkouts;
use yii\base\InvalidParamException;
use yii\helpers\ArrayHelper;

/**
 * Class OrderAbandonedMailer
 * @package sommerce\mail\mailers
 */
class OrderAbandonedMailer extends BaseNotificationMailer {

    /**
     * @var Checkouts
     */
    protected $_checkout;

    /**
     * Init options
     */
    public function init()
    {
        parent::init();

        $this->_checkout = ArrayHelper::getValue($this->options, 'checkout');

        if (empty($this->_checkout)) {
            throw new InvalidParamException();
        }

        $options = $this->getGlobalVars();

        $checkoutDetails = $this->_checkout->getDetails();

        $packages = Packages::find()
            ->select(['id', 'price', 'name'])
            ->andWhere(['id' => array_column($checkoutDetails, 'package_id')])
            ->asArray()
            ->indexBy('id')
            ->all();

        array_walk($checkoutDetails, function(&$checkoutItem) use ($packages) {
            $checkoutItem['price'] = ArrayHelper::getValue($packages, [$checkoutItem['package_id'], 'price']);
            $checkoutItem['name'] = ArrayHelper::getValue($packages, [$checkoutItem['package_id'], 'name']);
        });

        $data = [];
        $total = 0;

        /**
         * @var $suborder Suborders
         */
        foreach ($checkoutDetails as $checkoutItem) {
            $data[] = [
                'title' => htmlspecialchars($checkoutItem['link']),
                'quantity' => $checkoutItem['quantity'],
                'price' => $checkoutItem['price'],
            ];

            $total += $checkoutItem['price'];
        }

        $options['order'] = [
            'id' => $this->_checkout->id,
            'data' => $data,
            'sub_total' => $total,
            'total' => $total,
            'url' => null,
            'payment_method' => null
        ];

        $this->html = $this->renderTwig((string)$this->template->body, $options);

        $this->subject = $this->renderTwig((string)$this->template->subject, $options);
    }
}