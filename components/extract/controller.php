<?php
/**
 * @package    MOAM.Application
*
* @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/


namespace moam\components\extract;

defined('_EXEC') or die;

use moam\core\Framework;
// use moam\core\Application;
use moam\core\Properties;
use moam\core\Template;
use moam\libraries\core\utils\Utils;
// use moam\libraries\core\menu\Menu;
// use moam\libraries\core\mining\Mining;


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

Framework::import("Utils", "core/utils");
Framework::import("Mining", "core/mining");


Template::addHeader(array("tag"=>"link",
    "type"=>"text/css",
    "href"=>""
    . $application->getPathTemplate()
    . "/csc/table-excel.css"));

Template::addHeader(array("tag"=>"script",
    "type"=>"text/javascript",
    "src"=>""
    . $application->getPathTemplate()
    . "/javascript/base64.js"));


$utils = new Utils();


$csv = "";

$exterions = array("tex", "csv", "html", "txt");

//var_dump($_POST);exit();

$task = $application->getParameter("task");
$folder = $application->getParameter("folder");

if($folder != null){
    if(substr($folder, strlen($folder)-1)!="/"){
        $folder .= DIRECTORY_SEPARATOR;
    }
}




if($task == "folder"){
    
    
    
}else{
    
    
    
    
    
    
}

if($folder == null){
    
    $files_list = $utils->getListElementsDirectory1(
        Properties::getBase_directory_destine($application)
        .$application->getUser()
        .DIRECTORY_SEPARATOR, $exterions);
    
}else{
    
    
    //$to_folder =// Properties::getBase_directory_destine($application)
    //.$application->getUser()
    //.DIRECTORY_SEPARATOR
    //$application->getParameter("folder")
    //.DIRECTORY_SEPARATOR
    //.$application->getParameter("rename");
    
    //exit("ddd - ".$folder);
    $files_list = $utils->getListElementsDirectory1(
        Properties::getBase_directory_destine($application)
        .$application->getUser()
        .DIRECTORY_SEPARATOR
        .$folder
        //.DIRECTORY_SEPARATOR
        , $exterions);
}


foreach($files_list as $key=>$element){
    
    if($element["type"]=="dir"){
        if($element["name"]== DIRNAME_SCRIPT
            || $element["name"] == DIRNAME_TRASH
            || $element["name"] == DIRNAME_BACKUP){
                unset($files_list[$key]);
        }
    }else{
        
        /*echo substr($element["name"],strrpos($element["name"],".")+1);
         if(substr($element["name"],strrpos($element["name"],".")+1)=="log"){exit("bruno");
         unset($files_list[$key]);
         }*/
    }
}


/*$dir_list = $utils->getListDirectory(
 Properties::getBase_directory_destine($application)
 .$application->getUser()
 .DIRECTORY_SEPARATOR
 .$folder);*/


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
		if(typeof elementCookieChecked === 'undefined')//elementCookieChecked == null)
			alert(elementId);
		else{
			document.getElementById(elementId).checked = parseBool2(elementCookieChecked);
			
			//alert("element["+elementId+"]="+elementCookieChecked);//+elementCookieChecked);
	}

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
	
	

			
									

