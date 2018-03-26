<?php
namespace my\modules\superadmin\models\forms;

use Yii;
use common\models\panels\Project;
use yii\base\Model;
use yii\db\Exception;

/**
 * Class UpgradePanelForm
 * @package my\modules\superadmin\models\forms
 */
class UpgradePanelForm extends Model {

    const MODE_INVOICE_DISABLED = 0;
    const MODE_INVOICE_ENABLED = 1;

    public $mode;

    /**
     * @var Project
     */
    private $_project;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['mode'], 'number'],
        ];
    }

    /**
     * Set project
     * @param Project $project
     */
    public function setProject(Project $project)
    {
        $this->_project = $project;
    }

    /**
     * Save upgrade
     * @return bool
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            if (!$this->_project->upgrade()) {
                return false;
            }

            if (static::MODE_INVOICE_ENABLED == $this->mode) {
                $customInvoice = new CreateInvoiceForm();
                $customInvoice->total = $this->getTotal();
                $customInvoice->customer_id = $this->_project->cid;
                $customInvoice->description = Yii::t('app/superadmin', 'child_panels.upgrade.invoice_description', [
                    'panel' => $this->_project->getSite()
                ]);
                $customInvoice->setPanel($this->_project);

                if (!$customInvoice->save()) {
                    $transaction->rollBack();
                    return false;
                }
            }

        } catch (Exception $exception) {
            $transaction->rollBack();

            Yii::error($exception->getMessage() . $exception->getTraceAsString());
            return false;
        }

        $transaction->commit();

        return true;
    }

    /**
     * Get total price
     * @return mixed
     */
    public function getTotal()
    {
        return Yii::$app->params['panelDeployPrice'] - Yii::$app->params['childPanelDeployPrice'];
    }
}