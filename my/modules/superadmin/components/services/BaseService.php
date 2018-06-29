<?php
namespace my\modules\superadmin\components\services;

use Exception;

/**
 * Class BaseService
 * @package my\modules\superadmin\components\services
 */
abstract class BaseService  {

    protected $error = null;
    protected $connectionTimeout;

    public function __construct($connectionTimeout)
    {
        $this->connectionTimeout = $connectionTimeout;
    }

    /**
     * Get account balance of service
     * @return mixed
     */
    abstract public function getBalance();

    /**
     * Check service configuration
     * @return boolean
     */
    public function isValidConfiguration() {
        return true;
    }

    /**
     * @return bool
     */
    public function hasError()
    {
        return $this->error ? true: false;
    }

    /**
     * @return null|Exception
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param $url
     * @param array $getData
     * @param array $postData
     * @return mixed
     * @internal param bool $forcePost
     * @internal param bool $isFile
     */
    protected function call($url, $getData = array(), $postData = array())
    {
        if (!empty($getData)) {
            foreach ($getData as $key => $value) {
                $url .= (strpos($url, '?') !== false ? '&' : '?')
                    . urlencode($key) . '=' . rawurlencode($value);
            }
        }

        $post = (!empty($postData));
        $c = curl_init($url);
        if ($post) {
            curl_setopt($c, CURLOPT_POST, true);
        }

        //connection timeout
        curl_setopt($c, CURLOPT_CONNECTTIMEOUT, $this->connectionTimeout);

        if (!empty($postData)) {
            $queryData = http_build_query($postData);
            curl_setopt($c, CURLOPT_POSTFIELDS, $queryData);
        }
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($c, CURLOPT_SSL_VERIFYHOST, false);
        $result = curl_exec($c);
        curl_close($c);
        return $result;
    }
}