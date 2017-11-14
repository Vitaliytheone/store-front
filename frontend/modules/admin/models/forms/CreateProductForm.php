<?php

namespace frontend\modules\admin\models\forms;

use yii;
use yii\behaviors\AttributeBehavior;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use common\helpers\DbHelper;
use common\models\store\Pages;
use common\models\store\Packages;

/**
 * Class CreateProductForm
 * @package frontend\modules\admin\forms
 */
class CreateProductForm extends \common\models\store\Products
{
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
            ['url', 'match', 'pattern' => '/^[a-z0-9-_]+$/i'],
            [['properties', 'position'], 'safe'],
            ['url', 'validateNoConflictUrl',
                'message' => 'Product with same url already exist in the database! Please use another url.',
            ],
            ['url', 'validateNoConflictProductPage',
                'message' => 'Page with same url already exist in the database! Please use another url.',
            ],
        ];
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
     * Get Max position for new inserts
     * @return array|bool
     */
    public static function getMaxPosition()
    {
        $db = yii::$app->store->getInstance()->db_name;
        $query = (new Query())
            ->select(['MAX(position) position'])
            ->from("$db.products")
            ->one();

        return $query['position'];
    }

    /**
     * Move product to new position
     * @param $newPosition
     * @return bool|int
     */
    public function changePosition($newPosition)
    {
        $maxPosition = static::getMaxPosition();
        $currentPosition = $this->getAttribute('position');

        if ($newPosition < 0 || $newPosition > $maxPosition) {
            return false;
        }
        $db = $this->getDb();
        $query = $db->createCommand('
                  UPDATE `products` SET
                      `position` = CASE
                          WHEN (`position` = :curPos) THEN 
                                :newPos                       -- replace new within old
                          WHEN (`position` > :curPos and `position` <= :newPos) THEN 
                                `position`- 1                 -- moving up
                          WHEN (`position` < :curPos and `position` >= :newPos) THEN 
                                `position`+ 1                 -- moving down
                          ELSE 
                                `position`                    -- otherwise lets keep same value.
                      END
            ')
            ->bindValue(':newPos', $newPosition)
            ->bindValue(':curPos', $currentPosition)
            ->execute();

        if ($query) {
            $this->setAttribute('position', $newPosition);
        }
        return $this->getAttribute('position');
    }

    /**
     * Return Products - Packages
     * @return array
     */
    public static function getProductsPackages()
    {
        $storeDb = yii::$app->store->getInstance()->db_name;
        $storesDb = DbHelper::getDsnAttribute('name', yii::$app->getDb());

        $productsRows = (new Query())
            ->select([
                'pr.id pr_id', 'pr.name pr_name', 'pr.position pr_position', 'pr.visibility pr_visibility',
                'pk.id pk_id', 'pk.product_id pk_pr_id', 'pk.name pk_name', 'pk.position pk_position', 'pk.visibility pk_visibility', 'pk.mode pk_mode', 'pk.price pk_price', 'pk.quantity pk_quantity', 'pk.deleted pk_deleted',
                'prv.site'
            ])
            ->from("$storeDb.products pr")
            ->leftJoin("$storeDb.packages pk", 'pk.product_id = pr.id AND pk.deleted = :deleted', [':deleted' => Packages::DELETED_NO])
            ->leftJoin("$storesDb.providers prv", 'prv.id = pk.provider_id')
            ->orderBy(['pr.position' => SORT_ASC, 'pk.position' => SORT_ASC])
            ->all();

        // Make products packages
        $productIds = array_unique(array_column($productsRows, 'pr_id'));
        $productsPackages = [];
        foreach ($productIds as $productId) {

            // Make product`s packages
            $productPackages = array_filter($productsRows, function($productRow) use ($productId){
                return $productId == $productRow['pk_pr_id'];
            });
            array_walk($productPackages, function (&$package, $key) {
                $package = [
                    'id' => $package['pk_id'],
                    'product_id' => $package['pr_id'],
                    'name' => $package['pk_name'],
                    'position' => $package['pk_position'],
                    'visibility' => $package['pk_visibility'],
                    'mode' => $package['pk_mode'],
                    'price' => $package['pk_price'],
                    'quantity' => $package['pk_quantity'],
                    'provider' => $package['site'],
                    'deleted' => $package['pk_deleted'],
                ];
            });

            // Make product
            $currentProductKey = array_search($productId, array_column($productsRows, 'pr_id'));
            $currentRow = $productsRows[$currentProductKey];
            $productsPackages[$productId] = [
                'id' => $productId,
                'name' => $currentRow['pr_name'],
                'position' => $currentRow['pr_position'],
                'visibility' => $currentRow['pr_visibility'],
                'packages' => $productPackages,
            ];
        }

        return $productsPackages;
    }
}