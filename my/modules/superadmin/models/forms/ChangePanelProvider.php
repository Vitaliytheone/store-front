<?php

namespace superadmin\models\forms;


use common\models\panels\Project;
use my\helpers\ChildHelper;
use yii\base\Model;
use yii\db\ActiveRecord;

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
     */
    public function save(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        if (!array_key_exists($this->provider, $this->getProviders())) {
            return false;
        }

        $this->panel->provider_id = $this->provider;

        if (!$this->panel->save()) {
            return false;
        }

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
