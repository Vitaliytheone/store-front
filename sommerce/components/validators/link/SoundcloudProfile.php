<?php
namespace sommerce\components\validators\link;

use Yii;

/**
 * Class SoundcloudProfile
 * @package sommerce\components\validators\link
 */
class SoundcloudProfile extends BaseLinkValidator
{
    public function validate()
    {
        if (FALSE === strpos($this->link, '/')) {
            $this->link = 'soundcloud.com/' . $this->link;
        }

        $this->link = "https://" . parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);

        $content = null;

        if (!(preg_match("/https\:\/\/soundcloud\.com\/([a-z0-9\_-]+)(\/)?$/i", $this->link, $match))) {
            $this->addError(Yii::t('app', 'order.invalid_link', [
                'name' => $this->name
            ]));

            return false;
        } else if (!($content = $this->checkUrl($this->link, [
            'ssl' => true
        ]))) {
            $this->addError(Yii::t('app', 'order.invalid_link', [
                'name' => $this->name
            ]));

            return false;
        }

        return true;
    }
}