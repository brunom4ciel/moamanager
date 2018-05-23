<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\includes;

defined('_EXEC') or die();

use moam\core\Framework;
// use moam\core\Tempĺate;

// // Import the library loader if necessary.
// if (!class_exists('Loader'))
// {
// require_once PATH_LIBRARIES . DIRECTORY_SEPARATOR . 'loader.php';
// }

// Import the library loader if necessary.
if (! class_exists('AppException')) {
    require_once PATH_CORE . DIRECTORY_SEPARATOR . 'iexception.php';
    require_once PATH_CORE . DIRECTORY_SEPARATOR . 'customexception.php';
    require_once PATH_CORE . DIRECTORY_SEPARATOR . 'appexception.php';
}

require_once PATH_CORE . DIRECTORY_SEPARATOR . 'abstractapplication.php';
require_once PATH_CORE . DIRECTORY_SEPARATOR . 'application.php';

// Import the library loader if necessary.
if (! class_exists('Framework')) {
    require_once PATH_CORE . DIRECTORY_SEPARATOR . 'framework.php';
}

// Import the library loader if necessary.
if (! class_exists('Template')) {
    require_once PATH_CORE . DIRECTORY_SEPARATOR . 'template.php';
}

// Import the library loader if necessary.
if (! class_exists('Properties')) {
    require_once PATH_CORE . DIRECTORY_SEPARATOR . 'properties.php';
}

// PHP_SESSION_DISABLED se as sessões estiverem desabilitadas.
// PHP_SESSION_NONE se as sessões estiverem habilitadas, mas nenhuma existir.
// PHP_SESSION_ACTIVE se as sessões estiverem habilitadas, e uma existir.

if (session_status() == PHP_SESSION_DISABLED) {
    // alert error
}

?>
