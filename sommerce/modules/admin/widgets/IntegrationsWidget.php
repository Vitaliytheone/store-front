<?php

namespace sommerce\modules\admin\widgets;

use common\models\stores\Integrations;
use common\models\stores\StoreIntegrations;
use common\models\stores\Stores;
use yii\base\Widget;
use Yii;

class IntegrationsWidget extends Widget
{
    /** @var Stores */
    private $store;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->store = Yii::$app->store->getInstance();
        parent::init();
    }

    /**
     * @return string
     */
    public function run()
    {
        $storeIntegrations = StoreIntegrations::find()
            ->where([
                'store_id' => $this->store->id,
                'visibility' => StoreIntegrations::VISIBILITY_ON,
            ])
            ->all();
        $allIntegrations = Integrations::find()->asArray()->indexBy('id')->all();
        $integrationsContent = '';

        if (isset($storeIntegrations)) {
            foreach ($storeIntegrations as $storeIntegration) {
                /** @var StoreIntegrations $storeIntegration */
                $integration = $allIntegrations[$storeIntegration->integration_id];
                $snippet = $storeIntegration->getOptions();
                $snippet = $snippet['snippet'];

                if (!isset($integration['widget_class']) || $integration['widget_class'] === '') {
                    continue;
                }
                $widgetClass = 'sommerce\modules\admin\widgets\\' . $integration['widget_class'];
                $integrationsContent .= $widgetClass::widget([
                    'content' => $snippet,
                ]);
            }
        }

        return $integrationsContent;
    }
}
