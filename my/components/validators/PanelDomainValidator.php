<?php
namespace my\components\validators;

use my\helpers\CurlHelper;
use my\helpers\DomainsHelper;
use common\models\panels\OrderLogs;
use Yii;
use common\models\panels\Orders;
use common\models\panels\Project;
use yii\base\Model;
use yii\validators\Validator;

/**
 * Class PanelDomainValidator
 * @package my\components\validators
 */
class PanelDomainValidator extends Validator
{
    protected $domain;
    protected $user_id;

    public $child_panel = false;

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
        $this->user_id = $model->getUser()->id;

        $domain = $this->prepareDomain();

        if (!filter_var('info@' . $domain, FILTER_VALIDATE_EMAIL)) {
            $model->addError($attribute, Yii::t('app', 'error.panel.invalid_domain'));
            return false;
        }

        if (Yii::$app->params['whoisxml']) {
            $result = $this->isValidDomainName($domain);

            if (!$result['result']) {
                $model->addError($attribute, Yii::t('app', 'error.panel.domain_is_not_registered'));
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
    }

    /**
     * Check is valid domain
     * @param $domainName
     * @return array
     */
    private function isValidDomainName($domainName)
    {
        $result = array('result' => false);

        $list = CurlHelper::request('https://www.whoisxmlapi.com/whoisserver/WhoisService?cmd=GET_DN_AVAILABILITY&domainName=' . $domainName . '&username=' . Yii::$app->params['dnsLogin'] . '&password=' . Yii::$app->params['dnsPasswd'] . '&getMode=DNS_AND_WHOIS&outputFormat=JSON');
        $listEncode = json_decode($list);
        if ($listEncode !== false) {
            if (!empty($listEncode->DomainInfo) && $listEncode->DomainInfo->domainAvailability == 'UNAVAILABLE') {
                $result = array('result' => true, 'domain' => $listEncode->DomainInfo->domainName);
            }
        } else {
            $result = array('result' => true, 'domain' => $domainName);
        }

        $OrderLogsModel = new OrderLogs();

        $OrderLogsModel->domain = $domainName;
        $OrderLogsModel->cid = $this->user_id;
        $OrderLogsModel->date = time();
        $OrderLogsModel->log = json_encode(array('result' => $result, 'html' => $list));
        $OrderLogsModel->save();

        return $result;
    }

    /**
     * Prepare domain
     * @return string
     */
    public function prepareDomain()
    {
        $domain = trim(mb_strtolower($this->domain));
        $domain = DomainsHelper::idnToAscii($domain);

        $exp = explode("://", $domain);

        if (count($exp) > 1) {
            $domain = $exp['1'];
        }

        $exp = explode("/", $domain);

        $domain = $exp['0'];

        if (substr($domain, 0, 4) == 'www.') {
            $domain = substr($domain, 4);
        }

        return $domain;
    }
}