<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\components\backup;

defined('_EXEC') or die();

use moam\core\Framework;
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

$utils = new Utils();

$error = array();

$task = $application->getParameter("task");
$folder = $application->getParameter("folder");

if ($folder != null) {
    if (substr($folder, strlen($folder) - 1) != "/") {
        $folder .= DIRECTORY_SEPARATOR;
    }
}

$dirBackup = Properties::getBase_directory_destine($application) . $application->getUser() . DIRECTORY_SEPARATOR . DIRNAME_BACKUP . DIRECTORY_SEPARATOR;

if (is_dir($dirBackup)) {} else {

    mkdir($dirBackup, 0777);
}

if ($task == "folder") {} else {

    if ($task == "rename") {} else {

        if ($task == "remove") {

            $element = $application->getParameter("element");

            $dir = Properties::getBase_directory_destine($application) . $application->getUser() . DIRECTORY_SEPARATOR . DIRNAME_BACKUP . DIRECTORY_SEPARATOR . $application->getParameter("folder");

            foreach ($element as $key => $item) {

                if (is_file($dir . $item)) {

                    $from_file = $dir . $item;
                    unlink($from_file);
                    // echo "file - from: ".$from_file."<br>";
                } else {

                    if (is_dir($dir . $item)) {

                        $from_dir = $dir . $item;
                        $utils->delTree($from_dir);
                        // echo "dir - from: ".$from_dir."<br>";
                    }
                }
            }

            $application->redirect(PATH_WWW . "?component=" . $application->getComponent() . "&controller=" . $application->getController() . "&folder=" . $application->getParameter("folder"));

            /*
             * $from_folder = App::$base_directory_destine
             * .$application->getUser()
             * .DIRECTORY_SEPARATOR
             * .$application->getParameter("folder");
             *
             * $filename = $application->getParameter("filename");
             *
             *
             *
             * if($filename == null ){
             *
             *
             * if(is_dir($from_folder)){
             *
             * //exit($from_folder);
             * $utils->delTree($from_folder);
             *
             * $countDirs = explode("/", $folder);
             *
             *
             * if(count($countDirs)>1){
             *
             * $folder="";
             *
             * for($i=0; $i<count($countDirs)-2;$i++){
             *
             * $folder .= $countDirs[$i]."/";
             * }
             *
             * //exit("sim - ".$folder);
             * header("Location: ".App::rootApp()
             * ."?component=".App::getComponent()
             * ."&controller=".App::getController()
             * ."&folder=".$folder);//substr($folder,0,strrpos($folder,"/")));
             * }else{
             * //exit("bruno2-".$folder);
             *
             * header("Location: ".App::rootApp()
             * ."?component=".App::getComponent()
             * ."&controller=".App::getController());
             *
             * }
             *
             *
             *
             * }
             *
             * }else{
             *
             * $filename = App::$base_directory_destine
             * .$application->getUser()
             * .DIRECTORY_SEPARATOR
             * .$filename;
             * //exit($filename);
             * if(is_file($filename)){
             *
             * unlink($filename);
             *
             * header("Location: ".App::rootApp()
             * ."?component=".App::getComponent()
             * ."&controller=".App::getController()
             * ."&folder=".$folder);
             *
             * }
             *
             * }
             */
        } else {

            if ($task == 'move') {

                $element = $application->getParameter("element");
                $movedestine = $application->getParameter("movedestine");

                $dir = Properties::getBase_directory_destine($application) . $application->getUser() . DIRECTORY_SEPARATOR . DIRNAME_BACKUP . DIRECTORY_SEPARATOR . $application->getParameter("folder");

                foreach ($element as $key => $item) {

                    if ($movedestine != $item) {

                        if ($movedestine == "/") {

                            // $movedestine_ = substr($dir,0,strrpos($dir,"/"));
                            // $movedestine_ = substr($movedestine_,0,strrpos($movedestine_,"/")+1);

                            $movedestine_ = Properties::getBase_directory_destine($application) . $application->getUser() . DIRECTORY_SEPARATOR;
                        } else {

                            $movedestine_ = Properties::getBase_directory_destine($application) . $application->getUser() . DIRECTORY_SEPARATOR . $application->getParameter("folder") . $movedestine . DIRECTORY_SEPARATOR;
                        }

                        if (is_file($dir . $item)) {

                            // chmod($dir, 0777);

                            $from_file = $dir . $item;
                            $to_file = $movedestine_ . $item;

                            if (! file_exists($to_file))
                                rename($from_file, $to_file);

                            // echo "file - from: ".$from_file.", to: ".$to_file."<br>";
                        } else {

                            if (is_dir($dir . $item)) {

                                // chmod($dir, 0777);

                                $from_dir = $dir . $item;
                                $to_dir = $movedestine_ . $item;

                                if (! is_dir($to_dir))
                                    rename($from_dir, $to_dir);

                                // echo "dir - from: ".$from_dir.", to: ".$to_dir."<br>";
                            }
                        }
                    }
                    // echo $item."<br>";
                }

                // exit("<br>bruno - move");
            } else {}
        }
    }
}

