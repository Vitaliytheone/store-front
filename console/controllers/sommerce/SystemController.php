<?php
namespace console\controllers\sommerce;

use common\models\store\Languages;
use common\models\store\Messages;
use common\models\stores\StoreAdmins;
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
    public function actionAddNewMessages()
    {
        $db = Yii::$app->db;

        $messages = [
            [
                'section' => 'cart',
                'name' => 'payment_description',
                'value' => 'Order #{order_id}',
            ],
            [
                'section' => 'order',
                'name' => 'error.link',
                'value' => 'Incorrect link.',
            ],
        ];

        foreach (Stores::find()->all() as $store) {
            $dbName = $store->db_name;
            $isDbExist = $db->createCommand("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbName'")
                ->queryScalar();

            if (!$isDbExist) {
                continue;
            }

            Yii::$app->store->setInstance($store);

            foreach (Languages::find()->all() as $language) {

                foreach ($messages as $message) {
                    if (($messageModel = Messages::findOne([
                        'section' => $message['section'],
                        'name' => $message['name'],
                        'lang_code' => $language->code
                    ]))) {
                        $this->stderr('Can not add message ' . var_export($messageModel->attributes, true) . " Details: Message already exist\n", Console::FG_RED, Console::UNDERLINE);
                        continue;
                    }
                    $messageModel = new Messages();
                    $messageModel->lang_code = $language->code;
                    $messageModel->attributes = $message;

                    if (!$messageModel->save(false)) {
                        $this->stderr('Can not add message ' . var_export($messageModel->attributes, true) . " Details: " . var_export($messageModel->getErrors(), true) . "\n", Console::FG_RED, Console::UNDERLINE);
                        continue;
                    }

                    $this->stderr('Message was added ' . var_export($messageModel->attributes, true) . ". \n", Console::FG_GREEN, Console::UNDERLINE);
                }
            }
        }
    }
}