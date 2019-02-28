<?php

namespace console\controllers\store;

use common\models\store\Languages;
use common\models\store\Messages;
use common\models\stores\Integrations;
use common\models\stores\StoreAdmins;
use common\models\stores\StoreIntegrations;
use my\components\ActiveForm;
use store\helpers\MessagesHelper;
use yii\db\Exception;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use common\models\stores\Stores;
use store\helpers\StoreHelper;
use Yii;
use yii\helpers\FileHelper;
use common\models\stores\PaymentMethodsCurrency;
use common\models\stores\StorePaymentMethods;
use common\models\stores\PaymentMethods;

/**
 * Class SystemController
 * @package console\controllers
 */
class SystemController extends CustomController
{
    public $storeId;
    public $username;
    public $password;

    public function options($actionID)
    {
        return ['storeId', 'username', 'password'];
    }

    /**
     * Clear twig cache folder
     */
    public function actionClearTwigCache()
    {
        static::_clearTwigCache();
    }

    /**
     * Clear twig cache
     */
    protected static function _clearTwigCache()
    {
        if (!empty(Yii::$app->params['store.twig.cachePath'])) {
            $path = Yii::getAlias(Yii::$app->params['store.twig.cachePath']);
            if (is_dir($path)) {
                FileHelper::removeDirectory($path);
            }
        }
    }

    /**
     * Action to generate stores assets
     */
    public function actionGenerateAssets()
    {
        $query = Stores::find();

        $flushAssets = true;

        if ($this->storeId) {
            $flushAssets = false;

            $query->andWhere([
                'id' => $this->storeId
            ]);
        }

        if ($flushAssets) {
            static::_clearAssetsCache();
        }

        foreach ($query->batch() as $stores) {
            foreach ($stores as $store) {
                /**
                 * @var Stores $store
                 */

                if (!$flushAssets) {
                    static::_clearAssetsCache($store->folder);
                }

                $store->generateFolderName();
                $store->save(false);
                StoreHelper::generateAssets($store->id);

                $this->stderr('Generated for ' . $store->id . "\n", Console::FG_GREEN);
            }
        }

        $this->actionClearTwigCache();
    }

    /**
     * Remove assets dir
     * @param string|null $dirPath
     */
    protected static function _clearAssetsCache($dirPath = null)
    {
        $path = Yii::getAlias(Yii::$app->params['store.assets.cachePath']);

        if ($dirPath) {
            $dirPath = $path . DIRECTORY_SEPARATOR . $dirPath;

            if (is_dir($dirPath)) {
                FileHelper::removeDirectory($dirPath);
            }

            return;
        }
        if (is_dir($path)) {
            foreach (scandir($path) as $dirPath) {
                if (in_array($dirPath, ['.', '..'])) {
                    continue;
                }
                $dirPath = $path . DIRECTORY_SEPARATOR . $dirPath;

                if (is_dir($dirPath)) {
                    FileHelper::removeDirectory($dirPath);
                }
            }
        }
    }

    /**
     * Add new admin user
     */
    public function actionAddAdmin()
    {
        if (empty($this->storeId) || empty($this->username) || empty($this->password)) {
            $this->stderr("--storeId, --username and --password parameters are required" . "\n", Console::FG_RED);
            return;
        }

        $rules = [
            'payments' => 1,
            'orders' => 1,
            'products' => 1,
            'settings' => 1,
        ];

        $user = StoreAdmins::find()->where(['username' => $this->username])->one();

        if (empty($user)) {
            $user = new StoreAdmins();
            $user->store_id = $this->storeId;
            $user->username = $this->username;
            $user->status = true;
            $user->rules = json_encode($rules);
            $user->setPassword($this->password);
            $user->generateAuthKey();
            if ($user->save()) {
                error_log(print_r($user->attributes, 1), 0);
            }
        } else {
            $this->stderr("User $user->username already exist!" . "\n", Console::FG_RED);
            return;
        }

        $this->stderr('User created: ' . $user->id . "\n", Console::FG_GREEN);
    }

