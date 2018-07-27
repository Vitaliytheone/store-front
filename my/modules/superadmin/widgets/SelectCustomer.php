<?php

namespace my\modules\superadmin\widgets;

use common\models\panels\Customers;
use yii\base\Widget;

class SelectCustomer extends Widget
{
    public $context;
    public $name;
    public $selectedCustomerId = null;
    public $status = null;


    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $query  = Customers::find();

        if ($this->status == null) {
            $query->where([
                'status' => Customers::STATUS_ACTIVE
            ]);
        } else if ($this->status != 'all') {
            $query->where([
                'status' => $this->status
            ]);
        }

        $models = $query->limit(10)->all();

        return $this->render('_select_customer', [
            'models' => $models,
            'context' => $this->context,
            'status' => $this->status,
            'name' => $this->name,
            'selectedCustomerId' => $this->selectedCustomerId
        ]);
    }
}