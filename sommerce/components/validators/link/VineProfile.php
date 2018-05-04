<?php
namespace sommerce\components\validators\link;

use Yii;

/**
 * Class VineProfile
 * @package sommerce\components\validators\link
 */
class VineProfile extends BaseLinkValidator
{
    public function validate()
    {
        if (FALSE === strpos($this->link, '/')) {
            $this->link = 'vine.co/' . $this->link;
        }

        $this->link = "https://" . parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);

        $content = null;

        if (!(preg_match("/https\:\/\/vine\.co\/([a-z0-9\.]+)(\/)?$/uis", $this->link, $matchName))
            && !(preg_match("/https\:\/\/vine\.co\/u\/([0-9]+)(\/)?$/uis", $this->link, $matchId))) {
            $this->addError(Yii::t('app', 'order.error.link', [
                'name' => $this->name
            ]));

            return false;
        } else if (!(empty($matchId[1]))) {
            $content = @file_get_contents("https://archive.vine.co/profiles/" . $matchId[1] . ".json");
            $content = !empty($content) ? @json_decode($content, true) : null;

            if (is_array($content) && !empty($content['userId'])) {
                return true;
            }

            return false;
        } else if (!(empty($matchName[1]))) {
            $content = @file_get_contents("https://vine.co/api/users/profiles/vanity/" . $matchName[1]);
            $content = !empty($content) ? @json_decode($content, true) : null;
            if (is_array($content) && !empty($content['data']['userId'])) {
                return true;
            }

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