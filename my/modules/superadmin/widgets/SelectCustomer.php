<?php

namespace my\modules\superadmin\widgets;

use yii\base\Widget;

class SelectCustomer extends Widget
{
    public $models;
    public $context;
    public $name;
    public $selectedCustomerId = null;
    public $status = null;


    /**
     * {@inheritdoc}
     */
    public function run()
    {
        return $this->render('_select_customer', [
            'models' => $this->models,
            'context' => $this->context,
            'status' => $this->status,
            'name' => $this->name,
            'selectedCustomerId' => $this->selectedCustomerId
        ]);
    }
}