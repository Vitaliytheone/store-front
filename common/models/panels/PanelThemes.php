<?php

namespace common\models\panels;

use Yii;
use yii\db\ActiveRecord;
use common\models\panels\queries\PanelThemesQuery;

/**
 * This is the model class for table "{{%panel_themes}}".
 *
 * @property integer $id
 * @property integer $panel_id
 * @property integer $theme_id
 * @property integer $updated_at
 */
class PanelThemes extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return DB_PANELS . '.panel_themes';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['panel_id', 'theme_id', 'updated_at'], 'required'],
            [['panel_id', 'theme_id', 'updated_at'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'panel_id' => Yii::t('app', 'Panel ID'),
            'theme_id' => Yii::t('app', 'Theme ID'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     * @return PanelThemesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PanelThemesQuery(get_called_class());
    }
}
