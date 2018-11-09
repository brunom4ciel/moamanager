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
use moam\core\Template;

if (! class_exists('Application')) {
    $application = Framework::getApplication();
}

if (! $application->is_authentication() || $application->getUserType() != 1) {
    $application->alert("Error: you do not have credentials.");
}

Template::setDisabledMenu();

Template::addHeader(array("tag"=>"script",
    "type"=>"text/javascript",
    "src"=>""
    . $application->getPathTemplate()
    . "/javascript/edit_area/edit_area_full.js"));



Framework::import("Utils", "core/utils");

$utils = new Utils();

$filename = PATH_CORE . DIRECTORY_SEPARATOR . "properties.php";

if (isset($_POST['data'])) {

    $data = $application->getParameter("data");

    $utils->setContentFile($filename, $data);
} else {

    $data = $utils->getContentFile($filename);
}

?>


							<div class="page-header">
        						<h1>
        							<a href="<?php echo $_SERVER['REQUEST_URI']?>">Edit properties.php</a>
        						</h1>
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

						<div style="float: right; padding-left: 10px">
									
									<input type='button' class="btn btn-info" onclick='toogle_editable("data", this);' value='Toggle to edit mode' />
									
							<input type="submit" class="btn btn-success" name="save" value="Save" /> 
						
								<input type="button" class="btn btn-default"
    							onclick="javascript: window.location.href='?component=settings';"
    							name="cancel" value="Return" />
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
			,syntax: "php"	
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
					
					
					
					
					
					
					