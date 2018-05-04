<?php
namespace sommerce\components\validators\link;

use Yii;

/**
 * Class InstagramPost
 * @package sommerce\components\validators\link
 */
class InstagramPost extends BaseLinkValidator
{
    public function validate()
    {
        $this->link = "https://www." . parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);

        $content = null;

        if (!(preg_match("/https\:\/\/www\.instagram\.com\/p\/([a-z0-9_-]+)(\/)?$/i", $this->link))) {
            $this->addError(Yii::t('app', 'order.error.link', [
                'name' => $this->name
            ]));

            return false;
        } else if (!($content = $this->checkUrl($this->link . '?hl=en'))) {
            $this->addError(Yii::t('app', 'order.error.link', [
                'name' => $this->name
            ]));

            return false;
        }

        return true;
    }
}