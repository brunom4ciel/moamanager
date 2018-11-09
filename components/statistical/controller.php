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
use moam\core\Application;
use moam\core\Properties;
use moam\core\Template;
use moam\libraries\core\utils\Utils;
use moam\libraries\core\menu\Menu;
use moam\libraries\core\mining\Mining;
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
Framework::import("Mining", "core/mining");

Template::addHeader(array(
    "tag" => "link",
    "type" => "text/css",
    "href" => "" . $application->getPathTemplate() . "/csc/table-excel.css"
));

Template::addHeader(array(
    "tag" => "script",
    "type" => "text/javascript",
    "src" => "" . $application->getPathTemplate() . "/javascript/base64.js"
));

$utils = new Utils();

$csv = "";

$exterions = array(
    "tex",
    "html"
);

// var_dump($_POST);exit();

$task = $application->getParameter("task");
$folder = $application->getParameter("folder");

if ($folder != null) {
    if (substr($folder, strlen($folder) - 1) != "/") {
        $folder .= DIRECTORY_SEPARATOR;
    }
}

if ($task == "folder") {} else {}

if ($folder == null) {

    $files_list = $utils->getListElementsDirectory1(Properties::getBase_directory_destine($application) . $application->getUser() . DIRECTORY_SEPARATOR, array(
        "txt",
        "csv",
        "html",
        "tex",
        "report"
    ));
} else {

    // $to_folder =// Properties::getBase_directory_destine($application)
    // .$application->getUser()
    // .DIRECTORY_SEPARATOR
    // $application->getParameter("folder")
    // .DIRECTORY_SEPARATOR
    // .$application->getParameter("rename");

    // exit("ddd - ".$folder);
    $files_list = $utils->getListElementsDirectory1(Properties::getBase_directory_destine($application) . $application->getUser() . DIRECTORY_SEPARATOR . $folder, 
        // .DIRECTORY_SEPARATOR
        array(
            "txt",
            "csv",
            "html",
            "tex",
            "report"
        ));
}

foreach ($files_list as $key => $element) {

    if ($element["type"] == "dir") {
        if ($element["name"] == DIRNAME_SCRIPT || $element["name"] == DIRNAME_TRASH || $element["name"] == DIRNAME_BACKUP) {
            unset($files_list[$key]);
        }
    } else {

        /*
         * echo substr($element["name"],strrpos($element["name"],".")+1);
         * if(substr($element["name"],strrpos($element["name"],".")+1)=="log"){exit("bruno");
         * unset($files_list[$key]);
         * }
         */
    }
}

/*
 * $dir_list = $utils->getListDirectory(
 * Properties::getBase_directory_destine($application)
 * .$application->getUser()
 * .DIRECTORY_SEPARATOR
 * .$folder);
 */

?>


<script>


