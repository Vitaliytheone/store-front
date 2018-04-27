<?php

use yii\db\Migration;

/**
 * Class m180427_141249_db_stores_table_payment_gateways__pgs_added
 */
class m180427_141249_db_stores_table_payment_gateways__pgs_added extends Migration
{
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->execute('
            USE `' . DB_STORES . '`;
            INSERT INTO `payment_gateways` (`method`, `currencies`, `name`)
            VALUES
                (\'webmoney\', \'[\"RUB\"]\', \'WebMoney\'),
                (\'yandexmoney\', \'[\"RUB\"]\', \'Yandex.Money\'),
                (\'freekassa\', \'[\"RUB\"]\', \'Free-Kassa\'),
                (\'paytr\', \'[\"TRY\"]\', \'PayTR\'),
                (\'paywant\', \'[\"TRY\"]\', \'PayWant\'),
                (\'pagseguro\', \'[\"BRL\"]\', \'PagSeguro\'),
                (\'billplz\', \'[\"MYR\"]\', \'Billplz\');
            ');
    }

    public function down()
    {
        $this->execute('
            USE `' . DB_STORES . '`;
            DELETE FROM `payment_gateways` WHERE `method` IN (\'webmoney\', \'yandexmoney\', \'freekassa\', \'paytr\', \'paywant\', \'pagseguro\', \'billplz\');
        ');
    }
}
