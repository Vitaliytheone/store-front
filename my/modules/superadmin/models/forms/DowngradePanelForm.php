<?php
namespace superadmin\models\forms;

use common\models\panels\InvoiceDetails;
use common\models\panels\Invoices;
use my\helpers\ChildHelper;
use common\models\panels\AdditionalServices;
use common\models\panels\UserServices;
use Yii;
use common\models\panels\Project;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\ArrayHelper;

/**
 * Class DowngradePanelForm
 * @package superadmin\models\forms
 */
class DowngradePanelForm extends Model {

    public $provider;

    /**
     * @var Project
     */
    private $_project;

    /**
     * @var AdditionalServices
     */
    private $_providers;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['provider'], 'required'],
            [['provider'], 'integer'],
            [['provider'], 'in', 'range' => array_keys($this->getProviders())],
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
     * Save expied
     * @return bool
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        /**
         * @var UserServices $currentProviders
         */
        $currentProviders = ArrayHelper::index($this->_project->userServices, 'provider_id');

        $transaction = Yii::$app->db->beginTransaction();

        try {
            if (!$this->_project->downgrade()) {
                $this->addError('mode', Yii::t('app/superadmin', 'panels.downgrade.error'));
                return false;
            }

            if (!empty($currentProviders[$this->provider])) {
                unset($currentProviders[$this->provider]);
            } else {
                $userService = new UserServices();
                $userService->attributes = [
                    'panel_id' => $this->_project->id,
                    'provider_id' => $this->provider,
                ];

                $userService->save(false);
            }

            $projectDbConnection = $this->_project->getDbConnection();
            $resIds = [];

            foreach ($currentProviders as $res => $provider) {
                $resIds[] = $res;
                $provider->delete();
            }

            if ($projectDbConnection && !empty($resIds)) {
                $projectDbConnection->createCommand()->update('services', [
                    'res' => Yii::$app->params['manualProviderId']
                ], 'res IN (' . implode(",", $resIds) . ')')->execute();
            }

            // При downgrade - надо поменять сумму инфойса и айтем инвойса на продление child
            foreach (InvoiceDetails::find()->andWhere([
                'item' => InvoiceDetails::ITEM_PROLONGATION_PANEL,
                'item_id' => $this->_project->id,
                'invoices.status' => Invoices::STATUS_UNPAID
            ])->joinWith(['invoice'])->all() as $invoiceDetails) {
                $invoice = $invoiceDetails->invoice;
                $amount = Yii::$app->params['childPanelDeployPrice'];

                $invoice->total -= $amount;
                $invoiceDetails->amount = $amount;
                $invoiceDetails->item = InvoiceDetails::ITEM_PROLONGATION_CHILD_PANEL;

                if (!$invoice->save(false) || !$invoiceDetails->save(false)) {
                    $this->addError('mode', Yii::t('app/superadmin', 'panels.downgrade.error'));
                    return false;
                }
            }

        } catch (Exception $exception) {
            $transaction->rollBack();

            Yii::error($exception->getMessage() . $exception->getTraceAsString());

            $this->addError('mode', Yii::t('app/superadmin', 'panels.downgrade.error'));
            return false;
        }

        $transaction->commit();

        return true;
    }

    /**
     * Get providers
     * @return mixed
     */
    public function getProviders()
    {
        if (null !== $this->_providers) {
            return $this->_providers;
        }

        if (empty($this->_project)) {
            return [];
        }

        $this->_providers = ChildHelper::getProviders($this->_project->cid, [
            Project::STATUS_ACTIVE,
            Project::STATUS_FROZEN
        ]);

        if (($key = array_search($this->_project->site, $this->_providers))) {
            unset($this->_providers[$key]);
        }

        return $this->_providers;
    }
}