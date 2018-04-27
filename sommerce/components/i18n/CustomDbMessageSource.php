<?php

namespace sommerce\components\i18n;

use common\models\store\Languages;
use common\models\store\Messages;
use common\models\stores\StoreDefaultMessages;
use common\models\stores\Stores;
use sommerce\modules\admin\helpers\LanguagesHelper;
use yii\base\InvalidConfigException;
use yii\db\Connection;
use yii\di\Instance;
use yii\helpers\ArrayHelper;
use yii\i18n\DbMessageSource;

/**
 * Class CustomDbMessageSource
 * @package sommerce\components\i18n
 */
class CustomDbMessageSource extends DbMessageSource
{

    /**
     * @var Connection|array|string the DB connection object or the application component ID of the Store DB connection.
     *
     * After the DbMessageSource object is created, if you want to change this property, you should only assign
     * it with a DB connection object.*
     */
    public $storeDb = 'storeDb';

    /**
     * @var Connection|array|string the DB connection object or the application component ID of the Store DB connection.
     *
     * After the DbMessageSource object is created, if you want to change this property, you should only assign
     * it with a DB connection object.*
     */
    public $db = 'db';

    /**
     * Initializes the DbMessageSource component.
     * This method will initialize the [[db]] property to make sure it refers to a valid DB connection.
     * Configured [[cache]] component would also be initialized.
     * @throws InvalidConfigException if [[db]] is invalid or [[cache]] is invalid.
     */
    public function init()
    {
        parent::init();

        $this->db = Instance::ensure($this->db, Connection::class);
        if ($this->enableCaching) {
            $this->cache = Instance::ensure($this->cache, 'yii\caching\CacheInterface');
        }

        $this->storeDb = Instance::ensure($this->storeDb, Connection::class);
        if ($this->enableCaching) {
            $this->cache = Instance::ensure($this->cache, 'yii\caching\CacheInterface');
        }
    }

    /**
     * Loads the messages from database.
     * You may override this method to customize the message storage in the database.
     * @param string $category the message category.
     * @param string $language the target language.
     * @return array the messages loaded from database.
     */
    protected function loadMessagesFromDb($category, $language)
    {
        if (Languages::findOne(['code' => $language])) {
            $messages = Messages::find()
                ->select(['message' => "CONCAT(`section`, '.' ,`name`)", "translation" => 'value'])
                ->where(['lang_code' => $language])
                ->asArray()
                ->all();
        } elseif (in_array($language, LanguagesHelper::getDefaultLanguages())) {
            $messages = StoreDefaultMessages::find()
                ->select(['message' => "CONCAT(`section`, '.' ,`name`)", "translation" => 'value'])
                ->where(['lang_code' => $language])
                ->asArray()
                ->all();
        } else {
            $messages = StoreDefaultMessages::find()
                ->select(['message' => "CONCAT(`section`, '.' ,`name`)", "translation" => 'value'])
                ->where(['lang_code' => Stores::getDefaultLanguage()])
                ->asArray()
                ->all();
        }

        return ArrayHelper::map($messages, 'message', 'translation');
    }
}