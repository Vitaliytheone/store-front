<?php

namespace sommerce\components\validators\link;


class MixcloudProfile extends BaseLinkValidator
{
    public function validate()
    {
        if (FALSE === strpos($this->link, '/')) {
            $this->link = 'mixcloud.com/' . $this->link;
        }

        $this->link = "https://www." . parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);

        $content = null;

        if (!(preg_match("/https\:\/\/www\.mixcloud\.com\/([a-z0-9\_]+)(\/)?$/i", $this->link))) {
            $this->addError('Invalid mixcloud profile link.');

            return false;
        } else if (!($content = $this->checkUrl($this->link))) {
            $this->addError('Invalid mixcloud profile link.');

            return false;
        }

        return true;
    }
}