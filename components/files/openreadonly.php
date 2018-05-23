<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\components\files;

defined('_EXEC') or die();

use moam\core\Framework;
use moam\core\Application;
use moam\core\Properties;
use moam\libraries\core\utils\Utils;
use moam\libraries\core\menu\Menu;
use ZipArchive;
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
$folder = $application->getParameter("folder");
// $dirScriptsName = "scripts";

$data = "";

if ($filename != null) {

    $utils = new Utils();

    $filename = Properties::getBase_directory_destine($application) . $application->getUser() . 
    // .DIRECTORY_SEPARATOR
    // .$dirScriptsName
    DIRECTORY_SEPARATOR . $folder . 
    // .DIRECTORY_SEPARATOR
    $filename;
    // .$extension_scripts
    

    $task = $application->getParameter("task");

    if ($task == "save") {

        $data = $application->getParameter("data");
        $utils->setContentFile($filename, $data);

        $filenamenew = Properties::getBase_directory_destine($application) . $application->getUser() . 
        // .DIRECTORY_SEPARATOR
        // .$dirScriptsName
        DIRECTORY_SEPARATOR . $folder . 
        // .DIRECTORY_SEPARATOR
        $application->getParameter("filenamenew");

        if ($application->getParameter("filenamenew") != $application->getParameter("filename")) {

            if (file_exists($filename)) {

                if (file_exists($filenamenew . $extension_scripts)) {

                    while (file_exists($filenamenew . $extension_scripts)) {
                        $filenamenew = "copy-" . $filenamenew;
                    }
                }

                rename($filename, $filenamenew . $extension_scripts);

                $application->getParameter("filename", substr($filenamenew, strrpos($filenamenew, "/") + 1, strrpos($filenamenew, ".")));
            }
        }
    } else {

        if ($task == "remove") {

            if (file_exists($filename)) {

                unlink($filename);
                header("Location: " . PATH_WWW . "?component=" . $application->getComponent() . "&controller=files");
            }
        } else {

            if ($task == "new") {

                // exit($filename);

                $data = "";
                $utils->setContentFile($filename, $data);
            } else {

                $extension = substr($filename, strrpos($filename, ".") + 1);

                if ($extension == "zip") {
                    $data = "";

                    $za = new ZipArchive();

                    $za->open($filename);

                    for ($i = 0; $i < $za->numFiles; $i ++) {

                        $item = $za->statIndex($i);

                        $data .= $item["name"] . " - " . $utils->formatSize($item["size"]) . "<br>";
                        // print_r($za->statIndex($i));
                    }
                    // echo "numFile:" . $za->numFiles . "\n";
                } else {

                    $maxBytesFileLoadPart = Properties::getFileContentsMaxSize();

                    $data1 = $utils->getContentFilePart($filename, ($maxBytesFileLoadPart * 1024));

                    $data = $data1["data"];
                }
            }
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
							<a href="<?php echo $_SERVER['REQUEST_URI']?>">Open - Read-only</a>
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
									name="component"> <input type="hidden" value="editfile"
									name="controller"> <input type="hidden" value="save"
									name="task" id="task"> <input type="hidden"
									value="<?php echo $application->getParameter("filename");?>"
									name="filename"> <input type="hidden"
									value="<?php echo $application->getParameter("folder");?>"
									name="folder">

								<div style="float: left; padding-left: 20px; width: 100%">

									<div
										style="margin-left: 5px; display: table; width: 99%; height: 70px; background-color: #F3F781; border: 1px solid #000; text-align: center; vertical-align: middle;">
										Read-only <br>
											<?php

        echo "From Original: " . $utils->formatSize(filesize($filename)) . "<br>";

        $extension = substr($filename, strrpos($filename, ".") + 1);

        if ($extension == "zip") {} else {
            echo "Load: " . $utils->formatSize($data1["size"]);
        }
        ?>
											
											</div>


									<div
										style="float: left; padding-left: 5px; width: 100%; margin-top: 5px;">
												
												<?php

            if ($extension == "zip") {

                echo '<a href="' . PATH_WWW . '?component=resource&tmpl=tmpl&task=download&file=' . $application->getParameter("folder") . $application->getParameter("filename") . '">' . "<img width='16px' align='middle' src='" . $application->getPathTemplate() . "/images/icon_download.png' title='View contents'/></a> ";

                echo $application->getParameter("filename") . "<br><br>";
                echo $data;
            } else {
                ?>
													
												<input type="text" style="width: 100%" name="filenamenew"
											value="<?php echo $application->getParameter("filename");?>">
										<textarea id="data" style="width: 100%; height: 400px;"
											name="data"><?php echo $data?></textarea>		
													<?php
            }

            ?>
												
												
											</div>


									<div style="float: left; padding-left: 10px">
										<input type="button" value="Return"
											onclick="javascript: window.location.href='?component=files&folder=<?php echo $application->getParameter("folder");?>';">
										<!-- <input type="submit" value="Save">							
												<input type="submit" value="Remove" onclick="javascript: document.getElementById('task').value='remove'"> 	
											 -->
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


