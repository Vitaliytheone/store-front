<?php
namespace my\modules\superadmin\models\forms;

use common\helpers\PaymentHelper;
use common\models\panels\Invoices;
use common\models\panels\Params;
use common\models\panels\Payments;
use common\models\panels\services\GetPaymentMethodsService;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class AddInvoicePaymentForm
 * @package my\modules\superadmin\models\forms
 */
class AddInvoicePaymentForm extends Model {

    public $method;
    public $memo;

    /**
     * @var Invoices
     */
    private $_invoice;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['method'], 'validateInvoice'],
            [['method'], 'required'],
            [['memo'], 'string'],
        ];
    }

    /**
     * Set invoice
     * @param Invoices $invoice
     */
    public function setInvoice(Invoices $invoice)
    {
        $this->_invoice = $invoice;
    }

    /**
     * Save domain
     * @return bool
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $invoiceDetails = $this->_invoice->invoiceDetails;
        $invoiceDetails = array_shift($invoiceDetails);
        $panel = $invoiceDetails->panel;

        $payment = new Payments();
        $payment->mode = Payments::MODE_MANUAL;
        $payment->type = $this->method;
        $payment->comment = $this->memo;
        $payment->status = Payments::STATUS_COMPLETED;
        $payment->amount = $this->_invoice->total;
        $payment->iid = $this->_invoice->id;
        $payment->pid = $panel->id;

        if (!$payment->save(false)) {
            $this->addError('method', Yii::t('app/superadmin', 'error.invoices.can_not_create_payment'));
            return false;
        }

        // Mark invoice paid
        $this->_invoice->paid($this->method);

        return true;
    }

    /**
     * Get payment methods
     * @return array
     */
    public function getMethods()
    {
        $methods = ArrayHelper::map(Yii::$container->get(GetPaymentMethodsService::class)->get(), function($value) {
            return PaymentHelper::getTypeByCode($value['code']);
        }, 'name');
        $methods[0] = Yii::t('app', 'payment_gateway.method.other');
        return $methods;
    }

    /**
     * Validate invoice
     * @param $attribute
     */
    public function validateInvoice($attribute) {
        if ($this->hasErrors()) {
            return false;
        }

        if (Invoices::STATUS_UNPAID != $this->_invoice->status) {
            $this->addError($attribute, Yii::t('app/superadmin', 'error.invoices.incorrect_invoice_status'));
        }

        return true;
    }
}