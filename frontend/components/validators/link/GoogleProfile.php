<?php

namespace frontend\components\validators\link;


class GoogleProfile extends BaseLinkValidator
{
    public function validate()
    {
        if (FALSE === strpos($this->link, '/')) {
            $this->link = 'google.com/' . $this->link;
        }

        $this->link = preg_replace("/google.[a-z]+/i", "google.com", $this->link);

        $this->link = "https://" . parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);

        $content = null;

        if (!(preg_match("/https\:\/\/plus\.google\.com\/([0-9]+)(\/)?$/i", $this->link))
            && !(preg_match("/https\:\/\/plus\.google\.com\/\+([a-z0-9-]+)(\/)?$/i", $this->link))) {
            $this->addError('Invalid Google+ profile link.');

            return false;
        } else if (!($content = $this->checkUrl($this->link))) {
            $this->addError('Invalid Google+ profile link.');

            return false;
        }

        return true;
    }
}