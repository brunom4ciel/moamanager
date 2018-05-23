<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\components\generator;

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

$utils = new Utils();

$dirScriptsName = "scripts";

$dirstorage = $application->getParameter("dirstorage");

if ($dirstorage == null) {

    $dirStorage = Properties::getBase_directory_destine($application) . $application->getUser() . DIRECTORY_SEPARATOR . $dirScriptsName . DIRECTORY_SEPARATOR;
} else {

    $dirStorage = Properties::getBase_directory_destine($application) . $application->getUser() . DIRECTORY_SEPARATOR . $dirScriptsName . DIRECTORY_SEPARATOR . $application->getParameter("dirstorage") . DIRECTORY_SEPARATOR;
}

$filename = $dirStorage . $application->getParameter("filename") . ".data";

$console2 = $application->getParameter("console2");

$utils->setContentFile($filename, $console2);

$application->redirect(PATH_WWW . "?component=home&controller=scripts");

?>