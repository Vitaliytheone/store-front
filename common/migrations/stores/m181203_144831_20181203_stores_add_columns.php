<?php

use yii\db\Migration;

/**
 * Class m181203_144831_20181203_stores_add_columns
 */
class m181203_144831_20181203_stores_add_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(DB_STORES . '.stores', 'whois_lookup', $this->text()->defaultValue(null)->comment('Json domain data'));
        $this->addColumn(DB_STORES . '.stores', 'nameservers', $this->text()->defaultValue(null)->comment('Json domain nameservers data'));
        $this->addColumn(DB_STORES . '.stores', 'dns_checked_at', $this->integer(11)->defaultValue(null)->comment('Last dns-check timestamp'));
        $this->addColumn(DB_STORES . '.stores', 'dns_status', $this->tinyInteger(1)->defaultValue(null)->comment('dns-check result: null-неизвестно, 0-не наши ns, 1-наш ns'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(DB_STORES . '.stores', 'whois_lookup');
        $this->dropColumn(DB_STORES . '.stores', 'nameservers');
        $this->dropColumn(DB_STORES . '.stores', 'dns_checked_at');
        $this->dropColumn(DB_STORES . '.stores', 'dns_status');
    }
}
