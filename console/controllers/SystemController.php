<?php
namespace console\controllers;

use yii\helpers\Console;
use common\models\stores\Stores;
use frontend\helpers\StoreHelper;
use Yii;
use yii\console\Controller;
use yii\helpers\FileHelper;

/**
 * Class SystemController
 * @package console\controllers
 */
class SystemController extends Controller
{
    public $storeId;

    public function options($actionID)
    {
        return ['storeId'];
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
        if (!empty(Yii::$app->params['twig.cachePath'])) {
            $path = Yii::getAlias(Yii::$app->params['twig.cachePath']);
            if (is_dir($path)) {
                FileHelper::removeDirectory($path);
            }
        }
    }

    /**
     * Action to generate stores assets
     */
    public function actionGenerateAssets()
    {
        $query = Stores::find();

        $flushAssets = true;

        if ($this->storeId) {
            $flushAssets = false;

            $query->andWhere([
                'id' => $this->storeId
            ]);
        }

        if ($flushAssets) {
            static::_clearAssetsCache();
        }

        foreach ($query->batch() as $stores) {
            foreach ($stores as $store) {
                /**
                 * @var Stores $store
                 */

                if (!$flushAssets) {
                    static::_clearAssetsCache($store->folder);
                }

                $store->generateFolderName();
                $store->save(false);
                StoreHelper::generateAssets($store->id);

                $this->stderr('Generated for ' . $store->id . "\n", Console::FG_GREEN);
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
        $path = Yii::getAlias('@frontend/web/assets');

        if ($dirPath) {
            $dirPath = $path . DIRECTORY_SEPARATOR . $dirPath;

            if (is_dir($dirPath)) {
                FileHelper::removeDirectory($dirPath);
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