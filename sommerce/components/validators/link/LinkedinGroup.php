<?php
namespace sommerce\components\validators\link;

use Yii;

/**
 * Class LinkedinGroup
 * @package sommerce\components\validators\link
 */
class LinkedinGroup extends BaseLinkValidator
{
    public function validate()
    {
        $this->link = preg_replace("/[a-z]+\.linkedin/i", "linkedin", $this->link);

        $this->link = "https://www." . parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);

        $content = null;

        if (!(preg_match("/https\:\/\/www\.linkedin\.com\/groups\/([0-9]+)(\/profile)?(\/)?$/i", $this->link))) {
            $this->addError(Yii::t('app', 'order.error.link', [
                'name' => $this->name
            ]));

            return false;
        } else if (!($content = $this->checkUrl($this->link))) {
            $this->addError(Yii::t('app', 'order.error.link', [
                'name' => $this->name
            ]));
        }

        return true;
    }
}