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
            $request = new Request();
            $response = $request->process('array', $data);

            $result  = [
                'balance' => '$' . $response->resultRaw['balance'],
            ];

        } catch (Exception $e){
            $this->error = $e;
            return 0;
        }

        return $result;

    }


}