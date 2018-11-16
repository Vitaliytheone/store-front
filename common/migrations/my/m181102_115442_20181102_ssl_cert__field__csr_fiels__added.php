<?php

use yii\db\Migration;

/**
 * Class m181102_115442_20181102_ssl_cert__field__csr_fiels__added
 */
class m181102_115442_20181102_ssl_cert__field__csr_fiels__added extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE `ssl_cert` ADD `csr_files` TEXT  NULL  COMMENT \'All genered csr files content\'  AFTER `csr_key`;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('ALTER TABLE `ssl_cert` DROP `csr_files`;');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181102_115442_20181102_ssl_cert__field__csr_fiels__added cannot be reverted.\n";

        return false;
    }
    */
}
