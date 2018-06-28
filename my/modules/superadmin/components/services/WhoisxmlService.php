<?php
namespace my\modules\superadmin\components\services;

use Exception;
use SimpleXMLElement;

/**
 * Class WhoisxmlService
 * @package my\modules\superadmin\components\services
 */
class WhoisxmlService extends BaseService
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
     * Whoisxmlapi constructor.
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
     * Get account balance of service
     * @return mixed
     */
    public function getBalance()
    {
        $getData = [
            'servicetype' => '',
            'username' => $this->user,
            'password' => $this->password,
        ];
        try {
            $result = $this->call($this->url . '/accountServices.php', $getData);
            $xml = new SimpleXMLElement($result);

            if ($xml->error) {
                $this->error = new Exception($xml->error);
                return 0;
            }
        } catch(Exception $exception) {
            $this->error = $exception;
            return 0;
        }

        return ['balance' => $xml->balance];
    }
}