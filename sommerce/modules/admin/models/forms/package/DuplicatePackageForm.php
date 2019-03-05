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

        $transaction = Yii::$app->storeDb->beginTransaction();
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
            'product_id',
        ]);
        if (!$duplicate->save()) {
            $transaction->rollBack();
            return false;
        }

        $moveModel = new MovePackageForm();
        $moveModel->setPackage($duplicate);

        if (!$moveModel->changePosition(($this->_package->position + 1))) {
            $transaction->rollBack();
            return false;
        }

        ActivityLog::log($this->_user, ActivityLog::E_PACKAGES_PACKAGE_DUPLICATED, $duplicate->id, $duplicate->id);

        $transaction->commit();
        return true;
    }
}