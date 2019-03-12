<?php
namespace store\components\validators\link;

use Yii;

/**
 * Class VimeoChannel
 * @package store\components\validators\link
 */
class VimeoChannel extends BaseLinkValidator
{
    public function validate()
    {
        if (FALSE === strpos($this->link, '/')) {
            $this->link = 'vimeo.com/' . $this->link;
        }

        $this->link = "https://" . parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);

        $content = null;

        if (!(preg_match("/https\:\/\/vimeo\.com\/([a-z0-9]+)(\/)?$/uis", $this->link))
            && !(preg_match("/https\:\/\/vimeo\.com\/channels\/([a-z0-9]+)(\/)?$/uis", $this->link))
            && !(preg_match("/https\:\/\/vimeo\.com\/groups\/([a-z0-9]+)(\/)?$/uis", $this->link))) {
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