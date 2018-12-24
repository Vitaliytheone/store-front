<?php

namespace my\models\forms;


use common\models\gateways\Admins;
use common\models\panels\InvoiceDetails;
use common\models\panels\Invoices;
use common\models\panels\MyActivityLog;
use common\models\panels\Orders;
use my\components\validators\OrderDomainValidator;
use my\helpers\UserHelper;
use Yii;

/**
 * Class OrderGatewayForm
 * @package my\models\forms
 */
class OrderGatewayForm extends DomainForm
{
    public $username;
    public $password;
    public $password_confirm;

    public $code;

    /**
     * @var string - user IP address
     */
    protected $_ip;

    const SCENARIO_CREATE_PROJECT = 'gateway';

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return array_merge(
            parent::rules(), [
            [['username', 'password'], 'filter', 'filter' => function($value) { // Trim input values
                return is_string($value) || is_numeric($value) ? trim((string)$value) : null;
            }],
            [['domain', 'username', 'password', 'password_confirm'], 'required', 'except' => static::SCENARIO_CREATE_DOMAIN],
            [['domain'], OrderDomainValidator::class, 'gateway' => true],
            ['password', 'compare', 'compareAttribute' => 'password_confirm'],
            [['username'], 'safe'],
        ]);
    }

    /**
     * Set user IP
     * @param $ip
     */
    public function setIP($ip)
    {
        $this->_ip = $ip;
    }

    /**
     * Sign up method
     * @return bool
     * @throws \Exception
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();

        $invoiceModel = new Invoices();
        $invoiceModel->total = 0;
        $invoiceModel->cid = $this->getUser()->id;
        $invoiceModel->generateCode();
        $invoiceModel->daysExpired(Yii::$app->params['invoice.domainDuration']);

        if (!$invoiceModel->save()) {
            return false;
        }

        $result = $this->orderGateway($invoiceModel);

        if (!$result) {
            return false;
        }

        $invoiceModel->save(false);

        $transaction->commit();

        $this->code = $invoiceModel->code;

        return true;
    }

    /**
     * Order gateway
     * @param Invoices $invoiceModel
     * @return bool
     * @throws \Exception
     */
    private function orderGateway(Invoices &$invoiceModel)
    {
        $this->scenario = static::SCENARIO_CREATE_PROJECT;

        $model = new Orders();
        $model->cid = $this->getUser()->id;
        $model->item = Orders::ITEM_BUY_GATEWAY;
        $model->domain = $this->domain;
        $model->ip = $this->_ip;
        $model->setDetails([
            'username' => $this->username,
            'password' => Admins::hashPassword($this->password),
            'domain' => $this->domain,
        ]);

        if ($model->save()) {
            $invoiceDetailsModel = new InvoiceDetails();
            $invoiceDetailsModel->invoice_id = $invoiceModel->id;
            $invoiceDetailsModel->item_id = $model->id;
            $invoiceDetailsModel->amount = Yii::$app->params['gatewayDeployPrice'];
            $invoiceDetailsModel->item = InvoiceDetails::ITEM_BUY_GATEWAY;

            if (!$invoiceDetailsModel->save()) {
                $this->addError('domain', Yii::t('app', 'error.gateway.can_not_order_gateway'));
                return false;
            }
        } else {
            $this->addErrors($model->getErrors());
            return false;
        }

        $invoiceModel->total += $invoiceDetailsModel->amount;

        MyActivityLog::log(MyActivityLog::E_ORDERS_CREATE_GATEWAY_ORDER, $model->id, $model->id, UserHelper::getHash());

        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(
            parent::attributeLabels(),
            [
                'domain' => Yii::t('app', 'form.order_gateway.domain'),
                'username' => Yii::t('app', 'form.order_gateway.username'),
                'password' => Yii::t('app', 'form.order_gateway.password'),
                'password_confirm' => Yii::t('app', 'form.order_gateway.password_confirm'),
            ]
        );
    }
}
