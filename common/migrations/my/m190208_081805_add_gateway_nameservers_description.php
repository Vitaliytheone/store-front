<?php

use yii\db\Migration;

/**
 * Class m190208_081805_add_gateway_nameservers_description
 */
class m190208_081805_add_gateway_nameservers_description extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            USE `" . DB_PANELS . "`;
            
            INSERT INTO `content` ( `name`, `text`) VALUES
            ('gateways_nameservers',	'<p class=\"help - block\" style=\"margin - bottom: 5px\">Please visit your registrar\'s dashboard to change nameservers to:</p><ul style=\"color: #737373; padding-left: 20px\"><li>ns1.managerdns.com</li><li>ns2.managerdns.com</li></ul>');
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("
            USE `" . DB_PANELS . "`;
            
            DELETE FROM `content`
            WHERE ((`name` = 'gateways_nameservers'));
        ");
    }
}