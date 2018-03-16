<?php

namespace common\models\panels;

use Yii;
use yii\db\ActiveRecord;
use common\models\panels\queries\PanelDomainsQuery;

/**
 * This is the model class for table "{{%panel_domains}}".
 *
 * @property integer $id
 * @property integer $panel_id
 * @property string $domain
 * @property integer $type
 * @property string $created_at
 *
 * @property Project $panel
 */
class PanelDomains extends ActiveRecord
{
    const TYPE_STANDARD = 0;
    const TYPE_ADDITIONAL = 1;
    const TYPE_SUBDOMAIN = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%panel_domains}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['panel_id', 'domain', 'type'], 'required'],
            [['panel_id', 'type'], 'integer'],
            [['created_at'], 'safe'],
            [['domain'], 'string', 'max' => 255],
            [['domain'], 'unique'],
            [['panel_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::className(), 'targetAttribute' => ['panel_id' => 'id']],
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
            'domain' => Yii::t('app', 'Domain'),
            'type' => Yii::t('app', 'Type'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPanel()
    {
        return $this->hasOne(Project::className(), ['id' => 'panel_id']);
    }

    /**
     * @inheritdoc
     * @return PanelDomainsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PanelDomainsQuery(get_called_class());
    }

    /**
     * Get types list
     * @return array
     */
    public static function getTypes()
    {
        return [
            static::TYPE_STANDARD => Yii::t('app', 'panel_domains.type.standard'),
            static::TYPE_ADDITIONAL => Yii::t('app', 'panel_domains.type.additional'),
            static::TYPE_SUBDOMAIN => Yii::t('app', 'panel_domains.type.subdomain')
        ];
    }

    /**
     * Get type name
     * @return mixed
     */
    public function getTypeName()
    {
        return static::getTypes()[$this->type];
    }
}
