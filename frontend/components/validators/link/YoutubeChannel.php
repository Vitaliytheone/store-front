<?php

namespace frontend\components\validators\link;


class YoutubeChannel extends BaseLinkValidator
{
    public function validate()
    {
        if (FALSE === strpos($this->link, '/')) {
            $this->link = 'youtube.com/' . $this->link;
        }

        $this->link = preg_replace("/youtube.[a-z]+/i", "youtube.com", $this->link);
        $this->link = "https://www." . parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);

        $content = null;

        if (!(preg_match("/https\:\/\/www\.youtube\.com\/channel\/([a-z0-9-_]+)(\/)?$/i", $this->link))
            && !(preg_match("/https\:\/\/www\.youtube\.com\/c\/([a-z0-9-_]+)(\/)?$/i", $this->link))
            && !(preg_match("/https\:\/\/www\.youtube\.com\/(?!watch)([a-z0-9-_]+)(\/)?$/i", $this->link))
            && !(preg_match("/https\:\/\/www\.youtube\.com\/user\/([a-z0-9-_]+)(\/)?.*?/i", $this->link))) {
            $this->addError('Invalid youtube channel link.');

            return false;
        } else if (!($content = $this->checkUrl($this->link))) {
            $this->addError('Invalid youtube channel link.');

            return false;
        }

        return true;
    }
}