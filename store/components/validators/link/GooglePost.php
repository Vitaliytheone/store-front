<?php
namespace store\components\validators\link;

use Yii;

/**
 * Class GooglePost
 * @package store\components\validators\link
 */
class GooglePost extends BaseLinkValidator
{
    public function validate()
    {
        $this->link = preg_replace("/google.[a-z]+/i", "google.com", $this->link);

        $this->link = "https://" . parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);

        $content = null;

        if (!(preg_match("/https\:\/\/plus\.google\.com\/([0-9]+)\/posts\/([a-z0-9]+)$/i", $this->link))
            && !(preg_match("/https\:\/\/plus\.google\.com\/\+([a-z0-9-]+)\/posts\/([a-z0-9]+)$/i", $this->link))
            && !(preg_match("/https\:\/\/plus\.google\.com\/u\/0\/([0-9]+)\/posts\/([a-z0-9]+)$/i", $this->link))) {
            $this->addError(Yii::t('app', 'order.error.link', [
                'name' => $this->name
            ]));
        } else if (!($content = $this->checkUrl($this->link))) {
            $this->addError(Yii::t('app', 'order.error.link', [
                'name' => $this->name
            ]));
        }

        return $this->link;
    }
}
