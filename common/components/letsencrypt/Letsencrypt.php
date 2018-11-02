<?php

namespace common\components\letsencrypt;

use common\components\letsencrypt\exceptions\LetsencryptException;
use common\models\panels\LetsencryptSslHelper;
use common\models\panels\Params;
use my\helpers\ExpiryHelper;
use yii\console\ExitCode;
use yii\helpers\ArrayHelper;

/**
 * Class Letsencrypt
 * @package common\components\letsencrypt
 */
class Letsencrypt extends Acme
{
    const OPTION_ACCOUNT_THUMBPRINT = 'account_thumbprint';
    const OPTION_ACCOUNT_KEY = 'account_key';

    /**
     * Install ACME.sh library
     * @return bool
     * @throws LetsencryptException
     */
    public function install()
    {
        $dirs = [
            $this->getPath(self::CONFIG_PATH_CONFIG_HOME),
            $this->getPath(self::CONFIG_PATH_ACCOUNT),
            $this->getPath(self::CONFIG_PATH_SSL),
        ];

        foreach ($dirs as $dir) {
            if (!@file_exists($dir)) {
                if (!@mkdir($dir, 0755, true)) {
                    throw new LetsencryptException('Cannot create dir: ' . $dir);
                }
            }
        }

        return parent::install();
    }

    /**
     * Register new Letsencrypt account
     *
     * Create a new account even if there is an existing one
     * @param $force boolean
     *
     *
     * Return ACCOUNT_THUMBPRINT or null
     * @return null|string
     *
     * @throws LetsencryptException
     */
    public function registerAccount(bool $force = false)
    {
        $accountParams = Params::findOne([
            'category' => Params::CATEGORY_SERVICE,
            'code' => Params::CODE_LETSENCRYPT
        ]);

        if (!$accountParams) {
            $accountParams = new Params();
            $accountParams->category = Params::CATEGORY_SERVICE;
            $accountParams->code = Params::CODE_LETSENCRYPT;
        }

        if (!$force && $accountParams->getOption(self::OPTION_ACCOUNT_KEY)) {
            throw new LetsencryptException('Account already exist! Use $force = true param for register new one!');
        }

        $accountPrivateKey = null;
        $accountThumbprint = parent::registerAccount();

        if (!$accountThumbprint || !@file_exists($this->getPath(self::CONFIG_PATH_ACCOUNT_KEY))) {
            throw new LetsencryptException("Account private key was not created!");
        }

        $accountPrivateKey = @file_get_contents($this->getPath(self::CONFIG_PATH_ACCOUNT_KEY));

        if (!$accountPrivateKey) {
            throw new LetsencryptException('Cannot read account private key!');
        }

        $options = [
            self::OPTION_ACCOUNT_KEY => $accountPrivateKey,
            self::OPTION_ACCOUNT_THUMBPRINT => $accountThumbprint,
        ];

        $accountParams->setOptions($options);

        if(!$accountParams->save(false)) {
            throw new LetsencryptException('Account options have not been saved!');
        }

        return $accountThumbprint;
    }

    /**
     * Restore Letsencrypt account from database
     * @return null|string
     * @throws LetsencryptException
     */
    public function restoreAccountFromDb()
    {
        $accountParams = Params::findOne([
            'category' => Params::CATEGORY_SERVICE,
            'code' => Params::CODE_LETSENCRYPT
        ]);

        $backupAccountKey = $accountParams->getOption(self::OPTION_ACCOUNT_KEY);
        $backupAccountThumbprint = $accountParams->getOption(self::OPTION_ACCOUNT_THUMBPRINT);

        if (!$backupAccountKey) {
            throw new LetsencryptException('No backup copy of private account key in database!');
        }

        $accountKeyPath = $this->getPath(self::CONFIG_PATH_ACCOUNT_KEY);

        // Skip restoring if backup & current keys are equal
        if (@file_exists($accountKeyPath) && (@file_get_contents($accountKeyPath) === $backupAccountKey)) {
            return $backupAccountThumbprint;
        }

        // Delete exiting key
        if (@file_exists($accountKeyPath) && !@unlink($accountKeyPath)) {
            throw new LetsencryptException('Cannot delete old account RSA private key! [' . $accountKeyPath . ']');
        }

        // Write backup key
        if (!@file_put_contents($accountKeyPath, $backupAccountKey)) {
            throw new LetsencryptException('Cannot restore account RSA private key! [' . $accountKeyPath . ']');
        }

        $restoredKeyThumbprint = parent::registerAccount();

        if ($restoredKeyThumbprint !== $backupAccountThumbprint) {
            throw new LetsencryptException('Restored and backup ACCOUNT_THUMBPRINTs does not matched!');
        }

        return $restoredKeyThumbprint;
    }

    /**
     * Cut cert files
     * Copy local stored cert files contents and delete cert files dir
     * @param string $domain
     * @return array of cert files content
     * @throws LetsencryptException
     */
    public function cutCertFiles(string $domain)
    {
        $certDir = $this->getCertDir($domain);

        if (!@file_exists($certDir) || !@is_dir($certDir)) {
            throw new LetsencryptException('Corrupt certificate dir!');
        }

        $certFiles = array_diff(scandir($certDir), array('..', '.'));
        $certFilesContent = [];

        foreach ($certFiles as $fileName) {

            $filePath = $certDir . '/' . $fileName;
            $fileContent = @file_get_contents($filePath);

            if (!$fileContent) {
                throw new LetsencryptException('Cannot read cert file ['. $filePath  .']');
            }

            $certFilesContent[$fileName] = $fileContent;
        }

        exec('rm -rf ' . escapeshellarg($certDir), $output, $returnVar);

        if ($returnVar !== ExitCode::OK) {
            throw new LetsencryptException('Cannot delete domain ssl folder! [' . $certDir . ']');
        }

        return $certFilesContent;
    }

