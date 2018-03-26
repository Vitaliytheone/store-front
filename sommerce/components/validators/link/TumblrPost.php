<?php

namespace sommerce\components\validators\link;


class TumblrPost extends BaseLinkValidator
{
    public function validate()
    {
        $this->link = "https://" . parse_url($this->link, PHP_URL_PATH);

        $content = null;

        if (!(preg_match("/https\:\/\/([a-z0-9-]+)\.tumblr\.com\/post\/([0-9]+)(\/)?$/i", $this->link))
            && !(preg_match("/https\:\/\/([a-z0-9-]+)\.tumblr\.com\/post\/([0-9]+)\/.*?(\/)?$/i", $this->link))) {
            $this->addError('Invalid tumblr post link.');

            return false;
        } else if (!($content = $this->checkUrl($this->link))) {
            $this->addError('Invalid tumblr post link.');

            return false;
        }

        return true;
    }
}