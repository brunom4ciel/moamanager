<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\components\home;

defined('_EXEC') or die();

use moam\core\Framework;
use moam\core\Application;
use moam\core\Properties;
use moam\libraries\core\utils\Utils;
if (! class_exists('Application')) {
    $application = Framework::getApplication();
}

if (! $application->is_authentication()) {
    $application->alert("Error: you do not have credentials.");
}

Framework::import("Utils", "core/utils");
Framework::import("class_CPULoad", "core/sys");

$utils = new Utils();

$json_return = array();

$files_list = $utils->getListElementsDirectory1(Properties::getBase_directory_destine_exec() . $application->getUser() . DIRECTORY_SEPARATOR . "", array(
    "txt"
));

$i = 0;
foreach ($files_list as $key => $element) {

    $i ++;
    if ($element["type"] != "dir") {

        array_push($json_return, array(
            "name" => $element["name"],
            "size" => $element["size"],
            "datetime" => $element["datetime"]
        ));
    }
}

print json_encode($json_return);

?>	
								