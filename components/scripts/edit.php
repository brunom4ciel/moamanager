<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\components\scripts;

defined('_EXEC') or die();

use moam\core\Framework;
use moam\core\Template;
use moam\core\Properties;
use moam\libraries\core\utils\Utils;
// use moam\libraries\core\menu\Menu;
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

$extension_scripts = ".data";

$filename = $application->getParameter("filename");

if (strrpos($filename, ".") > - 1) {

    if (in_array(substr($filename, strrpos($filename, ".") + 1), array(
        "data", "txt"
    ))) {

//         $filename = substr($filename, 0, strrpos($filename, "."));

//         $application->setParameter("filename", $filename);
    }else{
        exit("error");
    }
}
else 
{
    $filename .= ".data";
}



$filenamenew = $application->getParameter("filename");

if(!empty($filenamenew))
{
    if (strrpos($filenamenew, ".") > - 1) {
        
        if (in_array(substr($filenamenew, strrpos($filenamenew, ".") + 1), array(
            "data", "txt"
        ))) {
            
            //         $filename = substr($filename, 0, strrpos($filename, "."));
            
            //         $application->setParameter("filename", $filename);
        }else{
            exit("error");
        }
    }
    else
    {
        $filenamenew .= ".data";
    }
}

$folder = $application->getParameter("folder");
$dirScriptsName = "scripts";

$data = "";

if ($filename != null) {

    $utils = new Utils();

    $filename = Properties::getBase_directory_destine($application) . $application->getUser() 
    . DIRECTORY_SEPARATOR . $dirScriptsName . DIRECTORY_SEPARATOR . $folder . 
    // .DIRECTORY_SEPARATOR
    $filename;// . $extension_scripts;

    $task = $application->getParameter("task");

    if ($task == "save") {
        
        $data = $application->getParameter("data");
        if(!$utils->setContentFile($filename, $data))
        {
            $utils->set_perms($filename, "0777");
            if(!$utils->setContentFile($filename, $data))
            {
                exit("Error: modifying the file was not allowed.");
            }
        }
        
        

        $filenamenew = Properties::getBase_directory_destine($application) . $application->getUser() 
        . DIRECTORY_SEPARATOR . $dirScriptsName . DIRECTORY_SEPARATOR . $folder . 
        // .DIRECTORY_SEPARATOR
        $application->getParameter("filenamenew"); // /.$extension_scripts;

        if ($application->getParameter("filenamenew") != $application->getParameter("filename")) {

            if (file_exists($filename)) {

                if (file_exists($filenamenew)) {

                    while (file_exists($filenamenew)) {
                        $filenamenew = "copy-" . $filenamenew;
                    }
                }

                rename($filename, $filenamenew);

                $application->setParameter("filename", substr($filenamenew, strrpos($filenamenew, "/") + 1, strrpos($filenamenew, ".")));
            }
        }
    } else {

        if ($task == "remove") {

//             if (file_exists($filename)) {

//                 unlink($filename);
//                 header("Location: " . PATH_WWW . "?component=" . $application->getComponent() . "");
//             }
            
            
            $filename = basename($filename);
            
            $dir = PATH_USER_WORKSPACE_STORAGE . DIRNAME_SCRIPT . DIRECTORY_SEPARATOR . $application->getParameter("folder");

            $movedestine_ = PATH_USER_WORKSPACE_STORAGE . DIRNAME_TRASH . DIRECTORY_SEPARATOR;
            
            if (is_file($dir . $filename)) {
                
                $from_file = $dir . $filename;
                $to_file = $movedestine_ . $filename;
                
                if (file_exists($to_file))
                {
                    chmod($to_file, octdec("0777"));
                    
                    if(!unlink($to_file))
                    {
                        exit("Error: operation not allowed. File: " . $to_file);
                    }
                }
                
                rename($from_file, $to_file);
                
            }

            
            header("Location: " . PATH_WWW . "?component=" . $application->getComponent() . "&folder=" . $application->getParameter("folder"));
            
            
            
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

							<div class="page-header">
        						<h1>
        							<a href="<?php echo $_SERVER['REQUEST_URI']?>">Edit Script</a>
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
										style="float: left; padding-left: 5px; width: 100%; margin-top: 5px;">

										<input type="text" style="width: 100%" name="filenamenew"
											value="<?php echo $application->getParameter("filename");?>">
										<textarea id="data" style="width: 100%; height: 400px;"
											name="data"><?php echo $data?></textarea>
									</div>


									

									<div style="float: right; padding-left: 10px">
									
										
										<input type='button' class="btn btn-info" onclick='toogle_editable("data", this);' value='Toggle to read only mode' />
										
										<input type="button" class="btn btn-warning" value="Execute"
											onclick="javascript: window.location.href='?component=taskinitializer&controller=run&task=open&filename=<?php echo $application->getParameter("filename");?>&folder=<?php echo $application->getParameter("folder");?>';">
										
										<input type="submit" class="btn btn-success" value="Save"> <input type="submit"
											class="btn btn-danger" value="Delete"
											onclick="javascript: document.getElementById('task').value='remove'">
											
										<input type="button" class="btn btn-default badge" value="Return" name="return"
										onclick="javascript: returnPage();" />
									</div>

							</form>





<script type="text/javascript">

function returnPage()
{

	window.location.href='?component=<?php echo $application->getParameter("component");?>'
			+'&controller=controller'
			+'&task=open'
			+'&folder=<?php echo $application->getParameter("folder");?>';
			
}


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




