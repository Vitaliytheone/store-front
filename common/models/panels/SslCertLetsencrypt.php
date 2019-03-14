<?php
namespace common\models\panels;

use common\components\letsencrypt\interfaces\SslCertLetsencryptInterface;
use common\components\letsencrypt\traits\SslCertLetsencryptTrait;

/**
 * Class SslCertLetsencrypt
 * @package common\models\panels
 */
class SslCertLetsencrypt extends SslCert implements SslCertLetsencryptInterface
{
    use SslCertLetsencryptTrait;
}
