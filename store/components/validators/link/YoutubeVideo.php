<?php
namespace store\components\validators\link;

use Yii;

/**
 * Class YoutubeVideo
 * @package store\components\validators\link
 */
class YoutubeVideo extends BaseLinkValidator
{
    public function validate()
    {
        $this->link = preg_replace("/youtube.[a-z]+/i", "youtube.com", $this->link);

        $getStr = parse_url($this->link, PHP_URL_QUERY);
        parse_str($getStr, $getParams);
        $this->link = parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);

        if (FALSE !== strpos($this->link, 'youtu.be')) {
            $this->link = "https://" . $this->link;
        } else {
            $this->link = "https://www." . $this->link;
        }

        if (!empty($getParams['v'])) {
            $this->link .= '?v=' . $getParams['v'];
        }

        $content = null;

        if (!(preg_match("/https\:\/\/www\.youtube\.([a-z]+)\/video\/([a-z0-9-_]+)(\/)?$/i", $this->link))
            && !(preg_match("/https\:\/\/www\.youtube\.([a-z]+)\/embed\/([a-z0-9-_]+)(\/)?$/i", $this->link))
            && !(preg_match("/https\:\/\/youtu\.be\/([a-z0-9-_]+)(\/)?$/i", $this->link))
            && !(preg_match("/https\:\/\/www\.youtube\.([a-z]+)\/watch\?v\=([a-z0-9-_]+)(\/)?$/i", $this->link))) {
            $this->addError(Yii::t('app', 'order.error.link', [
                'name' => $this->name
            ]));

            return false;
        } else if (!($content = $this->checkUrl($this->link))) {
            $this->addError(Yii::t('app', 'order.error.link', [
                'name' => $this->name
            ]));

            return false;
        }

        return true;
    }
}