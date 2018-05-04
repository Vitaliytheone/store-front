<?php
namespace sommerce\components\validators\link;

use Yii;

/**
 * Class VinePicture
 * @package sommerce\components\validators\link
 */
class VinePicture extends BaseLinkValidator
{
    public function validate()
    {
        $this->link = "https://" . parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);

        $content = null;

        if (!(preg_match("/https\:\/\/vine\.co\/v\/([a-z0-9]+)(\/)?$/uis", $this->link))) {
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