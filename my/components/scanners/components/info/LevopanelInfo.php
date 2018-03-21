<?php
namespace my\components\scanners\components\info;

use my\components\scanners\components\BasePanelInfo;
use yii\helpers\ArrayHelper;

/**
 * Class Levopanel
 * @package my\components\scanners\components\info
 */
class LevopanelInfo extends BasePanelInfo
{
    /**
     * Check if panel Active
     * @return bool
     */
    public function checkStatusActive()
    {
        $panelData = $this->currentPanelData;
        $content = ArrayHelper::getValue($panelData, 'content');
        $httpCode = ArrayHelper::getValue($panelData, 'info.http_code');

        if (empty($content) || $httpCode != self::HTTP_STATUS_200) {
            return false;
        }

        return
            boolval(stripos($content, 'type="text" required="" name="username"')) &&
            boolval(strpos($content, 'type="password" required="" name="password"'));
    }

    /**
     * Check if requested host frozen
     * @return bool
     */
    public function checkStatusDisabled()
    {
        $panelData = $this->currentPanelData;
        $content = ArrayHelper::getValue($panelData, 'content');
        $httpCode = ArrayHelper::getValue($panelData, 'info.http_code');

        if (empty($content) || $httpCode != self::HTTP_STATUS_200) {
            return false;
        }

        return
            boolval(stripos($content, 'Panel is disabled')) ||
            boolval(stripos($content, 'Panel is currently unavailable'));
    }
}