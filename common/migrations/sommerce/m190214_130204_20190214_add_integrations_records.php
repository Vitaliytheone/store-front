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
              (NULL, 'chats', 'zendesk', 'Zendesk', 'ChatsWidget', '{\"snippet\":{\"type\":\"textarea\",\"label\":\"settings.integrations_edit.code_label\",\"name\":\"snippet\"}}', '', '1', '2', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
              (NULL, 'chats', 'jivochat', 'Jivochat', 'ChatsWidget', '{\"snippet\":{\"type\":\"textarea\",\"label\":\"settings.integrations_edit.code_label\",\"name\":\"snippet\"}}', '', '1', '1', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
              (NULL, 'chats', 'smartsupp', 'Smartsupp', 'ChatsWidget', '{\"snippet\":{\"type\":\"textarea\",\"label\":\"settings.integrations_edit.code_label\",\"name\":\"snippet\"}}', '', '1', '3', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
              (NULL, 'analytics', 'google_analytics', 'Google Analytics', 'AnalyticsWidget', '{\"snippet\":{\"type\":\"textarea\",\"label\":\"settings.integrations_edit.code_label\",\"name\":\"snippet\"}}', '', '1', '1', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)

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
