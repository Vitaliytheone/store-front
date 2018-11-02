<?php

namespace common\components\letsencrypt;

use common\components\letsencrypt\exceptions\AcmeException;
use yii\base\Component;
use yii\console\ExitCode;

/**
 * Class Acmesh
 * @package common\components\letsencrypt
 */
class Acme extends Component
{
    const CONFIG_PATH_LIB = 'dir.lib';
    const CONFIG_PATH_CONFIG_HOME = 'dir.config.home';
    const CONFIG_PATH_ACCOUNT = 'dir.account';
    const CONFIG_PATH_SSL = 'dir.ssl';
    const CONFIG_PATH_SRC = 'dir.src';
    const CONFIG_PATH_ACCOUNT_KEY = 'file.account.key';
    const CONFIG_PATH_ACCOUNT_CONF = 'file.account.conf';

    const EXEC_RESULT_COMMAND = 'command';
    const EXEC_RESULT_RETURN_CODE = 'return_code';
    const EXEC_RESULT_RETURN_DATA = 'return_data';

    const CERT_DATA_CA = 'ca.cer';
    const CERT_DATA_FULLCHAIN = 'fullchain.cer';
    const CERT_DATA_CSR = 'domain.csr';
    const CERT_DATA_CER = 'domain.cer';
    const CERT_DATA_KEY = 'domain.key';

    /**
     * Enable/disable Letsencrypt stage (test) mode
     * @var bool
     */
    private $_stageMode = false;

    /**
     * Enable/disable Letsencrypt debug mode
     * @var bool
     */
    private $_debug_mode = false;

    /**
     * ACME.sh library path config
     * @var array
     */
    private $_paths = [
        self::CONFIG_PATH_LIB => '',
        self::CONFIG_PATH_CONFIG_HOME => '',
        self::CONFIG_PATH_ACCOUNT => '',
        self::CONFIG_PATH_ACCOUNT_KEY => '',
        self::CONFIG_PATH_SSL => '',
        self::CONFIG_PATH_SRC => '',
    ];

    /**
     * Last execute exec result
     * @var array
     */
    private $_exec_cmd_result;

    /**
     * Set stage mode
     * @param bool $mode
     */
    public function setStageMode(bool $mode)
    {
        $this->_stageMode = $mode;
    }

    /**
     * Get stage mode
     * @return bool
     */
    public function getStageMode()
    {
        return $this->_stageMode;
    }

    /**
     * Set debug mode
     * @param bool $mode
     */
    public function setDebugMode(bool $mode)
    {
        $this->_debug_mode = $mode;
    }

    /**
     * Get debug mode
     * @return bool
     */
    public function getDebugMode()
    {
        return $this->_debug_mode;
    }

    /**
     * Set ACME.sh library path config
     * @param array $paths
     */
    public function setPaths($paths)
    {
        $this->_paths[self::CONFIG_PATH_LIB] = $paths['lib'];
        $this->_paths[self::CONFIG_PATH_SSL] = $paths['ssl'];

        $this->_paths[self::CONFIG_PATH_CONFIG_HOME] = $this->getPath(self::CONFIG_PATH_LIB) . '/config';
        $this->_paths[self::CONFIG_PATH_ACCOUNT] = $this->getPath(self::CONFIG_PATH_LIB) . '/account';
        $this->_paths[self::CONFIG_PATH_ACCOUNT_KEY] = $this->getPath(self::CONFIG_PATH_ACCOUNT) . '/account.key';
        $this->_paths[self::CONFIG_PATH_ACCOUNT_CONF] = $this->getPath(self::CONFIG_PATH_ACCOUNT) . '/account.conf';
        $this->_paths[self::CONFIG_PATH_SRC] = $this->getPath(self::CONFIG_PATH_LIB) . '/src';
    }

    /**
     * Get ACME.sh library path config
     * @return array
     */
    public function getPaths()
    {
        return $this->_paths;
    }

    /**
     * Get ACME.sh library path by name
     * @param $path
     * @return mixed
     */
    public function getPath($path)
    {
        return $this->_paths[$path];
    }

    /**
     * Return certificates dir
     * @return string
     */
    public function getCertsDir()
    {
        return strtolower($this->getPath(self::CONFIG_PATH_SSL) . '/' . ($this->getStageMode() ? 'stage' : 'prod'));
    }

