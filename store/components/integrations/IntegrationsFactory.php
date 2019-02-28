<?php

namespace store\components\integrations;

use yii\base\Widget;

/**
 * Class IntegrationsFactory
 */
class IntegrationsFactory
{
    /** @var string */
    private $namespace = 'store\widgets';

    /**
     * Get widget object
     * @param string $className
     * @return Widget|null
     */
    public function getWidget(string $className): ?Widget
    {
        $widgetClass = $this->namespace . '\\' . $className;

        if (!class_exists($widgetClass)) {
            return null;
        }

        return new $widgetClass();
    }
}
