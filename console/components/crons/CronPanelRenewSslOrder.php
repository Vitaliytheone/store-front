<?php

namespace console\components\crons;

use common\models\panels\Project;
use common\models\panels\SslCert;
use common\models\panels\SslCertItem;
use common\models\panels\Orders;
use console\components\crons\exceptions\CronException;
use yii\helpers\Console;

/**
 * Class CronPanelRenewSslOrder
 * @package console\components\crons
 */
class CronPanelRenewSslOrder extends CronBase
{
    /** @inheritdoc */
    public function run()
    {
        $this->stdout($this->cronTaskName() . ' started now', Console::FG_GREEN);

        $timeLeftToExpiry = 20 * 24 * 60 * 60; // 20 Days

        $sslList = SslCert::find()
            ->select([
                'ssl_id' => 'ssl.id', 'ssl_item_id' => 'ssl.item_id', 'ssl_status' => 'ssl.status',
                'panel_id' => 'panel.id', 'panel_cid' => 'panel.cid', 'panel_domain' => 'panel.site',
                'order_id' => 'order.id',
            ])
            ->alias('ssl')
            ->leftJoin(['panel' => Project::tableName()], 'panel.id = ssl.pid')
            ->leftJoin(['ssl_item' => SslCertItem::tableName()], 'ssl_item.id = ssl.item_id')
            ->leftJoin(['order' => Orders::tableName()], 'order.item = :orderItem AND order.item_id = ssl.id AND order.processing = :orderProcessing', [
                ':orderItem' => Orders::ITEM_PROLONGATION_FREE_SSL,
                ':orderProcessing' => Orders::PROCESSING_NO,
            ])
            ->andWhere(['IS', 'order.id', null])
            ->andWhere([
                '<',
                'ssl.expiry_at_timestamp',
                (time() + $timeLeftToExpiry)
            ])
            ->andWhere([
                'ssl_item.provider' => SslCertItem::PROVIDER_LETSENCRYPT,
                'ssl_item.product_id' => SslCertItem::PRODUCT_ID_LETSENCRYPT_BASE,
            ])
            ->andWhere([
                'ssl.project_type' => SslCert::PROJECT_TYPE_PANEL,
                'ssl.status' => SslCert::STATUS_ACTIVE,
            ])
            ->andWhere([
                'panel.act' => Project::STATUS_ACTIVE
            ])
            ->asArray()
            ->all();

        $this->stdout('Prolongation SSL count (' . count($sslList) . ')');

        SslCert::updateAll(['status' => SslCert::STATUS_RENEWED], ['id' => array_column($sslList, 'ssl_id')]);

        foreach ($sslList as $ssl) {

            $order = new Orders();
            $order->cid = $ssl['panel_cid'];
            $order->status = Orders::STATUS_PAID;
            $order->hide = Orders::HIDDEN_OFF;
            $order->processing = Orders::PROCESSING_NO;
            $order->domain = $ssl['panel_domain'];
            $order->item = Orders::ITEM_PROLONGATION_FREE_SSL;
            $order->item_id = $ssl['ssl_id'];
            $order->ip = '127.0.0.1';
            $order->setDetails([
                'pid' => $ssl['panel_id'],
                'project_type' => Project::getProjectType(),
                'domain' => $ssl['panel_domain'],
                'ssl_cert_item_id' => $ssl['ssl_item_id'],
            ]);

            if (!$order->save(false)) {
                throw new CronException('Cannot create order!');
            }

            $this->stdout('Letsencrypt SSL-renew order has been created successfully!');
            $this->stdout(print_r($order->attributes,1));
        }

        $this->stdout($this->cronTaskName() . ' finished', Console::FG_GREEN);
    }
}
