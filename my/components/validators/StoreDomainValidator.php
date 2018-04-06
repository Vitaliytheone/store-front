<?php
namespace my\components\validators;

use common\models\stores\Stores;
use Yii;
use common\models\panels\Orders;
use common\models\panels\Project;
use yii\base\Model;

/**
 * Class PanelDomainValidator
 * @package my\components\validators
 */
class StoreDomainValidator extends BaseDomainValidator
{
    protected $domain;

    /**
     * Validate domain
     * @param Model $model
     * @param mixed $attribute
     * @return bool
     */
    public function validateAttribute($model, $attribute)
    {
        if (method_exists($model, 'isValidateDomain') && !$model->isValidateDomain()) {
            return true;
        }

        if ($model->hasErrors($attribute)) {
            return false;
        }

        $this->domain = $model->{$attribute};
        $storeId = $model->getStore()->id;
        $this->user_id = $model->getUser()->id;

        $domain = $this->prepareDomain();

        if (!filter_var('info@' . $domain, FILTER_VALIDATE_EMAIL)) {
            $model->addError($attribute, Yii::t('app', 'error.store.invalid_domain'));
            return false;
        }

        if (Yii::$app->params['whoisxml']) {
            $result = $this->isValidDomainName($domain);

            if (!$result['result']) {
                $model->addError($attribute, Yii::t('app', 'error.store.domain_is_not_registered'));
                return false;
            }

            $domain = $result['domain'];
            $domain = strtolower($domain);
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
            $model->addError($attribute, Yii::t('app', 'error.store.domain_is_already_exist'));
            return false;
        }

        $hasAvailableStores = Stores::find()
            ->joinWith([
                'storeDomains'
            ])->andWhere([
                'store_domains.domain' => $domain,
                'stores.status' => [
                    Project::STATUS_ACTIVE,
                    Project::STATUS_FROZEN
                ]
            ])->andWhere(
                'stores.id <> ' . $storeId
            )->exists();

        // Если есть активный магазин, то выдаем ошибку что уже существует панель или магазин
        if ($hasAvailableStores) {
            $model->addError($attribute, Yii::t('app', 'error.store.domain_is_already_exist'));
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
                $hasOrder->cancel();
            }
        }

        if ($hasOrder) {
            $model->addError($attribute, Yii::t('app', 'error.store.domain_is_already_exist'));
            return false;
        }
    }
}