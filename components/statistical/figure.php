<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\components\statistical;

defined('_EXEC') or die();

use moam\core\Framework;
use moam\libraries\core\utils\Utils;
use moam\core\Properties;

if (! class_exists('Application')) {
    $application = Framework::getApplication();
}

if (! $application->is_authentication()) {
    $application->alert("Error: you do not have credentials.");
}


Framework::import("Utils", "core/utils");
$utils = new Utils();

$files_tmp = array("png","pdf","eps");
$filename = $application->getParameter("filename");
$attachment = $application->getParameter("attachment");
$file = PATH_USER_WORKSPACE_PROCESSING . $filename;

if(in_array(substr($file, strrpos($file, ".")+1) , $files_tmp))
{	
	if($attachment == null)
	{
		header("Content-type: image/png");
		header('Expires: 0');
		header('Content-Length: ' . filesize($file));
		header('Content-Disposition: filename=' . $filename);
		readfile("$file");
	}
	else
	{
	    // force download
		@ob_end_clean();
		ob_start();

		//header('Content-type: image/' . substr($file, strrpos($file, ".")+1));
		Header('Content-Description: File Transfer');
		header('Content-Transfer-Encoding: binary');
		header('Accept-Ranges: bytes');
		header('Content-Length: ' . filesize($file));
		header('Content-Disposition: attachment;filename=' . "\"" . $filename . "\"");

		readfile("$file");            
	}

}



exit();

?>
