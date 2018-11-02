<?php
namespace superadmin\models\forms;

use common\models\panels\Invoices;
use common\models\panels\SuperCreditsLog;
use Yii;
use yii\base\Model;

/**
 * Class EditInvoiceCreditForm
 * @package superadmin\models\forms
 */
class EditInvoiceCreditForm extends Model {

    public $credit;
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

    /**
     * Save invoice credit value
     * @return bool
     * @throws \yii\db\Exception
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $this->_invoice->credit = $this->credit;

        if (!$this->_invoice->save(false)) {
            $this->addError('method', Yii::t('app/superadmin', 'error.invoices.can_not_edit_credit'));
            return false;
        }

        if (0 == $this->_invoice->getPaymentAmount()) {
            $this->_invoice->paid(null);
        }

        $creditLog = new SuperCreditsLog();
        $creditLog->super_admin_id = Yii::$app->superadmin->id;
        $creditLog->invoice_id = $this->_invoice->id;
        $creditLog->memo = $this->memo;
        $creditLog->credit = $this->credit;
        $creditLog->save(false);

        return true;
    }

    /**
     * Validate invoice credit
     * @param $attribute
     * @return bool
     */
    public function validateCredit($attribute) {
        if ($this->hasErrors()) {
            return false;
        }

        if (0 > $this->credit || $this->credit > $this->_invoice->total) {
            $this->addError($attribute, Yii::t('app/superadmin', 'error.invoices.incorrect_invoice_credit'));
        }

        return true;
    }

    /**
     * Validate invoice
     * @param $attribute
     * @return bool
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