<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\components\generator;

defined('_EXEC') or die();

use moam\core\Framework;
use moam\core\Application;
use moam\core\Properties;
use moam\libraries\core\utils\Utils;
use moam\libraries\core\menu\Menu;
if (! class_exists('Application')) {
    $application = Framework::getApplication();
}

if (! $application->is_authentication()) {
    $application->alert("Error: you do not have credentials.");
}

Framework::import("menu", "core/menu");

if (! class_exists('Menu')) {
    $menu = new Menu();
}

Framework::import("Utils", "core/utils");

$extension_scripts = ".data";

$filename = $application->getParameter("filename");

if (strrpos($filename, ".") > - 1) {

    if (in_array(substr($filename, strrpos($filename, ".") + 1), array(
        "data"
    ))) {

        $filename = substr($filename, 0, strrpos($filename, "."));

        $application->setParameter("filename", $filename);
    }
}

$folder = $application->getParameter("folder");
$dirScriptsName = "scripts";

$data = "";

if ($filename != null) {

    $utils = new Utils();

    $filename = Properties::getBase_directory_destine($application) . $application->getUser() . DIRECTORY_SEPARATOR . $dirScriptsName . DIRECTORY_SEPARATOR . $folder . 
    // .DIRECTORY_SEPARATOR
    $filename . $extension_scripts;

    $task = $application->getParameter("task");

    if ($task == "save") {

        $data = $application->getParameter("data");
        $utils->setContentFile($filename, $data);

        $filenamenew = Properties::getBase_directory_destine($application) . $application->getUser() . DIRECTORY_SEPARATOR . $dirScriptsName . DIRECTORY_SEPARATOR . $folder . 
        // .DIRECTORY_SEPARATOR
        $application->getParameter("filenamenew"); // /.$extension_scripts;

        if ($application->getParameter("filenamenew") != $application->getParameter("filename")) {

            if (file_exists($filename)) {

                if (file_exists($filenamenew . $extension_scripts)) {

                    while (file_exists($filenamenew . $extension_scripts)) {
                        $filenamenew = "copy-" . $filenamenew;
                    }
                }

                rename($filename, $filenamenew . $extension_scripts);

                $application->setParameter("filename", substr($filenamenew, strrpos($filenamenew, "/") + 1, strrpos($filenamenew, ".")));
            }
        }
    } else {

        if ($task == "remove") {

            if (file_exists($filename)) {

                unlink($filename);
                header("Location: " . PATH_WWW . "?component=" . $application->getComponent() . "");
            }
        } else {

            // if(in_array(substr($filename,strrpos($filename, ".")+1),
            // array("txt","data") )){

            if ($task == "new") {

                // exit($filename);

                $data = "";
                $utils->setContentFile($filename, $data);
            } else {

                $data = $utils->getContentFile($filename);
            }

            // }
        }
    }
}

?>


<div class="content content-alt">
	<div class="container" style="width: 90%">
		<div class="row">
			<div class="">

				<div class="card" style="width: 100%">
					<div class="page-header">
						<h1>
							<a href="<?php echo $_SERVER['REQUEST_URI']?>">Edit File</a>
						</h1>
					</div>

					<div style="width: 100%; padding-bottom: 15px; display: table">

						<div style="float: left; width: 200px; border: 1px solid #fff">
																
									<?php echo $application->showMenu($menu);?>							

								</div>

						<div style="float: left; width: 80%; border: 1px solid #fff">


							<form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>"
								name="saveform" async-form="login"
								class="ng-pristine ng-valid-email ng-invalid ng-invalid-required">
								<input type="hidden"
									value="<?php echo $application->getComponent()?>"
									name="component"> <input type="hidden" value="edit"
									name="controller"> <input type="hidden" value="save"
									name="task" id="task"> <input type="hidden"
									value="<?php echo $application->getParameter("filename");?>"
									name="filename"> <input type="hidden"
									value="<?php echo $application->getParameter("folder");?>"
									name="folder">

								<div style="float: left; padding-left: 20px; width: 100%">

									<div
										style="float: left; padding-left: 5px; width: 100%; margin-top: 5px;">

										<input type="text" style="width: 100%" name="filenamenew"
											value="<?php echo $application->getParameter("filename");?>">
										<textarea id="data" style="width: 100%; height: 400px;"
											name="data"><?php echo $data?></textarea>
									</div>


									<div style="float: left; padding-left: 10px">
										<input type="button" value="Return"
											onclick="javascript: window.location.href='?component=scripts&folder=<?php echo $application->getParameter("folder");?>';">
										<input type="button" value="Execute"
											onclick="javascript: window.location.href='?component=moa&controller=run&task=open&filename=<?php echo $application->getParameter("filename");?>&folder=<?php echo $application->getParameter("folder");?>';">
										<input type="submit" value="Save"> <input type="submit"
											value="Remove"
											onclick="javascript: document.getElementById('task').value='remove'">
									</div>

								</div>

							</form>


						</div>

					</div>

				</div>
			</div>
		</div>
	</div>
</div>


