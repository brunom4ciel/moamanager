<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\components\moa;

defined('_EXEC') or die();

use moam\core\Framework;
use moam\core\Application;
use moam\core\Properties;
use moam\core\Template;
use moam\libraries\core\utils\Utils;
use moam\libraries\core\menu\Menu;
use moam\libraries\core\json\JsonFile;
use moam\libraries\core\date\DateTimeFormats;
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
Framework::import("JsonFile", "core/json");
Framework::import("DateTimeFormats", "core/date");

Template::addHeader(array(
    "tag" => "script",
    "type" => "text/javascript",
    "src" => "" . $application->getPathTemplate() . "/javascript/base64.js"
));

$utils = new Utils();

$filename = $application->getParameter("filename");
$folder = $application->getParameter("folder");
$task = $application->getParameter("task");
$command = $application->getParameter("command");

if ($command == null)
    $command = 'all';

if ($task == null)
    $task = 'view';

$data = "";

if ($filename != null) {

    $filename = Properties::getBase_directory_destine($application) . $application->getUser() . 
    // .DIRECTORY_SEPARATOR
    // .$dirScriptsName
    DIRECTORY_SEPARATOR . $folder . 
    // .DIRECTORY_SEPARATOR
    $filename;
    // .$extension_scripts
    

    $jsonfile = new JsonFile();

    $jsonfile->open($filename);

    $data = $jsonfile->getData();
    $length_data = count($data);
    $script = "";

    if ($length_data > 0) {

        foreach ($data as $key => $element) {

            if ($command == 'all') {
                $script .= $element["script"] . "\n\n";
            } else {

                if ($command == "processed") { // echo $element["process"];
                    if ($element["process"] == true) {
                        $script .= $element["script"] . "\n\n";
                    }
                } else {
                    if ($command == "unprocessed") {
                        if ($element["process"] == false) {
                            $script .= $element["script"] . "\n\n";
                        }
                    }
                }
            }
        }
    }

    if ($task == 'download') {

        header('Content-disposition: attachment; filename=gen.txt');
        header('Content-type: text/plain');

        echo $script;

        exit();
    }
}

?>

<script>
function expand(id){

	if(id.alt=='' || id.alt === undefined){

		id.alt=id.text; 
		id.innerHTML=id.title;
	}else{
		id.innerHTML=id.alt; 
		id.alt='';
	}
}

function returnPage(){
	window.history.go(-1);
}

function downloadfile(){

	window.location.href='?component=<?php echo $application->getParameter("component");?>'
						+'&controller=reportscript'
						+'&filename=<?php echo $application->getParameter("filename");?>'
						+'&task=download'
						+'&command=<?php echo $command;?>'
						+'&folder=<?php echo $application->getParameter("folder");?>';
	
}

</script>
<div class="content content-alt">
	<div class="container" style="width: 90%">
		<div class="row">
			<div class="">

				<div class="card" style="width: 100%">
					<div class="page-header">
						<h1>
							<a href="<?php echo $_SERVER['REQUEST_URI']?>">Report View Script - <?php echo ucfirst($command);?></a>
						</h1>
					</div>

					<div style="width: 100%; padding-bottom: 15px; display: table">

						<div style="float: left; width: 200px; border: 1px solid #fff">
																
									<?php echo $application->showMenu($menu)?>								

								</div>

						<div style="float: left; width: 80%; border: 1px solid #fff">




							<input type="button" value="Return" name="return"
								onclick="javascript: returnPage();" /> <input type="button"
								value="Download" name="download"
								onclick="javascript: downloadfile();" />

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
											name="data"><?php echo $script?></textarea>
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


