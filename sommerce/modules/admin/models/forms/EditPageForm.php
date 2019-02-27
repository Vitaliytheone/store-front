<?php

namespace sommerce\modules\admin\models\forms;

use common\models\store\ActivityLog;
use common\models\store\Pages;
use common\models\stores\StoreAdminAuth;


/**
 * Class AddPageForm
 * @package sommerce\modules\admin\models\forms
 */
class AddPageForm extends PageForm
{
    /**
     * @return bool|int
     */
    public function save()
    {
        if (!$this->validate()){
            return false;
        }

        $transaction = Pages::getDb()->beginTransaction();
        try {
            $page = new Pages();

            $page->attributes = [
                'seo_title' => $this->title,
                'name' => $this->name,
                'seo_keywords' => $this->keywords,
                'seo_description' => $this->description,
                'visibility' => intval($this->visibility),
                'is_draft' => 1,
                'url' => $this->url
            ];

            $page->save(false);

            /** @var StoreAdminAuth $identity */
            $identity = $this->getUser()->getIdentity(false);

            ActivityLog::log($identity, ActivityLog::E_SETTINGS_PAGES_PAGE_ADDED, $page->id, $page->id);

            $transaction->commit();

        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->addError('', $e->getMessage());
            return false;
        }

        return true;
    }
}