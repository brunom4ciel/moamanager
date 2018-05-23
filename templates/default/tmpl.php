<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\templates;

defined('_EXEC') or die();

use moam\core\Template;
if (isset($_REQUEST['force-data-type'])) {
    header('Content-Type: ' . $_REQUEST['force-data-type']);
}

if (isset($_GET['alert'])) {
    if ($_GET['alert'] == true) {
        if (! empty($_GET['msg'])) {
            echo $_GET['msg'];
        }
    }
} else {
    echo Template::getBody();
}

?>
