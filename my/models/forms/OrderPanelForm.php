<?php
namespace my\models\forms;

use my\components\validators\OrderDomainValidator;
use my\helpers\UserHelper;
use common\models\panels\InvoiceDetails;
use common\models\panels\Invoices;
use common\models\panels\MyActivityLog;
use common\models\panels\Orders;
use common\models\panels\ProjectAdmin;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class OrderPanelForm
 * @package my\models\forms
 */
class OrderPanelForm extends DomainForm
{
    public $currency;
    public $username;
    public $password;
    public $password_confirm;

    public $code;

    /**
     * @var string - user IP address
     */
    protected $_ip;

    const SCENARIO_CREATE_PROJECT = 'project';

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
            ['username', 'match', 'pattern' => '/^[a-z0-9-_@.]*$/i'],
            ['username', 'string', 'min' => 3, 'max' => 32],
            [['domain', 'currency', 'username', 'password', 'password_confirm'], 'required', 'except' => static::SCENARIO_CREATE_DOMAIN],
            [['currency'], 'in', 'range' => array_keys($this->getCurrencies()), 'message' => Yii::t('app', 'error.panel.bad_currency')],
            [['domain'], OrderDomainValidator::class, 'panel' => true, 'child_panel' => false],
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
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();

        $invoiceModel = new Invoices();
        $invoiceModel->total = 0;
        $invoiceModel->cid = $this->_user->id;
        $invoiceModel->generateCode();
        $invoiceModel->daysExpired(Yii::$app->params['invoice.domainDuration']);

        if (!$invoiceModel->save()) {
            return false;
        }

        if (static::HAS_NOT_DOMAIN == $this->has_domain) {

            if (!$this->orderDomain($invoiceModel)) {
                $this->addError('domain', Yii::t('app', 'error.panel.can_not_order_domain'));
                return false;
            }
        } else {
            $this->domain = $this->preparedDomain;
        }

        $result = $this->orderPanel($invoiceModel);

        if (!$result) {
            return false;
        }

        $invoiceModel->save(false);

        $transaction->commit();

        $this->code = $invoiceModel->code;

        return true;
    }

    /**
     * Order panel
     * @param Invoices $invoiceModel
     * @return bool
     */
    private function orderPanel(&$invoiceModel)
    {
        $this->scenario = static::SCENARIO_CREATE_PROJECT;

        $model = new Orders();
        $model->cid = $this->_user->id;
        $model->item = Orders::ITEM_BUY_PANEL;
        $model->domain = $this->preparedDomain;
        $model->ip = $this->_ip;
        $model->setDetails([
            'username' => $this->username,
            'password' => ProjectAdmin::hashPassword($this->password),
            'domain' => $this->domain,
            'clean_domain' => $this->preparedDomain,
            'currency' => $this->currency,
            'subdomain' => static::HAS_SUBDOMAIN == $this->has_domain ? 1 : 0,
        ]);

        if ($model->save()) {
            $invoiceDetailsModel = new InvoiceDetails();
            $invoiceDetailsModel->invoice_id = $invoiceModel->id;
            $invoiceDetailsModel->item_id = $model->id;
            $invoiceDetailsModel->amount = Yii::$app->params['panelDeployPrice'];
            $invoiceDetailsModel->item = InvoiceDetails::ITEM_BUY_PANEL;

            if (!$invoiceDetailsModel->save()) {
                $this->addError('domain', Yii::t('app', 'error.panel.can_not_order_panel'));
                return false;
            }
        } else {
            $this->addErrors($model->getErrors());
            return false;
        }

        $invoiceModel->total += $invoiceDetailsModel->amount;

        MyActivityLog::log(MyActivityLog::E_ORDERS_CREATE_PANEL_ORDER, $model->id, $model->id, UserHelper::getHash());

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
                'domain' => Yii::t('app', 'form.order_panel.domain'),
                'currency' => Yii::t('app', 'form.order_panel.currency'),
                'username' => Yii::t('app', 'form.order_panel.username'),
                'password' => Yii::t('app', 'form.order_panel.password'),
                'password_confirm' => Yii::t('app', 'form.order_panel.password_confirm'),
            ]
        );
    }

    /**
     * Get currencies
     * @return mixed
     */
    public function getCurrencies()
    {
        $currencies = [];

        foreach (Yii::$app->params['currencies'] as $code => $currency) {
            $currencies[$code] = Yii::t('app', $currency['name']) . ' (' . $code . ')';
        }

        return $currencies;
    }
}
