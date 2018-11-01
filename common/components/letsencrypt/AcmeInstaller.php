<?php

namespace common\components\letsencrypt;

use console\controllers\my\SystemController;
use yii\base\Component;
use Yii;
use yii\base\Exception;
use yii\helpers\Console;
use yii\console\ExitCode;

/**
 * Class AcmeInstaller
 * @package common\components\letsencrypt
 */
class AcmeInstaller extends Component
{
    /**
     * Current console
     * @var SystemController
     */
    public $console;

    /**
     * Run
     * @return int
     * @throws \Exception
     */
    public function run()
    {
        $this->console->stdout('Letsencrypt ACME.sh library management script'. PHP_EOL, Console::FG_GREEN);

        $letsencrypt = new Letsencrypt();
        $letsencrypt->setPaths(Yii::$app->params['letsencrypt']['paths']);

//        if ($this->console->confirm('Use stage (test) mode?')) {
//            $letsencrypt->setStageMode(true);
//            $this->console->stdout( 'Letsencrypt configured to use in (test) mode' . PHP_EOL, Console::FG_CYAN);
//        } else {
//            $this->console->stdout( 'Letsencrypt configured to use in production mode' . PHP_EOL, Console::FG_YELLOW);
//        }

        $menuOptions = [
            '1' => 'Install ACME.sh library to project folder',
            '2' => 'Create Letsencrypt account',
            '3' => 'Restore Letsencrypt account from DB',
            '4' => 'Get ACCOUNT_THUMBPRINT code',
            '5' => 'Issue certificate',
            '7' => 'Get certificate content',
            '8' => 'Renew certificate',
            '9' => 'Show current config paths',
        ];

        foreach ($menuOptions as $itemNumber => $itemLabel) {
            $this->console->stdout("\t" . $itemNumber . '.' . ' ' . $itemLabel . PHP_EOL);
        }

        $menuOption = $this->console->prompt('Select menu item:', ['required' => true, 'validator' => function ($input, &$error) use ($menuOptions) {
            if (!in_array($input, array_keys($menuOptions))) {
                $error = 'Invalid menu option';
                return false;
            }
            return true;
        }]);


        // INSTALL ACME.sh
        if ($menuOption == 1) {
            $this->console->stdout(PHP_EOL . 'This script will install ACME.sh library to the "' . $letsencrypt->getPath(Letsencrypt::CONFIG_PATH_LIB) . '" folder' . PHP_EOL, Console::FG_GREEN);

            if (!$this->console->confirm('Continue installation?')) {
                $this->console->stdout('ACME.sh installation aborted!' . PHP_EOL, Console::FG_RED);
                return ExitCode::OK;
            }

            $this->console->stdout('Installation ACME.sh library...' . PHP_EOL, Console::FG_GREEN);

            if (!$letsencrypt->install()) {
                $this->console->stdout('ACME.sh installation crashes! See details belong' . PHP_EOL, Console::FG_RED);
                $this->console->stdout(json_encode($letsencrypt->getExecResult()) . PHP_EOL);
                return ExitCode::UNSPECIFIED_ERROR;
            }

            $this->console->stdout('The library ACME.sh has been successfully installed' . PHP_EOL . PHP_EOL, Console::FG_GREEN);

            if ($this->console->confirm('Remove ACME.sh src folder?')) {

                exec('rm -rf ' . $letsencrypt->getPath(Letsencrypt::CONFIG_PATH_SRC), $output, $returnVar);

                if ($returnVar !== 0) {
                    throw new \Exception('Cannot remove ACME.sh src folder! ' . $returnVar);
                }
            }

            return ExitCode::OK;
        }

        if ($menuOption == 2) {

            $this->console->stdout('Letsencrypt account registration...' . PHP_EOL, Console::FG_GREEN);

            if (!$this->console->confirm('Continue registration and destroy all existing account data?')) {
                $this->console->stdout('ACME.sh installation aborted!' . PHP_EOL, Console::FG_RED);
                return ExitCode::OK;
            }

            $accountThumbprint = $letsencrypt->registerAccount();

            if (!$accountThumbprint) {
                $this->console->stdout('Letsencrypt account registration crashes!' . PHP_EOL, Console::FG_RED);
                $this->console->stdout(json_encode($letsencrypt->getExecResult()) . PHP_EOL);
                return ExitCode::UNSPECIFIED_ERROR;
            }

            $this->console->stdout('ACCOUNT_THUMBPRINT="' . $accountThumbprint . '"'  . PHP_EOL, Console::FG_CYAN);
            $this->console->stdout('Letsencrypt account successfully registered' . PHP_EOL, Console::FG_GREEN);

            return ExitCode::OK;
        }

        if ($menuOption == 3) {
            $this->console->stdout('Restore Letsencrypt account from database...' . PHP_EOL, Console::FG_GREEN);

            $accountThumbprint = $letsencrypt->restoreAccountFromDb();

            if (!$accountThumbprint) {
                $this->console->stdout('Restore Letsencrypt account from database crashes!' . PHP_EOL, Console::FG_RED);
                $this->console->stdout(json_encode($letsencrypt->getExecResult()) . PHP_EOL);
                return ExitCode::UNSPECIFIED_ERROR;
            }

            $this->console->stdout('ACCOUNT_THUMBPRINT="' . $accountThumbprint . '"'  . PHP_EOL, Console::FG_CYAN);
            $this->console->stdout('Letsencrypt account successfully restored from database' . PHP_EOL, Console::FG_GREEN);

            return ExitCode::OK;
        }

        if ($menuOption == 4) {

            $accountThumbprint = $letsencrypt->getAccountThumbprint();

            if (!$accountThumbprint) {
                $this->console->stdout('Letsencrypt get account thumbprint crashes!' . PHP_EOL, Console::FG_RED);
                $this->console->stdout(json_encode($letsencrypt->getExecResult()) . PHP_EOL);
                return ExitCode::UNSPECIFIED_ERROR;
            }

            $this->console->stdout('ACCOUNT_THUMBPRINT="' . $letsencrypt->getAccountThumbprint() . '"'  . PHP_EOL, Console::FG_CYAN);
            return ExitCode::OK;
        }

        if ($menuOption == 5) {

            $this->console->stdout('Issue certificate...' . PHP_EOL, Console::FG_GREEN);

            $domain = $this->console->prompt('Input exiting domain:', ['required' => true, 'validator' => function ($input, &$error) {
                if (!filter_var('test@' . $input, FILTER_VALIDATE_EMAIL)) {
                    $error = 'Invalid domain!';
                    return false;
                }
                return true;
            }]);

            $letsencrypt->issueCert($domain);

            $this->console->stdout( print_r($letsencrypt->getExecResult(Acme::EXEC_RESULT_RETURN_DATA),1));

            $this->console->stdout('Issue certificate successfully finished. Check your SSL path.' .  PHP_EOL, Console::FG_GREEN);

            return ExitCode::OK;
        }


        if ($menuOption == 7) {
            $this->console->stdout('Get certificate files...' . PHP_EOL, Console::FG_GREEN);

            $domain = $this->console->prompt(PHP_EOL . 'Input exiting domain:', ['required' => true, 'validator' => function ($input, &$error) {
                if (!filter_var('test@' . $input, FILTER_VALIDATE_EMAIL)) {
                    $error = 'Invalid domain!';
                    return false;
                }
                return true;
            }]);

            $certFiles = $letsencrypt->getCertFiles($domain);

            $this->console->stdout("Possible domain certificate files: " . PHP_EOL);

            foreach ($certFiles as $index => $menuOption) {
                $this->console->stdout("\t" . $index . '. ' . $menuOption . PHP_EOL);
            }

            $certFileIndex = $this->console->prompt('Select certificate file from list above:', ['required' => true, 'validator' => function ($input, &$error) use ($certFiles) {
                if (empty($certFiles[$input])) {
                    $this->console->stdout("Select valid certificate file! ", Console::FG_YELLOW);
                    return false;
                }
                return true;
            }]);

            $this->console->stdout(PHP_EOL . $letsencrypt->getCertFileContent($domain, $certFiles[$certFileIndex]) . PHP_EOL);

            return ExitCode::OK;
        }

        if ($menuOption == 8) {

            $this->console->stdout('Renew domain certificate...' . PHP_EOL, Console::FG_GREEN);

            $domain = $this->console->prompt('Input exiting domain:', ['required' => true, 'validator' => function ($input, &$error) {
                if (!filter_var('test@' . $input, FILTER_VALIDATE_EMAIL)) {
                    $error = 'Invalid domain!';
                    return false;
                }
                return true;
            }]);

            $letsencrypt->renewCert($domain);

            $this->console->stdout(json_encode($letsencrypt->getExecResult(), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) . PHP_EOL);

            return ExitCode::OK;
        }

        if ($menuOption == 9) {
            $this->console->stdout('Current library SSL paths...' . PHP_EOL, Console::FG_GREEN);
            $this->console->stdout( $letsencrypt->getCertsDir() . PHP_EOL, Console::FG_CYAN);
        }

        return ExitCode::OK;
    }
}