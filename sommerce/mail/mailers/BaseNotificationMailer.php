<?php
namespace sommerce\mail\mailers;

use common\models\store\NotificationTemplates;
use Yii;
use common\models\stores\Stores;
use yii\base\InvalidParamException;
use yii\helpers\ArrayHelper;
use common\mail\mailers\BaseMailer;

/**
 * Class BaseNotificationMailer
 * @package app\mail\mailers
 */
class BaseNotificationMailer extends BaseMailer {

    /**
     * @var Stores
     */
    public $store;

    /**
     * @var NotificationTemplates
     */
    public $template;

    /**
     * Init options
     */
    public function init()
    {
        $this->store = ArrayHelper::getValue($this->options, 'store');
        $this->template = ArrayHelper::getValue($this->options, 'template');

        if (!($this->store instanceof Stores)) {
            throw new InvalidParamException();
        }

        if (!($this->template instanceof NotificationTemplates)) {
            throw new InvalidParamException();
        }
    }

    /**
     * Get global vars
     * @return array
     */
    public function getGlobalVars()
    {
        return [
            'site' => [
                'logo' => $this->store->logo,
                'language' => Yii::$app->language,
                'store_domain' => $this->store->getSite(),
                'store_name' => $this->store->name,
                'url' => $this->store->getSite(),
                'admin_email' => $this->store->getAdminEmail(),
                'admin_url' => $this->store->getSite() . '/admin',
                'cart_url' => $this->store->getSite() . '/cart',
                'domain' => $this->store->domain,
            ]
        ];
    }
}