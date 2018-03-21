<?php

namespace sommerce\modules\admin\models\forms;

use common\models\store\ActivityLog;
use common\models\stores\StoreAdminAuth;
use yii;
use yii\web\User;
use yii\behaviors\AttributeBehavior;
use common\models\store\Pages;
use common\models\store\Products;

/**
 * Class CreateProductForm
 * @package sommerce\modules\admin\forms
 */
class CreateProductForm extends Products
{
    /**
     * @var User
     */
    protected $_user;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => 'properties',
                    self::EVENT_BEFORE_UPDATE => 'properties',
                ],
                'value' => function ($event) {
                    /* @var $event yii\base\Event */
                    /* @var $model $this */
                    $model = $event->sender;
                    $propertiesValue = $model->getAttribute('properties');
                    return $propertiesValue ? json_encode($propertiesValue) : NULL;
                },
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    self::EVENT_AFTER_FIND => 'properties',
                ],
                'value' => function ($event) {
                    /* @var $event yii\base\Event */
                    /* @var $model $this */
                    $model = $event->sender;
                    return json_decode($model->getAttribute('properties'),true);
                },
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => 'position',
                ],
                'value' => function ($event) {
                    return $this->getNewProductPosition();
                },
            ],

        ];
    }

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return 'ProductForm';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'url', 'visibility'], 'required'],
            [['id', 'visibility'], 'integer'],
            [['seo_title', 'seo_description', 'url',], 'trim'],
            [['name', 'url'], 'string', 'max' => 255],
            [['description'], 'string'],
            [['seo_title', ], 'string', 'max' => 300],
            [['seo_description', ], 'string', 'max' => 1000],
            [['seo_keywords', ], 'string', 'max' => 2000],
            [['properties', 'position'], 'safe'],
            ['visibility', 'filter', 'filter' => function($value){ return (int)$value; }],

            ['url', 'match', 'pattern' => '/^[a-z0-9-_]+$/i'],
            ['url', 'unique'],
            ['url', 'unique', 'targetClass' => Pages::className(), 'targetAttribute' => ['url' => 'url'], 'filter' => ['deleted' => Pages::DELETED_NO]],
        ];
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->_user = $user;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * Custom model Validator
     * Validate does already exist _another_ `Product` with same `url`
     * @param $attribute
     * @param $params
     * @param $validator
     */
    function validateNoConflictUrl($attribute, $params, $validator)
    {
        $productModel = self::findOne(['url' => $this->getAttribute($attribute)]);
        if ($productModel && $productModel->id !== $this->id) {
            $this->addError($attribute, $validator->message);
        }
    }

    /**
     * Custom model Validator
     * Validate does already exist `Page` with same `url`
     * @param $attribute
     * @param $params
     * @param $validator
     */
    function validateNoConflictProductPage($attribute, $params, $validator)
    {
        $pageModel = Pages::findOne(['url' => $this->getAttribute($attribute)]);
        if ($pageModel) {
            $this->addError($attribute, $validator->message);
        }
    }

    /**
     * Check if exist `properties` array in post data on `create` or `update` action.
     * Populate postData by empty `properties` array if `properties` array does not exist.
     * @param array $postData
     * @return array
     */
    public function checkPropertiesField($postData)
    {
        if (!isset($postData[$this->formName()]['properties'])) {
            $postData[$this->formName()]['properties'] = [];
        }
        return $postData;
    }

    /**
     * Calculate `position` for new product record
     * @return array|bool|int
     */
    public function getNewProductPosition()
    {
        $maxPosition = static::getMaxPosition();
        $position = is_null($maxPosition) ? 0 : $maxPosition + 1;
        return $position;
    }

    /**
     * Create new product
     * @param $postData
     * @return $this|bool
     */
    public function create($postData)
    {
        $postData = $this->checkPropertiesField($postData);

        if (!$this->load($postData) || !$this->save()) {
            return false;
        }

        /** @var StoreAdminAuth $identity */
        $identity = $this->getUser()->getIdentity(false);

        ActivityLog::log($identity, ActivityLog::E_PRODUCTS_PRODUCT_ADDED, $this->id, $this->id);

        return $this;
    }

    /**
     * Edit exiting product form
     * @param $postData
     * @return $this|bool
     */
    public function edit($postData)
    {
        $postData = $this->checkPropertiesField($postData);

        if (!$this->load($postData) || !$this->validate()) {
            return false;
        }

        $changedAttributes = $this->getDirtyAttributes();

        if (!$this->save()) {
            return false;
        }

        $this->_changeLog($changedAttributes);

        return $this;
    }

    /**
     * Write changes to log
     * @param $changedAttributes
     * @return bool
     */
    private function _changeLog($changedAttributes)
    {
        /** @var StoreAdminAuth $identity */
        $identity = $this->getUser()->getIdentity(false);

        ActivityLog::log($identity, ActivityLog::E_PRODUCTS_PRODUCT_UPDATED, $this->id, $this->id);

        if (isset($changedAttributes['visibility'])) {
            ActivityLog::log($identity, ActivityLog::E_PRODUCTS_PRODUCT_VISIBILITY_CHANGED, $this->id, $this->id);
        }

        if (isset($changedAttributes['url'])) {
            ActivityLog::log($identity, ActivityLog::E_PRODUCTS_PRODUCT_URL_CHANGED, $this->id, $this->id);
        }
    }

}