    /**
     * Return certificate dir
     * @param $domain
     * @return string
     */
    public function getCertDir($domain)
    {
        return strtolower($this->getCertsDir() . '/' . $domain);
    }

    /**
     * Return required domain certificate files
     * @param $domain
     * @return array
     * @throws AcmeException
     */
    public function requiredCertFiles($domain)
    {
        $domainPath = $this->getCertsDir() . '/' . $domain;

        if (!file_exists($domainPath) || !is_dir($domainPath)) {
            throw new AcmeException('Domain ' . $domain . ' certificate does not exist!');
        }

        return [
            self::CERT_DATA_CA => $domainPath .  '/ca.cer',
            self::CERT_DATA_FULLCHAIN => $domainPath . '/fullchain.cer',
            self::CERT_DATA_CER => $domainPath . '/' . $domain . '.cer',
            self::CERT_DATA_CSR => $domainPath . '/' . $domain . '.csr',
            self::CERT_DATA_KEY => $domainPath . '/' . $domain . '.key',
        ];
    }

    /**
     * Return shell exec CMD additional params
     * @return array
     */
    private function _cmdConfigOptions()
    {
        $options = [
            '--home' => $this->getPath(self::CONFIG_PATH_LIB),
            '--config-home' => $this->getPath(self::CONFIG_PATH_CONFIG_HOME),
        ];

        if ($this->getStageMode()) {
            $options[] = '--staging';
        }

        if ($this->getDebugMode()) {
            $options[] = '--debug';
        }

        return $options;
    }

    /**
     * Return last execute exec result
     * @param string $resultField
     * @return mixed
     */
    public function getExecResult($resultField = null)
    {
        return $resultField ? $this->_exec_cmd_result[$resultField] : $this->_exec_cmd_result;
    }

    /**
     * Run ACME.sh command
     * @param string $cmd ACME.sh command
     * @param array $options ACME.sh cmd params
     * @param null|string $cmdPath script run path
     * @return boolean
     * @throws AcmeException
     */
    public function exec(string $cmd, $options = [], $cmdPath = null)
    {
        $currentDir = @getcwd();

        $cmdPath = $cmdPath ? $cmdPath : $this->getPath(self::CONFIG_PATH_LIB);

        if (!@chdir($cmdPath)) {
            throw new AcmeException('Cannot change path to ' . "$cmdPath");
        }

        // Build CMD string options
        $options = array_merge($this->_cmdConfigOptions(), $options);

        $cmdOptions = '';

        foreach ($options as $optionKey => $optionValue) {
            $cmdOptions .= ' ' . (is_int($optionKey) ? $optionValue : $optionKey . ' ' . $optionValue);
        }

        $cmd = './acme.sh ' . $cmd . $cmdOptions . ' 2>&1';

        exec($cmd, $output, $returnVar);

        $this->_exec_cmd_result = [
            self::EXEC_RESULT_COMMAND => $cmd,
            self::EXEC_RESULT_RETURN_DATA => $output,
            self::EXEC_RESULT_RETURN_CODE => $returnVar,
        ];

        if ($currentDir && !@chdir($currentDir)) {
            throw new AcmeException('Cannot restore working dir ' . "$currentDir");
        }

        return $returnVar === ExitCode::OK;
    }

    /**
     * Install ACME.sh library
     * @return boolean
     */
    public function install()
    {
        return $this->exec('--install', [
            '--accountconf' => $this->getPath(self::CONFIG_PATH_ACCOUNT_CONF),
            '--accountkey' => $this->getPath(self::CONFIG_PATH_ACCOUNT_KEY),
            '--nocron',
        ], $this->getPath(self::CONFIG_PATH_SRC));
    }

    /**
     * Update Letsencrypt account info
     * @param $accountEmail
     * @return boolean;
     */
    public function updateAccount($accountEmail = false)
    {
        $params = [];

        if ($accountEmail) {
            $params['--accountemail'] = $accountEmail;
        }

        return $this->exec('--updateaccount', $params);
    }

