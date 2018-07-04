<?php

use yii\db\Migration;

/**
 * Class m180704_081416_db_panels__table_content_update_item
 */
class m180704_081416_db_panels__table_content_update_item extends Migration
{
    public function up()
    {
        $this->execute("
            USE `" . DB_PANELS . "`;
            UPDATE `content` SET `text` = 'Please check your email <strong>{{email}}</strong> and follow payment approve link' WHERE `name` = 'paypal_verify_note';
        ");
    }

    public function down()
    {
        $this->execute("
            USE `" . DB_PANELS . "`;
            UPDATE `content` SET `text` = 'Please check your email and follow payment approve link' WHERE `name` = 'paypal_verify_note';
        ");
    }
}
