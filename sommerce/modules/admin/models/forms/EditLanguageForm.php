<?php

namespace sommerce\modules\admin\models\forms;

use common\models\store\Languages;
use common\models\store\Messages;
use common\models\stores\StoreDefaultMessages;
use common\models\stores\Stores;
use sommerce\modules\admin\helpers\LanguagesHelper;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * Class EditLanguageForm
 * @property $code
 * @package sommerce\modules\admin\models\forms
 */
class EditLanguageForm extends Model
{
    const MESSAGE_STRING_MAX_LENGTH = 2000;

    /** @var string Language code */
    public $code;

    /** @var array Messages Form-key-values */
    public $messages = [];

    /** @var  Languages|null cached language for current code */
    private $_language;

    /** @var  array Grouped by sections messages for current language */
    private $_mesagesBySection = [];

    /** @var array cached store message for current language */
    private $_languageMessages = [];

    /** @var array cached default language messages. Used for fill form placeholders */
    private $_defaultMessages = [];

    /** @var Stores */
    private $_store;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code','messages'], 'required'],

            ['code', 'string', 'max' => 5],
            ['code', 'in', 'range' => LanguagesHelper::getConfigLanguagesList()],

            ['messages', 'filter', 'filter' => function($messages){
                array_walk($messages, function(&$message){
                    $message = trim($message);
                    $message = mb_strlen($message) > self::MESSAGE_STRING_MAX_LENGTH ?
                        mb_substr($message, 0, self::MESSAGE_STRING_MAX_LENGTH - 3) . '...' :
                        $message;
                });
                return $messages;
            }]
        ];
    }

    /**
     * Set current store
     * @param Stores $store
     */
    public function setStore(Stores $store)
    {
        $this->_store = $store;
    }

    /**
     * Return current store
     * @return Stores|null
     */
    public function getStore()
    {
        return $this->_store;
    }

    /**
     * Return language for current language code
     * @return null|Languages
     */
    public function getLanguage()
    {
        if (empty($this->_language) || !$this->_language instanceof Languages) {
            $this->_language = Languages::findOne(['code' => $this->code]);
        }

        return $this->_language;
    }

    /**
     * Return cached language messages
     * @return array
     */
    public function getLanguageMessages()
    {
        if (empty($this->_languageMessages) || !is_array($this->_languageMessages)) {
            $this->_languageMessages = Messages::getMessagesByLanguageCode($this->code);
        }

        return $this->_languageMessages;
    }

    /**
     * Return store default language messages list
     * Used for fill edit-form messages placeholders
     * @return array|StoreDefaultMessages[]
     */
    public function getDefaultMessages()
    {
        if (empty($this->_defaultMessages) || !is_array($this->_languageMessages)) {
            $this->_defaultMessages = LanguagesHelper::getDefaultMessages($this->getStore()->getDefaultLanguage());
        }

        return $this->_defaultMessages;
    }

    /**
     * Return language messages grouped by section
     * @return array
     */
    public function getMessagesBySection()
    {
        return $this->_mesagesBySection;
    }

    /**
     * Fetch language message from DB
     * Create new lang and lang messages from defaults if lang not exist yet
     * @return bool
     */
    public function fetchMessages()
    {
        if (empty($this->getStore()) || !$this->getStore() instanceof Stores) {
            return false;
        }

        $store = $this->getStore();

        if (!$this->validate('code')) {
            return false;
        }

        /** Create store language/messages if not exist yet */
        if (!$this->getLanguage() &&
            !LanguagesHelper::createStoreLanguage($store, $this->code)
        ) {
            return false;
        }

        $messages = $this->getLanguageMessages();

        // Make grouped by `section` messages
        $sections = Messages::getSections();

        $sectionOrder = [
            Messages::SECTION_ORDER,
            Messages::SECTION_CART,
            Messages::SECTION_CHECKOUT,
            Messages::SECTION_PRODUCT,
            Messages::SECTION_CONTACT,
            Messages::SECTION_FOOTER,
            Messages::SECTION_PAYMENT_RESULT,
            Messages::SECTION_404,
            Messages::SECTION_ORDERS,
            Messages::SECTION_VIEW_ORDER,
        ];

        $this->_mesagesBySection = array_flip($sectionOrder);

        foreach ($messages as $message) {
            $section = $message['section'];
            $messageKey = $message['section'] . '.' . $message['name'];
            $messageValue = isset($this->messages[$messageKey]) ? $this->messages[$messageKey] : $message['value'];

            if (!is_array($this->_mesagesBySection[$section])) {
                $this->_mesagesBySection[$section] = [];
            }

            $this->_mesagesBySection[$section]['name'] = $sections[$section];
            $this->_mesagesBySection[$section]['messages'][$messageKey]['message'] = $messageValue;
            $this->_mesagesBySection[$section]['messages'][$messageKey]['default'] = ArrayHelper::getValue($this->getDefaultMessages(), $messageKey, '');
        }

        return true;
    }

    /**
     * Save loaded data
     * @param $postData
     * @return bool
     */
    public function save($postData)
    {
        if (empty($this->getStore()) || !$this->getStore() instanceof Stores) {
            return false;
        }

        if (!$this->load($postData) || !$this->validate()) {
            return false;
        }

        $language = $this->getLanguage();

        if (empty($language) || !$language instanceof Languages) {
            return false;
        }

        $implodedMessages = null;

        foreach ($this->getLanguageMessages() as $id => $message) {

            $messageKey = $message['section'] . '.' . $message['name'];
            $postedMessageValue = ArrayHelper::getValue($this->messages, $messageKey);

            // Implode only messages which has been changed
            if ($message['value'] == $postedMessageValue) {
                continue;
            }

            $postedMessageValue = (Messages::getDb()->quoteValue($postedMessageValue));

            $implodedMessages = ($implodedMessages ? $implodedMessages . ',' : '') . "('$id', $postedMessageValue)";
        }

        if (!$implodedMessages) {
            return false;
        }

        Messages::getDb()->createCommand('
                INSERT INTO ' . Messages::tableName() . '(`id`, `value`)
                VALUES ' . $implodedMessages . '
                ON DUPLICATE KEY UPDATE
                `value` = VALUES(`value`)
        ')->execute();

        $language->updated_at = time();

        return $language->update(false);
    }
}