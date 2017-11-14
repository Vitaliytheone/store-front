<?php

namespace frontend\modules\admin\models\forms;

use yii;
use yii\db\Query;
use yii\behaviors\AttributeBehavior;
use common\models\store\Products;
/**
 * Class CreatePackageForm
 * @package frontend\modules\admin\models\forms
 */
class CreatePackageForm extends \common\models\store\Packages
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
                    self::EVENT_BEFORE_INSERT => 'position',
                ],
                'value' => function ($event) {
                    return $this->getNewPosition();
                },
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => 'provider_id',
                    self::EVENT_BEFORE_UPDATE => 'provider_id',
                ],
                'value' => function ($event) {
                    return $this->mode == self::MODE_MANUAL ? NULL : $this->provider_id;
                },
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => 'provider_service',
                    self::EVENT_BEFORE_UPDATE => 'provider_service',
                ],
                'value' => function ($event) {
                    return $this->mode == self::MODE_MANUAL ? NULL : $this->provider_service;
                },
            ],

        ];
    }

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return 'PackageForm';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'name', 'price', 'quantity'], 'required'],
            [['id', 'quantity', 'link_type', 'product_id', 'visibility', 'best', 'mode', 'provider_id', 'deleted', 'position'], 'integer'],
            [['price'], 'number'],
            [['name', 'provider_service'], 'string', 'max' => 255],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Products::className(), 'targetAttribute' => ['product_id' => 'id']],
        ];
    }

    /**
     * Calculate `position` for new package record
     * @return int
     */
    public function getNewPosition()
    {
        $maxPosition = $this->getMaxPosition();
        $position = is_null($maxPosition) ? 0 : $maxPosition + 1;
        return $position;
    }

    /**
     * Get Max position of current records
     * @return array|bool
     */
    public function getMaxPosition()
    {
        $productId = $this->getAttribute('product_id');
        $db = yii::$app->store->getInstance()->db_name;
        $query = (new Query())
            ->select(['MAX(position) position'])
            ->from("$db.packages")
            ->where(['product_id' => $productId])
            ->one();

        return $query['position'];
    }

    /**
     * Move package to new position
     * @param $newPosition
     * @return bool|int
     */
    public function changePosition($newPosition)
    {
        $maxPosition = static::getMaxPosition();
        $currentPosition = $this->getAttribute('position');
        $productId = $this->getAttribute('product_id');

        if ($newPosition < 0 || $newPosition > $maxPosition) {
            return false;
        }

        $db = $this->getDb();
        $query = $db->createCommand('
                  UPDATE `packages` SET
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
                  WHERE `deleted` = :deleted AND `product_id` = :product
            ')
            ->bindValue(':newPos', $newPosition)
            ->bindValue(':curPos', $currentPosition)
            ->bindValue(':deleted', self::DELETED_NO)
            ->bindValue(':product', $productId)
            ->execute();

        if ($query) {
            $this->setAttribute('position', $newPosition);
        }
        return $this->getAttribute('position');
    }

    /**
     * Virtual package deleting
     * @return bool
     */
    public function deleteVirtual()
    {
        $oldPosition = $this->getAttribute('position');

        $this->setAttributes([
            'deleted' => self::DELETED,
            'position' => NULL
        ]);
        if (!$this->save(false)) {
           return false;
        }
        $this->updatePositionsAfterDelete($oldPosition);
        return true;
    }

    /**
     * Update packages positions in current product set
     * @param $oldPosition
     * @return int
     */
    public function updatePositionsAfterDelete($oldPosition)
    {
        $productId = $this->getAttribute('product_id');
        $db = $this->getDb();
        $query = $db->createCommand('UPDATE `packages` SET `position` = `position`-1 WHERE `product_id` = :product AND `position` > :oldPos AND `deleted` = :deleted')
            ->bindValue(':product', $productId)
            ->bindValue(':oldPos', $oldPosition)
            ->bindValue(':deleted', self::DELETED_NO)
            ->execute();
        return $query;
    }
}