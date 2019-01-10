<?php

namespace gateway\components\behaviors;

use common\models\gateways\Sites;
use console\helpers\ConsoleHelper;
use yii\helpers\ArrayHelper;
use Yii;
use yii\base\Behavior;
use yii\db\BaseActiveRecord;

/**
 * Class ThemesFilesBehavior
 * @package gateway\components\behaviors
 */
class ThemesFilesBehavior extends Behavior {

    /**
     * @var Sites
     */
    protected $_gateway;

    public function events()
    {
        return ArrayHelper::merge(parent::events(), [
            BaseActiveRecord::EVENT_BEFORE_DELETE => 'clearAssetsFolder',
            BaseActiveRecord::EVENT_BEFORE_UPDATE => 'clearAssetsFolder',
            BaseActiveRecord::EVENT_BEFORE_INSERT => 'clearAssetsFolder',
        ]);
    }

    /**
     * Add twig
     */
    public function clearAssetsFolder()
    {
        ConsoleHelper::execConsoleCommand('system-gateway/clear-assets --siteId=' . $this->_getGatewayModel()->id);
    }

    /**
     * @return Sites
     */
    protected function _getGatewayModel()
    {
        if (null === $this->_gateway) {
            $this->_gateway = Yii::$app->gateway->getInstance();
        }

        return $this->_gateway;
    }
}