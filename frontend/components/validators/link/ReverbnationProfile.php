<?php

namespace frontend\components\validators\link;


class ReverbnationProfile extends BaseLinkValidator
{
    public function validate()
    {
        if (FALSE === strpos($this->link, '/')) {
            $this->link = 'reverbnation.com/' . $this->link;
        }

        $this->link = "https://www." . parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);

        $content = null;

        if (!(preg_match("/https\:\/\/www\.reverbnation\.com\/([a-zа-я0-9\-]+)(\/)?$/i", $this->link))) {
            $this->addError('Invalid reverbnation profile link.');

            return false;
        } else if (!($content = $this->checkUrl($this->link))) {
            $this->addError('Invalid reverbnation profile link.');

            return false;
        }

        return true;
    }
}