    public function actionTestMessage()
    {
        echo Yii::t('app', 'checkout.redirect.title');
    }

    /**
     * Added new messages
     */
    public function actionSyncMessages()
    {
        $this->stderr("Started sync messages\n", Console::FG_GREEN);

        MessagesHelper::syncStoresMessages('en');

        // Clear global cache
        Yii::$app->runAction('cache/flush-all');

        $this->stderr("Finished sync messages\n", Console::FG_GREEN);
    }

    public function actionMigrateAdminEmails()
    {
        foreach ((new \yii\db\Query())->select([
            'db_name',
            'admin_email'
        ])->from(DB_STORES . '.stores')->all() as $store) {
            $db = $store['db_name'];
            $adminEmail = $store['admin_email'];

            $isDbExist = Yii::$app->db
                ->createCommand("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$db'")
                ->queryScalar();

            if (!$isDbExist) {
                continue;
            }

            Yii::$app->db->createCommand("INSERT INTO `{$db}`.`notification_admin_emails` (`email`, `status`, `primary`) VALUES ('{$adminEmail}', 1, 1)")->execute();
        }
    }

    public function actionAddOrderCode()
    {
        foreach ((new \yii\db\Query())->select([
            'db_name',
        ])->from(DB_STORES . '.stores')->all() as $store) {
            $db = $store['db_name'];

            $isDbExist = Yii::$app->db
                ->createCommand("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$db'")
                ->queryScalar();

            if (!$isDbExist) {
                continue;
            }

            foreach ((new \yii\db\Query())->select([
                'id',
            ])->from($db . '.orders')->all() as $order) {
                Yii::$app->db->createCommand("UPDATE `{$db}`.`orders` SET `code` = '" . Orders::generateCodeString(). "' WHERE `id` = '" . $order['id'] . "';")->execute();
            }
        }
    }

    /**
     * Set default pages at stores
     * @throws \yii\db\Exception
     */
    public function actionSetDefaultPages()
    {
        $stores = (new Query())
            ->select('db_name')
            ->from(DB_STORES . '.stores')
            ->where('db_name IS NOT NULL')
            ->andWhere(['!=', 'db_name', ''])
            ->all();

        $templates = (new Query())
            ->select('*')
            ->from(Yii::$app->params['storeDefaultDatabase'] . '.pages')
            ->indexBy('url')
            ->all();

        foreach ($stores as $store) {
            $storePages = (new Query())
                ->select('*')
                ->from($store['db_name'] . '.pages')
                ->indexBy('url')
                ->all();

            $batchInsertData = [];
            foreach ($templates as $key => $value) {
                if (!isset($storePages[$key])) {
                    $batchInsertData[] = array_values(array_slice($value, 1));
                } elseif ($storePages[$key]['template'] != $value['template']) {
                    $batchInsertData[] = array_values(array_slice($value, 1));
                } elseif (
                    $storePages[$key]['template'] == $value['template']
                    && $storePages[$key]['url'] == $value['url']
                ) {
                    Yii::$app->db->createCommand()->update($store['db_name'].'.pages', [
                        'is_default' => 1,
                        'deleted' => 0
                    ], ['url' => $value['url'], 'template' => $value['template']])
                        ->execute();
                }
            }

            Yii::$app->db->createCommand()->batchInsert($store['db_name'].'.pages', array_keys(array_slice($templates['contacts'], 1)), $batchInsertData)->execute();
        }
    }


