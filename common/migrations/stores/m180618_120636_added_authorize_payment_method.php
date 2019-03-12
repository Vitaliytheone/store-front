<?php

use yii\db\Migration;

/**
 * Class m180618_120636_added_authorize_payment_method
 */
class m180618_120636_added_authorize_payment_method extends Migration
{
    public function up()
    {
        $this->execute('
            USE `' . DB_STORES . '`;

            INSERT INTO `payment_gateways` (`method`, `currencies`, `name`, `class_name`, `url`, `position`, `options`) VALUES
            (\'authorize\',	\'[\"USD\"]\',	\'Authorize\',	\'Authorize\',	\'authorize\',	10,	\'{\"merchant_client_key\":\"\",\"merchant_login_id\":\"\",\"merchant_transaction_id\":\"\"}\');
        ');
    }

    public function down()
    {
        $this->execute('
            USE `' . DB_STORES . '`;
            
            DELETE FROM `payment_gateways`
            WHERE ((`method` = \'authorize\'));
        ');
    }
}
