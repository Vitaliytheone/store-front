<?php

namespace my\components;

use yii\filters\AccessControl;

class SuperAccessControl extends AccessControl
{
    public $user = 'superadmin';
}