<?php

namespace sommerce\modules\admin\models\forms;

use common\models\stores\Integrations;
use common\models\stores\StoreIntegrations;
use yii\base\Model;

/**
 * Class EditIntegrationForm
 * @package sommerce\modules\admin\models\forms
 */
class EditIntegrationForm extends Model
{
    /** @var array */
    public $options;

    /** @var StoreIntegrations */
    private $storeIntegration;

    /**
     * Set store integration
     * @param int $id
     */
    public function setStoreIntegration(int $id)
    {
        $this->storeIntegration = StoreIntegrations::findOne($id);
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            ['options', 'validateOptions'],
        ];
    }

    /**
     * @return bool
     */
    public function save(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        if (!isset($this->storeIntegration)) {
            return false;
        }

        $this->storeIntegration->setOptions($this->options);
        if (!$this->storeIntegration->save(false)) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function validateOptions(): bool
    {
        $integration = Integrations::findOne($this->storeIntegration->integration_id);
        $settingsForm = $integration->getSettingsForm();

        $this->options = array_intersect_key($this->options, $settingsForm);

        return true;
    }

    /**
     * Change PG active status
     * @param $active
     * @return mixed
     * @throws \Throwable
     */
    public function setActive($active)
    {
        $currentIntegration = Integrations::findOne($this->storeIntegration->integration_id);

        $activeIntegrations = StoreIntegrations::find()
            ->leftJoin(Integrations::tableName(), 'integrations.id = store_integrations.integration_id')
            ->where([
                'store_integrations.store_id' => $this->storeIntegration->store_id,
                'store_integrations.visibility' => StoreIntegrations::VISIBILITY_ON,
                'integrations.category' => $currentIntegration->category,
            ])
            ->all();

        if (isset($activeIntegrations)) {
            foreach ($activeIntegrations as $integration) {
                /** @var $integration StoreIntegrations*/
                $integration->visibility = StoreIntegrations::VISIBILITY_OFF;
                if (!$integration->save(false)) {
                    return false;
                }
            }
        }

        $this->storeIntegration->setAttribute('visibility', $active);
        if (!$this->storeIntegration->save(false)) {
            return false;
        }

        return $active;
    }
}
