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
            INSERT INTO `params` (`id`, `category`, `code`, `options`, `updated_at`, `position`) VALUES (NULL, \'service\', \'namesilo\', \'{\"namesilo.url\":\"http://sandbox.namesilo.com/api\",\"namesilo.key\":\"6f3bb35a23962b15be15c3c\",\"namesilo.payment_id\":\"485\",\"namesilo.version\":\"1\",\"namesilo.type\":\"xml\",\"namesilo.testmode\":\"0\"}\', \'1548833103\', NULL);
            INSERT INTO `domain_zones` (`id`, `zone`, `price_register`, `price_renewal`, `price_transfer`, `registrar`) VALUES (NULL, \'.XYZ\', \'2.00\', \'2.00\', \'2.00\', \'namesilo\');
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
            DELETE FROM `params` WHERE `params`.`code` = \'namesilo\';
            DELETE FROM `domain_zones` WHERE `domain_zones`.`zone` = \'.XYZ\';
        ');
    }


}
