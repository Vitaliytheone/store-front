<?php

namespace sommerce\components\validators\link;


class InstagramPost extends BaseLinkValidator
{
    public function validate()
    {
        $this->link = "https://www." . parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);

        $content = null;

        if (!(preg_match("/https\:\/\/www\.instagram\.com\/p\/([a-z0-9_]+)(\/)?$/i", $this->link))) {
            $this->addError('Invalid instagram post link.');

            return false;
        } else if (!($content = $this->checkUrl($this->link . '?hl=en'))) {
            $this->addError('Invalid instagram post link.');

            return false;
        }

        return true;
    }
}