<?php

namespace admin\models\forms\package;

use admin\models\forms\BaseForm;
use common\models\sommerce\ActivityLog;
use common\models\sommerce\Packages;
use Yii;

/**
 * Class DuplicatePackageForm
 * @package admin\models\forms\package
 */
class DuplicatePackageForm extends BaseForm
{
    /**
     * @var Packages
     */
    protected $_package;

    /**
     * @param Packages $package
     */
    public function setPackage(Packages $package)
    {
        $this->_package = $package;
    }

    /**
     * @return bool
     */
    public function save()
    {
        $duplicate = new Packages();

        $duplicate->attributes = $this->_package->getAttributes([
            'name',
            'price',
            'quantity',
            'mode',
            'best',
            'visibility',
            'provider_service',
            'link_type',
            'provider_id',
        ]);

        if (!$duplicate->save()) {
            return false;
        }

        ActivityLog::log($this->_user, ActivityLog::E_PACKAGES_PACKAGE_DUPLICATED, $duplicate->id, $duplicate->id);

        return true;
    }
}