<?php
namespace sommerce\components\validators\link;

use Yii;

/**
 * Class TumblrProfile
 * @package sommerce\components\validators\link
 */
class TumblrProfile extends BaseLinkValidator
{
    public function validate()
    {
        $this->link = strtolower($this->link);

        if (FALSE === strpos($this->link, 'tumblr.com')) {
            $this->link = $this->link . '.tumblr.com';
        }

        $this->link = "http://" . parse_url($this->link, PHP_URL_PATH);

        $content = null;
        if (!(preg_match("/http\:\/\/([a-z0-9-]+)\.tumblr\.com(\/)?$/i", $this->link))) {
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