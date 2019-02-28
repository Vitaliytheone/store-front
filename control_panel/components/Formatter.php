<?php

namespace control_panel\components;

use Yii;

/**
 * Class Formatter
 * @package control_panel\components
 */
class Formatter extends \yii\i18n\Formatter {
    public function init()
    {
        if ($this->timeZone === null) {
            $this->timeZone = Yii::$app->timeZone;
        }
        if ($this->locale === null) {
            $this->locale = Yii::$app->language;
        }
        if ($this->booleanFormat === null) {
            $this->booleanFormat = [Yii::t('yii', 'No', [], $this->locale), Yii::t('yii', 'Yes', [], $this->locale)];
        }
        if ($this->nullDisplay === null) {
            $this->nullDisplay = '<span class="not-set">' . Yii::t('yii', '(not set)', [], $this->locale) . '</span>';
        }

        if ($this->decimalSeparator === null) {
            $this->decimalSeparator = '.';
        }
        if ($this->thousandSeparator === null) {
            $this->thousandSeparator = ',';
        }
    }
}