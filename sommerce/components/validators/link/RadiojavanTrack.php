<?php

namespace sommerce\components\validators\link;


class RadiojavanTrack extends BaseLinkValidator
{
    public function validate()
    {
        $this->link = "https://www." . parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);

        $content = null;

        if (!(preg_match("/https\:\/\/www\.radiojavan\.com\/mp3s\/mp3\/([a-z0-9\-]+)(\/)?$/i", $this->link))) {
            $this->addError('Invalid radiojavan track link.');

            return false;
        } else if (!($content = $this->checkUrl($this->link))) {
            $this->addError('Invalid radiojavan track link.');

            return false;
        }

        return true;
    }
}