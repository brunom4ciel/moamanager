<?php

/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\components\moa;

defined('_EXEC') or die();

// use moam\core\AppException;
use moam\core\Framework;
use moam\core\Properties;
use moam\libraries\core\utils\Utils;
// use moam\libraries\core\log\ExecutionHistory;
// use moam\libraries\core\menu\Menu;
use moam\core\Template;
use moam\libraries\core\json\JsonFile;
// use moam\libraries\core\utils\ParallelProcess;
// use moam\libraries\core\file\Files;
// use moam\libraries\core\db\DBPDO;
// use moam\libraries\core\email\UsageReportMail;
// use moam\libraries\core\sms\Plivo;



if (!class_exists('Application'))
{
    $application = Framework::getApplication();
}

if(!$application->is_authentication())
{
    $application->alert ( "Error: you do not have credentials." );
}

// Framework::import("menu", "core/menu");

// if (!class_exists('Menu'))
// {
//     $menu = new Menu();
    
// }

// Template::setDisabledMenu();

Template::addHeader(array("tag"=>"script",
    "type"=>"text/javascript",
    "src"=>""
    . $application->getPathTemplate()
    . "/javascript/edit_area/edit_area_full.js"));



Framework::import("Utils", "core/utils");
// Framework::import("Plivo", "core/sms");
// Framework::import("ParallelProcess", "core/utils");
// Framework::import("UsageReportMail", "core/email");
// Framework::import("Files", "core/file");
Framework::import("JsonFile", "core/json");
// Framework::import("execution_history", "core/log");
// Framework::import("DBPDO", "core/db");

// $DB = new DBPDO(Properties::getDatabaseName(),
//     Properties::getDatabaseHost(),
//     Properties::getDatabaseUser(),
//     Properties::getDatabasePass());

// Template::addHeader(array("tag"=>"script",
//     "type"=>"text/javascript",
//     "src"=>""
//     . $application->getPathTemplate()
//     . "/javascript/base64.js"));

$utils = new Utils();
$extension_scripts = ".data";



$filename = $application->getParameter("filename");
$folder = $application->getParameter("folder");
$task = $application->getParameter("task");
$id = $application->getParameter("id");

$dir = PATH_USER_WORKSPACE_STORAGE . $folder;


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

    
    
    if ($task == "edit") {
        
    } else {

        
        if ($task == "save") {

            
            $find = FALSE;
            if($jsonfile->issetKeyValue("filename"))
            {
                $s = $data;
                $filename_aux = substr($s['filename'], strrpos($s['filename'],DIRECTORY_SEPARATOR)+1);
                $find = TRUE;                
            }
            else
            {                    
//                 $filename_ = substr($data['command'], strrpos($data['command'], "/") + 1);
//                 $filename_ = trim($filename_);
//                 $filename_f = substr($filename_, strrpos($filename_, DIRECTORY_SEPARATOR));                    
                
                //$s = $jsonfile->findDataKeyValue("command", $filename_f);
                
                $s = $data;
                
                $filename_aux = substr($s['command'], strrpos($s['command'],DIRECTORY_SEPARATOR)+1);
                $find = TRUE;
            }
                
                
                
            if($find == TRUE)
            {
                
                if(file_exists($dir . $filename_aux))
                {
                    chmod($dir . $filename_aux, octdec("0777"));
                    
                    if(!unlink($dir . $filename_aux))
                    {
                        exit("Error: operation not allowed.");
                    }
                }
                
//                 $jsonfile->removeDataKeyValue("id", $id);

                $data["process"] = false; // (strtolower(App::getParameter("process"))=="true"?true:false);
                $data["script"] = $application->getParameter("data");
                $data["starttime"] = "";
                $data["endtime"] = "";
                $data["running"] = false;
                $data["pid"] = "";
                
                $jsonfile->setDataKeyValue("id", $id, $data);
                
                $jsonfile->save();
                
//                 $data = $jsonfile->getDataKeyValue("id", $id);

                $application->redirect(PATH_WWW . "?component=" . $application->getComponent()
                    . "&controller=" . $application->getController()
                    . "&filename=" . basename($filename)
                    . "&folder=" . $folder . ""
                    . "&id=" . $id . ""
                    . "&task=open");
                
                
            }
                

            
        }
    }
}


if($jsonfile->issetKeyValue("filename"))
{
    $filename_element = $data['filename'];
}
else
{
    $s = $data;    
    $filename_element = substr($s['command'], strrpos($s['command'],DIRECTORY_SEPARATOR)+1);
    $filename_element = $dir . $filename_element;
}



