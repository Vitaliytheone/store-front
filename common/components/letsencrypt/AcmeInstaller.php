<?php

namespace common\components\letsencrypt;

use common\models\panels\SslCertLetsencrypt;
use common\models\panels\Customers;
use common\models\panels\Project;
use common\models\panels\SslCert;
use common\models\panels\SslCertItem;
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
            '3' => 'Restore Letsencrypt account from DB',
            '4' => 'Get ACCOUNT_THUMBPRINT code',
            '5' => 'Issue certificate',
            '6' => 'Renew certificate',
            '7' => 'Get certificate content',
            '8' => 'Show current config paths',
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
                if (!filter_var('test@' . trim($input), FILTER_VALIDATE_EMAIL)) {
                    $error = 'Invalid domain!';
                    return false;
                }
                return true;
            }]);

            $domain = trim($domain);

            if (SslCertLetsencrypt::findOne(['domain' => $domain])) {
                throw new Exception('SslCert already exist! Use "Renew certificate" menu item!');
            }

            $panel = Project::findOne(['site' => $domain]);

            if (!$panel) {
                throw new Exception('Panel [' . $domain . '] not exist!');
            }

            $customer = Customers::findOne(['id' => $panel->cid]);

            if (!$customer) {
                throw new Exception('Panel [' . $domain . '] customer [' . $customer->id . '] not exist!');
            }

            $sslCertItem = SslCertItem::findOne(['provider' => SslCertItem::PROVIDER_LETSENCRYPT]);

            if (!$sslCertItem) {
                throw new Exception('Cannot find PROVIDER_LETSENCRYPT SslCertItem');
            }

            $ssl = new SslCertLetsencrypt();
            $ssl->cid = $panel->id;
            $ssl->pid = $customer->id;
            $ssl->project_type = SslCert::PROJECT_TYPE_PANEL;
            $ssl->item_id = $sslCertItem->id;
            $ssl->status = SslCert::STATUS_PENDING;
            $ssl->checked = SslCert::CHECKED_NO;
            $ssl->domain = trim($domain);

            $letsencrypt->setSsl($ssl);
            $letsencrypt->issueCert(!(bool)$panel->subdomain);

            $ssl->status = SslCertLetsencrypt::STATUS_ACTIVE;
            $ssl->checked = SslCertLetsencrypt::CHECKED_YES;

            if (!$ssl->save(false)) {
                throw new Exception('Cannot create SslCertLetsencrypt [' . $domain . ']');
            }

            $this->console->stdout( print_r($letsencrypt->getExecResult(Acme::EXEC_RESULT_RETURN_DATA),1));

            $this->console->stdout('Issue certificate successfully finished. Check your SSL path.' .  PHP_EOL, Console::FG_GREEN);

            return ExitCode::OK;
        }

        if ($menuOption == 6) {

            $this->console->stdout('Renew domain certificate...' . PHP_EOL, Console::FG_GREEN);

            $domain = $this->console->prompt('Input exiting domain:', ['required' => true, 'validator' => function ($input, &$error) {
                if (!filter_var('test@' . $input, FILTER_VALIDATE_EMAIL)) {
                    $error = 'Invalid domain!';
                    return false;
                }
                return true;
            }]);

            $domain = trim($domain);

            $ssl = SslCertLetsencrypt::findOne([
                'domain' => $domain,
            ]);

            if (!$ssl) {
                throw new Exception('SslCertLetsencrypt does not exist for domain [' . $domain . ']!');
            }

            $ssl->status = SslCertLetsencrypt::STATUS_INCOMPLETE;
            $ssl->checked = SslCertLetsencrypt::CHECKED_NO;

            if (!$ssl->save(false)) {
                throw new Exception('Cannot update SslCertLetsencrypt item [sslId=' . $ssl->id . ']');
            }

            $panel = Project::findOne($ssl->pid);

            if (!$panel) {
                throw new Exception('Panel [' . $ssl->pid . '] not found!');
            }

            $letsencrypt->setSsl($ssl);

            $letsencrypt->renewCert(!(bool)$panel->subdomain);

            $ssl->status = SslCertLetsencrypt::STATUS_ACTIVE;
            $ssl->checked = SslCertLetsencrypt::CHECKED_YES;

            if (!$ssl->save(false)) {
                throw new Exception('Cannot update SslCertLetsencrypt item [sslId=' . $ssl->id . ']');
            }

            $this->console->stdout(json_encode($letsencrypt->getExecResult(), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) . PHP_EOL);

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

            $domain = trim($domain);

            $ssl = SslCertLetsencrypt::findOne([
                'domain' => $domain,
            ]);

            if (!$ssl) {
                throw new Exception('SslCertLetsencrypt does not exist for domain [' . $domain . ']!');
            }

            $letsencrypt->setSsl($ssl);

            $certFiles = $letsencrypt->getCertFilesList();

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

            $this->console->stdout(PHP_EOL . $letsencrypt->getCertFileContent($certFiles[$certFileIndex]) . PHP_EOL);

            return ExitCode::OK;
        }

        if ($menuOption == 8) {
            $this->console->stdout('Current library SSL paths...' . PHP_EOL, Console::FG_GREEN);
            $this->console->stdout( print_r($letsencrypt->getPaths(), 1) . PHP_EOL, Console::FG_CYAN);
        }

        return ExitCode::OK;
    }
}