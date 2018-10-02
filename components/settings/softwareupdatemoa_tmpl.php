<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\components\settings;

defined('_EXEC') or die();

use moam\core\Framework;
use moam\core\Application;
use moam\core\Properties;
use moam\libraries\core\utils\Utils;


if (! class_exists('Application')) {
    $application = Framework::getApplication();
}

if (! $application->is_authentication() || $application->getUserType() != 1) {
    $application->alert("Error: you do not have credentials.");
}

Framework::import("Utils", "core/utils");

$utils = new Utils();


$dirProcess = Properties::getBase_directory_destine_exec()
// .$application->getUser()
// .DIRECTORY_SEPARATOR
;

$chmod = "0777";

$tmp_update = $dirProcess . "repository/";

if(is_dir($tmp_update))
{
    chmod($tmp_update, octdec($chmod));    
}

$cmd = "sh " . $dirProcess . "repository/moamanager/setup/dependencies/update-moa.sh";
$s = $utils->runExternal($cmd);

echo $s['output'];

echo "Finished Update\n";


?>