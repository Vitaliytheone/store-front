<?php
namespace sommerce\components\validators\link;

use Yii;

/**
 * Class ReverbnationTrack
 * @package sommerce\components\validators\link
 */
class ReverbnationTrack extends BaseLinkValidator
{
    public function validate()
    {
        $this->link = "https://www." . parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);

        $content = null;

        if (!(preg_match("/https\:\/\/www\.reverbnation\.com\/([a-zа-я0-9\-]+)\/song\/([0-9]+)(-[a-zа-я0-9-]+)?(\/)?$/i", $this->link))) {
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