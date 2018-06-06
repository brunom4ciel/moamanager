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

set_time_limit(92000000000); //
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);
error_reporting(1);
ini_set('memory_limit', '4G');
header("Content-type: text/html; charset=utf-8");

/**
 * Constant that is checked in included files to prevent direct access.
 * define() is used in the installation folder rather than "const" to not error for PHP 5.2 and lower
 */
define('_EXEC', 1);


/**
 * Constant that path base.
 */
define('PATH_BASE', __DIR__);

// defines user overwrite file
// require_once PATH_BASE . '/defines.php';

if (! defined('_DEFINES')) {
    require_once PATH_BASE . '/includes/defines.php';
}

require_once PATH_BASE . '/includes/bootstrapping.php';

use moam\core\Framework;

// Instantiate the application.
$application = Framework::getApplication();

// Execute the application.
$application->execute();

// Non-blocking PHP sessions

?>
