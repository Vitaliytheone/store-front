<?php

namespace superadmin\models\forms;

use common\models\sommerces\Invoices;
use Yii;
use yii\base\Model;

/**
 * Class EditInvoiceForm
 * @package superadmin\models\forms
 */
class EditInvoiceForm extends Model {

    public $total;

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
            [['total'], 'required'],
            [['total'], 'number'],
        ];
    }

    /**
     * Set invoice
     * @param Invoices $invoice
     */
    public function setInvoice(Invoices $invoice)
    {
        $this->_invoice = $invoice;
        $this->total = $invoice->total;
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

        $invoiceDetails = $this->_invoice->invoiceDetails;

        $this->_invoice->total = $this->total;
        $this->_invoice->save(false);

        foreach ($invoiceDetails as $details) {
            $details->amount = $this->total;
            $details->save(false);
            break;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'site' => Yii::t('app/superadmin', 'invoices.edit.total'),
        ];
    }
}