// $filename_element = $data['filename'];// substr($application->getParameter("filename"), 0, strpos($application->getParameter("filename"), ".")) . "-" . $id . ".txt";
// $filename_element = App::getUser()."/".$folder.$filename_element;

// exit($filename_element);

if ($data["process"] == false)
{
    $process = "False";
}
else
{
    $process = "True";
}





?>



							<div class="page-header">
        						<h1>
        							<a href="<?php echo $_SERVER['REQUEST_URI']?>">Edit Script</a>
        						</h1>
        					</div>
        					
							<form method="POST"
								action="?<?php echo $_SERVER['QUERY_STRING'];?>#save" name="saveform"
							
								class="ng-pristine ng-valid-email ng-invalid ng-invalid-required">
								<input type="hidden" value="<?php echo $application->getComponent()?>"
									name="component"> <input type="hidden"
									value="<?php echo $application->getController()?>" name="controller"> <input
									type="hidden" value="save" name="task" id="task"> <input
									type="hidden"
									value="<?php echo $application->getParameter("filename");?>"
									name="filename"> <input type="hidden"
									value="<?php echo $application->getParameter("folder");?>" name="folder">

								
									<div
										style="float: left; padding-left: 5px; width: 100%; margin-top: 5px;">

										id: <br> <input type="text" style="width: 100%" name="id"
											value="<?php echo $application->getParameter("id");?>"><br> script:<br>
										<textarea id="data" style="width: 100%; height: 400px;"
											name="data"><?php echo $data['script']?></textarea>
										<br> Process: <?php echo $process;?> <br> 
										<?php  
										
										if(file_exists($filename_element))
										{
										    if($utils->isMetadataFileScript($filename_element))
										    {
										        $hash_file = $utils->checksumFileScriptMOA($filename_element,
										            Properties::getBase_directory_destine_exec() .
										            $application->getUser() . DIRECTORY_SEPARATOR, $data['script']);
										        
										        $metadata_hmac = $utils->getMetadataValueScript($filename_element,
										            "security-hash-hmac-file");
										        
										        echo "Checksum metadata: ". $metadata_hmac . "<br>";
										        echo "Checksum file: ". $hash_file."<br>";
										        
										        if($metadata_hmac == $hash_file)
										        {
										            echo "Trusted signature: authenticated data integrity";
										        }
										        else
										        {
										            echo "Non-trusted signature: non-authenticated data integrity";
										        }
										    }
										}
										
										?><br>
										Folder: <?php echo $folder;?><br>
										File name: 
												<?php

			$file = $filename_element;

			$filename_element = basename($filename_element);
			
            if (file_exists($file)) {

                $filesize = $utils->filesize_formatted($file);
                ?>
													
												<?php echo $filename_element;?> <br><a target="_blank"
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


									

								<div style="float: right; padding-left: 10px">
									
										<input type='button' class="btn btn-info" onclick='toogle_editable("data", this);' value='Toggle to read only mode' />
										
										<a name="save"></a><input
											type="submit" class="btn btn-success"  value="Save">
														
										<input type="button" class="btn btn-default badge" value="Return" name="return"
										onclick="javascript: returnPage();" />
										
									</div>
									
									
							</form>




									
									

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


// function setSelectBox(Value, idSelectBox){

// 	var selectbox = document.getElementById(idSelectBox);

// 	for(i=0; i < selectbox.options.length; i++){

// 		if(selectbox.options[i].text.toLowerCase() == Value.toLowerCase()){
// 			selectbox.selectedIndex = i;
// 			break;
// 		}
			

// 	}
		
// }

//setSelectBox("<?php echo $process;?>", "process");
</script>





<script type="text/javascript">
	// initialisation
	editAreaLoader.init({
		id: "data"	// id of the textarea to transform	
			,start_highlight: true	
			,font_size: "8"
			,is_editable: true
			,word_wrap: true
			,font_family: "verdana, monospace"
			,allow_resize: "y"
			,allow_toggle: true
			,language: "en"
			,syntax: "xml"	
			,toolbar: "go_to_line, |, undo, redo, |, select_font"
			//,load_callback: "my_load"
			//,save_callback: "my_save"
			,plugins: "charmap"
			,min_height: 300
			,charmap_default: "arrows"
	});


	function toogle_editable(id, id2)
	{
		if(id2.value == "Toggle to edit mode")
		{
			id2.value = "Toggle to read only mode";
		}
		else
		{
			id2.value = "Toggle to edit mode";
		}
		
		editAreaLoader.execCommand(id, 'set_editable', !editAreaLoader.execCommand(id, 'is_editable'));
	}

</script>

