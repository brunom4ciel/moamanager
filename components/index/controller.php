<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\components\index;

defined('_EXEC') or die();

use moam\core\Framework;
use moam\core\Template;


if (! class_exists('Application')) {
    $application = Framework::getApplication();
}

if (!$application->is_authentication()) {
    
    $application->redirect(PATH_WWW . "?component=user&controller=login");

}


$application->redirect(PATH_WWW . "?component=systemmonitor");

Template::setDisabledMenu();

?>


Welcome

<br><br><br><br>