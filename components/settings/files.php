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

Framework::import("Utils", "core/utils");

$utils = new Utils();

$task = $application->getParameter("task");
$folder = $application->getParameter("folder");

if ($folder != null) {
    if (substr($folder, strlen($folder) - 1) != "/") {
        $folder .= DIRECTORY_SEPARATOR;
    }
}

if ($task == "open") {

    $files_list = $utils->getListElementsDirectory1($folder, 
        // .DIRECTORY_SEPARATOR
        array(
            "txt",
            "tex",
            "csv",
            "html",
            "report",
            "data"
        ));

    $dir_list = $utils->getListDirectory($folder);
}

?>


<script>

function ConfirmDelete()
{
  var x = confirm("Are you sure you want to delete?");
  if (x)
     return true;
  else
    return false;
}
function renameFile(obj){
	
	var newName = prompt("Please enter file name", obj.name);
	
	if (newName != null) {
		window.location.href="?component=<?php echo $application->getComponent();?>&controller=<?php echo $application->getController() ;?>&task=rename&folder=<?php echo $folder;?>&filenow="+obj.name+"&rename="+newName;
    	
	}

}
function renameFolder(obj){
	
	var newName = prompt("Please enter folder name", obj.name);
	
	if (newName != null) {
		window.location.href="?component=<?php echo $application->getComponent();?>&controller=<?php echo $application->getController() ;?>&task=rename&folder=<?php echo $folder;?>&foldernow="+obj.name+"&rename="+newName;
    	
	}

}
function newFolder(){
	
	var folder = prompt("Please enter older name", "New Folder");
	
	
	if (folder != null) {
    	window.location.href="?component=<?php echo $application->getComponent();?>&controller=<?php echo $application->getController() ;?>&folder=<?php echo $folder;?>&task=folder&foldernew="+folder;
    	
	}
	
}

function newFile(){
	
	var filename = prompt("Please enter file name", "New file");	
	
	if (filename != null) {
    	window.location.href="?component=<?php echo $application->getComponent();?>&controller=edit&task=new&filename="+filename+"&folder=<?php echo $folder ;?>";
    	
	}
	
}

function sendAction(task){

	if(task == 'remove'){

	  var x = confirm("Are you sure you want to delete?");
	  if (!x)
	     return;

	}

	if(task == 'move'){

	  var x = confirm("Are you sure you want to move?");
	  if (!x)
	     return;

	}

	if(task == 'renamelote'){

		  var x = confirm("Are you sure you want to rename?");
		  if (!x)
		     return;

	}
	
	
	document.getElementById('task').value = task;
	document.getElementById('formulario').submit();
	
}




function do_this2(){

    var checkboxes = document.getElementsByName('element[]');
    var button = document.getElementById('checkall');
    
    if(button.checked ==  true){
        for (var i in checkboxes){
            checkboxes[i].checked = 'FALSE';
        }
        //button.value = 'deselect'
    }else{
        for (var i in checkboxes){
            checkboxes[i].checked = '';
        }
       // button.value = 'select';
        button.checked == false;
    }
}

function do_this(){

    var checkboxes = document.getElementsByName('element[]');
    var button = document.getElementById('toggle');

    if(button.value == 'select'){
        for (var i in checkboxes){
            checkboxes[i].checked = 'FALSE';
        }
        button.value = 'deselect'
    }else{
        for (var i in checkboxes){
            checkboxes[i].checked = '';
        }
        button.value = 'select';
    }
}


</script>



							<div class="page-header">
        						<h1>
        							<a href="<?php echo $_SERVER['REQUEST_URI']?>">Files</a>
        						</h1>
        					</div>




							<form name="formulario" id="formulario" action="" method="POST"
								enctype="multipart/form-data">
								<input type="hidden"
									value="<?php echo $application->getComponent()?>"
									name="component" id="component"> <input type="hidden"
									value=<?php echo $application->getController()?>
									name="controller" id="controller"> <input type="hidden"
									name="folder" value="<?php echo $folder;?>" /> <input
									type="hidden" name="task" id="task" value="" />



								<div id="container">


									<br> <a
										href="<?php echo PATH_WWW ?>?component=<?php echo $application->getComponent()?>&controller=<?php echo $application->getController();?>">Root</a>

