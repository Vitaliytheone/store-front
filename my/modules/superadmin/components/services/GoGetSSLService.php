<?php
namespace my\modules\superadmin\components\services;

use Exception;
use my\libs\GoGetSSLApi;

/**
 * Class GoGetSSLService
 * @package my\modules\superadmin\components\services
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
     * Get account balance of service
     * @return mixed
     */
    public function getBalance()
    {
        $this->api->auth($this->user, $this->password);
        try {
            $result = $this->api->getAccountBalance();
        } catch(Exception $exception) {
            $this->error = $exception;
            return 0;
        }

        return [
            'balance' => '$' . $result['balance'],
        ];
    }

}