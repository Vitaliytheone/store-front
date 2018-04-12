<?php

namespace sommerce\components\validators\link;


class TwitterProfile extends BaseLinkValidator
{
    public function validate()
    {
        if (FALSE === strpos($this->link, '/')) {
            $this->link = 'twitter.com/' . $this->link;
        }

        $this->link = str_replace('@', '', $this->link);
        $this->link = preg_replace("/(mobile\.)/is", "", $this->link);

        $this->link = "https://" . parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);

        $content = null;

        if (!(preg_match("/https\:\/\/twitter\.com\/([a-z0-9\_]{1,15})(\/)?$/i", $this->link))) {
            $this->addError('Invalid twitter profile link.');

            return false;
        } else if (!($content = $this->checkUrl($this->link))) {
            $this->addError('Invalid twitter profile link.');

            return false;
        }

        return true;
    }
}