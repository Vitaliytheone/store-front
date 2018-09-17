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
     * @throws \Exception
     */
    public static function validateResponse($response, $requiredKeys)
    {
        if (empty($response)) {
            throw new \Exception('Bad response');
        }

        foreach ($requiredKeys as $item) {
           if (!isset($response[$item])) {
               throw new \Exception('Bad response expected key: ' . $item);
           }
        }
        return true;
    }
}