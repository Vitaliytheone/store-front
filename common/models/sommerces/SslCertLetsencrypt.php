<?php
namespace common\models\sommerces;

use common\components\letsencrypt\interfaces\SslCertLetsencryptInterface;
use common\components\letsencrypt\traits\SslCertLetsencryptTrait;

/**
 * Class SslCertLetsencrypt
 * @package common\models\sommerces
 */
class SslCertLetsencrypt extends SslCert implements SslCertLetsencryptInterface
{
    use SslCertLetsencryptTrait;
}
