<?php

namespace sommerce\modules\admin\models\forms;

use common\models\store\Packages;
use Yii;
use common\models\stores\Stores;
use yii\base\Model;
use yii\db\Transaction;

/**
 * Class SavePackageForm
 * @package sommerce\modules\admin\models\forms
 */
class SavePackageForm extends Model
{
    /**
     * @var string
     */
    public $icon;

    /**
     * @var string
     */
    public $properties;

    /**
     * @var Stores
     */
    protected $_store;

    /**
     * @var Packages
     */
    protected $_package;

    /**
     * Set store
     * @param Stores $store
     */
    public function setStore(Stores $store)
    {
        $this->_store = $store;
    }

    /**
     * Return store
     * @return Stores
     */
    public function getStore()
    {
        return $this->_store;
    }

    /**
     * Set page
     * @param Packages $package
     */
    public function setPackage(Packages $package) {
        $this->_package = $package;

        // Init default values;
        $this->icon = $package->icon;
        $this->properties = $package->properties;
    }

    /**
     * Get package
     * @return Packages
     */
    public function getPackage()
    {
        return $this->_package;
    }

    /**
     * @return string
     */
    public function formName()
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['icon', 'properties'], 'string'],
            [['icon', 'properties'], 'trim'],
            [['icon'], 'string', 'max' => 180],
        ];
    }

    /**
     * @return bool
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->storeDb->beginTransaction();

        $package = $this->getPackage();

        $package->icon = $this->icon;
        $package->properties = $this->properties;

        if (!$package->save(false)) {
            $this->addError('page_file', 'Cannot save package!');
            $transaction->rollBack();

            return false;
        }

        $transaction->commit();

        return true;
    }
}