<?php

namespace frontend\components\validators\link;


class FacebookGroup extends BaseLinkValidator
{
    public function validate()
    {
        if (FALSE === strpos($this->link, '/')) {
            $this->link = 'facebook.com/' . $this->link;
        }

        $this->link = "https://www." . parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);

        $content = null;

        if (!(preg_match("/https\:\/\/www\.facebook\.com\/groups\/([a-z0-9а-я\_-]+)(\/.*?)?$/uis", $this->link))) {
            $this->addError('Invalid facebook group link.');

            return false;
        } else if (!($content = $this->checkUrl($this->link))) {
            $this->addError('Invalid facebook group link.');

            return false;
        }

        return true;
    }
}