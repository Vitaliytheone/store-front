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
        $this->execute('INSERT INTO `default_themes` (`name`, `folder`, `position`, `thumbnail`, `customize`) VALUES
        (\'Classic\', \'store_classic\', \'4\', \'/img/themes/preview_smm24.png\', \'1\');');
        $this->addColumn('default_themes', 'customize', 'tinyint(1) NOT NULL DEFAULT 0');
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
        $this->dropColumn('default_themes', 'customize');
    }

}
