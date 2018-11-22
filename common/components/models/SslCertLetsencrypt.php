<?php

namespace common\components\models;

use common\models\panels\SslCert;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class SslCertLetsencrypt
 * @package common\components\models
 */
class SslCertLetsencrypt extends SslCert
{
    const DOMAIN_PLACEHOLDER  = '{domain}';

    const SSL_FILE_CA = 'ca.cer';
    const SSL_FILE_FULLCHAIN = 'fullchain.cer';
    const SSL_FILE_CSR = self::DOMAIN_PLACEHOLDER . '.csr';
    const SSL_FILE_CER = self::DOMAIN_PLACEHOLDER . '.cer';
    const SSL_FILE_KEY = self::DOMAIN_PLACEHOLDER . '.key';

    const SSL_FILE_CSR_CONF = self::DOMAIN_PLACEHOLDER . '.csr.conf';
    const SSL_FILE_DOMAIN_CONF = self::DOMAIN_PLACEHOLDER . '.conf';

    /**
     * Return SSL file names list
     * @param $domain
     * @return array
     */
    public static function sslFileNames($domain)
    {
        $fileNames =  [
            self::SSL_FILE_CA,
            self::SSL_FILE_FULLCHAIN,
            self::SSL_FILE_CSR,
            self::SSL_FILE_CER,
            self::SSL_FILE_KEY,
            self::SSL_FILE_CSR_CONF,
            self::SSL_FILE_DOMAIN_CONF,
        ];

        foreach ($fileNames as &$fileName) {

            if (strpos($fileName, self::DOMAIN_PLACEHOLDER) === false) {
                continue;
            }

            $fileName = static::_prepareFilename($fileName, $domain);
        }

        return $fileNames;
    }

    /**
     * Get SSL files content
     * @return mixed
     */
    public function getCsrFiles()
    {
        $files = json_decode($this->csr_files, true);

        return is_array($files) ? $files : [];
    }

    /**
     * Set SSL files content
     * @param array $fileContents
     */
    public function setCsrFiles(array $fileContents)
    {
        $this->csr_files = json_encode($fileContents);
    }

    /**
     * Get SSL file content
     * @param $fileName string
     * @return null|string
     */
    public function getCsrFile(string $fileName)
    {
        static::_prepareFilename($fileName, $this->domain);

        $files = $this->getCsrFiles();

        return ArrayHelper::getValue($files, $fileName, null);
    }

    /**
     * Set SSL file content
     * @param $fileName string file name
     * @param $fileContent string
     */
    public function setCsrFile(string $fileName, string $fileContent)
    {
        static::_prepareFilename($fileName, $this->domain);

        $files = $this->getCsrFiles();

        $this->setCsrFiles(array_merge($files, [$fileName => $fileContent]));
    }

    /**
     * Prepare filename for class constant uses
     * @param string $fileName
     * @param string $domain
     */
    private static function _prepareFilename(string &$fileName, $domain)
    {
        $fileName = str_replace(self::DOMAIN_PLACEHOLDER, trim($domain), $fileName);
    }
}
