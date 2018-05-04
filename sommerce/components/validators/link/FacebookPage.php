<?php
namespace sommerce\components\validators\link;

use Yii;

/**
 * Class FacebookPage
 * @package sommerce\components\validators\link
 */
class FacebookPage extends BaseLinkValidator
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

        if (!(preg_match("/https\:\/\/www\.facebook\.com\/([a-z0-9а-я\_\-\.]+)(\/.*?)?$/uis", $this->link))
            && !(preg_match("/https\:\/\/www\.facebook\.com\/pages\/street\/([0-9]+)(\/)?$/uis", $this->link))
            && !(preg_match("/https\:\/\/www\.facebook\.com\/places\/([a-z0-9а-я\_\-\.]+)\/([0-9]+)?$/uis", $this->link))
            && !(preg_match("/https\:\/\/www\.facebook\.com\/pages\/([a-z0-9а-я\_\-\.]+)\/([0-9]+)?$/uis", $this->link))) {
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