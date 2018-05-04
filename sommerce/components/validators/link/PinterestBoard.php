<?php
namespace sommerce\components\validators\link;

use Yii;

/**
 * Class PinterestBoard
 * @package sommerce\components\validators\link
 */
class PinterestBoard extends BaseLinkValidator
{
    public function validate()
    {
        $this->link = parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);

        $domainFirst = "([a-z]+\.)";

        if (preg_match("/^" . $domainFirst . "/", $this->link)) {
            $this->link = "https://" . $this->link;
        } else {
            $this->link = "https://www." . $this->link;
        }

        $content = null;

        $domainZero = [
            'com',
            'co\.uk',
            'pt',
            'fr',
            'ca',
            'com\.au',
        ];

        $domainZero = "(" . implode(")|(", $domainZero) . ")";

        $content = null;

        if (!(preg_match("/https\:\/\/" . $domainFirst . "pinterest\." . $domainZero . "\/([a-z0-9\.\_-]+)\/(.*?)(\/)?$/i", $this->link))) {
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