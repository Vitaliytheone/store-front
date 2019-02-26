<?php

use yii\db\Migration;

/**
 * Class m190226_142939_store_table_pages_insert_404_page
 */
class m190226_142939_store_table_pages_insert_404_page extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            USE `' . DB_STORES . '`;
            INSERT INTO `pages` (`id`, `url`, `title`, `seo_description`, `seo_keywords`, `visibility`, `twig`, `json`, `json_draft`, `is_draft`, `created_at`, `updated_at`, `publish_at`) VALUES
            (NULL, \'404\', \'Page not found - 404\', \'Not found page\', \'not found, 404\', 1, \'<!DOCTYPE html>\r\n<html lang=\"en\">\r\n<head>\r\n<title>{{ title }}</title>\r\n</head>\r\n<style>\r\n    html,body{\r\n        background: #f8f9fa;\r\n        width: 100%;\r\n        height: 100%;\r\n        font-family: -apple-system,BlinkMacSystemFont,\"Segoe UI\",Roboto,\"Helvetica Neue\",Arial,sans-serif;\r\n        font-size: 1rem;\r\n        font-weight: 400;\r\n        line-height: 1.5;\r\n        margin: 0px;\r\n        padding: 0px;\r\n        color: #212529;\r\n    }\r\n    .page-row{\r\n        display: flex;\r\n        flex-wrap: wrap;\r\n        height: 100%;\r\n        width: 100%;\r\n        align-items: center;\r\n        text-align: center;\r\n    }\r\n    .page-content{\r\n        width: 100%;\r\n        text-align: center;\r\n    }\r\n    .page-title{\r\n        font-size: 150px;\r\n        margin-top: -100px;\r\n    }\r\n    .page-description{\r\n        color: #868e96;\r\n        font-size: 16px;\r\n    }\r\n    .page-description p{\r\n        padding: 0px;\r\n        margin: 0px;\r\n    }\r\n    @media(max-width: 768px){\r\n        .page-title{\r\n            font-size: 100px;\r\n            margin-top: -70px;\r\n        }\r\n    }\r\n</style>\r\n<body>\r\n<div class=\"page-row\">\r\n    <div class=\"page-content\">\r\n        <div class=\"page-title\">\r\n            {{code}}\r\n        </div>\r\n        <div class=\"page-description\">\r\n            <p>Not found</p>\r\n        </div>\r\n    </div>\r\n</div>\r\n</body>\r\n</html>\', \'\', \'\', 1, 1549537191, 1550760193, 1550050660);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('
            USE `' . DB_STORES . '`;
            DELETE FROM `pages` WHERE `pages`.`url` = 404;
        ');
    }

}
