<?php

namespace common\components\letsencrypt;

use console\controllers\my\SystemController;
use yii\base\Component;
use Yii;
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

        if ($this->console->confirm('Use stage (test) mode?')) {
            $letsencrypt->setStageMode(true);
            $this->console->stdout( 'Letsencrypt configured to use in (test) mode' . PHP_EOL, Console::FG_CYAN);
        } else {
            $this->console->stdout( 'Letsencrypt configured to use in production mode' . PHP_EOL, Console::FG_YELLOW);
        }

        $menuOptions = [
            '1' => 'Install ACME.sh library to project folder',
            '2' => 'Create Letsencrypt account',
            '3' => 'Get ACCOUNT_THUMBPRINT code',
            '4' => 'Issue certificate',
            '5' => 'List certificates',
            '6' => 'Get certificate content',
            '7' => 'Renew certificate',
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
            $this->console->stdout(PHP_EOL . 'This script will install ACME.sh library to the "' . $letsencrypt->getPath('lib') . '" folder' . PHP_EOL, Console::FG_GREEN);

            if (!$this->console->confirm('Continue installation?')) {
                $this->console->stdout('ACME.sh installation aborted!' . PHP_EOL, Console::FG_RED);
                return ExitCode::OK;
            }

            foreach ($letsencrypt->getPaths() as $path) {
                $this->console->stdout('Checking required path: ' . $path . PHP_EOL, Console::FG_GREEN);
                if (!@file_exists($path)) {
                    $this->console->stdout('Not exist, created!' . PHP_EOL, Console::FG_CYAN);
                    if (!@mkdir($path, 0755, true)) {
                        throw new \Exception('Cannot create dir: ' . $path);
                    }
                } {
                    $this->console->stdout('Exist, skipped!' . PHP_EOL, Console::FG_GREEN);
                }
            }

            if (!@file_exists($letsencrypt->getPath('src') . '/' . 'acme.sh')) {
                $this->console->stdout('Clone ACME.sh repository...' . PHP_EOL, Console::FG_GREEN);
                exec('git clone https://github.com/Neilpang/acme.sh.git ' . $letsencrypt->getPath('src'), $output, $returnVar);

                if ($returnVar !== 0) {
                    throw new \Exception('Error on clone ACME.sh repository! ' . $returnVar);
                }
            }

            $this->console->stdout('Installation ACME.sh library...' . PHP_EOL, Console::FG_GREEN);

            $letsencrypt->install();

            $this->console->stdout('The library ACME.sh has been successfully installed' . PHP_EOL . PHP_EOL, Console::FG_GREEN);

            if ($this->console->confirm('Remove ACME.sh src folder?')) {

                exec('rm -rf ' . $letsencrypt->getPath('src'), $output, $returnVar);

                if ($returnVar !== 0) {
                    throw new \Exception('Cannot remove ACME.sh src folder! ' . $returnVar);
                }
            }

            return ExitCode::OK;
        }

        if ($menuOption == 2) {

            $this->console->stdout('Letsencrypt account registration...' . PHP_EOL, Console::FG_GREEN);

            if (@file_exists($letsencrypt->getPath('account') . '/account.key')) {
                $this->console->stdout('An existing account private key has been detected!' . PHP_EOL, Console::FG_YELLOW);

                if (!$this->console->confirm('Continue registration and destroy all existing account data?')) {
                    $this->console->stdout('ACME.sh installation aborted!' . PHP_EOL, Console::FG_RED);

                    return ExitCode::OK;
                }
            }

            $email = $this->console->prompt('Enter Letsencrypt account email:', ['required' => true, 'validator' => function ($input, &$error) {
                if (!filter_var($input, FILTER_VALIDATE_EMAIL)) {
                    $error = 'invalid email!';
                    return false;
                }
                return true;
            }]);

            $letsencrypt->updateAccount($email);
            $accountThumbprint = $letsencrypt->registerAccount();

            $this->console->stdout('ACCOUNT_THUMBPRINT="' . $accountThumbprint . '"'  . PHP_EOL, Console::FG_CYAN);
            $this->console->stdout('You must add the following lines to your Nginx server configuration ' . PHP_EOL, Console::FG_GREEN);
            $this->console->stdout(PHP_EOL . "
                location ~ ^/\.well-known/acme-challenge/([-_a-zA-Z0-9]+)$ {
                    default_type text/plain;
                    return 200 \"$1.$accountThumbprint\";
                }
                " . PHP_EOL . PHP_EOL, Console::FG_PURPLE);

            $this->console->stdout('Letsencrypt account successfully registered' . PHP_EOL, Console::FG_GREEN);

            return ExitCode::OK;
        }

        if ($menuOption == 3) {
            $this->console->stdout('ACCOUNT_THUMBPRINT="' . $letsencrypt->getAccountThumbprint() . '"'  . PHP_EOL, Console::FG_CYAN);
            return ExitCode::OK;
        }

        if ($menuOption == 4) {

            $this->console->stdout('Issue certificate...' . PHP_EOL, Console::FG_GREEN);

            $domain = $this->console->prompt('Input exiting domain:', ['required' => true, 'validator' => function ($input, &$error) {
                if (!filter_var('test@' . $input, FILTER_VALIDATE_EMAIL)) {
                    $error = 'Invalid domain!';
                    return false;
                }
                return true;
            }]);

            $letsencrypt->issueCert($domain);

            $this->console->stdout( print_r($letsencrypt->getExecResult(Letsencrypt::EXEC_RESULT_FIELD_RETURN_DATA),1));

            $this->console->stdout('Issue certificate successfully finished. Check your SSL path.' .  PHP_EOL, Console::FG_GREEN);

            return ExitCode::OK;
        }

        if ($menuOption == 5) {

            $this->console->stdout('Registered certificates list...' . PHP_EOL, Console::FG_GREEN);

            foreach ($letsencrypt->listCerts() as $cert) {
                $this->console->stdout(json_encode($cert, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) . PHP_EOL);
            }

            return ExitCode::OK;
        }

        if ($menuOption == 6) {
            $this->console->stdout('Get certificate content...' . PHP_EOL, Console::FG_GREEN);

            $domain = $this->console->prompt('Input exiting domain:', ['required' => true, 'validator' => function ($input, &$error) {
                if (!filter_var('test@' . $input, FILTER_VALIDATE_EMAIL)) {
                    $error = 'Invalid domain!';
                    return false;
                }
                return true;
            }]);

            if ($this->console->confirm('Print completed certificate data?')) {
                $this->console->stdout($letsencrypt->getCertData($domain));

                return ExitCode::OK;
            }

            $certFiles = array_keys($letsencrypt->getCertFiles($domain));

            $this->console->stdout("Possible domain certificate files: " . PHP_EOL);

            foreach ($certFiles as $menuOption) {
                $this->console->stdout("\t" . $menuOption . PHP_EOL);
            }

            $certFile = $this->console->prompt('Input certificate file from list above:', ['required' => true, 'validator' => function ($input, &$error) use ($certFiles) {
                if (!in_array($input, $certFiles)) {
                    $this->console->stdout("Input valid certificate file! ", Console::FG_YELLOW);
                    return false;
                }
                return true;
            }]);

            $this->console->stdout(PHP_EOL . $letsencrypt->getCertData($domain, $certFile) . PHP_EOL);

            return ExitCode::OK;
        }

        if ($menuOption == 7) {

            $this->console->stdout('Renew domain certificate...' . PHP_EOL, Console::FG_GREEN);

            $domain = $this->console->prompt('Input exiting domain:', ['required' => true, 'validator' => function ($input, &$error) {
                if (!filter_var('test@' . $input, FILTER_VALIDATE_EMAIL)) {
                    $error = 'Invalid domain!';
                    return false;
                }
                return true;
            }]);

            $letsencrypt->renewSsl($domain);

            $this->console->stdout(json_encode($letsencrypt->getExecResult(), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) . PHP_EOL);

            return ExitCode::OK;
        }

        return ExitCode::OK;
    }
}