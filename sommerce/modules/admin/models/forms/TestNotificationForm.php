<?php
namespace sommerce\modules\admin\models\forms;

use common\mail\mailers\store\AbandonedCheckoutMailer;
use common\mail\mailers\store\OrderAdminMailer;
use common\models\store\Checkouts;
use common\models\store\Packages;
use common\models\store\Payments;
use common\models\store\NotificationTemplates;
use common\models\store\Orders;
use common\models\store\Suborders;
use common\models\stores\Stores;
use common\mail\mailers\store\OrderMailer;
use Yii;
use yii\base\Model;
use Faker\Factory;
use yii\helpers\ArrayHelper;

/**
 * Class TestNotificationForm
 * @package app\modules\superadmin\models\forms
 */
class TestNotificationForm extends Model {

    /**
     * @var NotificationTemplates
     */
    private $_notification;

    /**
     * @var Stores
     */
    private $_store;

    /**
     * @var string
     */
    private $_email;

    /**
     * Set notification
     * @param NotificationTemplates $notification
     */
    public function setNotification($notification)
    {
        $this->_notification = $notification;
    }

    /**
     * Set store
     * @param Stores $store
     */
    public function setStore($store)
    {
        $this->_store = $store;
    }

    /**
     * Set email
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->_email = $email;
    }

    /**
     * Get notification message with test data
     * @return bool
     */
    public function message()
    {
        if (empty($this->_notification->body)) {
            return '';
        }

        $mailer = $this->getMailer();

        if (empty($mailer)) {
            return '';
        }

        return ArrayHelper::getValue($mailer->getData(), 'html', 'No preview');
    }

    /**
     * Send notification message with test data
     * @return bool
     */
    public function send()
    {
        $mailer = $this->getMailer();

        if (empty($mailer)) {
            return false;
        }

        return $mailer->send();
    }

    /**
     * Get notification mailer
     * @return null|OrderMailer
     */
    public function getMailer()
    {
        $mailer = null;

        $faker = Factory::create();

        switch ($this->_notification->notification_code) {
            case 'order_confirmation':
            case 'order_in_progress':
            case 'order_completed':

                $order = new Orders([
                    'id' => $faker->numberBetween()
                ]);
                $suborders = [];

                for ($i = 0; $i < rand(1, 10); $i++) {
                    $suborders[] = new Suborders([
                        'id' => $faker->numberBetween(),
                        'link' => $faker->url,
                        'amount' => $faker->numberBetween(1, 100),
                        'quantity' => $faker->numberBetween(1000, 10000),
                    ]);
                }

                $payment = new Payments([
                    'id' => $faker->numberBetween(),
                    'method' => 'paypal'
                ]);

                $mailer = new OrderMailer([
                    'order' => $order,
                    'suborders' => $suborders,
                    'payment' => $payment,
                    'template' => $this->_notification,
                    'store' => $this->_store,
                    'to' => $this->_email ? $this->_email : $faker->email
                ]);
            break;

            case 'new_auto_order':
            case 'new_manual_order':
            case 'order_fail':
            case 'order_error':

                $order = new Orders([
                    'id' => $faker->numberBetween()
                ]);

                $mailer = new OrderAdminMailer([
                    'order' => $order,
                    'template' => $this->_notification,
                    'store' => $this->_store,
                    'to' => $this->_email ? $this->_email : $faker->email
                ]);
            break;

            case 'abandoned_checkout':
                $checkout = new Checkouts([
                    'id' => $faker->numberBetween(),
                ]);
                $suborders = [];

                $packages = ArrayHelper::index(Packages::find()->all(), 'id');

                for ($i = 0; $i < rand(1, 10); $i++) {
                    $suborders[] = [
                        'id' => $faker->numberBetween(),
                        'link' => $faker->url,
                        'package_id' => array_rand($packages),
                    ];
                }

                $checkout->setDetails($suborders);

                $payment = new Payments([
                    'id' => $faker->numberBetween(),
                    'method' => 'paypal'
                ]);

                $mailer = new AbandonedCheckoutMailer([
                    'checkout' => $checkout,
                    'payment' => $payment,
                    'template' => $this->_notification,
                    'store' => $this->_store,
                    'to' => $this->_email ? $this->_email : $faker->email
                ]);

            break;
        }

        return $mailer;
    }
}