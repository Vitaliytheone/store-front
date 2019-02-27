<?php

namespace store\components\filters;

use common\models\stores\Integrations;
use common\models\stores\StoreIntegrations;
use common\models\stores\Stores;
use store\components\integrations\IntegrationsFactory;
use store\widgets\AnalyticsWidget;
use store\widgets\ChatsWidget;
use yii\base\ActionFilter;
use Yii;

/**
 * Class IntegrationsFilter
 * @package store\components\filters
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
        $firstContentElement = null;

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

                if ($integration['code'] === Integrations::CODE_ANALYTICS_GOOGLE) {
                    $firstContentElement.= $widgetObject->run();
                    continue;
                }
                $integrationsContent .= $widgetObject->run();
            }
        }

        if (isset($firstContentElement)) {
            $this->owner->startHeadContent[0] = $firstContentElement;
        }
        $this->owner->startHeadContent[] = $integrationsContent;

        return parent::beforeAction($action);
    }
}
