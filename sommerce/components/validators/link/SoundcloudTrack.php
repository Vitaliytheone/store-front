<?php

namespace sommerce\components\validators\link;


class SoundcloudTrack extends BaseLinkValidator
{
    public function validate()
    {
        if (FALSE === strpos($this->link, '/')) {
            $this->link = 'soundcloud.com/' . $this->link;
        }

        $this->link = "https://" . parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);

        $content = null;

        if (!(preg_match("/https\:\/\/soundcloud\.com\/([a-z0-9\_-]+)\/([a-z0-9\_-]+)(\/)?$/i", $this->link))) {
            $this->addError('Invalid soundcloud track link.');

            return false;
        } /*else if (!($content = $this->checkUrl($this->link))) {
            $this->addError('Invalid soundcloud track link1.');

            return false;
        }*/

        return true;
    }
}