<form name="formulario" id="formulario" action="" method="POST" enctype="multipart/form-data">
		<input type="hidden" value="<?php echo $application->getComponent()?>" name="component" id="component">
		<input type="hidden" value="extract_tmpl" name="controller" id="controller">	
		<input type="hidden" name="folder" value="<?php echo $folder;?>"/>
		<input type="hidden" name="task" id="task" value=""/>		
		
		
 
    <div class="displayfix">

		
		<div class="boxlimit">
		<label><input type="radio" value="0" name="type_extract" id="type_extract" checked="checked" onclick="setCookieRadioBox(this);"/>Average of averages</label>
		<label><input type="radio" value="1" name="type_extract" id="type_extract" onclick="setCookieRadioBox(this);"/>All means</label>
		<label><input type="radio" value="2" name="type_extract" id="type_extract" onclick="setCookieRadioBox(this);"/>Scripts</label>
		
		</div>
		
		<br>
		<div class="boxlimit" style="margin-top:5px;width:100%">
					
			<label><input type="checkbox" name="accuracy" id="accuracy" value="1" onclick="setCookieCheckbox(this);"/>Accuracy</label>		
			<label><input type="checkbox" name="timer" id="timer" value="1" onclick="setCookieCheckbox(this);"/>Timer</label>
			<label><input type="checkbox" name="memory" id="memory" value="1" onclick="setCookieCheckbox(this);"/>Memory</label>
			

			<label><input type="checkbox" name="dist" id="dist" value="1" onclick="setCookieCheckbox(this);"/>dist</label>
			<label><input type="checkbox" name="fn" id="fn" value="1" onclick="setCookieCheckbox(this);"/>fn</label>
			<label><input type="checkbox" name="fp" id="fp" value="1" onclick="setCookieCheckbox(this);"/>fp</label>
			<label><input type="checkbox" name="tn" id="tn" value="1" onclick="setCookieCheckbox(this);"/>tn</label>
			<label><input type="checkbox" name="tp" id="tp" value="1" onclick="setCookieCheckbox(this);"/>tp</label>
			<label><input type="checkbox" name="precision" id="precision" value="1" onclick="setCookieCheckbox(this);"/>precision</label>
			<label><input type="checkbox" name="recall" id="recall" value="1" onclick="setCookieCheckbox(this);"/>recall</label>
			
			<label><input type="checkbox" name="mcc" id="mcc" value="1" onclick="setCookieCheckbox(this);"/>MCC</label>
			<label><input type="checkbox" name="f1" id="f1" value="1" onclick="setCookieCheckbox(this);"/>F1</label>
			<label><input type="checkbox" name="resume" id="resume" value="1" onclick="setCookieCheckbox(this);"/>TP+FN+Others</label>
			
			<div class="line"></div>
			
			<label><input type="checkbox" name="interval" id="interval" value="1" onclick="setCookieCheckbox(this);"/>Confidence Interval</label>
					
			
		</div>
		
		<div class="boxlimit" style="margin-top:5px;width:100%">
		
				
		Breaking line(s) <input type="text" name="breakline" id="breakline" value="7" style="width:40px;" onchange="setCookieElementValue(this);" /> <br>Decimal separator<label><input type="text" name="decimalformat" id="decimalformat" value="." style="width:40px;" onchange="setCookieElementValue(this);" /></label>
			
			
			
		</div>
		<div class="boxlimit" style="margin-top:5px;width:100%">
		Result Data<br>	
			
			<label><input type="radio" name="viewdata" id="viewdata" value="html" checked="checked" onclick="setCookieRadioBox(this);"/>HTML</label>
			<label><input type="radio" name="viewdata" id="viewdata" value="txt" onclick="setCookieRadioBox(this);"/>TXT</label>
			<label><input type="radio" name="viewdata" id="viewdata" value="tex" onclick="setCookieRadioBox(this);"/>LaTeX</label>
			
		</div>
		
		<div class="boxlimit" style="margin-top:5px;width:100%">
		Export File<br>	
			<label><input type="checkbox" name="tex" id="tex" value="1" onclick="setCookieCheckbox(this);"/>LaTeX</label>
			<label><input type="checkbox" name="csv" id="csv" value="1" onclick="setCookieCheckbox(this);"/>CSV</label>
			<label><input type="checkbox" name="html" id="html" value="1" onclick="setCookieCheckbox(this);"/>HTML</label>
			
			<label><input type="checkbox" name="save" id="save" value="1" onclick="setCookieCheckbox(this);"/>Save in file</label>
			<label><input type="checkbox" name="overwrite" id="overwrite" value="1" onclick="setCookieCheckbox(this);"/>Overwrite existing file</label>
			
		</div>
		
		<div class="boxlimit" style="margin-top:5px;width:100%">
		Statistical Test<br>	
			
			<label><input type="radio" name="statisticaltest" id="statisticaltest" value="no" checked="checked" onclick="setCookieRadioBox(this);"/>No</label>
			<label><input type="radio" name="statisticaltest" id="statisticaltest" value="Shaffer" onclick="setCookieRadioBox(this);"/>Shaffer</label>
			<label><input type="radio" name="statisticaltest" id="statisticaltest" value="Nemenyi" onclick="setCookieRadioBox(this);"/>Nemenyi</label>
			<label><input type="radio" name="statisticaltest" id="statisticaltest" value="Holm" onclick="setCookieRadioBox(this);"/>Holm</label>
			<label><input type="radio" name="statisticaltest" id="statisticaltest" value="Bonferroni-Dunn" onclick="setCookieRadioBox(this);"/>Bonferroni-Dunn</label>
			<label><input type="radio" name="statisticaltest" id="statisticaltest" value="Bergmann-Hommel" onclick="setCookieRadioBox(this);"/>Bergmann-Hommel</label>
		</div>
		
		<!-- <div class="boxlimit" style="margin-top:5px;width:100%">
		Process<br>
			<label><input type="radio" value="0" name="process_type" id="process_type"  onclick="setCookieRadioBox(this);"/>Single Folder</label>
			<label><input type="radio" value="1" name="process_type" id="process_type" onclick="setCookieRadioBox(this);"/>Merge Folders</label>
			
		</div> -->
		
		

	
		<div style="float:right;width:100%;text-align:right;padding-top:5px;">
			<input type="button" class="btn btn-success" value="Execute" name="extract" onclick="javascript: if(verificaChecks()==true){ sendAction('extract');}" />
			
		</div>
		
	</div>

