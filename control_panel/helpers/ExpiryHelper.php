<?php

namespace control_panel\helpers;


class ExpiryHelper
{
    /**
     * Generate expired time plus 1 month
     * @param int $time
     * @return int
     */
    public static function month($time)
    {
        if (date("j", $time) == 29 or date("j", $time) == 30 or date("j", $time) == 31) {
            $time = mktime(date("H", $time), date("i", $time), date("s", $time), date("n", $time)+1, 1, date("Y", $time));
        }

        return mktime(date("H", $time), date("i", $time), date("s", $time), date("n", $time)+1, date("j", $time), date("Y", $time));
    }

    /**
     * Generate expired time plus 1 year
     * @param int $time
     * @return int
     */
    public static function year($time)
    {
        if (date("j", $time) == 29 or date("j", $time) == 30 or date("j", $time) == 31) {
            $time = mktime(date("H", $time), date("i", $time), date("s", $time), date("n", $time) + 1, 1, date("Y", $time));
        }

        return mktime(date("H", $time), date("i", $time), date("s", $time), date("n", $time), date("j", $time), date("Y", $time) + 1);
    }

    /**
     * Generate expired time plus num $days, started from $fromTime
     * @param $days
     * @param $fromTime
     * @return false|int
     */
    public static function days($days, $fromTime = null)
    {
        $fromTime = empty($fromTime) ? time() : $fromTime;

        return $fromTime + $days * 24 * 60 * 60;
    }
}