    /**
     * Restore certificate files from DB
     * @param LetsencryptSslHelper $ssl
     * @throws LetsencryptException
     */
    public function restoreCertFilesFromDb(LetsencryptSslHelper $ssl)
    {
        $certFiles = $ssl->getFileContents();

        $certDir = $this->getCertDir($ssl->domain);

        if (!@file_exists($certDir)) {
            if (!@mkdir($certDir, 0755, true)) {
                throw new LetsencryptException('Cannot create dir [' . $certDir . ']');
            }
        }

        foreach ($certFiles as $fileName => $fileContent) {

            $filePath = $certDir . '/' . $fileName;

            if (!@file_exists($filePath)) {
                if (!@file_put_contents($filePath, $fileContent)) {
                    throw new LetsencryptException('Cannot create file [' . $filePath . ']');
                }
            }
        }
    }

    /**
     * Return account Thumbprint
     * @return null|string
     */
    public function getAccountThumbprint()
    {
        $this->restoreAccountFromDb();

        return parent::registerAccount();
    }

    /**
     * Issue Letsencrypt certificate
     * @param string $domain
     * @return int
     * @throws LetsencryptException
     */
    public function issueCert(string $domain)
    {
        $this->restoreAccountFromDb();

        $this->_prepareDomain($domain);

        if (!filter_var('test@' . $domain, FILTER_VALIDATE_EMAIL)) {
            throw new LetsencryptException('Invalid domain name!');
        }

        if (LetsencryptSslHelper::findOne(['domain' => $domain])) {
            throw new LetsencryptException('Certificate for domain [' . $domain . '] already exist! Use renewSsl instead!');
        }

        $this->restoreAccountFromDb();

        $parsedCert = parent::issueCert($domain);

        if (!$parsedCert) {
            throw new LetsencryptException('Cannot obtain issued Letsencrypt cert ['. $domain .'] data!');
        }

        $ssl = new LetsencryptSslHelper();
        $ssl->domain = $domain;
        $ssl->setFileContents($this->cutCertFiles($domain));
        $ssl->expired_at = static::_expiryDate($parsedCert);

        if (!$ssl->save(false)) {
            throw new LetsencryptException('Cannot create new LetsencryptSsl record!');
        }

        return $ssl->id;
    }

    /**
     * Renew certificate
     * @param $domain
     * @return bool
     * @throws LetsencryptException
     */
    public function renewCert($domain)
    {
        $this->_prepareDomain($domain);

        $this->restoreAccountFromDb();

        $ssl = $this->_fetchSsl($domain);

        $this->restoreCertFilesFromDb($ssl);

        $parsedCert = parent::renewCert($domain);

        if (!$parsedCert) {
            throw new LetsencryptException('Cannot obtain renewed Letsencrypt cert ['. $domain .'] data!');
        }

        $ssl->setFileContents($this->cutCertFiles($domain));
        $ssl->expired_at = static::_expiryDate($parsedCert);

        if (!$ssl->save(false)) {
            throw new LetsencryptException('Cannot save LetsencryptSsl domain ['. $domain .'] certificate!');
        }

        return true;
    }

    /**
     * Return certificate file names list
     * @param $domain
     * @return array
     * @throws LetsencryptException
     */
    public function getCertFiles($domain)
    {
        $this->_prepareDomain($domain);

        $ssl = $this->_fetchSsl($domain);

        return array_keys($ssl->getFileContents());
    }

    /**
     * Return certificate file content
     * @param $domain
     * @param $fileName
     * @return string
     */
    public function getCertFileContent($domain, $fileName)
    {
        $this->_prepareDomain($domain);

        $ssl = $this->_fetchSsl($domain);

        return $ssl->getFileContent($fileName);
    }

    /**
     * Prepare domain format
     * @param $domain
     * @throws LetsencryptException
     */
    private function _prepareDomain(&$domain)
    {
        $domain = trim($domain);

        if (!filter_var('test@' . $domain, FILTER_VALIDATE_EMAIL)) {
            throw new LetsencryptException('Invalid domain name!');
        }
    }

    /**
     * Fetch exiting ssl from db
     * @param $domain
     * @return null|LetsencryptSslHelper
     * @throws LetsencryptException
     */
    private function _fetchSsl($domain)
    {
        $ssl = LetsencryptSslHelper::findOne(['domain' => $domain]);

        if (!$ssl) {
            throw new LetsencryptException('SSL for domain [' . $domain . '] does not exist yet!');
        }

        return $ssl;
    }

    /**
     * Return ssl expiry date
     * @param array $parsedCert
     * @return integer
     */
    private static function _expiryDate(array $parsedCert)
    {
        $expiryTime = (int)ArrayHelper::getValue($parsedCert, 'validTo_time_t', null);

        return $expiryTime ? $expiryTime : ExpiryHelper::days(90, time());
    }

}