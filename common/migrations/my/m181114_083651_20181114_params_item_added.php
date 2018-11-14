<?php

use yii\db\Migration;

/**
 * Class m181114_083651_20181114_params_item_added
 */
class m181114_083651_20181114_params_item_added extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('INSERT INTO `params` (`id`, `category`, `code`, `options`, `updated_at`, `position`) VALUES (NULL, \'service\', \'whoxy\', \'{\"new_order_form\": \"\", \"ssl\": \"ae26975900b61353xe50fe8ed8a5926ff\", \"active_panel\": \"\"}\', \'0\', NULL);');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('DELETE FROM `params` WHERE `category` = \'service\' AND `code` = \'whoxy\';');
    }
}
