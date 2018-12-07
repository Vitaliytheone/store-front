<?php

namespace superadmin\models\forms;


use common\models\panels\AdditionalServices;
use common\models\panels\Project;
use my\helpers\ChildHelper;
use my\helpers\DomainsHelper;
use yii\base\Model;
use yii\helpers\ArrayHelper;

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
     */
    public function save(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $this->panel->provider_id = $this->provider;

        if (!$this->panel->save()) {
            return false;
        }

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

            $this->providers = [];

            if ($this->panel->provider_id && ($provider = AdditionalServices::findOne(['provider_id' => $this->panel->provider_id]))) {
                $this->providers[$this->panel->provider_id] = DomainsHelper::idnToUtf8($provider->name);
            }

            $this->providers = ArrayHelper::merge($this->providers, ChildHelper::getProviders($this->panel->cid, [
                Project::STATUS_ACTIVE
            ]));
        }

        return $this->providers;
    }
}
