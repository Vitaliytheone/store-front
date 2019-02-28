<?php
namespace store\components\validators\link;

use Yii;

/**
 * Class FyuseProfile
 * @package store\components\validators\link
 */
class FyuseProfile extends BaseLinkValidator
{
    public function validate()
    {
        if (FALSE === strpos($this->link, '/')) {
            $this->link = 'fyu.se/u/' . $this->link;
        }

        $this->link = "https://" . parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);

        $content = null;

        if (!(preg_match("/https\:\/\/fyu\.se\/u\/([a-z0-9\_]+)(\/)?$/i", $this->link))) {
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