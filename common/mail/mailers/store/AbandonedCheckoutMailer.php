<?php
namespace common\mail\mailers\store;

use common\models\store\Checkouts;
use common\models\store\Packages;
use common\models\store\Payments;
use common\models\stores\PaymentGateways;
use Yii;
use yii\base\InvalidParamException;
use yii\helpers\ArrayHelper;

/**
 * Class AbandonedCheckoutMailer
 * @package common\mail\mailers\store
 */
class AbandonedCheckoutMailer extends BaseNotificationMailer {

    /**
     * @var Checkouts
     *
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

        $items = $this->_checkout->getDetails();
        $options = $this->getGlobalVars();

        $packages = ArrayHelper::index(Packages::find()->andWhere([
            'id' => ArrayHelper::getColumn($items, 'package_id')
        ])->all(), 'id');

        $data = [];
        $total = 0;

        foreach ($items as $item) {
            /**
             * @var Packages $package
             */
            $package = $packages[$item['package_id']];

            $price = ArrayHelper::getValue($package, 'price');
            $data[] = [
                'title' => htmlspecialchars((string)ArrayHelper::getValue($item, 'link')),
                'quantity' => ArrayHelper::getValue($item, 'quantity'),
                'price' => $price,
            ];

            $total += $price;
        }

        $options['order'] = [
            'id' => $this->_checkout->id,
            'data' => $data,
            'sub_total' => $total,
            'total' => $total,
        ];

        $this->html = $this->renderTwig((string)$this->template->body, $options);

        $this->subject = $this->renderTwig((string)$this->template->subject, $options);
    }
}