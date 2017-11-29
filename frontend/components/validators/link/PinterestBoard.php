<?php

namespace frontend\components\validators\link;


class PinterestBoard extends BaseLinkValidator
{
    public function validate()
    {
        $this->link = "https://www." . parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);

        $content = null;

        if (!(preg_match("/https\:\/\/www\.pinterest\.com\/([a-z0-9\.\_-]+)\/(.*?)(\/)?$/i", $this->link))) {
            $this->addError('Invalid pinterest board link.');

            return false;
        } else if (!($content = $this->checkUrl($this->link))) {
            $this->addError('Invalid pinterest board link.');

            return false;
        }

        return true;
    }
}