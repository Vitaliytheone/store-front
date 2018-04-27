<?php

namespace common\models\store;

use Yii;
use \yii\db\Connection;
use \yii\db\ActiveRecord;
use \common\models\store\queries\MessagesQuery;

/**
 * This is the model class for table "{{%messages}}".
 *
 * @property integer $id
 * @property string $lang_code
 * @property string $section
 * @property string $name
 * @property string $value
 */
class Messages extends ActiveRecord
{
    const SECTION_404 = '404';
    const SECTION_CART = 'cart';
    const SECTION_CHECKOUT = 'checkout';
    const SECTION_CONTACT = 'contact';
    const SECTION_FOOTER = 'footer';
    const SECTION_ORDER = 'order';
    const SECTION_PAYMENT_RESULT = 'payment_result';
    const SECTION_PRODUCT = 'product';

    /**
     * @return Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->storeDb;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%messages}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lang_code'], 'string', 'max' => 10],
            [['section'], 'string', 'max' => 100],
            [['name'], 'string', 'max' => 500],
            [['value'], 'string', 'max' => 2000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'lang_code' => Yii::t('app', 'Language code in IETF lang format'),
            'section' => Yii::t('app', 'Message section'),
            'name' => Yii::t('app', 'Message variable name'),
            'value' => Yii::t('app', 'Message text'),
        ];
    }

    /**
     * @inheritdoc
     * @return MessagesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MessagesQuery(get_called_class());
    }

    /**
     * Return messages sections
     * @return array
     */
    public static function getSections()
    {
        return [
            static::SECTION_404 => Yii::t('admin', 'settings.languages_section_404'),
            static::SECTION_CART => Yii::t('admin', 'settings.languages_section_cart'),
            static::SECTION_CHECKOUT => Yii::t('admin', 'settings.languages_section_checkout'),
            static::SECTION_CONTACT => Yii::t('admin', 'settings.languages_section_contact'),
            static::SECTION_FOOTER => Yii::t('admin', 'settings.languages_section_footer'),
            static::SECTION_ORDER => Yii::t('admin', 'settings.languages_section_order'),
            static::SECTION_PAYMENT_RESULT => Yii::t('admin', 'settings.languages_section_result'),
            static::SECTION_PRODUCT => Yii::t('admin', 'settings.languages_section_product'),
        ];
    }

    /**
     * Return store messages array for language $langCode
     * @param $langCode
     * @return array
     */
    public static function getMessagesByLanguageCode($langCode)
    {
        return static::find()
            ->select(['id', 'lang_code', 'section', 'name', 'value'])
            ->where(['lang_code' => $langCode])
            ->indexBy('id')
            ->asArray()
            ->all();
    }

}
