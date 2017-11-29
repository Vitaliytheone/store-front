<?php

namespace frontend\components\validators\link;


class YoutubeVideo extends BaseLinkValidator
{
    public function validate()
    {
        $this->link = preg_replace("/youtube.[a-z]+/i", "youtube.com", $this->link);

        $getStr = parse_url($this->link, PHP_URL_QUERY);
        parse_str($getStr, $getParams);
        $this->link = "https://www." . parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);

        if (!empty($getParams['v'])) {
            $this->link .= '?v=' . $getParams['v'];
        }

        $content = null;

        if (!(preg_match("/https\:\/\/www\.youtube\.com\/video\/([a-z0-9-_]+)(\/)?$/i", $this->link))
            && !(preg_match("/https\:\/\/www\.youtube\.com\/embed\/([a-z0-9-_]+)(\/)?$/i", $this->link))
            && !(preg_match("/https\:\/\/www\.youtu\.be\/([a-z0-9-_]+)(\/)?$/i", $this->link))
            && !(preg_match("/https\:\/\/www\.youtube\.com\/watch\?v\=([a-z0-9-_]+)(\/)?$/i", $this->link))) {
            $this->addError('Invalid youtube video link.');

            return false;
        } else if (!($content = $this->checkUrl($this->link))) {
            $this->addError('Invalid youtube video link.');

            return false;
        }

        return true;
    }
}