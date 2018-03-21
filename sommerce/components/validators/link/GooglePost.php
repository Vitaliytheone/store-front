<?php

namespace sommerce\components\validators\link;


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
            $this->addError('Invalid Google+ post link.');
        } else if (!($content = $this->checkUrl($this->link))) {
            $this->addError('Invalid Google+ post link.');
        }

        return $this->link;
    }
}
