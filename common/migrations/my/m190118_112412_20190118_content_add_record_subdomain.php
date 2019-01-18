<?php

use yii\db\Migration;

/**
 * Class m190118_112412_20190118_content_add_record_subdomain
 */
class m190118_112412_20190118_content_add_record_subdomain extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $text = "<p class=\"help-block\" style=\"margin-bottom: 5px\">Please visit your registrar\'s dashboard to change nameservers to:</p><ul style=\"color: #737373; padding-left: 20px\"><li>ns1.perfectdns.com</li><li>ns2.perfectdns.com</li></ul>";

        Yii::$app->db->createCommand("INSERT INTO content (`id`, `name`, `text`, `updated_at`) VALUES (NULL, 'subdomain_nameservers', '" . $text . "', '')")
            ->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand('DELETE FROM `content` WHERE name = "subdomain_nameservers"')->execute();
    }
}
