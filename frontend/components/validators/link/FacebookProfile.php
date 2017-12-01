<?php

namespace frontend\components\validators\link;


class FacebookProfile extends BaseLinkValidator
{
    public function validate()
    {
        if (FALSE === strpos($this->link, '/')) {
            $this->link = 'facebook.com/' . $this->link;
        }

        $getStr = parse_url($this->link, PHP_URL_QUERY);
        parse_str($getStr, $getParams);
        $this->link = "https://www." . parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);

        if (!empty($getParams['id'])) {
            $this->link .= '?id=' . $getParams['id'];
        }

        $content = null;

        if (!(preg_match("/https\:\/\/www\.facebook\.com\/([a-z0-9а-я\_\-\.]+)(\/.*?)?$/uis", $this->link))
            && !(preg_match("/https\:\/\/www\.facebook\.com\/profile\.php\?id\=([0-9]+)$/uis", $this->link))) {
            $this->addError('Invalid facebook profile link.');

            return false;
        } else if (!($content = $this->checkUrl($this->link . '?hl=en'))) {
            $this->addError('Invalid facebook profile link.');

            return false;
        }

        return true;
    }
}