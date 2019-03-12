<?php
namespace common\components\letsencrypt\interfaces;

/**
 * Interface SslCertLetsencryptInterface
 * @package common\components\letsencrypt\interfaces
 */
interface SslCertLetsencryptInterface
{
    const DOMAIN_PLACEHOLDER  = '{domain}';

    const SSL_FILE_CA = 'ca.cer';
    const SSL_FILE_FULLCHAIN = 'fullchain.cer';
    const SSL_FILE_CSR = self::DOMAIN_PLACEHOLDER . '.csr';
    const SSL_FILE_CER = self::DOMAIN_PLACEHOLDER . '.cer';
    const SSL_FILE_KEY = self::DOMAIN_PLACEHOLDER . '.key';

    const SSL_FILE_CSR_CONF = self::DOMAIN_PLACEHOLDER . '.csr.conf';
    const SSL_FILE_DOMAIN_CONF = self::DOMAIN_PLACEHOLDER . '.conf';
}

