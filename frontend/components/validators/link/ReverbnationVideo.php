<?php

namespace frontend\components\validators\link;


class ReverbnationVideo extends BaseLinkValidator
{
    public function validate()
    {
        $this->link = "https://www." . parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);

        $content = null;

        if (!(preg_match("/https\:\/\/www\.reverbnation\.com\/artist\/video\/([0-9]+)(\/)?$/i", $this->link))) {
            $this->addError('Invalid reverbnation video link.');

            return false;
        } else if (!($content = $this->checkUrl($this->link))) {
            $this->addError('Invalid reverbnation video link.');

            return false;
        }

        return true;
    }
}