<?php

$levels = explode("/", $folder);

$fold = "";

foreach ($levels as $key => $item) {

    if (! empty($item)) {

        $fold .= $item . DIRECTORY_SEPARATOR;

        echo " > <a href=\"" . PATH_WWW . "?component=" . $application->getComponent() . "&controller=" . $application->getController() . "&folder=/" . $fold . "&task=open\">" . $item . "</a>";
    }
}

?>
		
		
	<table border='1' id="temporary_files" style="width: 100%;">
										<tr>
											<th>#</th>
											<th style="width: 60%;">Name</th>
											<th>Size</th>
											<th>DateTime</th>
										</tr>
<?php
$i = 0;
foreach ($files_list as $key => $element) {
    $i ++;

    if ($element["type"] == "dir") {

        echo "<tr><td>" . $i . "</td><td>" . 
        "<a href='?component=settings&controller=files&folder=" . (empty($folder) ? "" : $folder) . $element["name"] . "/&task=open'>" . "<img width='24px' align='middle' src='" . $application->getPathTemplate() . "/images/icon-folder.png' title='Open'/></a> " . 

        // ."<a title='Remove' onclick='javascript: return ConfirmDelete();' href='?component=home&controller=files&task=remove&folder=".$folder.$element["name"]."'>"
        // ."<img src='".App::getDirTmpl()."images/icon-remove.gif' border='0'></a>

        $element["name"] . "" . "</td><td>" . $element["size"] . "</td><td>" . $element["datetime"] . "</td></tr>";
    }
}

$i = 0;
foreach ($files_list as $key => $element) {

    $i ++;
    if ($element["type"] != "dir") {

        echo "<tr><td>" . $i . "</td><td>" . "<a onclick='javascript: renameFile(this);' name='" . $element["name"] . "' title='Rename' href='#'>" . "<img align='middle' width='24px' src='" . $application->getPathTemplate() . "/images/icon-rename.png' border='0'></a> " . "<a href='?component=" . $application->getComponent() . "&controller=openreadonly&filename=" . $element["name"] . "&folder=" . $application->getParameter("folder") . "'>" . "<img width='16px' align='middle' src='" . $application->getPathTemplate() . "/images/icon-view.png' title='View contents'/></a> " . "<label><input type='checkbox' name='element[]' value='" . $element["name"] . "' />" . $element["name"] . "</label> " . 

        // ."<a title='Move file' href='?component=moa&controller=run&filename=".$element["name"]."&folder=".$application->getParameter("folder")."'>"
        // ."<img align='middle' width='24px' src='".App::getDirTmpl()."/images/icon-play.png' border='0'></a> "
        // ."<a title='Remove' onclick='javascript: return ConfirmDelete();' href='?component=home&controller=files&task=remove&folder=".$folder."&filename=".$folder.$element["name"]."'>"
        // ."<img width='16px' src='".App::getDirTmpl()."images/icon-remove.gif' border='0'></a>"
        "</td><td>" . $element["size"] . "</td><td>" . $element["datetime"] . "</td></tr>";
    }
}

?>		
	</table>



									<br>
								<div style="float: right; padding-left: 10px">
									
									<input type="submit" class="btn btn-primary" name="Execute" value="Execute" />
						
        								<input type="button" class="btn btn-default"
            							onclick="javascript: window.location.href='?component=settings&controller=managerUsers';"
            							name="cancel" value="Cancel" />
        						</div>
							
							</form>
	

	
	
	
	
	
	
	
	
	
	
	
	
	
	