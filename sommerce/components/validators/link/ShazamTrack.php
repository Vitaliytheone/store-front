<?php

namespace sommerce\components\validators\link;


class ShazamTrack extends BaseLinkValidator
{
    public function validate()
    {
        $this->link = "https://www." . parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);

        $content = null;

        if (!(preg_match("/https\:\/\/www\.shazam\.com\/([a-z]{2}\/)?track\/([0-9]+)(\/.*?)?(\/)?$/iu", $this->link))) {
            $this->addError('Invalid shazam track link.');

            return false;
        } else if (!($content = $this->checkUrl($this->link))) {
            $this->addError('Invalid shazam track link.');

            return false;
        }

        return true;
    }
}