<?php

namespace frontend\modules\admin\models\forms;

use common\models\store\Navigations;

/**
 * Class DeleteNavigation form
 * @package frontend\modules\admin\models\forms
 */
class DeleteNavigationForm extends Navigations
{
    /**
     * Virtual navigation deleting
     * @return bool
     */
    public function deleteVirtual()
    {
        if ($this->deleted == self::DELETED_YES) {
            return false;
        }

        $this->setAttributes([
            'deleted' => self::DELETED_YES,
            'position' => NULL
        ]);

        if (!$this->save(false)) {
            return false;
        }

        return true;
    }

    public function updatePositionsAfterDelete($oldPosition)
    {

    }
}