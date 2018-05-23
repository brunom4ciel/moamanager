<?php
/**
 * @package    moam\includes
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\includes;

defined('_EXEC') or die();

// Global definitions
define('PATH_CORE', PATH_BASE . DIRECTORY_SEPARATOR . 'core');
define('PATH_LIBRARIES', PATH_BASE . DIRECTORY_SEPARATOR . 'libraries');
define('PATH_COMPONENTS', PATH_BASE . DIRECTORY_SEPARATOR . 'components');
define('PATH_TEMPLATES', PATH_BASE . DIRECTORY_SEPARATOR . 'templates');
define('PATH_LANGUAGE', PATH_BASE . DIRECTORY_SEPARATOR . 'language');

define('DIRNAME_SCRIPT', 'scripts');
define('DIRNAME_TRASH', 'trash');
define('DIRNAME_BACKUP', 'backup');

define('APPLICATION_NAME', 'MOAManager');
define('SESSION_NAME', 'MOAM');

define('PATH_WWW', substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], "/") + 1));
define('WWW_RESOURCE', PATH_WWW . 'resource');
define('WWW_IMAGES', WWW_RESOURCE . '/images');

define('TEMPLATE_DEFAULT', 'default');
define('LANGUAGE_DEFAULT', 'en-us');

define('PHPMAILER_HOST', 'smtp.gmail.com');
define('PHPMAILER_PORT', 587);
define('PHPMAILER_SMTPSECURE', 'tls');
define('PHPMAILER_SMTPAUTH', true);
define('PHPMAILER_USERNAME', '');
define('PHPMAILER_PASSWORD', '');
define('PHPMAILER_FROMNAME', 'Bruno Maciel');


// define('PATH_WWW_TEMPLATE', PATH_WWW . 'default');

// Set the platform root path as a constant if necessary.
if (! defined('PATH_PLATFORM')) {
    define('PATH_PLATFORM', __DIR__);
}

// Detect the native operating system type.
$os = strtoupper(substr(PHP_OS, 0, 3));

if (! defined('IS_WIN')) {
    define('IS_WIN', ($os === 'WIN') ? true : false);
}

if (! defined('IS_UNIX')) {
    define('IS_UNIX', (($os !== 'MAC') && ($os !== 'WIN')) ? true : false);
}

?>
