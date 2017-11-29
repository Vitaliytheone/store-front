<?php

namespace frontend\components\validators\link;


class PinterestPost extends BaseLinkValidator
{
    public function validate()
    {
        $this->link = "https://www." . parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);

        $content = null;

        if (!(preg_match("/https\:\/\/www\.pinterest\.com\/pin\/([0-9]+)(\/)?$/i", $this->link))) {
            $this->addError('Invalid pinterest post link.');

            return false;
        } else if (!($content = $this->checkUrl($this->link))) {
            $this->addError('Invalid pinterest post link.');

            return false;
        }

        return true;
    }
}