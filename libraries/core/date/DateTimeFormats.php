<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\libraries\core\date;

defined('_EXEC') or die();

use DateTime;
use Exception;

class DateTimeFormats
{

    private $datediff = null;

    public function getMinutes($date_diff_format)
    {}

    public function date_diff_format($datediff, $format = array("i","s"))
    {
        $result = "";
        $format_control = array(
            "y" => "y",
            "m" => "m",
            "d" => "d",
            "h" => "h",
            "i" => "min",
            "s" => "s"
        );

        try {

            if (is_array($datediff)) {

                // if(count($datediff)>0){

                foreach ($format as $key => $value) {

                    if (array_key_exists($value, $format_control)) {

                        if (isset($datediff[$value])) {

                            if ($value == "h" || $value == "i" || $value == "s") {

                                if (! empty($result))
                                    $result .= ":";

                                if ($datediff[$value] > 9) {
                                    $result .= $datediff[$value];
                                } else {
                                    $result .= "0" . $datediff[$value];
                                }
                            } else {

                                if ($value == "y" || $value == "m" || $value == "d") {

                                    if (! empty($result))
                                        $result .= "-";
                                    else
                                        $result .= " ";

                                    if ($datediff[$value] > 9) {
                                        $result .= $datediff[$value];
                                    } else {
                                        $result .= "0" . $datediff[$value];
                                    }
                                }
                            }
                        }
                    }
                }

                // }
            }
        } catch (Exception $e) {
            $result["error"] = $e->getMessage();
        }

        return $result;
    }

    public function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    public function date_diff($date_star, $date_end, $format = array("i","s"))
    {

        // /Object ( [y] => 0 [m] => 0 [d] => 0 [h] => 0 [i] => 4 [s] => 35 [weekday] => 0 [weekday_behavior] => 0 [first_last_day_of] => 0 [invert] => 1 [days] => 0 [special_type] => 0 [special_amount] => 0 [have_weekday_relative] => 0 [have_special_relative] => 0 )
        $result = array();
        $format_control = array(
            "y",
            "m",
            "d",
            "h",
            "i",
            "s"
        );

        try {

            if ($this->validateDate($date_star)) {
                $start = date_create($date_star);

                if ($this->validateDate($date_end)) {
                    $end = date_create($date_end);

                    $diff = date_diff($end, $start);

                    foreach ($format as $key => $value) {

                        if (in_array($value, $format_control)) {
                            $result[$value] = $diff->$value;
                        }
                    }
                }
            }
        } catch (Exception $e) {
            $result["error"] = $e->getMessage();
        }

        return $result;
    }
}

?>