<?php
namespace my\modules\superadmin\components\services;

use Yii;
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
        $getData = [
            'servicetype' => '',
            'username' => $this->user,
            'password' => $this->password,
        ];
        try {
            if (!$this->isValidConfiguration()) {
                throw new Exception(Yii::t('app/superadmin', 'error.incorrect_service_settings'));
            }

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