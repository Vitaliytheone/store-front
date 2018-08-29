<?php
namespace my\components\payments;

/**
 * Class BasePayment
 * @package my\components\payments
 */
class BasePayment
{
    /**
     * @param $params
     * @return bool
     */
    protected function _validateParams($params)
    {
        foreach ($params as $key => $param) {
            if (is_null($param)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param array $response
     * @param array $requiredKeys
     * @return bool
     */
    protected function _validateResponse($response, $requiredKeys)
    {
        if (empty($response)) {
            return false;
        }

        foreach ($requiredKeys as $item) {
           if (!isset($response[$item])) {
               return false;
           }
        }
        return true;
    }

    /**
     * @param array $response
     * @param array $requiredKeys
     * @return array|bool
     */
    public function get($response, $requiredKeys) {
        if (!$this->_validateResponse($response, $requiredKeys)) {
            return false;
        }
        return $response;
    }
}