<?php

namespace superadmin\components\services;

use common\helpers\Request;
use Exception;
use Yii;

/**
 * Class NamesiloService - api service
 * @package superadmin\components\services
 */
class NamesiloService extends BaseService
{
    /** @var string */
    private $url;

    /** @var string */
    private $key;

    /** @var string */
    private $version;

    /** @var string */
    private $type;

    /**
     * NamesiloService constructor
     * @param string $url
     * @param string $key
     * @param string $version
     * @param string $type
     * @param int $timeout
     */
    public function __construct($url, $key, $version, $type, $timeout = 1)
    {
        parent::__construct($timeout);
        $this->url = $url;
        $this->key = $key;
        $this->version = $version;
        $this->type = $type;
    }

    /**
     * @return bool
     */
    public function isValidConfiguration()
    {
        if (empty($this->url) || empty($this->key) || empty($this->version) || empty($this->type)) {
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

            $options = [
                'version' => $this->version,
                'type' => $this->type,
                'key' => $this->key,
            ];

            $result = Request::getContents($this->url . '/getAccountBalance?' . http_build_query($options));

            if (!$result) {
                throw new Exception(Yii::t('app/superadmin', 'error.result_is_null'));
            }

            $result = @json_decode(json_encode(simplexml_load_string($result)),true);

            return [
                'balance' => '$' . $result['reply']['balance']
            ];
        } catch(Exception $exception) {
            $this->error = $exception;
            return 0;
        }
    }
}