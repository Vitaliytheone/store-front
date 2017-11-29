<?php

namespace frontend\components\validators\link;


class RadiojavanVideo extends BaseLinkValidator
{
    public function validate()
    {
        $this->link = "https://www." . parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);

        $content = null;

        if (!(preg_match("/https\:\/\/www\.radiojavan\.com\/videos\/video\/([a-z0-9\-]+)(\/)?$/i", $this->link))) {
            $this->addError('Invalid radiojavan video link.');

            return false;
        } else if (!($content = $this->checkUrl($this->link))) {
            $this->addError('Invalid radiojavan video link.');

            return false;
        }

        return true;
    }
}