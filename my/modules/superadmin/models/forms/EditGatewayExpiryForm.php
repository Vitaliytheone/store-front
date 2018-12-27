<?php

namespace superadmin\models\forms;


use common\models\gateways\Sites;
use common\models\panels\ExpiredLog;
use yii\base\Model;
use Yii;

/**
 * Class EditGatewayExpiryForm
 * @package superadmin\models\forms
 */
class EditGatewayExpiryForm extends Model
{
    public $expired;

    /**
     * @var Sites
     */
    private $gateway;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['expired'], 'required'],
            [['expired'], 'date', 'format' => 'php:Y-m-d H:i:s']
        ];
    }

    /**
     * Set gateway22
     * @param Sites $site
     */
    public function setGateway(Sites $site)
    {
        $this->gateway = $site;
    }

    /**
     * Save expied
     * @return bool
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $lastExpired = $this->gateway->expired_at;
        $this->gateway->expired_at = strtotime($this->expired) - Yii::$app->params['time'];

        if (!$this->gateway->save(false)) {
            $this->addError('expiry', Yii::t('app/superadmin', 'gateways.edit_expiry.error'));
            return false;
        }

        $ExpiredLogModel = new ExpiredLog();
        $ExpiredLogModel->attributes = [
            'pid' => $this->gateway->id,
            'expired_last' => $lastExpired,
            'expired' => $this->gateway->expired_at,
            'created_at' => time(),
            'type' => ExpiredLog::TYPE_CHANGE_GATEWAY_EXPIRY,
        ];
        $ExpiredLogModel->save(false);

        return true;
    }
}