if ($folder == null) {

    $files_list = $utils->getListElementsDirectory1(Properties::getBase_directory_destine($application) . $application->getUser() . DIRECTORY_SEPARATOR . DIRNAME_BACKUP . DIRECTORY_SEPARATOR, array(
        "txt",
        "tex",
        "csv",
        "html",
        "report",
        "zip"
    ));
} else {

    $files_list = $utils->getListElementsDirectory1(Properties::getBase_directory_destine($application) . $application->getUser() . DIRECTORY_SEPARATOR . DIRNAME_BACKUP . DIRECTORY_SEPARATOR . $folder, 
        // .DIRECTORY_SEPARATOR
        array(
            "txt",
            "tex",
            "csv",
            "html",
            "report",
            "zip"
        ));
}

$dir_list = $utils->getListDirectory(Properties::getBase_directory_destine($application) . $application->getUser() . DIRECTORY_SEPARATOR);

foreach ($dir_list as $key => $element) {

    if ( trim($element) == DIRNAME_TRASH || trim($element) == DIRNAME_BACKUP) { //trim($element) == DIRNAME_SCRIPT ||

        unset($dir_list[$key]);
    }
}

$dir_list[] = "/";

?>



<script>











function parseBool2( str ){

    var boolmap = { 
        'no'    : false ,
        'NO'    : false ,
        'FALSE' : false ,
        'false' : false,
        'yes'   : true ,
        'YES'   : true ,
        'TRUE'  : true ,
        'true'  : true 
    };

    return ( str in boolmap && boolmap.hasOwnProperty(str)) ? 
      boolmap[ str ] :  !!str ;
};


function setCookieCheckbox(element){




	var chk_arr =  document.getElementsByName(element.name);
	var chklength = chk_arr.length;             

	for(k=0;k< chklength;k++)
	{
		var checkedbox = chk_arr[k].checked;
		
		if(checkedbox)
			checkedbox=true;
		else
			checkedbox=false;
		
		setCookie(element.name+"["+k+"]",checkedbox,365);

	} 


/*
	
		
	var values = [];
	  var vehicles = document.form_data.streams[];//document.getElementsByTagName("streams[]");//form.vehicle;

	  alert(vehicles.length);

	  
	  for (var i=0; i<vehicles.length; i++) {
	    if (vehicles[i].checked) {

		    alert("sim");
	    //  values.push(vehicles[i].value);
	    }
	  }*/

	  

}



function historicCookieCheckbox(elementId){


	var chk_arr =  document.getElementsByName(elementId);
	var chklength = chk_arr.length;             

	for(k=0;k< chklength;k++)
	{
		var elementCookieHistoric = getCookie(elementId+"["+k+"]");

		
		if(elementCookieHistoric==""){
			var elementCookieChecked=false;
		}else
			var elementCookieChecked=elementCookieHistoric;
		
		//alert(elementId+"="+elementCookieChecked);
		//alert(elementId+"="+checkedbox+", elementCookieChecked=");//+elementCookieChecked);
		

		if(typeof(elementCookieChecked) === "boolean")
			chk_arr[k].checked = elementCookieChecked;
		else
			if(elementCookieChecked === null)
				alert(elementId);
			else
				chk_arr[k].checked = parseBool2(elementCookieChecked);

	} 

	

}

















function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+d.toGMTString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i].trim();
        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
    }
    return "";
}







