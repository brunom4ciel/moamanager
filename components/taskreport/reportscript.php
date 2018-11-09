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
// use moam\core\Application;
use moam\core\Properties;
use moam\core\Template;
use moam\libraries\core\utils\Utils;
// use moam\libraries\core\menu\Menu;
use moam\libraries\core\json\JsonFile;
// use moam\libraries\core\date\DateTimeFormats;
if (! class_exists('Application')) {
    $application = Framework::getApplication();
}

if (! $application->is_authentication()) {
    $application->alert("Error: you do not have credentials.");
}

// Framework::import("menu", "core/menu");

// if (! class_exists('Menu')) {
//     $menu = new Menu();
// }

Template::setDisabledMenu();

Template::addHeader(array("tag"=>"script",
    "type"=>"text/javascript",
    "src"=>""
    . $application->getPathTemplate()
    . "/javascript/edit_area/edit_area_full.js"));


Framework::import("Utils", "core/utils");
Framework::import("JsonFile", "core/json");
// Framework::import("DateTimeFormats", "core/date");

// Template::addHeader(array(
//     "tag" => "script",
//     "type" => "text/javascript",
//     "src" => "" . $application->getPathTemplate() . "/javascript/base64.js"
// ));

$utils = new Utils();

$filename = $application->getParameter("filename");
$folder = $application->getParameter("folder");
$task = $application->getParameter("task");
$command = $application->getParameter("command");

if ($folder != null) {
    if (substr($folder, strlen($folder) - 1) != "/") {
        $folder .= DIRECTORY_SEPARATOR;
    }
}

$dir = PATH_USER_WORKSPACE_STORAGE . $folder;


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

            if ($command == 'all') 
            {
                $script .= $element["script"] . "\n\n";
                
            } 
            else 
            {
                
                if(strpos($element["filename"], PATH_USER_WORKSPACE_STORAGE) !== false)
                {
                    //old
                
                }
                else
                {
                    //new
                    $element["filename"] = PATH_USER_WORKSPACE_STORAGE .  $element["filename"];
                    
                }
                
                
                $file_real = $element["filename"];
                
                if (file_exists($file_real)) 
                {
                    if ($command == "processed")
                    {
                        $script .= $element["script"] . "\n\n";
                    }
                } 
                else 
                {
                    if ($command == "unprocessed")
                    {
                        $script .= $element["script"] . "\n\n";
                    }
                }
                
                
                /*if ($command == "processed") { // echo $element["process"];
                    if ($element["process"] == true) {
                        $script .= $element["script"] . "\n\n";
                    }
                } else {
                    if ($command == "unprocessed") {
                        if ($element["process"] == false) {
                            $script .= $element["script"] . "\n\n";
                        }
                    }
                }*/
                
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



							<div class="page-header">
        						<h1>
        							<a href="<?php echo $_SERVER['REQUEST_URI']?>">Read Script</a>
        						</h1>
        					</div>
        					
        					
							

							<form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>"
								name="saveform" 
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

								
									<div
										style="float: left;  width: 100%; margin-top: 5px;">

										<input type="text" style="width: 100%" name="filenamenew"
											value="<?php echo $application->getParameter("filename");?>">
										<textarea id="data" style="width: 100%; height: 400px;"
											name="data"><?php echo $script?></textarea>
									</div>



							</form>


									<div style="float: right; padding-left: 10px">
									
										<input type='button' class="btn btn-default" onclick='toogle_editable("data", this);' value='Toggle to read only mode' />
										
										<input type="button"  class="btn btn-default"
								value="Download" name="download"
								onclick="javascript: downloadfile();" />
											
										<input type="button" class="btn btn-default" value="Close"
											onclick="javascript: window.close();">
									</div>
									
									
									


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
			id2.value = "Toggle to readonly mode";
		}
		else
		{
			id2.value = "Toggle to edit mode";
		}
		
		editAreaLoader.execCommand(id, 'set_editable', !editAreaLoader.execCommand(id, 'is_editable'));
	}

</script>

	
									
									
									
									