<br>
<a href="<?php echo PATH_WWW ?>?component=<?php echo $application->getComponent()?>&controller=<?php echo $application->getController();?>">Root</a>

<?php 

	$levels = explode("/", $folder);
	
	$fold = "";
	
	foreach($levels as $key=>$item){
		
		if(!empty($item)){
			
			$fold .= $item.DIRECTORY_SEPARATOR;
			
			echo " > <a href=\"".PATH_WWW."?component="
					.$application->getComponent()."&controller="
					.$application->getController()."&folder=".$fold."\">".$item."</a>";
			
		}
		
		
	}
	
?>
		
		
	<table border='1' id="temporary_files" style="width:100%;">
		<tr><th>#</th><th style="width:60%;"><label><input type="checkbox" id="checkall" onClick="do_this2()" value="select"/>Name</label></th><th>Size</th><th>DateTime</th></tr>
<?php
	$i=0;
	foreach($files_list as $key=>$element){
		$i++;
		
		if($element["type"]=="dir"){

			echo "<tr><td>".$i."</td><td>"	
			
			//	."<a onclick='javascript: renameFolder(this);' name='".$element["name"]."' title='Rename' href='#'>"
			//."<img align='middle' width='24px' src='".App::getDirTmpl()."images/icon-rename.png' border='0'></a> "
			
			."<a href='?component=".$application->getComponent()."&controller=".$application->getController()."&folder=".(empty($folder)?"":$folder).$element["name"]."/&task=open'>"
			."<img width='24px' align='middle' src='".$application->getPathTemplate()."/images/icon-folder.png' title='Open'/></a> "
			
			//."<a title='Remove' onclick='javascript: return ConfirmDelete();' href='?component=home&controller=files&task=remove&folder=".$folder.$element["name"]."'>"
			//	."<img src='".App::getDirTmpl()."images/icon-remove.gif' border='0'></a>

			."<label><input type='checkbox' name='element[]' value='"
			.$element["name"]."' />".$element["name"]."</label> "
			."</td><td>".$element["size"]."</td><td>".$element["datetime"]."</td></tr>";
		
		}
	
	}

	$i=0;
	foreach($files_list as $key=>$element){
		
		$i++;		
		if($element["type"]!="dir"){
			
			
			$extension_file = $element["name"];
			$extension_file = substr($extension_file, strrpos($extension_file,".")+1);
			
			echo "<tr><td>".$i."</td><td>";
			
			if($element["name"] == "template.txt")
			{
			    if(in_array($extension_file, array("txt"))){
			        
			        echo "<a href='?component=".$application->getComponent()."&task=view&&controller=edit&filename=".$element["name"]."&folder=".$application->getParameter("folder")."'>"
                        ."<img width='16px' align='middle' src='".$application->getPathTemplate()."/images/icon-view.png' title='Edit'/></a> ";

			    }
			}
			else
			{
			    echo ""
                    ."<a target='_blank' href='?component=".$application->getComponent()."&task=view&type_extract=2&controller=extract_tmpl&filename=".$element["name"]."&folder=".$application->getParameter("folder")."'>"
                    ."<img width='16px' align='middle' src='".$application->getPathTemplate()."/images/icon-view.png' title='View Content'/></a> ";
                
			}
					
					
			
			if(in_array($extension_file, array("csv", "html"))){	
				
				echo "<a target='_blank' href='?component=".$application->getComponent()."&task=preview&type_extract=1&controller=extract_tmpl&filename=".$element["name"]."&folder=".$application->getParameter("folder")."'>"				
				."<img width='16px' align='middle' src='".$application->getPathTemplate()."/images/icon-table.png' title='Preview'/></a> ";
			
			}
			
			
			
			echo			"<a href='?component=".$application->getComponent()."&task=download&type_extract=1&controller=extract_tmpl&filename=".$element["name"]."&folder=".$application->getParameter("folder")."'>"				
				."<img width='16px' align='middle' src='".$application->getPathTemplate()."/images/icon_download.png' title='Download'/></a> "
				;
			
				

			if(in_array($extension_file, $exterions)){
				
				echo "<label>".$element["name"]."</label> ";
				
			}else{
				
				echo "<label><input type='checkbox' name='element[]' value='"
						.$element["name"]."' />".$element["name"]."</label> ";
				
			}
				
				
			
    			
				// ."<a title='Move file' href='?component=moa&controller=run&filename=".$element["name"]."&folder=".$application->getParameter("folder")."'>"
    			//."<img align='middle' width='24px' src='".App::getDirTmpl()."/images/icon-play.png' border='0'></a> "	
				//."<a title='Remove' onclick='javascript: return ConfirmDelete();' href='?component=home&controller=files&task=remove&folder=".$folder."&filename=".$folder.$element["name"]."'>"
				//		."<img width='16px' src='".App::getDirTmpl()."images/icon-remove.gif' border='0'></a>"					
				echo "</td><td>".$element["size"]."</td><td>".$element["datetime"]."</td></tr>";
		
		}
		
	
	}

?>		
	</table>
	

	
	</form>
	
	

	
				

<script>






historicCookieRadiobox('type_extract');

//historicCookieElementSelectValue("parallel_process");
historicCookieElementValue("breakline", "2");
//historicCookieElementValue("phone");

historicCookieCheckbox("accuracy");
historicCookieCheckbox("timer");
historicCookieCheckbox("memory");
historicCookieCheckbox("interval");
//historicCookieCheckbox("detector");
//historicCookieCheckbox("detectorsum");


historicCookieCheckbox("dist");
historicCookieCheckbox("fn");
historicCookieCheckbox("fp");
historicCookieCheckbox("tn");
historicCookieCheckbox("tp");
historicCookieCheckbox("precision");
historicCookieCheckbox("recall");

historicCookieCheckbox("mcc");
historicCookieCheckbox("f1");

historicCookieCheckbox("resume");

historicCookieCheckbox("save");
historicCookieCheckbox("overwrite");

historicCookieCheckbox("tex");
historicCookieCheckbox("csv");
historicCookieCheckbox("html");

historicCookieRadiobox('statisticaltest');

historicCookieRadiobox('viewdata');

historicCookieRadiobox('process_type');


historicCookieElementValue("decimalformat", ".");




</script>		
		
