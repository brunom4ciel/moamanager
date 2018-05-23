<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\libraries\core\file;

defined('_EXEC') or die();

class Files
{

    public function listView($files_list)
    {}

    public function loadListScripts($filename)
    {
        $result = array();

        $handle = fopen($filename, "r");

        if ($handle) {

            while (($buffer = fgets($handle, 4096)) !== false) {
                if (substr(trim($buffer), 0, 1) != "#" && trim($buffer) != "") {
                    $result[] = trim($buffer);
                }
            }

            fclose($handle);
        }

        return $result;
    }
}

?>