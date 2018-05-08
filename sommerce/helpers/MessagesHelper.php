<?php
namespace sommerce\helpers;

use common\models\store\Languages;
use common\models\store\Messages;
use common\models\stores\StoreDefaultMessages;
use Yii;
use common\models\stores\Stores;

/**
 * Class MessagesHelper
 * @package sommerce\helpers
 */
class MessagesHelper {

    /**
     * Sync stores messages uses default stores messages by lang code
     * @param string $defaultLang
     */
    public static function syncStoresMessages(string $defaultLang)
    {
        $messages = StoreDefaultMessages::find()->andWhere([
            'lang_code' => $defaultLang
        ])->asArray()->all();

        static::addStoresMessages($messages);
    }

    /**
     * Add messages to stores
     * @param array $messages
     * @param null|int|array $storeId
     */
    public static function addStoresMessages(array $messages = [], $storeId = null):void
    {
        if (empty($messages)) {
            return;
        }

        $db = Yii::$app->db;

        $storeQuery = Stores::find();

        if ($storeId) {
            $storeQuery->andWhere([
                'id' => $storeId
            ]);
        }

        foreach ($storeQuery->all() as $store) {
            $dbName = $store->db_name;
            $isDbExist = $db->createCommand("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbName'")
                ->queryScalar();

            if (!$isDbExist) {
                continue;
            }

            static::addStoreMessages($store, $messages);
        }
    }

    /**
     * Add messages to store
     * @param Stores $store
     * @param array $messages
     * @param null|string|array $lang
     */
    public static function addStoreMessages(Stores $store, array $messages, $lang = null):void
    {
        Yii::$app->store->setInstance($store);

        $languagesQuery = Languages::find();

        if ($lang) {
            $languagesQuery->andWhere([
                'code' => $lang
            ]);
        }

        foreach ($languagesQuery->all() as $language) {
            foreach ($messages as $message) {
                if (($messageModel = Messages::findOne([
                    'section' => $message['section'],
                    'name' => $message['name'],
                    'lang_code' => $language->code
                ]))) {
                    continue;
                }
                $messageModel = new Messages();
                $messageModel->lang_code = $language->code;
                $messageModel->attributes = $message;

                if (!$messageModel->save(false)) {
                    continue;
                }
            }
        }
    }
}