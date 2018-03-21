<?php 
  
namespace my\components;

use Yii;

class Paypal{
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
      'USER' => '',
      'PWD' => '',
      'SIGNATURE' => '',
   );

   /**
    * Указываем, куда будет отправляться запрос
    * Реальные условия - https://api-3t.paypal.com/nvp
    * Песочница - https://api-3t.sandbox.paypal.com/nvp
    * @var string
    */
   protected $_endPoint = 'https://api-3t.paypal.com/nvp';

    /**
     * Указываем, куда будет перенаправлять запрос для опталы
     * Реальные условия - https://www.paypal.com/webscr
     * Песочница - https://www.sandbox.paypal.com/webscr
     * @var string
     */
    protected $_paymentPoint = 'https://www.paypal.com/webscr';

   /**
    * Версия API
    * @var string
    */
   protected $_version = '95.0';

   public function __construct()
   {
       if (!empty(Yii::$app->params['testPayPal'])) {
           $this->_endPoint = 'https://api-3t.sandbox.paypal.com/nvp';
           $this->_paymentPoint = 'https://www.sandbox.paypal.com/webscr';
       }
   }

    /**
    * Сформировываем запрос
    *
    * @param string $method Данные о вызываемом методе перевода
    * @param array $params Дополнительные параметры
    * @return array / boolean Response array / boolean false on failure
    */
   public function request($method,$params = array()) {
      $paypalInfo = \common\models\panels\PaymentGateway::findOne(['pgid' => 1, 'visibility' => 1, 'pid' => -1]);

      $username = '';
      $password = '';
      $signature = '';

      $paypalInfo = json_decode($paypalInfo->options);

      if (!empty($paypalInfo->username)) {
        $username = $paypalInfo->username;
      }

      if (!empty($paypalInfo->password)) {
        $password = $paypalInfo->password;
      }

      if (!empty($paypalInfo->signature)) {
        $signature = $paypalInfo->signature;
      }

      $this->_credentials['USER'] = $username;
      $this->_credentials['PWD'] = $password;
      $this->_credentials['SIGNATURE'] = $signature;
      $this ->_errors = array();

      if( empty($method) ) { // Проверяем, указан ли способ платежа
         $this ->_errors = array('Не указан метод перевода средств');
         return false;
      }

      // Параметры нашего запроса
      $requestParams = array(
         'METHOD' => $method,
         'VERSION' => $this ->_version
      ) + $this ->_credentials;

      // Сформировываем данные для NVP
      $request = http_build_query($requestParams + $params);

      // Настраиваем cURL
      $curlOptions = array (
            CURLOPT_URL => $this ->_endPoint,
            //CURLOPT_VERBOSE => 1,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_CAINFO => Yii::getAlias('@common') . '/config/certificates/pp.pem', // Файл сертификата
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $request,
      );

      $ch = curl_init();
      curl_setopt_array($ch,$curlOptions);

      // Отправляем наш запрос, $response будет содержать ответ от API
      $response = curl_exec($ch);

        // Проверяем, нету ли ошибок в инициализации cURL
        if (curl_errno($ch)) {
            $this ->_errors = curl_error($ch);
            curl_close($ch);
            return false;
        } else  {
            curl_close($ch);
            $responseArray = array();
            parse_str($response,$responseArray); // Разбиваем данные, полученные от NVP в массив
            return $responseArray;
        }
   }

    /**
     * Checkout payment
     * @param $token
     */
   public function checkout($token)
   {
       header('Location: ' . $this->_paymentPoint . '?cmd=_express-checkout&token=' . urlencode($token));
       exit;
   }
}
?>