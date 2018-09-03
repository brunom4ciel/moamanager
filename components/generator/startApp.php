<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\components\generator;

defined('_EXEC') or die();

use moam\core\AppException;
use moam\core\Framework;
// use moam\core\Application;
use moam\libraries\core\menu\Menu;

if (! class_exists('Application')) {
    $application = Framework::getApplication();
}

if (! $application->is_authentication()) {
    $application->alert("Error: you do not have credentials.");
}

Framework::import("menu", "core/menu");

if (! class_exists('Menu')) {
    $menu = new Menu();
}

Framework::import("Utils", "core/utils");

// error_reporting(E_ALL | E_STRICT);
// ini_set('display_errors', 1);

$script = "";
$prefix = "objbm";

try {

    require ("GeneratorDatasets.php");
    require ("AppCustom.class.php");
    require ("App.php");

    if (! empty($_POST[$prefix . "task"])) {

        App::instance();

        // Application::getScripts();

        print App::getScripts();
        exit();
    }
} catch (AppException $e) {
    throw new AppException($e->getMessage());
}

?>