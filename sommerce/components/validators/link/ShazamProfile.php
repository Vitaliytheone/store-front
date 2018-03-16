<?php

namespace sommerce\components\validators\link;


class ShazamProfile extends BaseLinkValidator
{
    public function validate()
    {
        $this->link = "https://www." . parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);

        $content = null;

        if (!(preg_match("/https\:\/\/www\.shazam\.com\/([a-z]{2}\/)?artist\/([0-9]+)(\/.*?)?(\/)?$/i", $this->link))) {
            $this->addError('Invalid shazam profile link.');

            return false;
        } else if (!($content = $this->checkUrl($this->link))) {
            $this->addError('Invalid shazam profile link.');

            return false;
        }

        return true;
    }
}