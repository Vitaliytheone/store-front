<?php

namespace sommerce\modules\admin\models\forms;

use Codeception\PHPUnit\Constraint\Page;
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
                'class' => AttributeBehavior::class,
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
                'class' => AttributeBehavior::class,
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
                'class' => AttributeBehavior::class,
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
            [['name', 'visibility'], 'required'],
            [['id', 'visibility'], 'integer'],
            [['seo_title', 'seo_description'], 'trim'],
            [['name', 'url'], 'string', 'max' => 255],
            [['description'], 'string'],
            [['seo_title', ], 'string', 'max' => 300],
            [['seo_description', ], 'string', 'max' => 1000],
            [['seo_keywords', ], 'string', 'max' => 2000],
            [['properties', 'position'], 'safe'],
            ['visibility', 'filter', 'filter' => function($value){ return (int)$value; }],
            ['color', 'string', 'max' => 255],
            ['color', 'filter', 'filter' => function($color){ return empty($color) ? null : $color; }],

            ['url', 'trim' ],
            ['url', 'match', 'pattern' => '/^[a-z0-9-_]+$/i'],
            ['url', 'unique'],
            ['url', 'unique', 'targetClass' => Pages::class, 'targetAttribute' => ['url' => 'url'], 'filter' => ['deleted' => Pages::DELETED_NO]],
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
     * @return array|bool
     * @throws \Throwable
     */
    public function create($postData)
    {
        if (isset($postData['packages'])) {
            unset($postData['packages']);
        }

        if (!$this->load($postData, '') || !$this->validate() || !$this->save(false)) {
            return false;
        }

        $this->_filterUrl();

        /** @var StoreAdminAuth $identity */
        $identity = $this->getUser()->getIdentity(false);

        ActivityLog::log($identity, ActivityLog::E_PRODUCTS_PRODUCT_ADDED, $this->id, $this->id);

        return $this->attributes;
    }

    /**
     * Edit exiting product form
     * @param $postData
     * @return $this|bool
     * @throws \Throwable
     */
    public function edit($postData)
    {
        if (!$this->load($postData, '') || !$this->validate()) {
            return false;
        }

        $changedAttributes = $this->getDirtyAttributes();

        if (!$this->save()) {
            return false;
        }

        $this->_changeLog($changedAttributes);

        return $this;
    }

    /** Get result data
     * @param bool $packages
     * @return array
     */
    public function getData($packages = false)
    {
        $data = $this->getAttributes();
        $data['properties'] = isset($data['properties']) ? $data['properties'] : [];

        if (!$packages) {
            return $data;
        }

        $data['packages'] = [];

        return $data;
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
     * Write changes to log
     * @param $changedAttributes
     * @throws \Throwable
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

    /**
     * Filter and generate URL for empty product url
     */
    private function _filterUrl()
    {
        $url = trim($this->url, ' ');
        $url = trim($url, '_');
        $url = trim($url, '-');

        if (!empty($url)) {
            return;
        }

        $url = Products::NEW_PRODUCT_URL_PREFIX . $this->id;

        $_url = $url;
        $postfix = 1;

        while (Pages::findOne(['url' => $_url, 'deleted' => Pages::DELETED_NO])) {
            $_url = $url . '-' . $postfix;
            $postfix++;
        };

        $this->url = $_url;
        $this->save(false);
    }
}