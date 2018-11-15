<?php

use yii\db\Migration;

/**
 * Class m181114_113439_20181114_params_item_changed
 */
class m181114_113439_20181114_params_item_changed extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('UPDATE `params` SET `code` = \'whoisxmlapi\' WHERE `code` = \'whoxy\';');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('UPDATE `params` SET `code` = \'whoxy\' WHERE `code` = \'whoisxmlapi\';');
    }
}
