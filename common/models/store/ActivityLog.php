<?php

namespace common\models\store;

use common\models\stores\StoreAdminAuth;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\store\queries\ActivityLogQuery;
use yii\web\Controller;

/**
 * This is the model class for table "{{%store.activity_log}}".
 *
 * @property integer $id
 * @property integer $admin_id
 * @property integer $super_user
 * @property integer $created_at
 * @property string $ip
 * @property string $controller
 * @property string $action
 * @property string $request_data
 * @property string $details
 * @property integer $details_id
 * @property integer $event
 */
class ActivityLog extends ActiveRecord
{
    // Events constants

    const E_ADMIN_ADMIN_AUTHORIZATION = 1001;
    const E_ADMIN_PASSWORD_CHANGED = 1002;

    const E_SETTINGS_GENERAL_STORE_NAME_CHANGED = 2001;
    const E_SETTINGS_GENERAL_STORE_TIMEZONE_CHANGED = 2002;
    const E_SETTINGS_GENERAL_STORE_ADMIN_EMAIL_CHANGED = 2003;
    const E_SETTINGS_GENERAL_STORE_SEO_TITLE_CHANGED = 2004;
    const E_SETTINGS_GENERAL_STORE_SEO_META_DESCRIPTION_CHANGED = 2005;

    const E_SETTINGS_PAYMENTS_PG_ACTIVE_STATUS_CHANGED = 2101;
    const E_SETTINGS_PAYMENTS_PG_SETTINGS_CHANGED = 2102;

    const E_SETTINGS_PROVIDERS_PROVIDER_ADEDD = 2201;
    const E_SETTINGS_PROVIDERS_PROVIDER_API_KEY_CHANGED = 2202;

    const E_SETTINGS_NAVIGATION_MENU_ITEM_ADDED = 2301;
    const E_SETTINGS_NAVIGATION_MENU_ITEM_DELETED = 2302;
    const E_SETTINGS_NAVIGATION_MENU_ITEM_UPDATED = 2303;
    const E_SETTINGS_NAVIGATION_MENU_ITEM_POSITION_CHANGED = 2304;

    const E_SETTINGS_PAGES_PAGE_ADDED = 2401;
    const E_SETTINGS_PAGES_PAGE_DELETED = 2402;
    const E_SETTINGS_PAGES_PAGE_UPDATED = 2403;

    const E_SETTINGS_THEMES_THEME_ADDED = 2501;
    const E_SETTINGS_THEMES_THEME_ACTIVATED = 2502;
    const E_SETTINGS_THEMES_THEME_FILE_UPDATED = 2503;
    const E_SETTINGS_THEMES_THEME_FILE_RESETED = 2504;

    const E_SETTINGS_BLOCKS_BLOCK_ACTIVE_STATUS_CHANGED = 2601;
    const E_SETTINGS_BLOCKS_BLOCK_UPDATED = 2602;

    const E_ORDERS_ORDER_STATUS_CHANGED = 3001;
    const E_ORDERS_ORDER_CANCELED = 3002;
    const E_ORDERS_ORDER_RESENT = 3003;

    const E_PRODUCTS_PRODUCT_ADDED = 5001;
    const E_PRODUCTS_PRODUCT_UPDATED = 5002;
    const E_PRODUCTS_PRODUCT_VISIBILITY_CHANGED = 5003;
    const E_PRODUCTS_PRODUCT_URL_CHANGED = 5004;
    const E_PRODUCTS_PRODUCT_POSITION_CHANGED = 5005;
    const E_PACKAGES_PACKAGE_ADDED = 5006;
    const E_PACKAGES_PACKAGE_UPDATED = 5007;
    const E_PACKAGES_PACKAGE_DELETED = 5008;
    const E_PACKAGES_PACKAGE_POSITION_CHANGED = 5009;
    const E_PACKAGES_PACKAGE_PRICE_CHANGED = 5010;
    const E_PACKAGES_PACKAGE_QUANTITY_CHANGED = 5011;
    const E_PACKAGES_PACKAGE_LINK_TYPE_CHANGED = 5012;
    const E_PACKAGES_PACKAGE_AVAILABILITY_CHANGED = 5013;
    const E_PACKAGES_PACKAGE_MODE_CHANGED = 5014;
    const E_PACKAGES_PACKAGE_PROVIDER_CHANGED = 5015;
    const E_PACKAGES_PACKAGE_PROVIDER_SERVICE_CHANGED = 5016;

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
                ],
                'value' => function() {
                    return time();
                },
            ],
        ];
    }

    /**
     * @inheritdoc
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
        return '{{%activity_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['admin_id', 'super_user', 'created_at', 'details_id', 'event'], 'integer'],
            [['request_data'], 'string'],
            [['ip', 'controller', 'action'], 'string', 'max' => 300],
            [['details'], 'string', 'max' => 1000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'admin_id' => Yii::t('app', 'Admin ID'),
            'super_user' => Yii::t('app', 'Super User'),
            'created_at' => Yii::t('app', 'Created At'),
            'ip' => Yii::t('app', 'Ip'),
            'controller' => Yii::t('app', 'Controller'),
            'action' => Yii::t('app', 'Action'),
            'request_data' => Yii::t('app', 'Request Data'),
            'details' => Yii::t('app', 'Details'),
            'details_id' => Yii::t('app', 'Details ID'),
            'event' => Yii::t('app', 'Event'),
        ];
    }

    /**
     * @inheritdoc
     * @return ActivityLogQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ActivityLogQuery(get_called_class());
    }

    /**
     * Create log record for admin activity event
     * @param StoreAdminAuth $identity
     * @param int $event
     * @param int|null $detailsId
     * @param string|null $details
     */
    public static function log(StoreAdminAuth $identity, int $event, $detailsId = null, $details = null)
    {

        if (empty($identity) || !$identity instanceof StoreAdminAuth) {
            return;
        }

        /** @var Controller $controller */
        $controller = Yii::$app->controller;

        if (empty($controller) || !$controller instanceof Controller) {
            return;
        }

        $model = new self();

        $model->setAttributes([
            'event' => $event,
            'details_id' => $detailsId,
            'details' => $details,
            'admin_id' => $identity->id,
            'super_user' => $identity->isSuperAdmin(),
            'ip' => Yii::$app->getRequest()->getUserIP(),
            'controller' => $controller->id,
            'action' => $controller->action->id,
            'request_data' => json_encode([$_SERVER, $_POST, $_GET]),
        ]);

        $model->save(false);
    }
}
