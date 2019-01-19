<?php
/**
 * @package    moam
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam;

define('MOAM_MINIMUM_PHP', '5.3.10');

if (version_compare(PHP_VERSION, MOAM_MINIMUM_PHP, '<')) {
    die('Your host needs to use PHP ' . MOAM_MINIMUM_PHP . ' or higher to run this version of MOAM!');
}

set_time_limit(-1);//9200000000000); //
// error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', '0');
error_reporting(0);
//ini_set('memory_limit', '8G');
ini_set("memory_limit",-1);
ini_set("upload_max_filesize",'200000M');
ini_set("post_max_size",'200000M');
header("Content-type: text/html; charset=utf-8");


/**
 * Constant that is checked in included files to prevent direct access.
 * define() is used in the installation folder rather than "const" to not error for PHP 5.2 and lower
 */
define('_EXEC', 1);

/**
 * Constant of MOAManager version
 */
define('MOAMANAGER_VERSION', '1.0.33');
define('MOAMANAGER_RELEASES', '2019/01/19');

/**
 * Constant of MOA version
 */
define('MOA_VERSION', '2014.0.3');
define('MOA_RELEASES', '2018/11/01');

/**
 * Constant of Statistical Tests version
 */
define('STATISTICAL_TESTS_VERSION', '1.0.4');
define('STATISTICAL_TESTS_RELEASES', '2019/01/19');


/**
 * Constant of path base.
 */
define('PATH_BASE', __DIR__);

// defines user overwrite file
if(file_exists(PATH_BASE . '/defines.php')) 
{
    require_once PATH_BASE . '/defines.php';   
}
else 
{
    require_once PATH_BASE . '/includes/defines.php';
}

require_once PATH_BASE . '/includes/bootstrapping.php';

use moam\core\Framework;

// Instantiate the application.
$application = Framework::getApplication();

// Execute the application.
$application->execute();


?>
