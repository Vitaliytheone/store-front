<?php

use yii\db\Migration;

/**
 * Class m190125_092417_add_registrator_column
 */
class m190125_092417_add_registrator_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            USE `' . DB_PANELS . '`;
            ALTER TABLE `domains` ADD `registrar` VARCHAR(250) NOT NULL AFTER `details`;
            UPDATE `domains` SET `registrar`=\'ahnames\';
            ALTER TABLE `domain_zones` ADD `registrar` VARCHAR(250) NOT NULL AFTER `price_transfer`;
            UPDATE `domain_zones` SET `registrar`=\'ahnames\';
        ');


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('
            USE `' . DB_PANELS . '`;
            ALTER TABLE `domains` DROP `registrar`;
            ALTER TABLE `domain_zones` DROP `registrar`;
        ');
    }


}
