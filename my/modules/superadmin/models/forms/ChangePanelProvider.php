<?php

namespace superadmin\models\forms;


use common\models\panels\Project;
use common\models\panels\UserServices;
use my\helpers\ChildHelper;
use yii\base\Model;
use yii\db\ActiveRecord;
use Yii;

/**
 * Class ChangePanelProvider
 * @package superadmin\models\forms
 */
class ChangePanelProvider extends Model
{
    public $provider;

    /** @var Project */
    protected $panel;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['provider'], 'safe'],
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
     * @return array|ActiveRecord[]
     */
    public function getProviders()
    {
        return ChildHelper::getProviders($this->panel->cid, [
            Project::STATUS_ACTIVE
        ]);
    }
}
