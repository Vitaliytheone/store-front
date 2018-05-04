<?php
namespace sommerce\components\validators\link;

use Yii;

/**
 * Class FacebookEvent
 * @package sommerce\components\validators\link
 */
class FacebookEvent extends BaseLinkValidator
{
    public function validate()
    {
        if (FALSE === strpos($this->link, '/')) {
            $this->link = 'facebook.com/' . $this->link;
        }

        // Удаляем порт, если он есть в урле
        $this->link  = preg_replace("/\:[0-9]+/", "", $this->link);

        $this->link = "https://www." . parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);
        $this->link = str_replace(["https://www.m.", "https://www.web."], "https://www.", $this->link);

        $content = null;

        if (!(preg_match("/https\:\/\/www\.facebook\.com\/events\/([0-9]+)(\/)?$/uis", $this->link))) {
            $this->addError(Yii::t('app', 'order.invalid_link', [
                'name' => $this->name
            ]));

            return false;
        } else if (!($content = $this->checkUrl($this->link . '?hl=en'))) {
            $this->addError(Yii::t('app', 'order.invalid_link', [
                'name' => $this->name
            ]));

            return false;
        }

        return true;
    }
}