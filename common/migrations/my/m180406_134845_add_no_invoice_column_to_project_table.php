<?php

use yii\db\Migration;

/**
 * Handles adding no_invoice to table `project`.
 */
class m180406_134845_add_no_invoice_column_to_project_table extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `project` ADD `no_invoice` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 - disabled, 1 - enabled';");
    }

    public function down()
    {
        $this->execute("ALTER TABLE `project` DROP `no_invoice`;");
    }
}
