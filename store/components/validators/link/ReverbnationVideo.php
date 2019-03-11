<?php
namespace store\components\validators\link;

use Yii;

/**
 * Class ReverbnationVideo
 * @package store\components\validators\link
 */
class ReverbnationVideo extends BaseLinkValidator
{
    public function validate()
    {
        $this->link = "https://www." . parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);

        $content = null;

        if (!(preg_match("/https\:\/\/www\.reverbnation\.com\/artist\/video\/([0-9]+)(\/)?$/i", $this->link))
            && !(preg_match("/https\:\/\/www\.reverbnation\.com\/collection\/([^\/]+)(\/)?$/i", $this->link))) {
            $this->addError(Yii::t('app', 'order.error.link', [
                'name' => $this->name
            ]));

            return false;
        } else if (!($content = $this->checkUrl($this->link))) {
            $this->addError(Yii::t('app', 'order.error.link', [
                'name' => $this->name
            ]));

            return false;
        }

        return true;
    }
}