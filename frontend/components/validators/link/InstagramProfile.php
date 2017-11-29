<?php

namespace frontend\components\validators\link;


class InstagramProfile extends BaseLinkValidator
{
    public function validate()
    {
        if (FALSE === strpos($this->link, '/')) {
            $this->link = 'instagram.com/' . $this->link;
        }

        $this->link = "https://www." . parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);

        $content = null;

        if (!(preg_match("/https\:\/\/www\.instagram\.com\/([a-z0-9\.\_]+)(\/)?$/i", $this->link))) {
            $this->addError('Invalid instagram profile link.');

            return false;
        } else if (!($content = $this->checkUrl($this->link . '?hl=en'))) {
            $this->addError('Invalid instagram profile link.');

            return false;
        }

        return true;
    }
}