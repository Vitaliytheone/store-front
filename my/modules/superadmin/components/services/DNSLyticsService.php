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
    private $url;

    /**
     * DNSLyticsService constructor.
     * @param string $apiKey
     * @param string $url
     * @param mixed $timeout
     */
    public function __construct($apiKey, $url, $timeout)
    {
        parent::__construct($timeout);
        $this->apiKey = $apiKey;
        $this->url = $url;
    }

    /**
     * @return bool
     */
    public function isValidConfiguration()
    {
        if (empty($this->apiKey) || empty($this->url)) {
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
                'apikey' => $this->apiKey,
            ];

            $result = @json_decode($this->call($this->url . '/accountinfo', $getData),true);

            if (!$result && is_array($result)) {
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