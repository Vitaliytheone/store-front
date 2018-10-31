<?php

namespace common\components\letsencrypt;

use yii\base\Component;
use yii\base\Exception;

/**
 * Class Letsencrypt
 * See detailed use in {common/components/letsencrypt/AcmeInstaller.php}
 * @package common\components\letsencrypt
 */
class Letsencrypt extends Component
{
    const CHALLENGE_MODE_STATELESS = '--stateless';

    const EXEC_RESULT_FIELD_COMMAND = 'command';
    const EXEC_RESULT_FIELD_RETURN_CODE = 'return_code';
    const EXEC_RESULT_FIELD_RETURN_DATA = 'return_data';

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
        'lib' => '',
        'config' => '',
        'account' => '',
        'ssl' => '',
        'src' => '',
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
        $this->_paths['lib'] = $paths['lib'];
        $this->_paths['ssl'] = $paths['ssl'];

        $this->_paths['config'] = $this->_paths['lib'] . '/config';
        $this->_paths['account'] = $this->_paths['lib'] . '/account';
        $this->_paths['src'] = $this->_paths['lib'] . '/src';
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
     * Return certificates path
     * @return string
     */
    public function getCertPath()
    {
        return $this->getPath('ssl') . '/' . ($this->getStageMode() ? 'stage' : 'prod');
    }

    /**
     * Return domain certificate file paths
     * @param $domain
     * @return array
     * @throws Exception
     */
    public function getCertFiles($domain)
    {
        $domainPath = $this->getCertPath() . '/' . $domain;

        if (!file_exists($domainPath) || !is_dir($domainPath)) {
            throw new Exception('Domain ' . $domain . ' certificate does not exist!');
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
            '--home' => $this->_paths['lib'],
            '--config-home' => $this->_paths['config'],
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
     * @throws Exception
     */
    public function exec(string $cmd, $options = [], $cmdPath = null)
    {
        $currentDir = @getcwd();

        $cmdPath = $cmdPath ? $cmdPath : $this->_paths['lib'];

        if (!@chdir($cmdPath)) {
            throw new Exception('Cannot change path to ' . "$cmdPath");
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
            self::EXEC_RESULT_FIELD_COMMAND => $cmd,
            self::EXEC_RESULT_FIELD_RETURN_DATA => $output,
            self::EXEC_RESULT_FIELD_RETURN_CODE => $returnVar,
        ];

        if ($returnVar !== 0) {
            throw new Exception('Shell CMD execution error! ' . PHP_EOL .
                '[ CMD = ' . $cmd . ']' . PHP_EOL .
                '[ Exit Code = ' . $returnVar . ']' . PHP_EOL .
                json_encode($output, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
        }

        if ($currentDir && !@chdir($currentDir)) {
            throw new Exception('Cannot restore working dir ' . "$currentDir");
        }
    }

    /**
     * Install ACME.sh library
     */
    public function install()
    {
        $this->exec('--install', [
            '--accountconf' => $this->getPath('account') . '/account.conf',
            '--accountkey' => $this->getPath('account') . '/account.key',
            '--nocron',
        ], $this->getPath('src'));
    }

    /**
     * Update Letsencrypt account info
     * @param $accountEmail
     */
    public function updateAccount($accountEmail)
    {
        $this->exec('--updateaccount', [
            '--accountemail ' => $accountEmail,
        ]);
    }

    /**
     * Register Letsencrypt account
     * @return null|string
     * @throws Exception
     */
    public function registerAccount()
    {
        $this->exec('--registeraccount');

        $accountThumbprint = null;

        foreach ($this->getExecResult(self::EXEC_RESULT_FIELD_RETURN_DATA) as $string) {

            if (strpos($string, 'ACCOUNT_THUMBPRINT') === false) {
                continue;
            }

            if (!(preg_match('/ACCOUNT_THUMBPRINT=\'(.*?)\'/', $string, $match) == 1)) {
                throw new Exception('Account registration filed! ACCOUNT_THUMBPRINT not found or empty!');
            }

            $accountThumbprint = $match[1];
            break;
        }

        return $accountThumbprint;
    }

    /**
     * Issue letsencrypt certificate
     * @param string $domain
     * @throws Exception
     */
    public function issueCert(string $domain)
    {
        $domain = trim($domain);

        if (!filter_var('test@' . $domain, FILTER_VALIDATE_EMAIL)) {
           throw new Exception('Invalid domain!');
        }

        $this->exec('--issue', [
            '--force',
            '--domain' => $domain,
            '--certhome' => $this->getCertPath(),
            self::CHALLENGE_MODE_STATELESS,
        ]);
    }

    /**
     * Return current account thumbprint
     * @return null|string
     * @throws Exception
     */
    public function getAccountThumbprint()
    {
        return $this->registerAccount();
    }

    /**
     * Return registered certificates list
     * @param $columns array
     * @return mixed
     * @throws Exception
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
            throw new Exception('Invalid column name(s)!');
        }

        $this->exec('--list', [
            '--listraw',
            '--certhome' => $this->getCertPath()
        ]);

        $certsList = $this->getExecResult( self::EXEC_RESULT_FIELD_RETURN_DATA);

        if (!is_array($certsList)) {
            throw new Exception('Invalid domains list data!');
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
     * Return domain certificate data
     * @param $domain
     * @param null $certDataKey Return completed certificate data if $data is not specified
     * @return false|int|string
     * @throws Exception
     */
    public function getCertData($domain, $certDataKey = null)
    {
        $domain = trim($domain);

        if (!filter_var('test@' . $domain, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid domain!');
        }

        $certFiles = $this->getCertFiles($domain);

        /**
         * Read certificate file content
         * @param $filePath
         * @return false|int
         * @throws Exception
         */
        $getFileContent = function($filePath) {
            if (!file_exists($filePath) || !is_file($filePath)) {
                throw new Exception('Cannot find certificate file!');
            }

            $content = file_get_contents($filePath);

            if (!$content) {
                throw new Exception('Cannot read certificate file!');
            }

            return $content;
        };

        $certData = '';

        // Only one file
        if ($certDataKey) {
            if (!in_array($certDataKey, array_keys($certFiles))) {
                throw new Exception('Unknown requested certificate data [' . $certFiles . ']!');
            }

            $certData = $getFileContent($certFiles[$certDataKey]);

        } else {
            // All cert files
            foreach ($certFiles as $key => $file) {
                $certData .=
                    PHP_EOL . '=============[' . $key . ']=================' . PHP_EOL .
                    $getFileContent($certFiles[$key]) . PHP_EOL;
            }
        }

        return $certData;
    }

    /**
     * Renew domain certificate
     * @param $domain
     */
    public function renewSsl($domain)
    {
        $domain = trim($domain);

        $this->exec('--renew', [
            '--domain' => $domain,
            '--force',
            '--certhome' => $this->getCertPath(),
        ]);
    }

}