    /**
     * 1) Create currency list from pay_methods
     * @return string
     * @throws Exception
     */
    public function actionApplyCurrency(): string
    {
        if (Yii::$app->db->getTableSchema('{{%payment_methods_currency}}', true) === null) {
            return $this->stdout("Table `payment_methods_currency` does not exist. Apply SQL first.\n", Console::FG_RED);
        }

        Yii::$app->db->createCommand('USE `' . DB_STORES . '`;
        
        UPDATE `payment_methods` SET `currencies` = \'[\"USD\",\"AUD\",\"BRL\",\"CAD\",\"CZK\",\"DKK\",\"EUR\",\"HKD\",\"HUF\",\"ILS\",\"JPY\",\"MYR\",\"MXN\",\"NZD\",\"NOK\",\"PHP\",\"PLN\",\"GBP\",\"RUB\",\"SGD\",\"SEK\",\"CHF\",\"TWD\",\"THB\",\"INR\",\"IDR\",\"KRW\"]\' 
        WHERE `payment_methods`.`class_name` = \'Paypal\';
        
        UPDATE `payment_methods` SET `currencies` = \'[\"USD\",\"AUD\",\"BRL\",\"CAD\",\"CZK\",\"DKK\",\"EUR\",\"HKD\",\"HUF\",\"ILS\",\"JPY\",\"MYR\",\"MXN\",\"NZD\",\"NOK\",\"PHP\",\"PLN\",\"GBP\",\"RUB\",\"SGD\",\"SEK\",\"CHF\",\"TWD\",\"THB\",\"TRY\"]\' 
        WHERE `payment_methods`.`class_name` = \'Twocheckout\';
        
        UPDATE `payment_methods` SET `currencies` = \'[\"USD\",\"AUD\",\"BRL\",\"CAD\",\"CZK\",\"DKK\",\"EUR\",\"HKD\",\"HUF\",\"ILS\",\"JPY\",\"MYR\",\"MXN\",\"NZD\",\"NOK\",\"PHP\",\"PLN\",\"GBP\",\"RUB\",\"SGD\",\"SEK\",\"CHF\",\"TWD\",\"THB\",\"INR\",\"IDR\",\"TRY\",\"KRW\"]\' 
        WHERE `payment_methods`.`class_name` = \'Coinpayments\';
        
        UPDATE `payment_methods` SET `currencies` = \'[\"BRL\"]\'
        WHERE `payment_methods`.`class_name` = \'Pagseguro\';
        
        UPDATE `payment_methods` SET `currencies` = \'[\"RUB\"]\'
        WHERE `payment_methods`.`class_name` = \'Webmoney\';
        
        UPDATE `payment_methods` SET `currencies` = \'[\"RUB\"]\'
        WHERE `payment_methods`.`class_name` = \'Yandexmoney\';
        
        UPDATE `payment_methods` SET `currencies` = \'[\"RUB\"]\'
        WHERE `payment_methods`.`class_name` = \'Freekassa\';
        
        UPDATE `payment_methods` SET `currencies` = \'[\"TRY\"]\'
        WHERE `payment_methods`.`class_name` = \'Paytr\';
        
        UPDATE `payment_methods` SET `currencies` = \'[\"TRY\"]\'
        WHERE `payment_methods`.`class_name` = \'Paywant\';
        
        UPDATE `payment_methods` SET `currencies` = \'[\"MYR\"]\'
        WHERE `payment_methods`.`class_name` = \'Billplz\';
        
        UPDATE `payment_methods` SET `currencies` = \'[\"USD\"]\'
        WHERE `payment_methods`.`class_name` = \'Authorize\';
        
        UPDATE `payment_methods` SET `currencies` = \'[\"RUB\"]\'
        WHERE `payment_methods`.`class_name` = \'Yandexcards\';
        
        UPDATE `payment_methods` SET `currencies` = \'[\"USD\", \"EUR\"]\'
        WHERE `payment_methods`.`class_name` = \'Stripe\';
        
        UPDATE `payment_methods` SET `currencies` = \'[\"BRL\"]\'
        WHERE `payment_methods`.`class_name` = \'Mercadopago\';
        
        UPDATE `payment_methods` SET `currencies` = \'[\"USD\"]\'
        WHERE `payment_methods`.`class_name` = \'Paypalstandard\';
        
        UPDATE `payment_methods` SET `currencies` = \'[\"EUR\"]\'
        WHERE `payment_methods`.`class_name` = \'Mollie\';
        
        UPDATE `payment_methods` SET `currencies` = \'[\"USD\", \"EUR\"]\'
        WHERE `payment_methods`.`class_name` = \'Stripe3dSecure\';
        ')->execute();

        $methods = (new Query())
            ->select(['id', 'currencies', 'options', 'position'])
            ->from(DB_STORES . '.payment_methods')
            ->all();

        $count = 0;

        foreach ($methods as $method) {
            $currencies = json_decode($method['currencies'], true);

            foreach ($currencies as $currency) {
                $paymentMethodCurrency = new PaymentMethodsCurrency();
                $paymentMethodCurrency->method_id = $method['id'];
                $paymentMethodCurrency->currency = $currency;
                $paymentMethodCurrency->position = $method['position'];
                $paymentMethodCurrency->created_at = time();
                $paymentMethodCurrency->updated_at = time();
                $paymentMethodCurrency->save(false);

                $count++;
            }
        }
        return $this->stdout("SUCCESS add {$count} currencies\n");
    }

    /**
     * 2) Change settings for store_payment_methods
     * @return string
     * @throws Exception
     * @throws \Throwable
     */
    public function actionApplyStorePay(): string
    {
        if (Yii::$app->db->getTableSchema('{{%store_payment_methods}}', true) === null) {
            return $this->stdout("Table `store_payment_methods` does not exist. Apply SQL first.\n", Console::FG_RED);
        }

        $methods = (new Query())
            ->select('id, method')
            ->from(DB_STORES . '.store_payment_methods')
            ->indexBy('id')
            ->all();

        $count = $delete = 0;

        foreach ($methods as $key => $methodName) {
            $storeMethod = StorePaymentMethods::findOne($key);

            if (!$storeMethod) {
                continue;
            }

            $payMethod = PaymentMethods::findOne(['method_name' => $methodName['method']]);

            if (!$payMethod) {
                $storeMethod->delete();
                $delete++;
                continue;
            }

            $store = (new Query())
                ->from(DB_STORES . '.stores')
                ->where(['id' => $storeMethod['store_id']])
                ->one();

            if (!$store) {
                continue;
            }

            $storeCurrency = PaymentMethodsCurrency::findOne(['method_id' => $payMethod->id, 'currency' => $store['currency']]);

            if (!$storeCurrency) {
                $storeMethod->delete();
                $delete++;
                continue;
            }

            $lastPositions = StorePaymentMethods::find()
                ->where(['store_id' => $storeMethod->store_id])
                ->max('position');

            $storeMethod->method_id = $payMethod->id;
            $storeMethod->currency_id = $storeCurrency->id;
            $storeMethod->name = $payMethod->name;
            $storeMethod->position = isset($lastPositions) ? $lastPositions + 1 : 1;
            $storeMethod->created_at = time();
            $storeMethod->updated_at = time();
            $storeMethod->save(false);

            $count++;
        }

        Yii::$app->db->createCommand('USE `' . DB_STORES . '`; ALTER TABLE `store_payment_methods` DROP COLUMN `method`')->execute();

        return $this->stdout("SUCCESS change in {$count} store_payment_methods settings and delete {$delete} unsupported methods\n");
    }

    /**
     * 3) Add new column to checkout table
     * @return string
     * @throws Exception
     */
    public function actionApplyCheckout(): string
    {
        $stores = (new Query())
            ->select('db_name')
            ->from(DB_STORES . '.stores')
            ->where('db_name is not null')
            ->andWhere('db_name != ""')
            ->all();

        $templateDb = Yii::$app->params['storeDefaultDatabase'];
        $stores[] = ['db_name' => $templateDb];

        $count = 0;
        foreach ($stores as $store) {
            if (Yii::$app->db->getTableSchema($store['db_name'].'.checkouts', true) === null) {
                continue;
            }
            Yii::$app->db->createCommand('USE `' . $store['db_name'] . '`;
                ALTER TABLE `checkouts` ADD `currency_id` int(11) unsigned NULL AFTER `method_id`;)')->execute();

            $count++;
        }

        return $this->stdout("SUCCESS add new column to {$count} checkouts dbs\n");
    }

    /**
     * 4) Update PaymentsMethod IDS and delete duplicates
     * @return string
     * @throws Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionChangePaymentsId(): string
    {
        $paymentMethods = PaymentMethods::find()->all();
        $last = count($paymentMethods);
        $count = $delete = 0;

        foreach ($paymentMethods as $method) {
            $countCurrentMethod = PaymentMethods::find()
                ->where(['method_name' => $method->method_name])
                ->count();

            if ($countCurrentMethod > 1) {
                $method->delete();
                $last--;
                $delete++;
                continue;
            }

            switch ($method->method_name) {
                case 'paypal':
                    static::_checkPaymentMethodId($method, PaymentMethods::METHOD_PAYPAL);
                    break;
                case '2checkout':
                    static::_checkPaymentMethodId($method, PaymentMethods::METHOD_2CHECKOUT);
                    break;
                case 'coinpayments':
                    static::_checkPaymentMethodId($method, PaymentMethods::METHOD_COINPAYMENTS);
                    break;
                case 'pagseguro':
                    static::_checkPaymentMethodId($method, PaymentMethods::METHOD_PAGSEGURO);
                    break;
                case 'webmoney':
                    static::_checkPaymentMethodId($method, PaymentMethods::METHOD_WEBMONEY);
                    break;
                case 'yandexmoney':
                    static::_checkPaymentMethodId($method, PaymentMethods::METHOD_YANDEX_MONEY);
                    break;
                case 'freekassa':
                    static::_checkPaymentMethodId($method, PaymentMethods::METHOD_FREE_KASSA);
                    break;
                case 'paytr':
                    static::_checkPaymentMethodId($method, PaymentMethods::METHOD_PAYTR);
                    break;
                case 'paywant':
                    static::_checkPaymentMethodId($method, PaymentMethods::METHOD_PAYWANT);
                    break;
                case 'billplz':
                    static::_checkPaymentMethodId($method, PaymentMethods::METHOD_BILLPLZ);
                    break;
                case 'authorize':
                    static::_checkPaymentMethodId($method, PaymentMethods::METHOD_AUTHORIZE);
                    break;
                case 'yandexcards':
                    static::_checkPaymentMethodId($method, PaymentMethods::METHOD_YANDEX_CARDS);
                    break;
                case 'stripe':
                    static::_checkPaymentMethodId($method, PaymentMethods::METHOD_STRIPE);
                    break;
                case 'mercadopago':
                    static::_checkPaymentMethodId($method, PaymentMethods::METHOD_MERCADOPAGO);
                    break;
                case 'paypalstandard':
                    static::_checkPaymentMethodId($method, PaymentMethods::METHOD_PAYPAL_STANDARD);
                    break;
                case 'mollie':
                    static::_checkPaymentMethodId($method, PaymentMethods::METHOD_MOLLIE);
                    break;
                case 'stripe_3d_secure':
                    static::_checkPaymentMethodId($method, PaymentMethods::METHOD_STRIPE_3D_SECURE);
                    break;
                default:
                    $method->id = $last + 1;
                    $last++;
            }
            $count++;
        }
        return $this->stdout("SUCCESSFULLY change {$count} PaymentMethods IDS and delete {$delete} duplicates\n");
    }

    /**
     * @param PaymentMethods $method
     * @param int $id
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    protected static function _checkPaymentMethodId(PaymentMethods $method, int $id)
    {
        if ($method->id !== $id) {
            $stores = Stores::find()
                ->where('db_name is not null')
                ->andWhere(['!=', 'db_name', ''])
                ->all();

            $currentId = $method->id;
            $method->id = $id;
            if (!$method->update(false))
            {
                echo $method->method_name . ' - update error: ' . ActiveForm::firstError($method);
                return;
            }

            foreach ($stores as $store) {
                if (Yii::$app->db->getTableSchema($store['db_name'].'.checkouts', true) === null) {
                    continue;
                }
                Yii::$app->db->createCommand()->update($store->db_name . '.checkouts', [
                        'method_id' => $id
                    ], ['method_id' => $currentId])->execute();
            }
        }
    }

    /**
     * 5) Update old checkouts.method_id ID in checkouts to new one
     * @throws Exception
     * @return string
     */
    public function actionCheckoutsDataPrepare(): string
    {
        $count = $storeCount = 0;

        $stores = (new Query())
            ->select('db_name')
            ->from(DB_STORES . '.stores')
            ->where('db_name is not null')
            ->andWhere('db_name != ""')
            ->all();

        foreach ($stores as $store) {
            if (Yii::$app->db->getTableSchema($store['db_name'].'.payments', true) === null) {
                continue;
            }
            $payments = (new Query())
                ->select(['checkout_id', 'method'])
                ->from($store['db_name'] . '.payments')
                ->all();

            foreach ($payments as $payment) {
                if ($payment['method'] == 'twocheckout') {
                    $payment['method'] = '2checkout';
                }
                $method = PaymentMethods::findOne(['method_name' => $payment['method']]);

                if (!isset($method)) {
                    continue;
                }

                if (Yii::$app->db->getTableSchema($store['db_name'].'.checkouts', true) === null) {
                    continue;
                }
                Yii::$app->db->createCommand()->update($store['db_name'] . '.checkouts', [
                    'method_id' => $method->id
                ], ['id' => $payment['checkout_id']])->execute();

                $count++;
            }
            $storeCount++;
        }
        return $this->stdout("SUCCESSFULLY change {$count} checkouts.method_id in {$storeCount} stores.checkouts DB\n");
    }

    /**
     * 6) Rename values in payment_methods.method_name as payment_methods.name
     * @return string
     * @throws Exception
     */
    public function actionRenamePaymentsMethodname(): string
    {
        Yii::$app->db->createCommand('USE `' . DB_STORES . '`; UPDATE payment_methods SET `method_name` = `name`;')->execute();

        return $this->stdout("SUCCESS rename values in method_name \n");
    }

    /**
     * Copy settings from payment_methods_last to store_payment_methods
     * @return string
     * @throws Exception
     * @throws \Throwable
     */
    public function actionCopySettingsToStorepaymethod(): string
    {
        if (Yii::$app->db->getTableSchema('{{%store_payment_methods}}', true) === null) {
            return $this->stdout("Table `store_payment_methods` does not exist. Create it first.\n", Console::FG_RED);
        }

        $methods = (new Query())
            ->from(DB_STORES . '.payment_methods_last')
            ->indexBy('id')
            ->all();

        $payMethods = (new Query())
            ->from(DB_STORES . '.payment_methods')
            ->indexBy('id')
            ->all();

        $stores = (new Query())
            ->from(DB_STORES . '.stores')
            ->indexBy('id')
            ->all();

        $storeCurrencys = (new Query())
            ->from(DB_STORES . '.payment_methods_currency')
            ->indexBy('id')
            ->all();

        $count = $delete = 0;

        foreach ($methods as $key => $methodName) {

            unset($payMethod, $store, $storeCurrency);

            $payMethodId = PaymentMethods::getPaymentsId($methodName['method']);
            if (isset($payMethods[$payMethodId])) {
                $payMethod = $payMethods[$payMethodId];
            }

            if (empty($payMethod)) {
                $delete++;
                $this->stdout("Payment Method {$methodName['method']} do not exist, skip adding settings.\n", Console::FG_RED);
                continue;
            }

            if (isset($stores[$methodName['store_id']])) {
                $store = $stores[$methodName['store_id']];
            }

            if (empty($store)) {
                $delete++;
                $this->stdout("Store ({$methodName['store_id']}) do not exist, skip adding settings.\n", Console::FG_RED);
                continue;
            }


            foreach ($storeCurrencys as $storeCurrencyLocal) {
                if ($storeCurrencyLocal['method_id'] == $payMethodId && $storeCurrencyLocal['currency'] == $store['currency']) {
                    $storeCurrency = $storeCurrencyLocal;
                    break;
                }
            }

            if (empty($storeCurrency)) {
                $delete++;
                $this->stdout("This payment method ({$methodName['method']}) is not supported by the store currency ({$store['currency']}), skip adding settings.\n", Console::FG_RED);
                continue;
            }

            $lastPositions = (new Query())
                ->from(DB_STORES . '.store_payment_methods')
                ->where(['store_id' => $methodName['store_id']])
                ->max('position');


            Yii::$app->db->createCommand()->insert(DB_STORES .'.store_payment_methods', [
                'store_id' => $methodName['store_id'],
                'visibility' => $methodName['active'] ?? 0,
                'options' => $methodName['details'] ?? '',
                'method_id' => $payMethod['id'],
                'currency_id' => $storeCurrency['id'],
                'name' => $payMethod['name'],
                'position' => isset($lastPositions) ? $lastPositions + 1 : 1,
                'created_at' => time(),
                'updated_at' => time(),
            ])->execute();

            $this->stdout("Added new settings for {$payMethod['name']} -> store_id = {$methodName['store_id']} ({$store['currency']}) \n");

            $count++;
        }

        return $this->stdout("SUCCESS add {$count} store_payment_methods settings and skip {$delete} unsupported methods\n", Console::FG_GREEN);
    }

    /**
     * Update "Incorrect phone" message in all store db
     * @return string
     * @throws Exception
     */
    public function actionUpdateStoreMessages()
    {
        $stores = Stores::find()
            ->select(['db_name'])
            ->andWhere('db_name IS NOT NULL')
            ->andWhere('db_name != ""')
            ->asArray()
            ->all();

        $count = 0;

        foreach ($stores as $store) {
            if (Yii::$app->db->getTableSchema($store['db_name'].'.messages', true) === null) {
                continue;
            }
            $rows = Yii::$app->db->createCommand()->update($store['db_name'] . '.messages',
                ['value' => 'Incorrect phone'],
                ['value' => 'Incorrect phone.']
            )->execute();

            $this->stdout("Update {$rows} rows in {$store['db_name']}.messages\n");
            $count++;
        }

        return $this->stdout("Updated Paytr 'Incorrect phone' messages in {$count} dbs\n", Console::FG_GREEN);
    }

    /**
     * Add integrations for stores
     */
    public function actionAddIntegrations()
    {
        $stores = Stores::find()->all();
        $integrations = Integrations::find()->all();

        foreach ($stores as $store) {
            foreach ($integrations as $integration) {
                /** @var Integrations $integration*/
                $storeIntegration = new StoreIntegrations();
                $storeIntegration->integration_id = $integration->id;
                $storeIntegration->store_id = $store->id;
                $storeIntegration->visibility = StoreIntegrations::VISIBILITY_OFF;
                $storeIntegration->position = $integration->position;

                if (!$storeIntegration->save(false)) {
                    $this->stderr('Save error, integration: ' . $integration->name . "\n", Console::FG_RED);
                } else {
                    $this->stderr('Integration saved: ' . $integration->name . "\n", Console::FG_GREEN);
                }
            }
        }
    }

}