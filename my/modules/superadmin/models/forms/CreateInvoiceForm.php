<?php
namespace superadmin\models\forms;

use common\models\panels\Customers;
use common\models\panels\InvoiceDetails;
use common\models\panels\Invoices;
use common\models\panels\Project;
use my\helpers\SpecialCharsHelper;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class CreateInvoiceForm
 * @package superadmin\models\forms
 */
class CreateInvoiceForm extends Model {

    /**
     * @var integer
     */
    public $customer_id;

    /**
     * @var float|integer
     */
    public $total;

    /**
     * @var string
     */
    public $description;

    /**
     * @var Customers
     */
    private $_customer;

    /**
     * @var Customers[]
     */
    private $_customers;

    /**
     * @var Project
     */
    private $_panel;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['total', 'customer_id'], 'required'],
            [['total'], 'number'],
            [['customer_id'], 'integer'],
            [['description'], 'string'],
            [['customer_id'], 'validateCustomer'],
        ];
    }

    /**
     * @param Project $panel
     */
    public function setPanel(Project $panel)
    {
        $this->_panel = $panel;
    }

    /**
     * Save invoice changes
     * @return bool
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();

        $invoiceModel = new Invoices();
        $invoiceModel->cid = $this->_customer->id;
        $invoiceModel->total = $this->total;
        $invoiceModel->generateCode();
        $invoiceModel->daysExpired(Yii::$app->params['invoice.customDuration']);

        if (!$invoiceModel->save()) {
            $this->addError('customer_id', Yii::t('app/superadmin', 'error.invoices.can_not_create_invoice'));
            return false;
        }

        $invoiceDetailsModel = new InvoiceDetails();
        $invoiceDetailsModel->invoice_id = $invoiceModel->id;
        $invoiceDetailsModel->amount = $this->total;
        $invoiceDetailsModel->item = InvoiceDetails::ITEM_CUSTOM_CUSTOMER;
        $invoiceDetailsModel->description = $this->description;
        $invoiceDetailsModel->item_id = $this->_customer->id;

        if ($this->_panel) {
            $invoiceDetailsModel->item_id = $this->_panel->id;
            $invoiceDetailsModel->item = InvoiceDetails::ITEM_CUSTOM_PANEL;
        }

        if (!$invoiceDetailsModel->save()) {
            $transaction->rollBack();
            $this->addError('customer_id', Yii::t('app/superadmin', 'error.invoices.can_not_create_invoice'));
            return false;
        }

        $transaction->commit();

        return true;
    }

    /**
     * Validate customer
     * @param string|mixed $attribute
     * @return bool
     */
    public function validateCustomer($attribute)
    {
        if ($this->hasErrors()) {
            return false;
        }

        if (!($this->_customer = Customers::findOne($this->customer_id))) {
            $this->addError($attribute, Yii::t('app/superadmin', 'error.invoices.incorrect_invoice_customer'));
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'total' => Yii::t('app/superadmin', 'invoices.create.total'),
            'customer_id' => Yii::t('app/superadmin', 'invoices.create.customer_id'),
            'description' => Yii::t('app/superadmin', 'invoices.create.description'),
        ];
    }

    /**
     * Get customers
     * @return Customers[]
     */
    public function getCustomers()
    {
        if (null !== $this->_customers) {
            return $this->_customers;
        }

        $this->_customers = ArrayHelper::index(SpecialCharsHelper::multiPurifier(Customers::find()->all()), 'id');

        return $this->_customers;
    }
}