<?php

use yii\db\Migration;

/**
 * Class m190109_080400_20190109_added_stripe_3d_payment_method
 */
class m190109_080400_20190109_added_stripe_3d_payment_method extends Migration
{
    public function up()
    {
        $this->execute('
            USE `' . DB_STORES . '`;
            INSERT INTO `payment_gateways` (`id`, `method`, `currencies`, `name`, `class_name`, `url`, `position`, `options`, `visibility`) VALUES (NULL, \'stripe_3d_secure\', \'[\"RUB\", \"USD\"]\', \'Stripe 3D Secure\', \'Stripe3dSecure\', \'stripe\', \'17\', \'{\"public_key\":\"\",\"secret_key\":\"\",\"webhook_secret\": \"\"}\', \'1\');
        ');
    }

    public function down()
    {
        $this->execute('
            USE `' . DB_STORES . '`;
            DELETE FROM `payment_gateways` WHERE `method` = \'stripe_3d_secure\';
        ');
    }
}
