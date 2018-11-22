<?php

namespace superadmin\models\forms;

use common\models\panels\Orders;
use common\models\panels\Project;
use common\models\panels\SslCert;
use my\helpers\order\OrderSslHelper;
use Yii;
use yii\base\Exception;
use yii\base\Model;

/**
 * Class DisableSslForm
 * @package superadmin\models\forms
 */
class DisableSslForm extends Model
{
    /**
     * @var SslCert
     */
    private $_ssl;

    /**
     * Set SSL
     * @param SslCert $ssl
     */
    public function setSsl(SslCert $ssl)
    {
        $this->_ssl = $ssl;
    }

    /**
     * Return Ssl
     * @return SslCert
     */
    public function getSsl()
    {
        return $this->_ssl;
    }

    /**
     * Disable SSL cert
     * @return bool
     * @throws
     */
    public function disabled()
    {
        $panel = Project::findOne($this->_ssl->pid);

        if (!$panel) {
            throw new Exception('Project [' . $this->_ssl->pid . '] not found!');
        }

        if (!OrderSslHelper::addDdos($this->_ssl, [
            'site' => $this->_ssl->domain,
            'crt' => null,
            'key' => null,
            'isSSL' => false,
        ])) {
            throw new Exception('Cannot reconfigure SSL at DDoS-guard!');
        }

        $transaction = Yii::$app->db->beginTransaction();

        $panel->ssl = Project::SSL_MODE_OFF;
        $panel->dns_status = Project::DNS_STATUS_NOT_DEFINED;

        if (!$panel->save(false)) {
            throw new Exception('Cannot update project!');
        }

        $this->_ssl->status = SslCert::STATUS_CANCELED;

        if (!$this->_ssl->save(false)) {
            throw new Exception('Cannot update SSL!');
        }

        $order = Orders::findOne([
            'domain' => $this->_ssl->domain,
            'item' => [
                Orders::ITEM_FREE_SSL,
                Orders::ITEM_PROLONGATION_FREE_SSL,
                Orders::ITEM_BUY_SSL,
                Orders::ITEM_PROLONGATION_SSL,
            ],
        ]);

        if ($order) {

            $order->status = Orders::STATUS_CANCELED;

            if (!$order->save(false)) {
                throw new Exception('Cannot update order!');
            }
        }

        $transaction->commit();

        return true;
    }

}
