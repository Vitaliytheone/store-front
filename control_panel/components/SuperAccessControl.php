<?php

namespace control_panel\components;

use yii\filters\AccessControl;

class SuperAccessControl extends AccessControl
{
    public $user = 'superadmin';
}