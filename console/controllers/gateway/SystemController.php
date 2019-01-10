<?php
namespace console\controllers\gateway;

use common\models\gateways\Sites;
use gateway\helpers\GatewayHelper;
use yii\helpers\Console;
use Yii;
use yii\helpers\FileHelper;

/**
 * Class SystemController
 * @package console\controllers\gateway
 */
class SystemController extends CustomController
{
    public $siteId;

    public function options($actionID)
    {
        return ['siteId'];
    }

    /**
     * Clear twig cache folder
     */
    public function actionClearTwigCache()
    {
        static::_clearTwigCache();
    }

    /**
     * Clear twig cache
     */
    protected static function _clearTwigCache()
    {
        if (!empty(Yii::$app->params['gateway.twig.cachePath'])) {
            $path = Yii::getAlias(Yii::$app->params['gateway.twig.cachePath']);
            if (is_dir($path)) {
                FileHelper::removeDirectory($path);
            }
        }
    }

    /**
     * Action to generate gateways assets
     */
    public function actionGenerateAssets()
    {
        $query = Sites::find();

        $flushAssets = true;

        if ($this->siteId) {
            $flushAssets = false;

            $query->andWhere([
                'id' => $this->siteId
            ]);
        }

        if ($flushAssets) {
            static::_clearAssetsCache();
        }

        foreach ($query->batch() as $gateways) {
            foreach ($gateways as $gateway) {
                Yii::$app->gateway->setInstance($gateway);

                /**
                 * @var Sites $gateway
                 */

                if (!$flushAssets) {
                    static::_clearAssetsCache($gateway->folder);
                }

                $gateway->generateFolderName();
                $gateway->save(false);
                GatewayHelper::generateAssets($gateway->id);

                $this->stderr('Generated for ' . $gateway->id . "\n", Console::FG_GREEN);
            }
        }

        $this->actionClearTwigCache();
    }

    /**
     * Action to generate gateways assets
     */
    public function actionClearAssets()
    {
        $query = Sites::find();

        $flushAssets = true;

        if ($this->siteId) {
            $flushAssets = false;

            $query->andWhere([
                'id' => $this->siteId
            ]);
        }

        if ($flushAssets) {
            static::_clearAssetsCache();
        }

        foreach ($query->batch() as $gateways) {
            foreach ($gateways as $gateway) {
                Yii::$app->gateway->setInstance($gateway);

                /**
                 * @var Sites $gateway
                 */

                if (!$flushAssets) {
                    static::_clearAssetsCache($gateway->folder);
                }

                $this->stderr('Cleared for ' . $gateway->id . "\n", Console::FG_GREEN);
            }
        }

        $this->actionClearTwigCache();
    }

    /**
     * Remove assets dir
     * @param string|null $dirPath
     */
    protected static function _clearAssetsCache($dirPath = null)
    {
        $path = Yii::getAlias(Yii::$app->params['gateway.assets.cachePath']);

        if ($dirPath) {
            $path = $path . DIRECTORY_SEPARATOR . $dirPath;
            if (is_dir($path)) {
                FileHelper::removeDirectory($path);
            }

            return;
        }

        if (is_dir($path)) {
            foreach (scandir($path) as $dirPath) {
                if (in_array($dirPath, ['.', '..'])) {
                    continue;
                }
                $dirPath = $path . DIRECTORY_SEPARATOR . $dirPath;

                if (is_dir($dirPath)) {
                    FileHelper::removeDirectory($dirPath);
                }
            }
        }
    }
}