<?php

/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\components\moa;

defined('_EXEC') or die();

use moam\core\AppException;
use moam\core\Framework;
use moam\core\Properties;
use moam\libraries\core\utils\Utils;
use moam\libraries\core\log\ExecutionHistory;
use moam\libraries\core\menu\Menu;
use moam\core\Template;
use moam\libraries\core\json\JsonFile;
use moam\libraries\core\utils\ParallelProcess;
use moam\libraries\core\file\Files;
use moam\libraries\core\db\DBPDO;
use moam\libraries\core\email\UsageReportMail;
use moam\libraries\core\sms\Plivo;



if (!class_exists('Application'))
{
    $application = Framework::getApplication();
}

if(!$application->is_authentication())
{
    $application->alert ( "Error: you do not have credentials." );
}

Framework::import("menu", "core/menu");

if (!class_exists('Menu'))
{
    $menu = new Menu();
    
}


Framework::import("Utils", "core/utils");
Framework::import("Plivo", "core/sms");
Framework::import("ParallelProcess", "core/utils");
Framework::import("UsageReportMail", "core/email");
Framework::import("Files", "core/file");
Framework::import("JsonFile", "core/json");
Framework::import("execution_history", "core/log");
Framework::import("DBPDO", "core/db");

$DB = new DBPDO(Properties::getDatabaseName(),
    Properties::getDatabaseHost(),
    Properties::getDatabaseUser(),
    Properties::getDatabasePass());

Template::addHeader(array("tag"=>"script",
    "type"=>"text/javascript",
    "src"=>""
    . $application->getPathTemplate()
    . "/javascript/base64.js"));

$utils = new Utils();
$extension_scripts = ".data";



$filename = $application->getParameter("filename");
$folder = $application->getParameter("folder");
$task = $application->getParameter("task");
$id = $application->getParameter("id");

$data = "";

if ($filename != null) {

    $utils = new Utils();

    $filename = Properties::getBase_directory_destine($application) . $application->getUser() . 
    // .DIRECTORY_SEPARATOR
    // .$dirScriptsName
    DIRECTORY_SEPARATOR . $folder . 
    // .App::getDirectorySeparator()
    $filename;
    // .$extension_scripts
    

    Framework::import("JsonFile", "core/json");

    $jsonfile = new JsonFile();
    $jsonfile->open($filename);

    $data = $jsonfile->getDataKeyValue("id", $id);

    if ($task == "edit") {} else {

        if ($task == "save") {

            $data["process"] = false; // (strtolower(App::getParameter("process"))=="true"?true:false);
            $data["script"] = $application->getParameter("data");
            $data["starttime"] = "";
            $data["endtime"] = "";
            $data["running"] = false;
            $data["pid"] = "";

            $dir = Properties::getBase_directory_destine() . $application->getUser() . DIRECTORY_SEPARATOR . $folder;

            $filename_ = substr($filename, strrpos($filename, "/") + 1);
            $filename_ = substr($filename_, 0, strrpos($filename_, "."));
            $filename_ = trim($filename_);
            $filename_ .= ".txt";

            $dir = substr($filename, 0, strrpos($filename, "/") + 1);
            $dir = trim($dir);

            $filename_ = substr($filename_, 0, strpos($filename_, ".")) . "-" . $id . ".txt";

            // echo $dir.$filename_;

            // echo $dir.$id."-".$filename_;

            if (file_exists($dir . $filename_)) {
                unlink($dir . $filename_);
            }

            $jsonfile->setDataKeyValue("id", $id, $data);

            // echo "<br><br><br><br>";

            $data = $jsonfile->getDataKeyValue("id", $id);

            // var_dump($data);exit($id);

            $jsonfile->save();
            $jsonfile->load();

            $data = $jsonfile->getDataKeyValue("id", $id);
        }
    }
}

$filename_element = substr($application->getParameter("filename"), 0, strpos($application->getParameter("filename"), ".")) . "-" . $id . ".txt";
// $filename_element = App::getUser()."/".$folder.$filename_element;

