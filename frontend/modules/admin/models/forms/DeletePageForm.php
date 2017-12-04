<?php

namespace frontend\modules\admin\models\forms;

use common\models\store\Pages;

/**
 * Class DeletePageForm
 * @package frontend\modules\admin\models\forms
 */
class DeletePageForm extends Pages
{
    /**
     * Virtual deleting page
     * @return bool
     */
    public function deleteVirtual()
    {
        if ($this->deleted == self::DELETED_YES) {
            return false;
        }

        $this->setAttribute('deleted', self::DELETED_YES);

        return $this->save(false);
    }
}