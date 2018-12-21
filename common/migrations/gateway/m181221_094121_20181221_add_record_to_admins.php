<?php

use yii\db\Migration;
use common\models\gateways\Admins;

/**
 * Class m181221_094121_20181221_add_record_to_admins
 */
class m181221_094121_20181221_add_record_to_admins extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('SET foreign_key_checks = 0;');

        $admin = Admins::findOne(1);

        if ($admin) {
            $newAdmin = new Admins();
            $newAdmin->load($admin->attributes);
            $newAdmin->id = null;

            $newAdmin->save();
            $admin->delete();
        }

        $superadmin = new Admins();
        $superadmin->id = 1;
        $superadmin->site_id = Admins::SUPERADMIN_SITE_ID;
        $superadmin->status = Admins::STATUS_ACTIVE;
        $superadmin->save(false);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return;
    }
}
