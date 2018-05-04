<?php
namespace sommerce\components\validators\link;

use Yii;

/**
 * Class TwitchChannel
 * @package sommerce\components\validators\link
 */
class TwitchChannel extends BaseLinkValidator
{
    public function validate()
    {
        $getStr = parse_url($this->link, PHP_URL_QUERY);
        parse_str($getStr, $getParams);

        $this->link = parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);

        if (preg_match("/^player\./", $this->link)) {
            $this->link = "https://" . $this->link;
        } else {
            $this->link = "https://www." . $this->link;
        }

        if (!empty($getParams['channel'])) {
            $this->link .= '?channel=' . $getParams['channel'];
        }

        $content = null;

        if (!(preg_match("/https\:\/\/www\.twitch\.tv\/([a-z0-9_]+)(\/)?$/iu", $this->link))
            && !(preg_match("/https\:\/\/player\.twitch\.tv\/\?channel\=([a-z0-9_]+)(\/)?$/iu", $this->link))) {
            $this->addError(Yii::t('app', 'order.invalid_link', [
                'name' => $this->name
            ]));

            return false;
        } else if (!($content = $this->checkUrl($this->link))) {
            $this->addError(Yii::t('app', 'order.invalid_link', [
                'name' => $this->name
            ]));

            return false;
        }

        return true;
    }
}