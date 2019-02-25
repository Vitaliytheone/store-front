<?php

namespace superadmin\models\forms;


use common\models\panels\Project;
use control_panel\helpers\ChildHelper;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use Yii;
use common\models\panels\UserServices;

/**
 * Class ChangeChildPanelProvider
 * @package superadmin\models\forms
 */
class ChangeChildPanelProvider extends Model
{
    public $provider;

    /** @var Project */
    protected $panel;

    /**
     * @var array
     */
    protected $providers;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['provider'], 'required'],
            [['provider'], 'integer'],
            ['provider', 'in', 'range' => array_keys($this->getProviders())],
        ];
    }

    /**
     * @param Project $panel
     */
    public function setProject(Project $panel)
    {
        $this->panel = $panel;
    }

    /**
     * @return bool
     * @throws \yii\db\Exception
     */
    public function save(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        if (!array_key_exists($this->provider, $this->getProviders())) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();

        $this->panel->provider_id = $this->provider;

        if (!$this->panel->save()) {
            $transaction->rollBack();
            return false;
        }

        if (!UserServices::deleteAll(['panel_id' => $this->panel->id])) {
            $transaction->rollBack();
            return false;
        }

        $newService = new UserServices();
        $newService->panel_id = $this->panel->id;
        $newService->provider_id = $this->provider;

        if (!$newService->save()) {
            $transaction->rollBack();
            return false;
        }

        $transaction->commit();
        return true;
    }

    /**
     * @return array
     */
    public function getProviders()
    {
        if (null === $this->providers) {
            if (empty($this->panel)) {
                return [];
            }

            $this->providers = ChildHelper::getProviders($this->panel->cid, [
                Project::STATUS_ACTIVE
            ]);

            if (array_key_exists($this->panel->provider_id, $this->providers)) {
                unset($this->providers[$this->panel->provider_id]);
            }
        }

        return $this->providers;
    }
}