    /**
     * Register Letsencrypt account
     * @throws AcmeException
     * @return null|string
     */
    public function registerAccount()
    {
        $this->exec('--registeraccount');

        $accountThumbprint = null;

        foreach ($this->getExecResult(self::EXEC_RESULT_RETURN_DATA) as $string) {

            if (strpos($string, 'ACCOUNT_THUMBPRINT') === false) {
                continue;
            }

            if ((preg_match('/ACCOUNT_THUMBPRINT=\'(.*?)\'/', $string, $match) == 1)) {
                $accountThumbprint = $match[1];
                break;
            }
        }

        return $accountThumbprint;
    }

    /**
     * Issue letsencrypt certificate
     * @param string $domain
     * @return null|array Null if crashes, parsed certificate data if success
     * @throws AcmeException
     */
    public function issueCert(string $domain)
    {
        $domain = trim($domain);

        $success = $this->exec('--issue', [
            '--force',
            '--domain' => $domain,
            '--certhome' => $this->getCertsDir(),
            '--stateless',
        ]);;

        if (!$success) {
            return null;
        }

        $certFiles = $this->requiredCertFiles($domain);

        foreach ($certFiles as $certFile) {
            if (!@file_exists($certFile)) {
                throw new AcmeException('One of required domain [' . $domain . '] certificate file [' . $certFile . '] does not exist!');
            }
        }

        return $this->parseCert($domain, $certFiles[self::CERT_DATA_CER]);
    }

    /**
     * Return current account thumbprint
     * @return null|string
     * @throws AcmeException
     */
    public function getAccountThumbprint()
    {
        return $this->registerAccount();
    }

    /**
     * Return registered certificates list
     * @param $columns array
     * @return false|array
     * @throws AcmeException
     */
    public function listCerts($columns = ['Main_Domain', "Created", "Renew"])
    {
        $allowedColumns = [
            'Main_Domain',
            'KeyLength',
            'SAN_Domains',
            'Created',
            'Renew'
        ];

        if (array_diff($columns, $allowedColumns)) {
            throw new AcmeException('Invalid column name(s)!');
        }

        if (!$this->exec('--list', [
            '--listraw',
            '--certhome' => $this->getCertsDir()
        ])) {
            return false;
        };

        $certsList = $this->getExecResult( self::EXEC_RESULT_RETURN_DATA);

        if (!is_array($certsList)) {
            throw new AcmeException('Invalid domains list data!');
        }

        array_shift($certsList);

        // Remove additional line debug data
        if ($this->getStageMode()) {
            array_shift($certsList);
        }

        foreach ($certsList as &$cert) {
            $fullCert = explode('|', $cert);

            $cert = [];

            foreach ($columns as $column) {
                $idx = array_search($column, $allowedColumns, false);
                $cert[strtolower($column)] = $fullCert[$idx];
            }
        }

        return $certsList;
    }

    /**
     * Renew domain certificate
     * @param $domain
     * @return null|array Null if crashed, parsed certificate data if success
     * @throws AcmeException
     */
    public function renewCert($domain)
    {
        $domain = trim($domain);

        $success = $this->exec('--renew', [
            '--domain' => $domain,
            '--force',
            '--certhome' => $this->getCertsDir(),
        ]);

        if (!$success) {
            return null;
        }

        $certFiles = $this->requiredCertFiles($domain);

        foreach ($certFiles as $certFile) {
            if (!@file_exists($certFile)) {
                throw new AcmeException('One of required domain [' . $domain . '] certificate file [' . $certFile . '] does not exist!');
            }
        }

        return $this->parseCert($domain, $certFiles[self::CERT_DATA_CER]);
    }

    /**
     * Parse letsencrypt certificate
     * @param string $certFilePath
     * @param string $domain
     * @return array
     * @throws AcmeException
     */
    public function parseCert(string $domain, string $certFilePath)
    {
        $certContent = @file_get_contents($certFilePath);

        if (!$certContent) {
            throw new AcmeException('Cannot read domain [' . $domain . '] certificate file [' . $certFilePath . ']!');
        }

        $parsedCert = openssl_x509_parse($certContent);

        if (!$parsedCert || !is_array($parsedCert)) {
            throw new AcmeException('Cannot parse domain [' . $domain . '] certificate file [' . $certFilePath . ']!');
        }

        return $parsedCert;
    }
}