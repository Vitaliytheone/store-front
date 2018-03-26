<?php

namespace sommerce\components\validators\link;


class FacebookPost extends BaseLinkValidator
{
    public function validate()
    {
        if (FALSE === strpos($this->link, '/')) {
            $this->link = 'facebook.com/' . $this->link;
        }

        $getStr = parse_url($this->link, PHP_URL_QUERY);
        parse_str($getStr, $getParams);
        $this->link = "https://www." . parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);

        if (!empty($getParams['id']) && !empty($getParams['story_fbid'])) {
            $this->link .= '?story_fbid=' . $getParams['story_fbid'] . '&id=' . $getParams['id'];
        }

        if (!empty($getParams['fbid']) && !empty($getParams['set'])) {
            $this->link .= '?fbid=' . $getParams['fbid'] . '&set=' . $getParams['set'];
        }

        $content = null;

        if (!(preg_match("/https\:\/\/www\.facebook\.com\/([a-z0-9а-я\_\-\.]+)\/(posts|videos)\/([0-9]+)(\/)?$/uis", $this->link))
            && !(preg_match("/https\:\/\/www\.facebook\.com\/profile\.php\?story_fbid\=([0-9]+)\&id\=([0-9]+)$/uis", $this->link))
            && !(preg_match("/https\:\/\/www\.facebook\.com\/groups\/([a-z0-9а-я\_\-\.]+)\/permalink\/([0-9]+)(\/)?$/uis", $this->link))
            && !(preg_match("/https\:\/\/www\.facebook\.com\/([a-z0-9а-я\_\-\.]+)\/photos\/([a-z0-9\.]+)\/([0-9]+)(\/)?$/uis", $this->link))
            && !(preg_match("/https\:\/\/www\.facebook\.com\/photo\.php\?fbid\=([0-9]+)\&set\=([0-9a-z\.]+)$/uis", $this->link))) {
            $this->addError('Invalid facebook profile link.');

            return false;
        } else if (!($content = $this->checkUrl($this->link))) {
            $this->addError('Invalid facebook profile link.');

            return false;
        }

        return true;
    }
}