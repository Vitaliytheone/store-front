<?php
namespace sommerce\modules\admin\helpers;

use common\models\store\Languages;
use common\models\store\Messages;
use common\models\stores\StoreDefaultMessages;
use common\models\stores\Stores;
use Yii;
use yii\db\Exception;
use yii\helpers\ArrayHelper;

/**
 * Class LanguagesHelper
 * @package sommerce\modules\admin\helpers
 */
class LanguagesHelper
{
    /**
     * Return all languages list
     * @param $withNamesOnly bool Return lang_code => lang_name arrays if true
     * @return array
     */
    public static function getConfigLanguagesList($withNamesOnly = false)
    {
        $languages =  Yii::$app->params['languages'];

        return $withNamesOnly ? ArrayHelper::getColumn($languages, 'name', true) : array_keys($languages);
    }

    /**
     * Return config language params (`rtl`, `name`)  or single param (i.e. `rtl`)
     * @param $langCode string language code
     * @param $param string language param
     * @return mixed
     */
    public static function getConfigLanguageParams($langCode, $param = null) {
        $languages = Yii::$app->params['languages'];

        $path[] = $langCode;

        if ($param) {
            $path[] = $param;
        }

        return ArrayHelper::getValue($languages, $path, null);
    }

    /**
     * Return available default languages list
     * @return array
     */
    public static function getDefaultLanguages()
    {
        return StoreDefaultMessages::find()
            ->select(['lang_code'])
            ->groupBy(['lang_code'])
            ->column();
    }

    /**
     * Return Store language RTL
     * @param Stores $store
     * @return string
     */
    public static function getLanguageRtl(Stores $store)
    {
        $storeLanguage = Languages::findOne(['code' => $store->language]);

        return $storeLanguage ? $storeLanguage->rtl : static::getConfigLanguageParams($store->language, 'rtl');
    }

    /**
     * Return default messages list for $languageCode
     * @param $languageCode
     * @return array
     */
    public static function getDefaultMessages($languageCode)
    {
        return StoreDefaultMessages::find()
            ->select(['value', 'key' => "CONCAT(section, '.', name)"])
            ->andWhere(['lang_code' => $languageCode])
            ->indexBy('key')
            ->asArray()
            ->column();
    }

    /**
     * Create store language & language messages
     * @param Stores $store
     * @param $languageCode
     * @return Languages|null
     * @throws Exception
     */
    public static function createStoreLanguage(Stores $store, $languageCode)
    {
        if (empty($store) || !$store instanceof Stores) {
            return null;
        }

        $configLanguages = static::getConfigLanguagesList();

        if (!in_array($languageCode, array_keys($configLanguages))) {
            return null;
        }

        if (Languages::findOne(['code' => $languageCode])) {
            return null;
        }

        $language = new Languages();
        $language->code = $languageCode;
        $language->rtl = static::getConfigLanguageParams($languageCode, 'rtl');

        if (!$language->save(false)) {
            return null;
        }

        // Create requested language messages from available default language
        // or from store default language
        $sourceLanguageCode = $languageCode;

        if (!in_array($languageCode, static::getDefaultLanguages())) {
            $sourceLanguageCode = $store->getDefaultLanguage();
        }

        $defaultMessages = StoreDefaultMessages::find()
            ->andWhere(['lang_code' => $sourceLanguageCode])
            ->all();

        /** @var StoreDefaultMessages $defaultMessage */
        foreach ($defaultMessages as $defaultMessage) {
            $message = new Messages();
            $message->lang_code = $languageCode;
            $message->section = $defaultMessage->section;
            $message->value = $defaultMessage->value;
            $message->name = $defaultMessage->name;

            if (!$message->save(false)) {
                throw new Exception('Error on create store language message!');
            }
        }

        return $language;
    }
}