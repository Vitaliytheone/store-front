<?php

use yii\db\Migration;

/**
 * Class m181114_082723_20181114_stores_ssl_sert_item_changed
 */
class m181114_082723_20181114_stores_ssl_sert_item_changed extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('UPDATE `ssl_cert_item` SET `product_id` = \'1\' WHERE `provider` = \'2\';');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('UPDATE `ssl_cert_item` SET `product_id` = \'0\' WHERE `provider` = \'2\';');
    }
}
