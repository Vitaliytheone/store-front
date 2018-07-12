<?php

use yii\db\Migration;

/**
 * Class m180706_154714_db_panels__table_panel_export__datetime_converter
 */
class m180706_154714_db_panels__table_panel_export__datetime_converter extends Migration
{

    /**
     * Converter panel_export export users items datetime to timestamp
     */
    public function up()
    {
        $exports = (new \yii\db\Query())
            ->select(['id', 'panel_id', 'details',])
            ->from(DB_PANELS . '.panel_exports')
            ->where(['type' => 2])
            ->indexBy('id')
            ->all();

        foreach ($exports as $id => $export) {

            $details = json_decode($export['details'], true);

            $fromDate = strtotime(\yii\helpers\ArrayHelper::getValue($details, 'from_date'));
            $toDate = strtotime(\yii\helpers\ArrayHelper::getValue($details, 'to_date'));

            \yii\helpers\ArrayHelper::setValue($details, ['from_date'], $fromDate);
            \yii\helpers\ArrayHelper::setValue($details, ['to_date'], $toDate);

            $details = json_encode($details);

            $this->execute("
                USE `" . DB_PANELS . "`;
                UPDATE `panel_exports` SET `details` = '$details' WHERE `id` = $id AND `type` = 2;
            ");
        }
    }

    public function down()
    {
    }
}