function sendAction(task){

	if(task == 'remove'){

	  var x = confirm("Are you sure you want to delete?");
	  if (!x)
	     return;

	}

	if(task == 'move'){

	  var x = confirm("Are you sure you want to restore?");
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





							<form name="formulario" id="formulario" action="" method="POST"
								enctype="multipart/form-data">
								<input type="hidden"
									value="<?php echo $application->getComponent()?>"
									name="component" id="component"> <input type="hidden"
									value=<?php echo $application->getController()?>
									name="controller" id="controller"> <input type="hidden"
									name="folder" value="<?php echo $folder;?>" /> <input
									type="hidden" name="task" id="task" value="" /> <input
									type="hidden" name="filename" id="filename" value="" /> <input
									type="hidden" name="overwrite" id="overwrite" value="" />

								<div id="container">
    
    
    <?php

    if (count($error) > 0) {

        for ($i = 0; $i < count($error); $i ++) {
            echo $error[$i] . "<br>";
        }
    }

    ?>
    
     <div style="float: left;width:100%; padding-top: 10px">
     
<input type="button" class="btn btn-danger" value="Empty" name="empty"
										onclick="javascript: sendAction('remove');" />
</div>

<div style="float:left;width:100%;border:0px solid #000;padding:5px;">
											<div style="float: right;">
											Restore to:
									<select name="movedestine" class="btn btn-default" id=movedestine>		
		<?php

foreach ($dir_list as $key => $element) {

    // if($element["type"]=="dir"){

    echo "<option value=\"" . $element . "\">" . $element . "</option>";
    // }
}

?>
													
												</select> <input type="button" class="btn btn-warning" value="Restore" name="move"
										id="move" onclick="javascript: sendAction('move');" /> 
</div>
										
										<div style="float:left; vertical-align: middle;padding-top:10px;">
										<a
										href="<?php echo PATH_WWW ?>?component=<?php echo $application->getComponent()?>&controller=<?php echo $application->getController();?>">Root</a>

<?php

$levels = explode("/", $folder);

$fold = "";

foreach ($levels as $key => $item) {

    if (! empty($item)) {

        $fold .= $item . DIRECTORY_SEPARATOR;

        echo " > <a href=\"" . PATH_WWW . "?component=" . $application->getComponent() . "&controller=" . $application->getController() . "&folder=" . $fold . "\">" . $item . "</a>";
    }
}

?>
						</div>
		</div>
		
			
		
	<table border='1' id="temporary_files" style="width: 100%;">
										<tr>
											<th>#</th>
											<th style="width: 60%;"><label><input type="checkbox"
													id="checkall" onClick="do_this2()" value="select" />Name</label></th>
											<th>Size</th>
											<th>DateTime</th>
										</tr>
<?php
$i = 0;
foreach ($files_list as $key => $element) {
    $i ++;

    if ($element["type"] == "dir") {

        echo "<tr><td>" . $i . "</td><td>" . 
        "<a href='?component=" . $application->getComponent() . "&folder=" . (empty($folder) ? "" : $folder) . $element["name"] . "/&task=open'>" . "<img width='24px' align='middle' src='" . $application->getPathTemplate() . "/images/icon-folder.png' title='Open'/></a> " . 

        // ."<a title='Remove' onclick='javascript: return ConfirmDelete();' href='?component=home&controller=files&task=remove&folder=".$folder.$element["name"]."'>"
        // ."<img src='".$application->getPathTemplate()."images/icon-remove.gif' border='0'></a>

        "<label><input type='checkbox' name='element[]' value='" . $element["name"] . "' />" . $element["name"] . "</label> " . "</td><td>" . $element["size"] . "</td><td>" . $element["datetime"] . "</td></tr>";
    }
}

$i = 0;
foreach ($files_list as $key => $element) {

    $i ++;
    if ($element["type"] != "dir") {

        echo "<tr><td>" . $i . "</td><td>" . 
        "<label><input type='checkbox' name='element[]' value='" . $element["name"] . "' />" . $element["name"] . "</label> " . 

        // ."<a title='Move file' href='?component=moa&controller=run&filename=".$element["name"]."&folder=".$application->getParameter("folder")."'>"
        // ."<img align='middle' width='24px' src='".$application->getPathTemplate()."/images/icon-play.png' border='0'></a> "
        // ."<a title='Remove' onclick='javascript: return ConfirmDelete();' href='?component=home&controller=files&task=remove&folder=".$folder."&filename=".$folder.$element["name"]."'>"
        // ."<img width='16px' src='".$application->getPathTemplate()."images/icon-remove.gif' border='0'></a>"
        "</td><td>" . $element["size"] . "</td><td>" . $element["datetime"] . "</td></tr>";
    }
}

?>		
	</table>
									</div>
							</form>






							<script>



historicCookieCheckbox("overwrite_file");


</script>	
	
	
	