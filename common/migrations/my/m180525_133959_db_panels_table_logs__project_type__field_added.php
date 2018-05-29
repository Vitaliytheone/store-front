<?php

use yii\db\Migration;

/**
 * Class m180525_133959_db_panels_table_logs__project_type__field_added
 */
class m180525_133959_db_panels_table_logs__project_type__field_added extends Migration
{
    public function up()
    {
        $this->execute('
            USE `' . DB_PANELS . '`;
            ALTER TABLE `logs` ADD `project_type` TINYINT(1)  NULL  DEFAULT NULL  COMMENT \'1-Panel, 2-Store\'  AFTER `id`;
            UPDATE `logs` SET `project_type` = \'1\' WHERE `project_type` IS NULL;
            ALTER TABLE `logs` CHANGE `panel_id` `panel_id` INT(11)  NOT NULL  COMMENT \'panel_id, store_id\';
        ');
    }

    public function down()
    {
        $this->execute('
            USE `' . DB_PANELS . '`;
            ALTER TABLE `logs` DROP `project_type`;
            ALTER TABLE `logs` CHANGE `panel_id` `panel_id` INT(11)  NOT NULL;
        ');
    }
}
