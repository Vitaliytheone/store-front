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
        Yii::$app->db->createCommand('USE `' . DB_STORES . '`;
        
        UPDATE `payment_methods` SET `currencies` = \'[\"USD\",\"AUD\",\"BRL\",\"CAD\",\"CZK\",\"DKK\",\"EUR\",\"HKD\",\"HUF\",\"ILS\",\"JPY\",\"MYR\",\"MXN\",\"NZD\",\"NOK\",\"PHP\",\"PLN\",\"GBP\",\"RUB\",\"SGD\",\"SEK\",\"CHF\",\"TWD\",\"THB\",\"INR\",\"IDR\",\"KRW\"]\' 
        WHERE `payment_methods`.`class_name` = \'Paypal\';
        
        UPDATE `payment_methods` SET `currencies` = \'[\"USD\",\"AUD\",\"BRL\",\"CAD\",\"CZK\",\"DKK\",\"EUR\",\"HKD\",\"HUF\",\"ILS\",\"JPY\",\"MYR\",\"MXN\",\"NZD\",\"NOK\",\"PHP\",\"PLN\",\"GBP\",\"RUB\",\"SGD\",\"SEK\",\"CHF\",\"TWD\",\"THB\",\"TRY\"]\' 
        WHERE `payment_methods`.`class_name` = \'Twocheckout\';
        
        UPDATE `payment_methods` SET `currencies` = \'[\"USD\",\"AUD\",\"BRL\",\"CAD\",\"CZK\",\"DKK\",\"EUR\",\"HKD\",\"HUF\",\"ILS\",\"JPY\",\"MYR\",\"MXN\",\"NZD\",\"NOK\",\"PHP\",\"PLN\",\"GBP\",\"RUB\",\"SGD\",\"SEK\",\"CHF\",\"TWD\",\"THB\",\"INR\",\"IDR\",\"TRY\",\"KRW\"]\' 
        WHERE `payment_methods`.`class_name` = \'Coinpayments\';
        
        UPDATE `payment_methods` SET `currencies` = \'[\"BRL\"]\'
        WHERE `payment_methods`.`class_name` = \'Pagseguro\';
        
        UPDATE `payment_methods` SET `currencies` = \'[\"RUB\"]\'
        WHERE `payment_methods`.`class_name` = \'Webmoney\';
        
        UPDATE `payment_methods` SET `currencies` = \'[\"RUB\"]\'
        WHERE `payment_methods`.`class_name` = \'Yandexmoney\';
        
        UPDATE `payment_methods` SET `currencies` = \'[\"RUB\"]\'
        WHERE `payment_methods`.`class_name` = \'Freekassa\';
        
        UPDATE `payment_methods` SET `currencies` = \'[\"TRY\"]\'
        WHERE `payment_methods`.`class_name` = \'Paytr\';
        
        UPDATE `payment_methods` SET `currencies` = \'[\"TRY\"]\'
        WHERE `payment_methods`.`class_name` = \'Paywant\';
        
        UPDATE `payment_methods` SET `currencies` = \'[\"MYR\"]\'
        WHERE `payment_methods`.`class_name` = \'Billplz\';
        
        UPDATE `payment_methods` SET `currencies` = \'[\"USD\"]\'
        WHERE `payment_methods`.`class_name` = \'Authorize\';
        
        UPDATE `payment_methods` SET `currencies` = \'[\"RUB\"]\'
        WHERE `payment_methods`.`class_name` = \'Yandexcards\';
        
        UPDATE `payment_methods` SET `currencies` = \'[\"USD\", \"EUR\"]\'
        WHERE `payment_methods`.`class_name` = \'Stripe\';
        
        UPDATE `payment_methods` SET `currencies` = \'[\"BRL\"]\'
        WHERE `payment_methods`.`class_name` = \'Mercadopago\';
        
        UPDATE `payment_methods` SET `currencies` = \'[\"USD\"]\'
        WHERE `payment_methods`.`class_name` = \'Paypalstandard\';
        
        UPDATE `payment_methods` SET `currencies` = \'[\"EUR\"]\'
        WHERE `payment_methods`.`class_name` = \'Mollie\';
        
        UPDATE `payment_methods` SET `currencies` = \'[\"USD\", \"EUR\"]\'
        WHERE `payment_methods`.`class_name` = \'Stripe3dSecure\';
        ')->execute();

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
            USE `' . DB_STORES . '`;

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
            USE `' . DB_STORES . '`;

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
