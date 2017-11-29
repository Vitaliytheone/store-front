<?php

namespace frontend\components\validators\link;


class TwitterPost extends BaseLinkValidator
{
    public function validate()
    {
        $this->link = preg_replace("/(mobile\.)/i", "", $this->link);

        $this->link = "https://" . parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);

        $content = null;

        if (!(preg_match("/https\:\/\/twitter\.com\/([a-z0-9\_]{1,15})\/status\/([0-9]+)(\/)$?/i", $this->link))
            && !(preg_match("/https\:\/\/twitter\.com\/statuses\/([0-9]+)(\/)?$/i", $this->link))
            && !(preg_match("/https\:\/\/t\.co\/([a-z0-9-_]+)(\/)?$/i", $this->link))
            && !(preg_match("/https\:\/\/twitter\.com\/i\/web\/status\/([0-9]+)(\/)?$/i", $this->link))) {
            $this->addError('Invalid twitter post link.');

            return false;
        } else if (!($content = $this->checkUrl($this->link))) {
            $this->addError('Invalid twitter post link.');

            return false;
        }

        return true;
    }
}