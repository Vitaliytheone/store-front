<?php
namespace my\components\scanners\components\info;

use my\components\scanners\components\BasePanelInfo;
use yii\helpers\ArrayHelper;

/**
 * Class Levopanel
 * @package my\components\scanners\components\info
 */
class RentapanelInfo extends BasePanelInfo
{
    /**
     * Check if panel Active
     * @return bool
     */
    public function checkStatusActive()
    {
        $panelData = $this->currentPanelData;
        $content = ArrayHelper::getValue($panelData, 'content');

        $matchesApiLink = [];
        $matchesServicesLink = [];
        preg_match('/<a href="api_docs">\s*API\s*<\/a>/i', $content, $matchesApiLink);
        preg_match('/<a href="services">\s*Services\s*<\/a>/i', $content, $matchesServicesLink);

        if (empty($matchesApiLink) || empty($matchesServicesLink)) {
            return false;
        }

        if (empty($content) || ArrayHelper::getValue($panelData, 'info.http_code') != self::HTTP_STATUS_200 || $this->checkStatusDisabled()) {
            return false;
        }

        return $this->_isValid();
    }

    /**
     * Check if requested host frozen
     * @return bool
     */
    public function checkStatusDisabled()
    {
        $panelData = $this->currentPanelData;
        $content = ArrayHelper::getValue($panelData, 'content');

        if (empty($content) || (ArrayHelper::getValue($panelData, 'info.http_code') != self::HTTP_STATUS_200
                && ArrayHelper::getValue($panelData, 'info.http_code') != 302)) {
            return false;
        }

        return
            boolval(stripos($content, 'Panel is disabled')) ||
            boolval(stripos($content, 'Panel temporarily unavailable')) ||
            boolval(stripos($content, 'Panel is currently unavailable'));
    }

    /**
     * Check if requested host is valid panel
     * @return bool
     */
    private function _isValid()
    {
        $host = parse_url(ArrayHelper::getValue($this->currentPanelData, 'info.url'), PHP_URL_HOST);

        if (!$host) {
            return false;
        }
        echo PHP_EOL . "get url info with curl: ". $host . '/api_docs' . PHP_EOL;
        $panelData = $this->getUrlInfo($host . '/api_docs');
        $content = ArrayHelper::getValue($panelData, 'content');

        if (empty($content) || ArrayHelper::getValue($panelData, 'info.http_code') != self::HTTP_STATUS_200) {
            return false;
        }

        $valid_html = [
            '<td>apiKey</td>',
            '<td>actionType</td>',
            '<td>orderType</td>',
            '<td>orderID</td>',
            '<td>orderUrl</td>',
            '<td>orderQuantity</td>',
        ];

        foreach ($valid_html as $needle) {
            if (!boolval(stripos($content, $needle))) {
                return false;
            }
        }

        return true;
    }
}


