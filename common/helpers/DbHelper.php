<?php

namespace common\helpers;

use Yii;

class DbHelper
{
    /**
     * Return DB dsn attribute values
     * Useful for get current db name
     * @param $name
     * @param \yii\db\Connection $db
     * @return null
     */
    public static function getDsnAttribute($name, \yii\db\Connection $db)
    {
        if ($db && preg_match('/' . $name . '=([^;]*)/', $db->dsn, $match)) {
            return $match[1];
        } else {
            return null;
        }
    }
}