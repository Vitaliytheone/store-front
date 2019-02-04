<?php
namespace common\components\behaviors;

use yii\helpers\ArrayHelper;

/**
 * Class SluggableBehavior
 * @package common\components\behaviors
 */
class SluggableBehavior extends \yii\behaviors\SluggableBehavior {

    /**
     * {@inheritdoc}
     */
    protected function getValue($event)
    {
        if (!$this->isNewSlugNeeded()) {
            return $this->owner->{$this->slugAttribute};
        }

        if ($this->attribute !== null) {
            $slugParts = [];
            foreach ((array) $this->attribute as $attribute) {
                if (is_callable($attribute)) {
                    $part = $attribute();
                } else {
                    $part = ArrayHelper::getValue($this->owner, $attribute);
                }
                if ($this->skipOnEmpty && $this->isEmpty($part)) {
                    return $this->owner->{$this->slugAttribute};
                }
                $slugParts[] = $part;
            }
            $slug = $this->generateSlug($slugParts);
        } else {
            $slug = parent::getValue($event);
        }

        return $this->ensureUnique ? $this->makeUnique($slug) : $slug;
    }

    /**
     * Checks whether the new slug generation is needed
     * This method is called by [[getValue]] to check whether the new slug generation is needed.
     * You may override it to customize checking.
     * @return bool
     * @since 2.0.7
     */
    protected function isNewSlugNeeded()
    {
        if (empty($this->owner->{$this->slugAttribute})) {
            return true;
        }

        if ($this->immutable) {
            return false;
        }

        if ($this->attribute === null) {
            return true;
        }

        foreach ((array) $this->attribute as $attribute) {
            if (is_callable($attribute)) {
                return true;
            }
            if ($this->owner->isAttributeChanged($attribute)) {
                return true;
            }
        }

        return false;
    }
}