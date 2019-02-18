<?php

use yii\db\Migration;

/**
 * Class m190214_130204_20190214_add_integrations_records
 */
class m190214_130204_20190214_add_integrations_records extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            USE `" . DB_STORES . "`;

            INSERT INTO `integrations`
              (`id`, `category`, `code`, `name`, `widget_class`, `settings_form`, `settings_description`, `visibility`, `position`, `created_at`, `updated_at`)
            VALUES
              (NULL, 'chats', 'zendesk', 'Zendesk', 'ChatsWidget', '{\"snippet\":{\"type\":\"textarea\",\"label\":\"settings.integrations_edit.code_label\",\"name\":\"snippet\"}}', 'Enabling this integration allows you to add Online Chat widget to your shop website. Copy and paste a code snippet from your  <a href=\"https://www.zendesk.com/chat/live-chat-widget/\" target=\"_blank\">Zendesk</a> account to the form below.', '1', '2', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
              (NULL, 'chats', 'jivochat', 'Jivochat', 'ChatsWidget', '{\"snippet\":{\"type\":\"textarea\",\"label\":\"settings.integrations_edit.code_label\",\"name\":\"snippet\"}}', 'Enabling this integration allows you to add Online Chat widget to your shop website. Copy and paste a code snippet from your  <a href=\"https://www.jivochat.com/\" target=\"_blank\">Jivochat</a> account to the form below.', '1', '1', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
              (NULL, 'chats', 'smartsupp', 'Smartsupp', 'ChatsWidget', '{\"snippet\":{\"type\":\"textarea\",\"label\":\"settings.integrations_edit.code_label\",\"name\":\"snippet\"}}', 'Enabling this integration allows you to add Online Chat widget to your shop website. Copy and paste a code snippet from your  <a href=\"https://www.smartsupp.com/\" target=\"_blank\">Smartsupp</a> account to the form below.', '1', '3', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
              (NULL, 'analytics', 'google_analytics', 'Google Analytics', 'AnalyticsWidget', '{\"snippet\":{\"type\":\"textarea\",\"label\":\"settings.integrations_edit.code_label\",\"name\":\"snippet\"}}', 'Enabling this integration allows you to add Google Analytics to your shop website. Copy and paste a Global Site Tag (gtag.js) from your <a href=\"https://analytics.google.com\" target=\"_blank\">Google Analytics</a> account and paste it to the form below', '1', '1', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('integrations');
    }
}
