<?php
namespace my\modules\superadmin\components\services;

use opensrs\Request;
use Exception;
use Yii;

require_once(Yii::getAlias('@my/config/services/open_srs.php'));

/**
 * Class OpenSRSService
 * @package my\modules\superadmin\components\services
 */
class OpenSRSService extends BaseService
{
    /**
     * @var string
     */
    private $registrantIp;

    /**
     * OpenSRSService constructor.
     * @param string $registrantIp
     */
    public function __construct($registrantIp)
    {
        parent::__construct(null);
        $this->registrantIp = $registrantIp;
    }

    /**
     * @return bool
     */
    public function isValidConfiguration()
    {
        if (empty($this->registrantIp)) {
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
        $data = [
            "func" => "lookupGetBalance",
            "data" => [
                "registrant_ip " => $this->registrantIp,
            ]
        ];

        try {

            if (!$this->isValidConfiguration()) {
                throw new Exception(Yii::t('app/superadmin', 'error.incorrect_service_settings'));
            }

            $request = new Request();
            $response = $request->process('array', $data);

            if (empty($response->resultRaw['balance'])) {
                throw new Exception(Yii::t('app/superadmin', 'error.result_is_null'));
            }

        } catch (Exception $e){
            $this->error = $e;
            return 0;
        }

        return [
            'balance' => '$' . $response->resultRaw['balance'],
        ];

    }


}