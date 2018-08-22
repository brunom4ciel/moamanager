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
use moam\libraries\core\utils\Utils;
if (! class_exists('Application')) {
    $application = Framework::getApplication();
}

if (! $application->is_authentication()) {
    $application->alert("Error: you do not have credentials.");
}

Framework::import("Utils", "core/utils");

$utils = new Utils();

$filename = PATH_BASE . DIRECTORY_SEPARATOR . "includes" 
    . DIRECTORY_SEPARATOR . "defines.php";

if (isset($_POST['data'])) {

    $data = $application->getParameter("data");

    $utils->setContentFile($filename, $data);
} else {

    $data = $utils->getContentFile($filename);
}

?>



<div class="content content-alt">
	<div class="container" style="width: 70%">
		<div class="row">
			<div class="">
				<div class="card" style="width: 100%">



					<div class="page-header">
						<h1>Edit Defines</h1>
					</div>


					<form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>"
						name="loginForm" enctype="multipart/form-data">
						<input type="hidden"
							value="<?php echo $application->getComponent()?>"
							name="component"> <input type="hidden"
							value="<?php echo $application->getController()?>"
							name="controller">

						<textarea id="data" style="width: 100%; height: 400px;"
							name="data"><?php echo $data?></textarea>
						<br>

						<div style="text-align: right; display: block;">

							<input type="submit" name="save" value="Save" /> <input
								type="button"
								onclick="javascript: window.location.href='?component=settings';"
								name="cancel" value="Cancel" />

						</div>

					</form>


				</div>

			</div>
		</div>
	</div>
</div>