function SetSelectIndex(idElement, elementText)
{
    var elementObj = document.getElementById(idElement);
//alert("id"+elementObj.id);

    for(i = 0; i < elementObj.length; i++)
    {
      // check the current option's text if it's the same with the input box
      if (elementObj.options[i].innerHTML == elementText)
      {
         elementObj.selectedIndex = i;
         break;
      }     
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






function setCookieElementSelectValue(element){
	
//	alert(" "+element.name);
	//alert("==="+element.options[element.selectedIndex].innerHTML);
	setCookie(element.id,element.options[element.selectedIndex].innerHTML,365);
}


function setCookieElementValue(element){
	
	setCookie(element.id,Base64.encode(element.value),365);
}




function historicCookieElementSelectValue(elementId, defaultValue){

	var elementCookieHistoric = getCookie(elementId);
	
	if(elementCookieHistoric==""){
		elementCookieValue=defaultValue;//=defaultValue;"";
	}else
		elementCookieValue=elementCookieHistoric;
	
	//document.getElementById(elementId).value = elementCookieValue;
	SetSelectIndex(elementId, elementCookieValue);
	
	
}


function historicCookieElementValue(elementId, defaultValue){

	var elementCookieHistoric = getCookie(elementId);
	
	if(elementCookieHistoric=="")//{
		elementCookieValue=defaultValue;//=defaultValue;"";
	//}else
	//	elementCookieValue=elementCookieHistoric;
	
	document.getElementById(elementId).value = Base64.decode(elementCookieHistoric);
}





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

	var checkedbox = element.checked;
	
	if(checkedbox)
		checkedbox=true;
	else
		checkedbox=false;
	
	setCookie(element.id,checkedbox,365);
}



function historicCookieCheckbox(elementId){

	var elementCookieHistoric = getCookie(elementId);
	
	if(elementCookieHistoric==""){
		var elementCookieChecked=false;
	}else
		var elementCookieChecked=elementCookieHistoric;
	
	//alert(elementId+"="+elementCookieChecked);
	//alert(elementId+"="+checkedbox+", elementCookieChecked=");//+elementCookieChecked);
	

	if(typeof(elementCookieChecked) === "boolean")
		document.getElementById(elementId).checked = elementCookieChecked;
	else
		if(elementCookieChecked === null)
			alert(elementId);
		else
			document.getElementById(elementId).checked = parseBool2(elementCookieChecked);
	

}








function setCookieRadioBox(element){

	var value = getRadioValue(element.id);

	setCookie(element.id,value,365);
}

function getRadioValue(groupName) {
    var _result;
    try {
        var o_radio_group = document.getElementsByName(groupName);
        for (var a = 0; a < o_radio_group.length; a++) {
            if (o_radio_group[a].checked) {
                _result = o_radio_group[a].value;
                break;
            }
        }
    } catch (e) { }
    return _result;
}


function historicCookieRadiobox(elementId){

	var elementCookieHistoric = getCookie(elementId);
	
	if(elementCookieHistoric==""){
		var elementCookieChecked=0;
	}else
		var elementCookieChecked=elementCookieHistoric;
	
	//alert(elementId+"="+elementCookieChecked);
	//alert(elementId+"="+checkedbox+", elementCookieChecked=");//+elementCookieChecked);
	

	var o_radio_group = document.getElementsByName(elementId);//document.getElementById(elementId);//document.getElementsByName(groupName);
	
	for (var a = 0; a < o_radio_group.length; a++) {

		//alert(o_radio_group[a].value +'=='+ elementCookieChecked);
		
            if (o_radio_group[a].value == elementCookieChecked) {
               // _result = o_radio_group[a].value;
                o_radio_group[a].checked = true;
                break;
            }
	}
        
//	alert('historico='+elementCookieChecked);
	
	//document.getElementById(elementId).checked = elementCookieChecked;
	

}


























function sendAction(task){	
	
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

function verificaChecks() {
	
	var aChk = document.getElementsByName("element[]");  
	var nenhum = false;
	
	for (var i=0;i<aChk.length;i++){  
		if (aChk[i].checked == true){  
			// CheckBox Marcado... Faça alguma coisa... Ex:
			//alert(aChk[i].value + " marcado.");
			nenhum = true;
			break;
		//}  else {
			// CheckBox Não Marcado... Faça alguma outra coisa...
		}
	}

	if(nenhum == false)
		alert('You need to select a directory or file.');
		
	return nenhum;
	
	
} 

</script>


<div class="content content-alt">
	<div class="container" style="width: 90%">
		<div class="row">
			<div class="">

				<div class="card" style="width: 100%">
					<div class="page-header">
						<h1>
							<a href="<?php echo $_SERVER['REQUEST_URI']?>"><?php echo TITLE_COMPONENT?></a>
						</h1>
					</div>

					<div style="width: 100%; padding-bottom: 15px; display: table">



						<div
							style="float: left; width: 18%; border: 1px solid #fff; display: table-cell">
																
									<?php echo $application->showMenu($menu);?>								

								</div>

						<div
							style="float: left; width: 80%; border: 1px solid #fff; display: table-cell">







							<form name="formulario" id="formulario" action="" method="POST"
								target="_blank" enctype="multipart/form-data">
								<input type="hidden"
									value="<?php echo $application->getComponent()?>"
									name="component" id="component"> <input type="hidden"
									value="extract_tmpl" name="controller" id="controller"> <input
									type="hidden" name="folder" value="<?php echo $folder;?>" /> <input
									type="hidden" name="task" id="task" value="" />



								<div id="container">

									<a
										href="?component=<?php echo $application->getComponent()?>&controller=texteditor">Test
										online</a><br>



									<div class="displayfix">




										<!-- <div class="boxlimit" style="margin-top:5px;width:100%">
		Process<br>
			<label><input type="radio" value="0" name="process_type" id="process_type"  onclick="setCookieRadioBox(this);"/>Single Folder</label>
			<label><input type="radio" value="1" name="process_type" id="process_type" onclick="setCookieRadioBox(this);"/>Merge Folders</label>
			
		</div> -->




										<div
											style="float: right; width: 100%; text-align: right; padding-top: 5px;">
											<input type="button" value="Execute" name="extract"
												onclick="javascript: if(verificaChecks()==true){ sendAction('extract');}" />

										</div>

									</div>

									<br> <a
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

        // ."<a onclick='javascript: renameFolder(this);' name='".$element["name"]."' title='Rename' href='#'>"
        // ."<img align='middle' width='24px' src='".App::getDirTmpl()."images/icon-rename.png' border='0'></a> "

        "<a href='?component=" . $application->getComponent() . "&controller=" . $application->getController() . "&folder=" . (empty($folder) ? "" : $folder) . $element["name"] . "/&task=open'>" . "<img width='24px' align='middle' src='" . $application->getPathTemplate() . "/images/icon-folder.png' title='Open'/></a> " . 

        // ."<a title='Remove' onclick='javascript: return ConfirmDelete();' href='?component=home&controller=files&task=remove&folder=".$folder.$element["name"]."'>"
        // ."<img src='".App::getDirTmpl()."images/icon-remove.gif' border='0'></a>

        "<label>" . $element["name"] . "</label> " . "</td><td>" . $element["size"] . "</td><td>" . $element["datetime"] . "</td></tr>";
    }
}

