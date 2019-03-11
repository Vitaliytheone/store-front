<?php
namespace common\components\letsencrypt;

use common\components\letsencrypt\exceptions\LetsencryptException;
use common\components\letsencrypt\interfaces\SslCertLetsencryptInterface;
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
    public $paramsClass = Params::class;

    public const OPTION_ACCOUNT_THUMBPRINT = 'account_thumbprint';
    public const OPTION_ACCOUNT_KEY = 'account_key';

    /**
     * Current Letsencrypt SSL model
     * @var SslCertLetsencryptInterface
     */
    private $_ssl;

    /**
     * Set current SslCertLetsencryptInterface
     * @param SslCertLetsencryptInterface $sslCert
     */
    public function setSsl(SslCertLetsencryptInterface &$sslCert)
    {
        $this->_ssl = &$sslCert;
    }

    /**
     * Return current SslCertLetsencryptInterface
     * @return SslCertLetsencryptInterface
     */
    public function getSsl() : SslCertLetsencryptInterface
    {
       return $this->_ssl;
    }

    /**
     * Install ACME.sh library
     * @return bool
     * @throws LetsencryptException
     */
    public function install()
    {
        $dirs = [
            $this->getPath(static::CONFIG_PATH_CONFIG_HOME),
            $this->getPath(static::CONFIG_PATH_ACCOUNT),
            $this->getPath(static::CONFIG_PATH_SSL),
        ];

        foreach ($dirs as $dir) {
            if (!@file_exists($dir)) {
                if (!@mkdir($dir, 0755, true)) {
                    throw new LetsencryptException('Cannot create dir: ' . $dir);
                }
            }
        }

        return $this->cmdInstall();
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
        $accountParams = $this->paramsClass::findOne([
            'category' => $this->paramsClass::CATEGORY_SERVICE,
            'code' => $this->paramsClass::CODE_LETSENCRYPT
        ]);

        if (!$accountParams) {
            $accountParams = new $this->paramsClass();
            $accountParams->category = $this->paramsClass::CATEGORY_SERVICE;
            $accountParams->code = $this->paramsClass::CODE_LETSENCRYPT;
        }

        if (!$force && $accountParams->getOption(static::OPTION_ACCOUNT_KEY)) {
            throw new LetsencryptException('Account already exist! Use "Restore Letsencrypt account from DB" menu options or  $force = true param for register new one!');
        }

        $accountPrivateKey = null;
        $accountThumbprint = $this->cmdRegisterAccount();

        if (!$accountThumbprint || !@file_exists($this->getPath(static::CONFIG_PATH_ACCOUNT_KEY))) {
            throw new LetsencryptException("Account private key was not created!");
        }

        $accountPrivateKey = @file_get_contents($this->getPath(static::CONFIG_PATH_ACCOUNT_KEY));

        if (!$accountPrivateKey) {
            throw new LetsencryptException('Cannot read account private key!');
        }

        $options = [
            static::OPTION_ACCOUNT_KEY => $accountPrivateKey,
            static::OPTION_ACCOUNT_THUMBPRINT => $accountThumbprint,
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
        $accountParams = $this->paramsClass::findOne([
            'category' => $this->paramsClass::CATEGORY_SERVICE,
            'code' => $this->paramsClass::CODE_LETSENCRYPT
        ]);

        $backupAccountKey = $accountParams->getOption(static::OPTION_ACCOUNT_KEY);
        $backupAccountThumbprint = $accountParams->getOption(static::OPTION_ACCOUNT_THUMBPRINT);

        if (!$backupAccountKey) {
            throw new LetsencryptException('No backup copy of private account key in database!');
        }

        $accountKeyPath = $this->getPath(static::CONFIG_PATH_ACCOUNT_KEY);

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

        $restoredKeyThumbprint = $this->cmdRegisterAccount();

        if ($restoredKeyThumbprint !== $backupAccountThumbprint) {
            throw new LetsencryptException('Restored and backup ACCOUNT_THUMBPRINTs does not matched!');
        }

        return $restoredKeyThumbprint;
    }

    /**
     * Return account Thumbprint
     * @return null|string
     */
    public function getAccountThumbprint()
    {
        $this->restoreAccountFromDb();

        return $this->cmdRegisterAccount();
    }

    /**
     * Issue Letsencrypt certificate
     * @param $withWww boolean Request additionsl www-subdomain if true
     * @throws LetsencryptException
     */
    public function issueCert($withWww = false)
    {
        $this->restoreAccountFromDb();

        $parsedCert = $this->cmdIssueCert($this->_ssl->domain, $withWww);

        if (!$parsedCert) {
            throw new LetsencryptException(json_encode([
                'message' => 'Cannot obtain issued Letsencrypt cert [' . $this->_ssl->domain . ']',
                'acme_result' => $this->getExecResult(),
            ], JSON_PRETTY_PRINT));
        }

        $certFiles = $this->_cutCertFiles($this->_ssl->domain);

        $this->_ssl->setCsrFiles($certFiles);
        $this->_ssl->expiry_at_timestamp = static::_expiryDate($parsedCert);
        $this->_ssl->csr_code = $this->getCertFileContent(SslCertLetsencryptInterface::SSL_FILE_CSR);
        $this->_ssl->csr_key = $this->getCertFileContent(SslCertLetsencryptInterface::SSL_FILE_KEY);
        $this->_ssl->setOrderDetails($this->getExecResult());
    }

    /**
     * Renew certificate
     * @param $withWww boolean Request additionsl www-subdomain if true
     * @throws LetsencryptException
     */
    public function renewCert($withWww = false)
    {
        $this->restoreAccountFromDb();

        $this->_restoreCertFilesFromDb();

        $parsedCert = $this->cmdRenewCert($this->_ssl->domain, $withWww);

        if (!$parsedCert) {
            throw new LetsencryptException(json_encode([
                'message' => 'Cannot obtain renewed Letsencrypt cert [' . $this->_ssl->domain . ']',
                'acme_result' => $this->getExecResult(),
            ], JSON_PRETTY_PRINT));
        }

        $certFiles = $this->_cutCertFiles($this->_ssl->domain);

        $this->_ssl->setCsrFiles($certFiles);
        $this->_ssl->expiry_at_timestamp = static::_expiryDate($parsedCert);
        $this->_ssl->csr_code = $this->getCertFileContent(SslCertLetsencryptInterface::SSL_FILE_CSR);
        $this->_ssl->csr_key = $this->getCertFileContent(SslCertLetsencryptInterface::SSL_FILE_KEY);
        $this->_ssl->setOrderDetails($this->getExecResult());

        if (!$this->_ssl->save(false)) {
            throw new LetsencryptException('Cannot save SslCertLetsencrypt domain ['. $this->_ssl->domain .'] certificate!');
        }
    }

    /**
     * Return certificate file names list
     * @return array
     * @throws LetsencryptException
     */
    public function getCertFilesList()
    {
        return array_keys($this->_ssl->getCsrFiles());
    }

    /**
     * Return certificate file content
     * @param $fileName
     * @return string
     */
    public function getCertFileContent($fileName)
    {
        return $this->_ssl->getCsrFile($fileName);
    }

    /**
     * Cut cert files
     * Copy local stored cert files contents and delete cert files dir
     * @param string $domain
     * @return array of cert files content
     * @throws LetsencryptException
     */
    private function _cutCertFiles(string $domain)
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
     * @throws LetsencryptException
     */
    private function _restoreCertFilesFromDb()
    {
        $ssl = $this->_ssl;

        $certFiles = $ssl->getCsrFiles();

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