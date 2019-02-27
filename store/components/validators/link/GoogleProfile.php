<?php
namespace store\components\validators\link;

use Yii;

/**
 * Class GoogleProfile
 * @package store\components\validators\link
 */
class GoogleProfile extends BaseLinkValidator
{
    public function validate()
    {
        if (FALSE === strpos($this->link, '/')) {
            $this->link = 'google.com/' . $this->link;
        }

        $this->link = preg_replace("/google.[a-z]+/i", "google.com", $this->link);

        if (preg_match('/[a-z]/uis', $this->link)) {
            $this->link = preg_replace("/^(.*?)(plus\.google\.com.*?)/uis", "$2", $this->link);
            $this->link = "https://" . $this->link;
        } else {
            $this->link = "https://" . parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);
        }

        $content = null;

        if (!(preg_match("/https\:\/\/plus\.google\.com\/([0-9]+)(\/)?$/i", $this->link))
            && !(preg_match("/https\:\/\/plus\.google\.com\/\+([a-zа-я0-9-_]+)(\/)?$/ui", $this->link))
            && !(preg_match("/https\:\/\/plus\.google\.com\/communities\/([0-9]+)(\/)?$/ui", $this->link))
            && !(preg_match("/https\:\/\/plus\.google\.com\/collection\/([a-z0-9]+)(\/)?$/ui", $this->link))) {
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