<?php

namespace common\models\panels;

use Yii;
use yii\db\ActiveRecord;
use common\models\panels\queries\PanelLanguagesQuery;

/**
 * This is the model class for table "panel_languages".
 *
 * @property string $code
 * @property string $name
 * @property int $rtl (1- enabled, 0 - disabled)
 * @property int $default (1- enabled, 0 - disabled)
 */
class PanelLanguages extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'panel_languages';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code', 'name'], 'required'],
            [['rtl', 'default'], 'integer'],
            [['code'], 'string', 'max' => 10],
            [['name'], 'string', 'max' => 64],
            [['code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'code' => Yii::t('app', 'Code'),
            'name' => Yii::t('app', 'Name'),
            'rtl' => Yii::t('app', 'Rtl'),
            'default' => Yii::t('app', 'Default'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return PanelLanguagesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PanelLanguagesQuery(get_called_class());
    }
}
