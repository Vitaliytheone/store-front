<?php
namespace console\controllers\sommerce;

use common\models\panels\AdditionalServices;
use common\models\store\Languages;
use common\models\store\Messages;
use common\models\store\Packages;
use common\models\store\Suborders;
use common\models\stores\StoreAdmins;
use common\models\stores\StoreProviders;
use sommerce\helpers\MessagesHelper;
use yii\db\Query;
use yii\helpers\Console;
use common\models\stores\Stores;
use sommerce\helpers\StoreHelper;
use Yii;
use yii\helpers\FileHelper;

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
        if (!empty(Yii::$app->params['sommerce.twig.cachePath'])) {
            $path = Yii::getAlias(Yii::$app->params['sommerce.twig.cachePath']);
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
        $path = Yii::getAlias(Yii::$app->params['sommerce.assets.cachePath']);

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


    public function actionChangeProvidersId()
    {
        $stores = (new \yii\db\Query())->select([
            'db_name',
        ])->from(DB_STORES . '.stores')->all();

        foreach ((new \yii\db\Query())->select([
                'id',
                'site'
        ])->from(DB_STORES . '.providers')->all() as $provider) {
            $res = (new \yii\db\Query())
                ->select(['res'])->from(AdditionalServices::tableName())
                ->where([
                    'name' => $provider['site'],
                    'store' => 1,
                    'status' =>  0
                ])->one()['res'];

            $store_tables = [
                'suborders',
                'packages'
            ];

            $StoreProvidersTable = StoreProviders::tableName();

            Yii::$app->db->createCommand("UPDATE {$StoreProvidersTable} SET `provider_id` = '" . $res. "' WHERE `provider_id` = '" . $provider['id'] . "';")->execute();
            foreach ($stores as $store) {
                foreach ($store_tables as $table) {
                    Yii::$app->db->createCommand("UPDATE `{$store['db_name']}`.`{$table}` SET `provider_id` = '" . $res. "' WHERE `provider_id` = '" . $provider['id'] . "';")->execute();
                }
            }
        }
    }
}