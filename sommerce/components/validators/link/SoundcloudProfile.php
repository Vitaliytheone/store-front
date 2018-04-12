<?php

namespace sommerce\components\validators\link;


class SoundcloudProfile extends BaseLinkValidator
{
    public function validate()
    {
        if (FALSE === strpos($this->link, '/')) {
            $this->link = 'soundcloud.com/' . $this->link;
        }

        $this->link = "https://" . parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);

        $content = null;

        if (!(preg_match("/https\:\/\/soundcloud\.com\/([a-z0-9\_-]+)(\/)?$/i", $this->link, $match))) {
            $this->addError('Invalid soundcloud profile link.');

            return false;
        } else if (!($content = $this->checkUrl($this->link, true))) {
            $this->addError('Invalid soundcloud profile link.');

            return false;
        }

        return true;
    }
}