<?php

use yii\db\Migration;

/**
 * Class m180726_124226_db_panels_updated_project_added_no_referral_column
 */
class m180726_124226_db_panels_updated_project_added_no_referral_column extends Migration
{
    public function up()
    {
        $this->execute("
            USE `" . DB_PANELS . "`;

            ALTER TABLE `project`
            ADD `no_referral` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - disabled, 1 - enabled';
        ");
    }

    public function down()
    {
        $this->execute('
            ALTER TABLE `' . DB_PANELS . '`.`project`
            DROP `no_referral`;
        ');
    }
}
