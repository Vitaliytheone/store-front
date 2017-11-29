<?php

namespace frontend\components\validators\link;


class TwitchChannel extends BaseLinkValidator
{
    public function validate()
    {
        $this->link = "https://www." . parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);

        $content = null;

        if (!(preg_match("/https\:\/\/www\.twitch\.tv\/([a-z0-9_]+)(\/)?$/iu", $this->link))) {
            $this->addError('Invalid shazam track link.');

            return false;
        } else if (!($content = $this->checkUrl($this->link))) {
            $this->addError('Invalid twitch channel link.');

            return false;
        }

        return true;
    }
}