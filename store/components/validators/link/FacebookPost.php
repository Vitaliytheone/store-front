<?php
namespace store\components\validators\link;

use Yii;

/**
 * Class FacebookPost
 * @package store\components\validators\link
 */
class FacebookPost extends BaseLinkValidator
{
    public function validate()
    {
        if (FALSE === strpos($this->link, '/')) {
            $this->link = 'facebook.com/' . $this->link;
        }

        // Удаляем порт, если он есть в урле
        $this->link  = preg_replace("/\:[0-9]+/", "", $this->link);

        $getStr = parse_url($this->link, PHP_URL_QUERY);
        parse_str($getStr, $getParams);


        $this->link = "https://www." . parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);
        $this->link = str_replace(["https://www.m.", "https://www.web."], "https://www.", $this->link);

        if (!empty($getParams['id']) && !empty($getParams['story_fbid'])) {
            $this->link .= '?story_fbid=' . $getParams['story_fbid'] . '&id=' . $getParams['id'];
        }

        if (!empty($getParams['fbid']) && !empty($getParams['set'])) {
            $this->link .= '?fbid=' . $getParams['fbid'] . '&set=' . $getParams['set'];
        } elseif (!empty($getParams['fbid'])) {
            $this->link .= '?fbid=' . $getParams['fbid'];
        }

        $content = null;

        if (!(preg_match("/https\:\/\/www\.facebook\.com\/([a-z0-9а-я\_\-\.]+)\/(posts|videos)\/([0-9]+)(\/)?$/uis", $this->link))
            && !(preg_match("/https\:\/\/www\.facebook\.com\/profile\.php\?story_fbid\=([0-9]+)\&id\=([0-9]+)$/uis", $this->link))
            && !(preg_match("/https\:\/\/www\.facebook\.com\/story\.php\?story_fbid\=([0-9]+)\&id\=([0-9]+)$/uis", $this->link))
            && !(preg_match("/https\:\/\/www\.facebook\.com\/permalink\.php\?story_fbid\=([0-9]+)\&id\=([0-9]+)$/uis", $this->link))
            && !(preg_match("/https\:\/\/www\.facebook\.com\/groups\/([a-z0-9а-я\_\-\.]+)\/permalink\/([0-9]+)(\/)?$/uis", $this->link))
            && !(preg_match("/https\:\/\/www\.facebook\.com\/([a-z0-9а-я\_\-\.]+)\/photos\/([a-z0-9\.]+)\/([0-9]+)(\/)?$/uis", $this->link))
            && !(preg_match("/https\:\/\/www\.facebook\.com\/photo\.php\?fbid\=([0-9]+)\&set\=([0-9a-z\.]+)$/uis", $this->link))
            && !(preg_match("/https\:\/\/www\.facebook\.com\/photo\.php\?fbid\=([0-9]+)$/uis", $this->link))) {
            $this->addError(Yii::t('app', 'order.error.link', [
                'name' => $this->name
            ]));

            return false;
        } else if (!($content = $this->checkUrl($this->link))) {
            $this->addError(Yii::t('app', 'order.error.link', [
                'name' => $this->name
            ]));

            return false;
        }

        return true;
    }
}