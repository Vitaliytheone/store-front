<?php

namespace sommerce\components\filters;

use common\models\stores\Integrations;
use common\models\stores\StoreIntegrations;
use common\models\stores\Stores;
use sommerce\components\integrations\IntegrationsFactory;
use sommerce\widgets\AnalyticsWidget;
use sommerce\widgets\ChatsWidget;
use yii\base\ActionFilter;
use Yii;

/**
 * Class IntegrationsFilter
 * @package sommerce\components\filters
 */
class IntegrationsFilter extends ActionFilter
{
    /** @var Stores */
    public $store;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->store = Yii::$app->store->getInstance();

        parent::init();
    }

    /**
     * @param \yii\base\Action $action
     * @return bool
     */
    public function beforeAction($action)
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
                $snippet = $snippet['snippet'] ?? null;

                if (!isset($snippet) || empty($snippet)) {
                    continue;
                }

                if (!isset($integration['widget_class']) || $integration['widget_class'] === '') {
                    continue;
                }

                $factory = new IntegrationsFactory();
                /** @var ChatsWidget|AnalyticsWidget $widgetObject */
                $widgetObject = $factory->getWidget($integration['widget_class']);
                if (!isset($widgetObject)) {
                    continue;
                }

                $widgetObject->content = $snippet;
                $integrationsContent .= $widgetObject->run();
            }
        }

        $this->owner->startHeadContent[] = $integrationsContent;

        return parent::beforeAction($action);
    }
}
