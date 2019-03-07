<?php
namespace common\components\letsencrypt\traits;

use common\components\letsencrypt\interfaces\SslCertLetsencryptInterface;

/**
 * Trait SslCertLetsencryptTrait
 * @package common\components\letsencrypt\traits
 *
 * @mixin SslCertLetsencryptInterface
 */
trait SslCertLetsencryptTrait {

    /**
     * Return SSL file names list
     * @param $domain
     * @return array
     */
    public static function sslFileNames($domain)
    {
        $fileNames =  [
            static::SSL_FILE_CA,
            static::SSL_FILE_FULLCHAIN,
            static::SSL_FILE_CSR,
            static::SSL_FILE_CER,
            static::SSL_FILE_KEY,
            static::SSL_FILE_CSR_CONF,
            static::SSL_FILE_DOMAIN_CONF,
        ];

        foreach ($fileNames as &$fileName) {

            if (strpos($fileName, static::DOMAIN_PLACEHOLDER) === false) {
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