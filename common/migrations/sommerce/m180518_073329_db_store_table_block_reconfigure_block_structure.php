<?php

use yii\db\Migration;
use \common\models\stores\Stores;
use \common\models\store\Blocks;
use \yii\helpers\ArrayHelper;

/**
 * Class m180518_073329_db_store_table_block_reconfigure_block_structure
 *
 * Конвертация старых иконок некоторых блоков в новый формат. Применяется ко всем базам магазинов.
 */
class m180518_073329_db_store_table_block_reconfigure_block_structure extends Migration
{
    protected $storesDbs = [];

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub

        $storePrefix = trim(Stores::STORE_DB_NAME_PREFIX, '_');

        $this->storesDbs = Yii::$app->db
            ->createCommand("SELECT SCHEMA_NAME FROM `INFORMATION_SCHEMA`.`SCHEMATA` WHERE `SCHEMA_NAME` LIKE '$storePrefix\_%'")
            ->queryColumn();
    }

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        foreach ($this->storesDbs as $db) {

            $isDbExist = Yii::$app->db
                ->createCommand("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$db'")
                ->queryScalar();

            if (!$isDbExist) {
                echo PHP_EOL . 'Database ' . $db . ' not exist. Skipped!';
                continue;
            }

            $blocksTableName = Blocks::tableName();

            $blocks = Yii::$app->db
                ->createCommand("
                    SELECT * FROM $db.$blocksTableName;
                ")
                ->queryAll();

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

                // Convert old block `settings`
                if (is_array($blockSettings)) {
                    switch ((int)$blockSettings['column']) {
                        case 6:
                            $blockSettings['column'] = 2;
                            $blockSettings['bootstrap_column'] = 'col-md-6';
                            break;
                        case 4:
                            $blockSettings['column'] = 3;
                            $blockSettings['bootstrap_column'] = 'col-md-4';
                            break;
                        case 3:
                            $blockSettings['column'] = 4;
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

                $this->execute("
                    UPDATE $db.$blocksTableName
                    SET `content` = '$blockContent'
                    WHERE `code` =  '$blockCode';
                ");
            }
        }
    }

    public function down()
    {
        echo "m180518_113237_db___store_template__defaults__updates cannot be reverted.\n";

        return false;
    }
}
