<?php
namespace sommerce\components\validators\link;

use Yii;

/**
 * Class LinkedinPost
 * @package sommerce\components\validators\link
 */
class LinkedinPost extends BaseLinkValidator
{
    public function validate()
    {
        $this->link = preg_replace("/[a-z]+\.linkedin/i", "linkedin", $this->link);

        $this->link = "https://www." . parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);

        $content = null;

        if (!(preg_match("/https\:\/\/www\.linkedin\.com\/hp\/update\/([0-9]+)(\/)?$/i", $this->link))
            && !(preg_match("/https\:\/\/www\.linkedin\.com\/feed\/update\/([a-z\:]+)([0-9]+)(\/)?$/i", $this->link))
            && !(preg_match("/https\:\/\/www\.linkedin\.com\/pulse\/([^\/]+)(\/)?$/i", $this->link))
            && !(preg_match("/https\:\/\/www\.linkedin\.com\/hp\/update\/([0-9]+)(\/)?$/i", $this->link))) {
            $this->addError(Yii::t('app', 'order.invalid_link', [
                'name' => $this->name
            ]));

            return false;
        } else if (!($content = $this->checkUrl($this->link))) {
            $this->addError(Yii::t('app', 'order.invalid_link', [
                'name' => $this->name
            ]));
        }

        return true;
    }
}