// exit($filename_element);

if ($data["process"] == false)
    $process = "false";
else
    $process = "true";

?>

<div class="content content-alt">
	<div class="container" style="width: 90%">
		<div class="row">
			<div class="">

				<div class="card" style="width: 100%">
					<div class="page-header">
						<h1>
							<a href="<?php echo $_SERVER['REQUEST_URI']?>">Report Script Edit</a>
						</h1>
					</div>

					<div style="width: 100%; padding-bottom: 15px; display: table">

						<div style="float: left; width: 200px; border: 1px solid #fff">
																
									<?php echo $application->showMenu($menu);?>								

								</div>

						<div style="float: left; width: 80%; border: 1px solid #fff">







							<form method="POST"
								action="<?php echo $_SERVER['PHP_SELF'];?>#save" name="saveform"
								async-form="login"
								class="ng-pristine ng-valid-email ng-invalid ng-invalid-required">
								<input type="hidden" value="<?php echo $application->getComponent()?>"
									name="component"> <input type="hidden"
									value="<?php echo $application->getController()?>" name="controller"> <input
									type="hidden" value="save" name="task" id="task"> <input
									type="hidden"
									value="<?php echo $application->getParameter("filename");?>"
									name="filename"> <input type="hidden"
									value="<?php echo $application->getParameter("folder");?>" name="folder">

								<div style="float: left; padding-left: 20px; width: 100%">

									<div
										style="float: left; padding-left: 5px; width: 100%; margin-top: 5px;">

										id: <br> <input type="text" style="width: 100%" name="id"
											value="<?php echo $application->getParameter("id");?>"><br> script:<br>
										<textarea id="data" style="width: 100%; height: 400px;"
											name="data"><?php echo $data['script']?></textarea>
										<br> Process: <br> <select id="process" name="process">
											<option value="true">True</option>
											<option value="false">False</option>
										</select> <br> file name: <br>
												<?php

			$file = Properties::getBase_directory_destine($application) . $application->getUser() . DIRECTORY_SEPARATOR . $folder . $filename_element;

            if (file_exists($file)) {

                $filesize = $utils->filesize_formatted($file);
                ?>
													
												<?php echo $filename_element;?> <a target="_blank"
											href="<?php echo PATH_WWW ."?component=resource&tmpl=false&task=open&file=".$folder.$filename_element;?>">
											[Open]</a> <a
											href="<?php echo PATH_WWW."?component=resource&tmpl=false&task=download&file=".$folder.$filename_element;?>">
											[Download]</a> <?php echo $filesize?><br>								
													
												
												<?php
            } else {

                echo "<b style='color:red'>file does not exist.</b>";
            }

            ?>
												
												<br>
										<br>*If save data, the file will be removed automatic.

									</div>


									<div style="float: right; padding-left: 5px">


										<a name="save"></a><input type="button" value="Return"
											name="return" onclick="javascript: returnPage();" /> <input
											type="submit" value="Save">

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
	//window.history.go(-1);

	//http://localhost/iea/?component=moa&controller=reportview&filename=maciel.log&folder=New%20Folder/

		window.location.href='?component=<?php echo $application->getParameter("component");?>'
			+'&controller=reportview'
			+'&filename=<?php echo $application->getParameter("filename");?>'
			+'&folder=<?php echo $application->getParameter("folder");?>';
		
}

function downloadfile(){

	window.location.href='?component=<?php echo $application->getParameter("component");?>'
						+'&controller=reportscript'
						+'&filename=<?php echo $application->getParameter("filename");?>'
						+'&folder=<?php echo $application->getParameter("folder");?>';
	
}


function setSelectBox(Value, idSelectBox){

	var selectbox = document.getElementById(idSelectBox);

	for(i=0; i < selectbox.options.length; i++){

		if(selectbox.options[i].text.toLowerCase() == Value.toLowerCase()){
			selectbox.selectedIndex = i;
			break;
		}
			

	}
		
}

setSelectBox("<?php echo $process;?>", "process");
</script>

