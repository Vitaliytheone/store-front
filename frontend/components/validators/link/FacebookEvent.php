<?php

namespace frontend\components\validators\link;


class FacebookEvent extends BaseLinkValidator
{
    public function validate()
    {
        if (FALSE === strpos($this->link, '/')) {
            $this->link = 'facebook.com/' . $this->link;
        }

        $this->link = "https://www." . parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);

        $content = null;

        if (!(preg_match("/https\:\/\/www\.facebook\.com\/events\/([0-9]+)(\/)?$/uis", $this->link))) {
            $this->addError('Invalid facebook page link.');

            return false;
        } else if (!($content = $this->checkUrl($this->link . '?hl=en'))) {
            $this->addError('Invalid facebook event link.');

            return false;
        }

        return true;
    }
}