$i = 0;
foreach ($files_list as $key => $element) {

    $i ++;
    if ($element["type"] != "dir") {

        $extension_file = $element["name"];
        $extension_file = substr($extension_file, strrpos($extension_file, ".") + 1);

        echo "<tr><td>" . $i . "</td><td>" . "<a target='_blank' href='?component=" . $application->getComponent() . "&task=view&type_extract=2&controller=extract_tmpl&filename=" . $element["name"] . "&folder=" . $application->getParameter("folder") . "'>" . "<img width='16px' align='middle' src='" . $application->getPathTemplate() . "/images/icon-view.png' title='View Content'/></a> ";

        // if(in_array($extension_file, array("csv", "html"))){

        // echo "<a target='_blank' href='?component=".$application->getComponent()."&task=preview&type_extract=1&controller=extract_tmpl&filename=".$element["name"]."&folder=".$application->getParameter("folder")."'>"
        // ."<img width='16px' align='middle' src='".$application->getPathTemplate()."/images/icon-table.png' title='Preview'/></a> ";

        // }

        echo "<a href='?component=" . $application->getComponent() . "&task=download&type_extract=1&controller=extract_tmpl&filename=" . $element["name"] . "&folder=" . $application->getParameter("folder") . "'>" . "<img width='16px' align='middle' src='" . $application->getPathTemplate() . "/images/icon_download.png' title='Download'/></a> ";

        if (in_array($extension_file, $exterions)) {

            echo "<label>" . $element["name"] . "</label> ";
        } else {

            echo "<label><input type='checkbox' name='element[]' value='" . $element["name"] . "' />" . $element["name"] . "</label> ";
        }

        // ."<a title='Move file' href='?component=moa&controller=run&filename=".$element["name"]."&folder=".$application->getParameter("folder")."'>"
        // ."<img align='middle' width='24px' src='".App::getDirTmpl()."/images/icon-play.png' border='0'></a> "
        // ."<a title='Remove' onclick='javascript: return ConfirmDelete();' href='?component=home&controller=files&task=remove&folder=".$folder."&filename=".$folder.$element["name"]."'>"
        // ."<img width='16px' src='".App::getDirTmpl()."images/icon-remove.gif' border='0'></a>"
        echo "</td><td>" . $element["size"] . "</td><td>" . $element["datetime"] . "</td></tr>";
    }
}

?>		
	</table>
							
							</form>
	
	

	
									<?php 
																	
									/*	for($i=0; $i<count($files_list); $i++){
										
											echo "<span style='margin-left:65px;' data-reactid=\".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1.0\">".$files_list[$i]."</span><br>\n";
										
										}*/
										
									?>
								
								</div>

					</div>

				</div>
			</div>
		</div>
	</div>
</div>


<script>






</script>
