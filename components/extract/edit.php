<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\components\extract;

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

// Template::setDisabledMenu();

Template::addHeader(array("tag"=>"script",
    "type"=>"text/javascript",
    "src"=>""
    . $application->getPathTemplate()
    . "/javascript/edit_area/edit_area_full.js"));


Framework::import("Utils", "core/utils");

$extension_scripts = ".txt";

$filename = $application->getParameter("filename");

if (strrpos($filename, ".") > - 1) {

    if (in_array(substr($filename, strrpos($filename, ".") + 1), array(
        "txt"
    ))) {

//         $filename = substr($filename, 0, strrpos($filename, "."));

//         $application->setParameter("filename", $filename);
    }else{
        exit("error");
    }
}
else 
{
    $filename .= ".txt";
}

$folder = $application->getParameter("folder");

$data = "";

if ($filename != null) {

    $utils = new Utils();

    $filename = Properties::getBase_directory_destine($application) . $application->getUser() 
    . DIRECTORY_SEPARATOR . $folder . 
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
                $application->alert("Error: modifying the file was not allowed.");
            }
        }
        
        $redirect = array();
        
        $redirect['url'] = '?';
        $redirect['component'] = $application->getComponent();
        //$redirect['controller'] = $application->getController();
        $redirect['folder'] = $application->getParameter("folder");
        
        $application->redirect($redirect);
        
    } else {

        if ($task == "remove") {


        } else {

            // if(in_array(substr($filename,strrpos($filename, ".")+1),
            // array("txt","data") )){

            if ($task == "new") {

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
        							<a href="<?php echo $_SERVER['REQUEST_URI']?>">Edit File</a>
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

								<div style="float: left; padding-left: 20px; width: 100%">

									<div
										style="float: left; padding-left: 5px; width: 100%; margin-top: 5px;">

										Filename: <?php echo $application->getParameter("filename");?><br>
										<textarea id="data" style="width: 100%; height: 400px;"
											name="data"><?php echo $data?></textarea>
									</div>


									<div style="float: right; padding-left: 10px">
																			
										<input type='button' class="btn btn-info" onclick='toogle_editable("data", this);' value='Toggle to read only mode' />
																			
										<input type="submit" class="btn btn-success" value="Save"> 
										
										<input type="button" class="btn btn-default" value="Return" name="return"
										onclick="javascript: window.history.go(-1);" />
									</div>

								</div>

							</form>




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
			,toolbar: " undo, redo, |, select_font"
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



