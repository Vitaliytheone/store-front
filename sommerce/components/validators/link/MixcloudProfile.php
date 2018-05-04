<?php
namespace sommerce\components\validators\link;

use Yii;

/**
 * Class MixcloudProfile
 * @package sommerce\components\validators\link
 */
class MixcloudProfile extends BaseLinkValidator
{
    public function validate()
    {
        if (FALSE === strpos($this->link, '/')) {
            $this->link = 'mixcloud.com/' . $this->link;
        }

        $this->link = "https://www." . parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);

        $content = null;

        if (!(preg_match("/https\:\/\/www\.mixcloud\.com\/([^\/]+)(\/)?$/uis", $this->link))) {
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