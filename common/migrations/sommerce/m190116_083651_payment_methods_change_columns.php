<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Class m190116_083651_payment_methods_change_columns
 */
class m190116_083651_payment_methods_change_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $methods = (new Query())
            ->select(['id', 'currencies', 'options', 'position'])
            ->from(DB_STORES . '.payment_methods')
            ->all();

        foreach ($methods as $method) {
            $currencies = json_decode($method['currencies'], true);
            if (empty($currencies)) {
                continue;
            }
            foreach ($currencies as $currency) {
                Yii::$app->db->createCommand()->insert(DB_STORES .'.payment_methods_currency', [
                    'method_id' => $method['id'],
                    'currency' => $currency,
                    'position' => $method['position'],
                    'created_at' => time(),
                    'updated_at' => time(),
                ])->execute();
            }
        }

        $this->execute('
            ALTER TABLE payment_methods
              DROP COLUMN `currencies`;
            
            ALTER TABLE payment_methods
              DROP COLUMN `position`;
            
            ALTER TABLE payment_methods
              DROP COLUMN `visibility`;
            
            ALTER TABLE payment_methods
              CHANGE `method` `method_name` varchar(255) NOT NULL;
            
            ALTER TABLE payment_methods
              CHANGE `options` `settings_form` text NULL;
            
            ALTER TABLE payment_methods
              CHANGE `name` `name` varchar(255) NOT NULL;
            
            ALTER TABLE payment_methods
              CHANGE `class_name` `class_name` varchar(255) NOT NULL;
            
            ALTER TABLE payment_methods
              CHANGE `url` `url` varchar(255) NOT NULL;
            
            ALTER TABLE payment_methods
              ADD `icon` varchar(64);
            
            ALTER TABLE payment_methods
              ADD `addfunds_form` text NULL;
            
            ALTER TABLE payment_methods
              ADD `settings_form_description` text NULL;
            
            ALTER TABLE payment_methods
              ADD `manual_callback_url` smallint(1) NOT NULL DEFAULT 0;
            
            ALTER TABLE payment_methods
              ADD `created_at` int(11) NULL;
            
            ALTER TABLE payment_methods
              ADD `updated_at` int(11) NULL;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('
            ALTER TABLE payment_methods
              CHANGE `method_name` `method` varchar(255) NOT NULL;

            ALTER TABLE payment_methods
              CHANGE `settings_form` `options` text NOT NULL;

            ALTER TABLE payment_methods
              ADD `currencies` varchar(3000) NULL;
            
            ALTER TABLE payment_methods
              ADD `position` tinyint(2) NOT NULL;

            ALTER TABLE payment_methods
              CHANGE `name` `name` varchar(300) NULL;

            ALTER TABLE payment_methods
              DROP COLUMN `addfunds_form`;
            
            ALTER TABLE payment_methods
              DROP COLUMN `settings_form_description`;
            
            ALTER TABLE payment_methods
              DROP COLUMN `manual_callback_url`;
            
           ALTER TABLE payment_methods
              DROP COLUMN `created_at`;

            ALTER TABLE payment_methods
              DROP COLUMN `updated_at`;
        ');
    }

}
