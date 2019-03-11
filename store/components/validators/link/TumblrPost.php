<?php
namespace store\components\validators\link;

use Yii;

/**
 * Class TumblrPost
 * @package store\components\validators\link
 */
class TumblrPost extends BaseLinkValidator
{
    public function validate()
    {
        $this->link = "https://" . parse_url($this->link, PHP_URL_PATH);

        $content = null;

        if (!(preg_match("/https\:\/\/([a-z0-9-]+)\.tumblr\.com\/(post|image)\/([0-9]+)(\/)?$/i", $this->link))
            && !(preg_match("/https\:\/\/([a-z0-9-]+)\.tumblr\.com\/(post|image)\/([0-9]+)\/.*?(\/)?$/i", $this->link))) {
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