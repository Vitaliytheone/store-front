<?php

namespace frontend\components\validators\link;


class RadiojavanPodcast extends BaseLinkValidator
{
    public function validate()
    {
        $this->link = "https://www." . parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);

        $content = null;

        if (!(preg_match("/https\:\/\/www\.radiojavan\.com\/podcasts\/podcast\/([a-z0-9\-]+)(\/)?$/i", $this->link))) {
            $this->addError('Invalid radiojavan podcast link.');

            return false;
        } else if (!($content = $this->checkUrl($this->link))) {
            $this->addError('Invalid radiojavan podcast link.');

            return false;
        }

        return true;
    }
}