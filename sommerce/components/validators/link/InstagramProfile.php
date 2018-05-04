<?php
namespace sommerce\components\validators\link;

use Yii;

/**
 * Class InstagramProfile
 * @package sommerce\components\validators\link
 */
class InstagramProfile extends BaseLinkValidator
{
    public function validate()
    {
        if (FALSE === strpos($this->link, '/')) {
            $this->link = ltrim($this->link, '@');
            $this->link = 'instagram.com/' . $this->link;
        }

        $this->link = "https://www." . parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);

        $content = null;

        if (!(preg_match("/https\:\/\/www\.instagram\.com\/([a-z0-9\.\_]+)(\/)?$/i", $this->link))) {
            $this->addError(Yii::t('app', 'order.invalid_link', [
                'name' => $this->name
            ]));

            return false;
        } else if (!($content = $this->checkUrl($this->link . '?hl=en', [
            'headers' => [
                'Accept-Encoding' => '',
            ]
        ]))) {
            $this->addError(Yii::t('app', 'order.invalid_link', [
                'name' => $this->name
            ]));

            return false;
        } else if (false !== strpos($content, '"is_private":true')) {
            $this->addError(Yii::t('app', 'order.invalid_link', [
                'name' => $this->name
            ]));

            return false;
        }

        return true;
    }
}