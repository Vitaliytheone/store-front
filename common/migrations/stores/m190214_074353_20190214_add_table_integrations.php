<?php

use yii\db\Migration;

/**
 * Class m190214_074353_20180214_add_table_integrations
 */
class m190214_074353_20190214_add_table_integrations extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute(
            "USE `" . DB_STORES . "`;
        
            CREATE TABLE integrations (
              id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
              category varchar(255) NOT NULL,
              code varchar(255) NOT NULL UNIQUE,
              name varchar(255),
              widget_class varchar(255),
              settings_form text,
              settings_description text,
              visibility tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 - visible to all, 0 - invisible to all',
              position int,
              created_at int,
              updated_at int
            );"
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('integrations');
    }
}
