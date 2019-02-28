<?php
namespace superadmin\models\forms;

use common\models\panels\Customers;
use common\models\panels\Invoices;
use common\models\panels\ReferralEarnings;
use Yii;
use yii\base\Model;
use common\models\panels\SuperCreditsLog;

/**
 * Class EditInvoiceCreditForm
 * @package superadmin\models\forms
 */
class AddInvoiceEarningsForm extends Model {

    public $credit;
    public $memo;

    /**
     * @var Invoices
     */
    private $_invoice;

    /**
     * @var Customers
     */
    private $_customer;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['credit'], 'required'],
            [['credit'], 'validateInvoice'],
            [['credit'], 'validateCredit'],
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

    public function setCustomer(Customers $customer)
    {
        $this->_customer = $customer;
    }

    /**
     * Save invoice credit value
     * @return bool
     * @throws \yii\db\Exception
     */
    public function save()
    {
        $transaction = Yii::$app->db->beginTransaction();
        if (!$this->validate()) {
            $transaction->rollBack();
            return false;
        }

        $this->_invoice->credit = $this->credit;

        if (!$this->_invoice->save(false)) {
            $transaction->rollBack();
            $this->addError('method', Yii::t('app/superadmin', 'error.invoices.can_not_edit_credit'));
            return false;
        }

        if (0 == $this->_invoice->getPaymentAmount()) {
            $this->_invoice->paid(null);
        }

        $referralEarnings = new ReferralEarnings();
        $referralEarnings->customer_id = $this->_customer->id;
        $referralEarnings->earnings = $this->credit;
        $referralEarnings->invoice_id = $this->_invoice->id;
        $referralEarnings->status = ReferralEarnings::STATUS_DEBIT;

        $creditLog = new SuperCreditsLog();
        $creditLog->super_admin_id = Yii::$app->superadmin->id;
        $creditLog->invoice_id = $this->_invoice->id;
        $creditLog->memo = $this->memo;
        $creditLog->credit = $this->credit;
        $creditLog->save(false);

        if ($referralEarnings->save(false)) {
            $transaction->commit();
        } else {
            $transaction->rollBack();
        }

        return true;
    }

    /**
     * @param $attribute
     * @return bool
     */
    public function validateCredit($attribute)
    {
        if ($this->hasErrors()) {
            return false;
        }

        if (0 > $this->credit || $this->credit > $this->_invoice->total) {
            $this->addError($attribute, Yii::t('app/superadmin', 'error.invoices.incorrect_invoice_credit'));
        }

        if ($this->_customer->getUnpaidEarnings() < $this->credit) {
            $this->addError('method', Yii::t('app/superadmin', 'error.invoices.not_enough_funds'));
        }

        return true;
    }

    /**
     * Validate invoice
     * @param $attribute
     * @return bool
     */
    public function validateInvoice($attribute)
    {
        if ($this->hasErrors()) {
            return false;
        }

        if (Invoices::STATUS_UNPAID != $this->_invoice->status) {
            $this->addError($attribute, Yii::t('app/superadmin', 'error.invoices.incorrect_invoice_status'));
        }

        return true;
    }
}