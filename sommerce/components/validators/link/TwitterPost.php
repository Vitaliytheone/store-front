<?php
namespace sommerce\components\validators\link;

use Yii;

/**
 * Class TwitterPost
 * @package sommerce\components\validators\link
 */
class TwitterPost extends BaseLinkValidator
{
    public function validate()
    {
        $this->link = preg_replace("/(mobile\.)/i", "", $this->link);

        $this->link = "https://" . parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);

        $content = null;

        if (!(preg_match("/https\:\/\/twitter\.com\/([a-z0-9\_]+)\/status\/([0-9]+)(\/)?$/i", $this->link))
            && !(preg_match("/https\:\/\/twitter\.com\/statuses\/([0-9]+)(\/)?$/i", $this->link))
            && !(preg_match("/https\:\/\/t\.co\/([a-z0-9-_]+)(\/)?$/i", $this->link))
            && !(preg_match("/https\:\/\/twitter\.com\/i\/web\/status\/([0-9]+)(\/)?$/i", $this->link))) {
            $this->addError(Yii::t('app', 'order.invalid_link', [
                'name' => $this->name
            ]));

            return false;
        } else if (!($content = $this->checkUrl($this->link))) {
            $this->addError(Yii::t('app', 'order.invalid_link', [
                'name' => $this->name
            ]));

            return false;
        }

        return true;
    }
}