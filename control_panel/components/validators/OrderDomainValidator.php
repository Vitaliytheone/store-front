<?php

namespace control_panel\components\validators;

use common\models\sommerces\Stores;
use control_panel\helpers\DomainsHelper;
use control_panel\models\forms\OrderStoreForm;
use Yii;
use common\models\sommerces\Orders;
use yii\base\Model;

/**
 * Class OrderDomainValidator
 * @package control_panel\components\validators
 */
class OrderDomainValidator extends BaseDomainValidator
{
    protected $domain;

    /** @var bool */
    public $store = false;

    /**
     * Validate domain
     * @param Model $model
     * @param mixed $attribute
     * @return bool
     * @throws \yii\base\UnknownClassException
     */
    public function validateAttribute($model, $attribute)
    {
        $this->domain = $model->{$attribute};

        if (!$this->isValidDomainZone()) {
            $model->addError($attribute, Yii::t('app', 'error.panel.invalid_domain'));
            return false;
        }

        if (!$this->isValidDomainName()) {
            $model->addError($attribute, Yii::t('app', 'error.panel.invalid_domain'));
            return false;
        }

        if (method_exists($model, 'isValidateDomain') && !$model->isValidateDomain()) {
            return true;
        }

        if ($model->hasErrors($attribute)) {
            return false;
        }

        $this->user_id = $model->getUser()->id;

        $originalDomain = $this->domain;
        $domain = $this->prepareDomain();

        if (empty($domain)) {
            $model->addError($attribute, Yii::t('app', 'error.panel.empty_domain'));
            return false;
        }

        if (!filter_var('info@' . $domain, FILTER_VALIDATE_EMAIL)) {
            $model->addError($attribute, Yii::t('app', 'error.panel.invalid_domain'));
            return false;
        }

        if (Yii::$app->params['whoisxml']) {
            $result = $this->isExistDomainName($domain);

            if (!$result['result']) {
                $model->addError($attribute, Yii::t('app', 'error.panel.domain_is_not_registered'));
                return false;
            }

            $domain = $model->has_domain == OrderStoreForm::HAS_SUBDOMAIN ? $domain : $result['domain'];
            $domain = mb_strtolower(trim($domain));
        }

        $model->preparedDomain = $domain;

        $hasAvailableStores = Stores::find()
            ->joinWith([
                'storeDomains'
            ])->andWhere([
                'store_domains.domain' => $originalDomain,
                'stores.status' => [
                    Stores::STATUS_ACTIVE,
                    Stores::STATUS_FROZEN
                ]
            ])->exists();

        // Если есть активный магазин, то выдаем ошибку что уже существует панель или магазин
        if ($hasAvailableStores) {
            $model->addError($attribute, Yii::t('app', 'error.panel.domain_is_already_exist'));
            return false;
        }

        /**
         * @var Orders $hasOrder
         */
        // Если есть заказы отличные неоплаченные или оплаченные и не добавленные
        $hasOrder = Orders::find()
            ->andWhere([
                'status' => [
                    Orders::STATUS_PENDING,
                    Orders::STATUS_PAID,
                ],
                'domain' => $domain,
            ])
            ->orderBy('id DESC')
            ->one();

        if (!empty($hasOrder)) {
            if (Orders::STATUS_PENDING == $hasOrder->status) {
                $item = Orders::ITEM_BUY_STORE;

                // Для заказов с отличным item и статусом pending не проверяем, а после создания нового заказа - отменяем предыдущий заказ
                if ($item != $hasOrder->item) {
                    return true;
                }
            }
        }

        if ($hasOrder) {
            $model->addError($attribute, Yii::t('app', 'error.panel.domain_is_already_exist'));
            return false;
        }

        $existsDomain = Orders::find()->andWhere([
            'domain' => DomainsHelper::idnToAscii($domain),
            'item' => Orders::ITEM_BUY_DOMAIN,
            'status' => [
                Orders::STATUS_PENDING,
                Orders::STATUS_PAID,
                Orders::STATUS_ERROR
            ]
        ])->exists();

        if ($existsDomain) {
            $model->addError($attribute, Yii::t('app', 'error.panel.domain_is_already_exist'));
            return false;
        }
    }
}