<?php
namespace control_panel\components\letsencrypt;

use common\models\sommerces\Params;

/**
 * Class Letsencrypt
 * @package control_panel\components\letsencrypt
 */
class Letsencrypt extends \common\components\letsencrypt\Letsencrypt
{
    public $paramsClass = Params::class;
}