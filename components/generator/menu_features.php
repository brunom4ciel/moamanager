<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\components\home;

defined('_EXEC') or die();

use moam\core\Framework;
use moam\core\Application;
if (! class_exists('Application')) {
    $application = Framework::getApplication();
}

if (! $application->is_authentication()) {
    $application->alert("Error: you do not have credentials.");
}

if ($application->is_authentication()) {

    ?>
<ul id="nav">

	<li><a href="<?php echo PATH_WWW;?>">Home</a></li>
	<li><a href="<?php echo PATH_WWW;?>?component=scripts">Scripts</a></li>


</ul>

<?php }else{ ?>

<?php }?>