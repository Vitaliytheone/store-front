<?php

namespace frontend\components\i18n;

use common\models\stores\Stores;
use Yii;
use yii\helpers\ArrayHelper;
use yii\i18n\PhpMessageSource;

/**
 * Class CustomMessageSource
 * PhpMessageSource represents a message source that stores translated messages in PHP scripts.
 * при запросе страницы магазина:
 * смотрим его текущий язык
 * проверяем существует ли файл в папке пользовательского языка,
 * если не существует проверяем существует ли дефолтный язык для магазина,
 * если не существует используем язык из дефолтной папки и конфига params default_language
 *
 * @package frontend\components\i18n
 */
class CustomMessageSource extends PhpMessageSource
{
    const STORE_LANG_FOLDER_PREFIX = 'store_';
    const DEFAULT_LANG_FOLDER = 'default';

    /** @var  Stores */
    private $_store;

    private $_default_language;

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub

        $this->_store = Yii::$app->store->getInstance();
        $this->_default_language = ArrayHelper::getValue(Yii::$app->params, 'default_language', null);

        if (!$this->_default_language) {
            Yii::error("Default language does not configured yet!", __METHOD__);
        }
    }

    /**
     * Loads the message translation for the specified $language.
     * @param string $category the message category
     * @param string $language the target language
     * @return array the loaded messages. The keys are original messages, and the values are the translated messages.
     * @see sourceLanguage
     */
    protected function loadMessages($category, $language)
    {
        $messageFile = $this->getMessageFilePath($category, $language);

        if (!$messageFile) {
            Yii::error("The message file for language $language does not exist!", __METHOD__);
            return [];
        }

        $messages = $this->loadMessagesFromFile($messageFile);

        return (array) $messages;
    }

    /**
     * Returns message file path for the specified language
     *
     * @param string $category the message category
     * @param string $language the target language
     * @return string path to message file
     */
    protected function getMessageFilePath($category, $language)
    {
        $basePath = Yii::getAlias($this->basePath);

        $storeFolderName = self::STORE_LANG_FOLDER_PREFIX . $this->_store->id;

        // проверяем существует ли файл в папке пользовательского языка
        $messageFile = $basePath . '/' . $storeFolderName . '/' . $language . '.php';
        if (file_exists($messageFile)) {

            return $messageFile;
        }

        // если не существует проверяем существует ли дефолтный язык для магазина
        $messageFile = $basePath . '/' . self::DEFAULT_LANG_FOLDER . '/' . $language . '.php';
        if (file_exists($messageFile)) {

            return $messageFile;
        }

        // если не существует используем язык из дефолтной папки и конфига param
        $messageFile = $basePath . '/' . self::DEFAULT_LANG_FOLDER . '/' . $this->_default_language . '.php';
        if (file_exists($messageFile)) {

            return $messageFile;
        }

        return false;
    }

    /**
     * Loads the message translation for the specified language and category or returns null if file doesn't exist.
     *
     * @param string $messageFile path to message file
     * @return array|null array of messages or null if file not found
     */
    protected function loadMessagesFromFile($messageFile)
    {
        if (is_file($messageFile)) {
            $messages = include($messageFile);
            if (!is_array($messages)) {
                $messages = [];
            }

            return $messages;
        } else {
            return null;
        }
    }
}