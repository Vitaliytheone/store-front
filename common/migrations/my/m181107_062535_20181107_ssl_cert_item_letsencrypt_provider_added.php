<?php

use yii\db\Migration;

/**
 * Class m181107_062535_20181107_ssl_cert_item_letsencrypt_provider_added
 */
class m181107_062535_20181107_ssl_cert_item_letsencrypt_provider_added extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('INSERT INTO `ssl_cert_item` (`name`, `product_id`, `price`, `allow`, `generator`, `provider`) VALUES (\'Letsencrypt\', 0, 0.00, NULL, NULL, 2);');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('DELETE FROM `ssl_cert_item` WHERE `name` = \'Letsencrypt\';');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181107_062535_20181107_ssl_cert_item_letsencrypt_provider_added cannot be reverted.\n";

        return false;
    }
    */
}
