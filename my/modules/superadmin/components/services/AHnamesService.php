<?php
namespace superadmin\components\services;

use Exception;
use Yii;

/**
 * Class AHnamesService - api service
 * @package superadmin\components\services
 */
class AHnamesService extends BaseService
{
    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $url;

    /**
     * AHnamesService constructor.
     * @param string $url
     * @param string $user
     * @param string $password
     * @param mixed $timeout
     */
    public function __construct($url, $user, $password, $timeout)
    {
        parent::__construct($timeout);
        $this->url = $url;
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * @return bool
     */
    public function isValidConfiguration()
    {
        if (empty($this->url) || empty($this->user) || empty($this->password)) {
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

            $getData = [
                'auth_login' => $this->user,
                'auth_password' => $this->password,
            ];

            $result = $this->call($this->url . '/clientGetBalance', $getData);

            if (!$result) {
                throw new Exception(Yii::t('app/superadmin', 'error.result_is_null'));
            }

            $result = @json_decode($result, true);

            return [
                'balance' => '$' . $result['balance']
            ];
        } catch(Exception $exception) {
            $this->error = $exception;
            return 0;
        }
    }
}