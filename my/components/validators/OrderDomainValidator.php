<?php

namespace my\components\validators;

use common\models\gateways\Sites;
use common\models\stores\Stores;
use my\helpers\DomainsHelper;
use my\models\forms\OrderPanelForm;
use Yii;
use common\models\panels\Orders;
use common\models\panels\Project;
use yii\base\Model;

/**
 * Class OrderDomainValidator
 * @package my\components\validators
 */
class OrderDomainValidator extends BaseDomainValidator
{
    protected $domain;

    /** @var bool */
    public $panel = false;

    /** @var bool */
    public $store = false;

    /** @var bool */
    public $child_panel = false;

    /** @var bool */
    public $gateway = false;

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

            $domain = $model->has_domain == OrderPanelForm::HAS_SUBDOMAIN ? $domain : $result['domain'];
            $domain = mb_strtolower(trim($domain));
        }

        $model->preparedDomain = $domain;

        $hasAvailableProject = Project::find()->andWhere([
            'site' => $domain,
            'act' => [
                Project::STATUS_ACTIVE,
                Project::STATUS_FROZEN
            ]
        ])->exists();

        // Если есть активная панель, то выдаем ошибку что уже существует панель
        if ($hasAvailableProject) {
            $model->addError($attribute, Yii::t('app', 'error.panel.domain_is_already_exist'));
            return false;
        }

        $hasAvailableStores = Stores::find()
            ->joinWith([
                'storeDomains'
            ])->andWhere([
                'store_domains.domain' => $domain,
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

        $hasAvailableGateway = Sites::find()
            ->where([
                'domain' => $domain,
                'status' => [
                    Sites::STATUS_ACTIVE,
                    Sites::STATUS_FROZEN
                ]
            ])
            ->exists();

        if ($hasAvailableGateway) {
            $model->addError($attribute, Yii::t('app', 'error.panel.domain_is_already_exist'));
            return false;
        }

        /**
         * @var Orders $hasOrder
         */
        // Если есть заказы отличные неоплаченные или оплаченные и не добавленные
        $hasOrder = Orders::find()->andWhere([
            'status' => [
                Orders::STATUS_PENDING,
                Orders::STATUS_PAID
            ],
            'domain' => $domain
        ])->one();

        if (!empty($hasOrder)) {
            if (Orders::STATUS_PENDING == $hasOrder->status) {
                $item = Orders::ITEM_BUY_PANEL;
                if ($this->child_panel) {
                    $item = Orders::ITEM_BUY_CHILD_PANEL;
                } elseif ($this->store) {
                    $item = Orders::ITEM_BUY_STORE;
                } elseif ($this->gateway) {
                    $item = Orders::ITEM_BUY_GATEWAY;
                }

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

        // uncomment to enable only (a-Z\d-) validation for all domains (including user domains)
        /*
        $registrar = DomainsHelper::getRegistrarClass($domain);
        $result = $registrar::domainsCheck($domain);

        if (empty($result[$domain])) {
            $model->addError($attribute, Yii::t('app', 'error.panel.invalid_domain_name'));
            return false;
        }
        */

        $existsDomain = Orders::find()->andWhere([
            'domain' => DomainsHelper::idnToAscii($domain),
            'item' => Orders::ITEM_BUY_DOMAIN,
            'status' => [
                Orders::STATUS_PENDING,
                Orders::STATUS_PAID,
                Orders::STATUS_ADDED,
                Orders::STATUS_ERROR
            ]
        ])->exists();

        if ($existsDomain) {
            $model->addError($attribute, Yii::t('app', 'error.panel.domain_is_already_exist'));
            return false;
        }
    }
}