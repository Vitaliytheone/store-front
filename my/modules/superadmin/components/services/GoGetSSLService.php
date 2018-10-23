<?php
namespace superadmin\components\services;

use Yii;
use Exception;
use my\libs\GoGetSSLApi;

/**
 * Class GoGetSSLService
 * @package superadmin\components\services
 */
class GoGetSSLService extends BaseService
{
    /**
     * @var GoGetSSLApi
     */
    private $api;

    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $password;

    /**
     * GoGetSSLService constructor.
     * @param string $user
     * @param string $password
     * @param mixed $timeout
     * @param bool $sandbox
     * @param null $key
     */
    public function __construct($user, $password, $timeout, $sandbox = false, $key = null)
    {
        parent::__construct($timeout);
        $this->api = new GoGetSSLApi($sandbox, $key, $timeout);
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * @return bool
     */
    public function isValidConfiguration()
    {
        if (empty($this->user) || empty($this->password)) {
            return false;
        }

        return true;
    }

    /**
     * Get account balance of service
     * @return mixed
     */
    public function getBalance()
    {

        try {
            if (!$this->isValidConfiguration()) {
                return [
                    'balance' => '',
                ];
            }

            $this->api->auth($this->user, $this->password);

            $result = $this->api->getAccountBalance();

            if (empty($result['balance'])) {
                throw new Exception(Yii::t('app/superadmin', 'error.result_is_null'));
            }

        } catch(Exception $exception) {
            $this->error = $exception;
            return 0;
        }

        return [
            'balance' => '$' . $result['balance'],
        ];
    }

}