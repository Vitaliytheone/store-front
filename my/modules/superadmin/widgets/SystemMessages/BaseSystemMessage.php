<?php

namespace superadmin\widgets\SystemMessages;

use yii\base\Widget;
/**
 * Abstract widget class for system messages
 * Class BaseSystemMessage
 * @package superadmin\widgets\SystemMessages
 */
abstract class BaseSystemMessage extends Widget
{
    public $data;
    public $date;
    public $admin;
    public $admins;
}