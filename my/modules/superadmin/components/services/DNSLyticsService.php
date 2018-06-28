<?php
namespace my\modules\superadmin\components\services;

use Exception;
use Yii;

/**
 * Class DNSLyticsService
 * @package my\modules\superadmin\components\services
 */
class DNSLyticsService extends BaseService
{
    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $balanceUrl;

    /**
     * DNSLyticsService constructor.
     * @param string $apiKey
     * @param string $balanceUrl
     * @param mixed $timeout
     */
    public function __construct($apiKey, $balanceUrl, $timeout)
    {
        parent::__construct($timeout);
        $this->apiKey = $apiKey;
        $this->balanceUrl = $balanceUrl;
    }

    /**
     * Get account balance of service
     * @return mixed
     */
    public function getBalance()
    {
        $getData = [
            'apikey' => $this->apiKey,
        ];
        try {
            $result = json_decode($this->call($this->balanceUrl, $getData),true);

            if (!$result) {
                throw new Exception(Yii::t('app/superadmin', 'error.result_is_null'));
            }

            if ($result['status'] == 'error') {
                throw new Exception($result['data']);
            } else {
                return [
                    'balance' => $result['data']['apicredits'],
                ];
            }
        } catch(Exception $exception) {
            $this->error = $exception;

            return 0;
        }
    }
}