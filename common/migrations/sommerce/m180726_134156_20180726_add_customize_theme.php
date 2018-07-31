<?php

use yii\db\Migration;

/**
 * Class m180726_134156_20180726_add_customize_theme
 */
class m180726_134156_20180726_add_customize_theme extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('USE ' . DB_STORES);
        $this->addColumn('default_themes', 'customize_js', 'tinyint(1) NOT NULL DEFAULT 0');
        $this->execute('INSERT INTO `default_themes` (`name`, `folder`, `position`, `thumbnail`, `customize_js`) VALUES
        (\'Classic\', \'store_classic\', \'4\', \'/\', \'1\');');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('
            USE `' . DB_STORES . '`;
            
            DELETE FROM `default_themes`
            WHERE ((`name` = \'Classic\'));
        ');
        $this->dropColumn('default_themes', 'customize_js');
    }

}
