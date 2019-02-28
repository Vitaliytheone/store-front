<?php

namespace control_panel\components\payments;

use common\models\panels\Params;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class TwoCheckout
 * @package control_panel\components\payments
 */
class TwoCheckout extends BasePayment
{
    /**
     * Последние сообщения об ошибках
     * @var array
     */
    protected $_errors = array();

    /**
     * Данные API
     * Обратите внимание на то, что для песочницы нужно использовать соответствующие данные
     * @var array
     */
    protected $_credentials = array(
        'username' => '',
        'password' => '',
    );

    /**
     * Указываем, куда будет отправляться запрос
     * @var string
     */
    protected $_endPoint = 'https://www.2checkout.com/api/';

    public function __construct()
    {
        if (!empty(Yii::$app->params['testTwoCheckout'])) {
            $this->_endPoint = 'https://sandbox.2checkout.com/api/';
        }
    }

    /**
     * @param $params array | false
     * @return string
     */
    public function detailSale($params) {

        if (!$this->_validateParams($params))  {
            $this ->_errors = array('invalid params');
            return false;
        }

        $this->_endPoint .= 'sales/detail_sale';

        $response = $this->request($this->_endPoint, http_build_query($params));
        $responseStatus = ArrayHelper::getValue($response, 'response_code');

        if ($responseStatus === 'OK') {
            return ArrayHelper::getValue($response, ['sale', 'invoices', 0, 'fees_2co']);
        }

        return null;
    }

    /**
     * @param $url string
     * @param $params array
     * @return bool|mixed
     */
    private function request($url, $params)
    {
        $this->_errors = array();
        $auth = $this->getAuth();

        if (!$auth) {
            return false;
        }

        $curlOptions = array (
            CURLOPT_URL => $url . '?' . $params,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_USERPWD => $auth,
        );

        if (!empty(PROXY_CONFIG['main']['ip'])) {
            $curlOptions += [
                CURLOPT_PROXYTYPE => CURLPROXY_HTTP,
                CURLOPT_PROXY => PROXY_CONFIG['main']['ip'] . ':' . PROXY_CONFIG['main']['port']
            ];
        }

        $ch = curl_init();
        curl_setopt_array($ch,$curlOptions);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-type: application/json',
            'Accept: application/json',
        ));

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $this->_errors = curl_error($ch);
            curl_close($ch);
            return false;
        } else  {
            curl_close($ch);
            return json_decode($response, true);
        }
    }

    /**
     * Get authentication data
     * @return string
     */
    private function getAuth()
    {
        $this->_credentials['username'] = ArrayHelper::getValue(Params::get(Params::CATEGORY_PAYMENT, Params::CODE_TWO_CHECKOUT), ['credentials', 'username']);
        $this->_credentials['password'] = ArrayHelper::getValue(Params::get(Params::CATEGORY_PAYMENT, Params::CODE_TWO_CHECKOUT), ['credentials', 'password']);

        return $this->_credentials['username'] . ':' . $this->_credentials['password'];
    }
}
