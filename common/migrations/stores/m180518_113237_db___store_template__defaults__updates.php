<?php

use yii\db\Migration;
use yii\helpers\ArrayHelper;
use common\models\store\Blocks;
use sommerce\helpers\BlockHelper;
use yii\base\Exception;

/**
 * Class m180518_113237_db___store_template__defaults__updates
 */
class m180518_113237_db___store_template__defaults__updates extends Migration
{
    /**
     * Обновляет таблицу блоков шаблонной базы магазинов новыми дефолтными значениями блоков
     * @throws Exception
     */
    public function up()
    {
        $dbName = Yii::$app->params['storeDefaultDatabase'];
        $tableName = $dbName . '.' . Blocks::tableName();

        $isDbExist = Yii::$app->db
            ->createCommand("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbName'")
            ->queryScalar();

        if (!$isDbExist) {
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

            $this->execute("
                UPDATE $tableName
                SET `content` = '$defaultContent'
                WHERE `code` =  '$blockCode';
            ");
        }
    }

    public function down()
    {
        echo "m180518_113237_db___store_template__defaults__updates cannot be reverted.\n";

        return false;
    }
}
