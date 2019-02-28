<?php
namespace store\components\i18n;

use Yii;
use yii\i18n\MissingTranslationEvent;

/**
 * Class TranslationEventHandler
 * @package store\components\i18n
 */
class TranslationEventHandler
{
    public static function handleMissingTranslation(MissingTranslationEvent $event) {
        $event->translatedMessage = Yii::t($event->category, $event->message, [], 'en-US');
    }
}