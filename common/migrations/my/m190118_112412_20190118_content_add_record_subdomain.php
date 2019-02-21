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
        $text = "<p class=\"help-block\" style=\"margin-bottom:5px\">Please visit your domain\'s DNS zone editor and set CNAME-record:</p>\r\n subdomain.yourdomain.com CNAME perfectpanel.com";

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
