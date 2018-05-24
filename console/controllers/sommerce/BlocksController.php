<?php
namespace console\controllers\sommerce;

use common\models\store\Blocks;
use common\models\stores\Stores;
use sommerce\helpers\BlockHelper;
use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;

/**
 * Class BlocksController
 * @package console\controllers
 */
class BlocksController extends CustomController
{
    /**
     * Updating all stores blocks settings
     */
    public function actionUpdateStoresBlocks()
    {
        $storesDbs = Stores::find()
            ->select("db_name")
            ->column();

        foreach ($storesDbs as $db) {

            $isDbExist = Yii::$app->db
                ->createCommand("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$db'")
                ->queryScalar();

            if (!$isDbExist) {
                $this->stderr('Store template database ' . "[$db]" . " does not exist!" .  "\n", Console::FG_RED);
                return;
            }

            $blocksTableName = $db . '.' . Blocks::tableName();

            $blocks = Yii::$app->db
                ->createCommand("
                    SELECT * FROM $blocksTableName;
                ")
                ->queryAll();


            $this->stderr('Updating  DB: ' . "[$db]" . "\n", Console::FG_YELLOW);

            foreach ($blocks as $block) {

                $blockCode = ArrayHelper::getValue($block, 'code');

                if (!$blockCode || !in_array($block['code'], [Blocks::CODE_PROCESS, Blocks::CODE_FEATURES])) {
                    continue;
                }

                $blockContent = json_decode(ArrayHelper::getValue($block, 'content'), true);

                if (!is_array($blockContent)) {
                    continue;
                }

                $blockSettings = ArrayHelper::getValue($blockContent, 'settings');

                if (isset($blockSettings['bootstrap_column'])) {
                    $this->stderr('Block ' . "[$blockCode]" .  "Already updated. Skipped!" . "\n", Console::FG_YELLOW);
                    continue;
                }

                // Convert old block `settings`
                if (is_array($blockSettings)) {
                    switch ((int)$blockSettings['column']) {
                        case 6:
                            $blockSettings['column'] = '2';
                            $blockSettings['bootstrap_column'] = 'col-md-6';
                            break;
                        case 4:
                            $blockSettings['column'] = '3';
                            $blockSettings['bootstrap_column'] = 'col-md-4';
                            break;
                        case 3:
                            $blockSettings['column'] = '4';
                            $blockSettings['bootstrap_column'] = 'col-md-3';
                            break;
                    }

                    $blockContent['settings'] = $blockSettings;
                }

                // Convert old icons format to new only for `process` and `features` blocks
                $contentData = ArrayHelper::getValue($blockContent, 'data');

                if (is_array($contentData)) {
                    foreach ($contentData as &$item) {
                        $item['icon_class'] = "fas fa-image";
                        $item['icon_name'] = "image";
                        unset($item['icon']);
                    }

                    $blockContent['data'] = $contentData;
                }

                $blockContent = json_encode($blockContent);

                Yii::$app->db->createCommand("UPDATE $blocksTableName SET `content`=:block_content WHERE `code`=:block_code")
                    ->bindValues([
                        ':block_content' => $blockContent,
                        ':block_code' => $blockCode,
                    ])
                    ->execute();

                $this->stderr('Updated block: ' . "[$blockCode]" . "\n", Console::FG_GREEN);
            }
        }

        $this->stderr('Completed' . "\n", Console::FG_GREEN);
    }

    /**
     * Updating template store blocks settings
     */
    public function actionUpdateTemplateStoreBlocks()
    {
        $dbName = Yii::$app->params['storeDefaultDatabase'];
        $tableName = $dbName . '.' . Blocks::tableName();

        $isDbExist = Yii::$app->db
            ->createCommand("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbName'")
            ->queryScalar();

        if (!$isDbExist) {
            $this->stderr('Store template database ' . "[$dbName]" . " does not exist!" .  "\n", Console::FG_RED);
            return;
        }

        $blocks = Yii::$app->db
            ->createCommand("
                    SELECT * FROM $tableName;
                ")
            ->queryAll();

        foreach ($blocks as $block) {

            $blockCode = ArrayHelper::getValue($block, 'code');

            if (!$blockCode) {
                continue;
            }

            $defaultContent = BlockHelper::getDefaultBlock($blockCode);

            if (!is_array($defaultContent)) {
                throw new Exception('Default block content does not exist!');
            }

            $defaultContent = json_encode($defaultContent);

            Yii::$app->db->createCommand("
                UPDATE $tableName
                SET `content` = '$defaultContent'
                WHERE `code` =  '$blockCode';
            ")->execute();

            $this->stderr('Updated block: ' . "[$blockCode]" . "\n", Console::FG_GREEN);
        }

        $this->stderr('Completed' . "\n", Console::FG_GREEN);
    }

}