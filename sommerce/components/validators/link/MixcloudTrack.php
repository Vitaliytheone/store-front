<?php

namespace sommerce\components\validators\link;


class MixcloudTrack extends BaseLinkValidator
{
    public function validate()
    {
        $this->link = "https://www." . parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);

        $content = null;

        if (!(preg_match("/https\:\/\/www\.mixcloud\.com\/([^\/]+)\/([^\/]+)(\/)?$/i", $this->link))) {
            $this->addError('Invalid mixcloud track link.');

            return false;
        } else if (!($content = $this->checkUrl($this->link))) {
            $this->addError('Invalid mixcloud track link.');

            return false;
        }

        return true;
    }
}