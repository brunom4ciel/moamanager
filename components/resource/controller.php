<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\components\resource;

defined('_EXEC') or die();

use moam\core\Framework;
use moam\core\Template;

use moam\core\Properties;
if (! class_exists('Application')) {
    $application = Framework::getApplication();
}

if (! $application->is_authentication()) {
    $application->alert("Error: you do not have credentials.");
}

$file = Properties::getBase_directory_destine($application) . $application->getUser() . DIRECTORY_SEPARATOR . $application->getParameter("file");

$task = $application->getParameter("task");

if (file_exists($file)) {

    if ($task == "open") {

        header("Content-Type: text/plain");
    } else {

        if ($task == "download") {

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file) . '"');
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
        }
    }

    ob_clean();
    ob_end_flush();

    // readfile($file);

    $handle = fopen($file, "rb");
    while (! feof($handle)) {
        echo fread($handle, 1000);
    }
    
    fclose($handle);
    

} else {
    // echo "Error: file not found.";
    $application->alert("Error: file not found.");
}

?>						
