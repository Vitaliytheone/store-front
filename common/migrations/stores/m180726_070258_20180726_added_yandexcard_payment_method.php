<?php

use yii\db\Migration;

/**
 * Class m180726_070258_20180726_added_yandexcard_payment_method
 */
class m180726_070258_20180726_added_yandexcard_payment_method extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            USE `' . DB_STORES . '`;
            
            INSERT INTO `payment_gateways` (`method`, `currencies`, `name`, `class_name`, `url`, `position`, `options`) VALUES 
            (\'yandexcards\', \'[\"RUB\"]\', \'Yandex.Cards\', \'Yandexcards\', \'yandexcards\', \'14\', \'{\"wallet_number\":\"\",\"secret_word\":\"\",\"test_mode\":1}\');

        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('
            USE `' . DB_STORES . '`;
            
            DELETE FROM `payment_gateways`
            WHERE ((`method` = \'yandexcards\'));
        ');
    }
}
