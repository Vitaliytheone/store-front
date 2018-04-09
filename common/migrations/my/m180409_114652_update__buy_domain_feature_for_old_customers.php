<?php

use yii\db\Migration;
use \common\models\panels\Project;
use \yii\db\Query;

/**
 * Class m180409_114652_update__buy_domain_feature_for_old_customers
 */
class m180409_114652_update__buy_domain_feature_for_old_customers extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180409_114652_update__buy_domain_feature_for_old_customers cannot be reverted.\n";

        return false;
    }

    public function up()
    {
        $customerIds = (new Query())
            ->select(`cid`)
            ->from(Project::tableName())
            ->where('`expired`-`date` > (45*24*6060)')
            ->orderBy(`cid`)
            ->column();

        $customerIds = implode(',', $customerIds);

        if (!$customerIds) {
            return;
        }

        $this->execute("
          USE `panels`;
          UPDATE `customers` SET `buy_domain` = '1' WHERE `id` IN ('$customerIds');
        ");
    }

    public function down()
    {
        echo "m180409_114652_update__buy_domain_feature_for_old_customers cannot be reverted.\n";

        return false;
    }
}
