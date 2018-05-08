<?php

use yii\db\Migration;
use \yii\helpers\ArrayHelper;
use \common\models\panels\SslCert;


/**
 * Class m180508_091331_db_panels_table_ssl_sert__csr_code__csr_key__fields_added
 */
class m180508_091331_db_panels_table_ssl_sert__csr_code__csr_key__fields_added extends Migration
{
    public function up()
    {
        $this->execute('
            USE `' . DB_PANELS . '`;
            ALTER TABLE `ssl_cert` ADD `csr_code` TEXT  NULL  AFTER `domain`;
            ALTER TABLE `ssl_cert` ADD `csr_key` TEXT  NULL  AFTER `csr_code`;        
        ');

        foreach (SslCert::find()->all() as $ssl) {
            $csr = ArrayHelper::getValue($ssl->getDetails(), SslCert::DETAILS_CSR);

            $ssl->csr_code = ArrayHelper::getValue($csr, 'csr_code');
            $ssl->csr_key =  ArrayHelper::getValue($csr, 'csr_key');;
            $ssl->save(false);
        }
    }

    public function down()
    {
        $this->execute('
            USE `' . DB_PANELS . '`;
            ALTER TABLE `ssl_cert` DROP `csr_code`;
            ALTER TABLE `ssl_cert` DROP `csr_key`;        
        ');
    }
}
