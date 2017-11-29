<?php

namespace frontend\components\validators\link;


class ReverbnationTrack extends BaseLinkValidator
{
    public function validate()
    {
        $this->link = "https://www." . parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);

        $content = null;

        if (!(preg_match("/https\:\/\/www\.reverbnation\.com\/([a-zа-я0-9\-]+)\/song\/([0-9]+)(-[a-zа-я0-9-]+)?(\/)?$/i", $this->link))) {
            $this->addError('Invalid reverbnation track link.');

            return false;
        } else if (!($content = $this->checkUrl($this->link))) {
            $this->addError('Invalid reverbnation track link0.');

            return false;
        }

        return true;
    }
}