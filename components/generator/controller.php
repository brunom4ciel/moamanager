<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\components\generator;

defined('_EXEC') or die();

use moam\core\Framework;
use moam\core\Application;
use moam\libraries\core\menu\Menu;
use moam\core\Template;
use moam\core\Properties;
use moam\libraries\core\utils\Utils;
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

Template::addHeader(array(
    "tag" => "script",
    "type" => "text/javascript",
    "src" => "" . PATH_WWW . "templates/default/javascript/json-to-table.js"
));

Template::addHeader(array(
    "tag" => "script",
    "type" => "text/javascript",
    "src" => "" . PATH_WWW . "templates/default/javascript/base64.js"
));

Template::addHeader(array(
    "tag" => "script",
    "type" => "text/javascript",
    "src" => "" . PATH_WWW . "templates/default/javascript/jquery.min.1.5.2.js"
    // . "http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js")
));

Template::addHeader(array(
    "tag" => "script",
    "type" => "text/javascript",
    "src" => "" . PATH_WWW . "templates/default/javascript/jquery.zclip.min.js"
));

Template::addHeader(array(
    "tag" => "script",
    "type" => "text/javascript",
    "src" => "" . PATH_WWW . "templates/default/javascript/generator.js"
));

require_once ("startApp.php");
// Framework::includeFile("app/startApp.php");

$dirScriptsName = "scripts";

?>


<script type="text/javascript">

<?php

$arrayJS = "";

foreach ($arrayTask as $key => $item) {

    if (empty($arrayJS)) {
        $arrayJS = "[" . "\"" . $item . "\"";
    } else {
        $arrayJS .= ", " . "\"" . $item . "\"";
    }
}

echo "var optionArrayTask = " . $arrayJS . "];\n";

$arrayJS = "";

foreach ($arrayLearners as $key => $item) {

    if (empty($arrayJS)) {
        $arrayJS = "[" . "\"" . $item . "\"";
    } else {
        $arrayJS .= ", " . "\"" . $item . "\"";
    }
}

echo "var optionArrayLearner = " . $arrayJS . "];\n";

$arrayJS = "";

foreach ($arrayLearnersParameters as $key => $item) {

    if (empty($arrayJS)) {
        $arrayJS = "'" . $key . "': ";
    } else {
        $arrayJS .= ", '" . $key . "': ";
    }

    if (is_array($item)) {

        $arrayJS2 = "[";

        foreach ($item as $key2 => $item2) {

            if (is_array($item2)) {

                // if(empty($arrayJS2))
                if (substr($arrayJS2, strlen($arrayJS2) - 1, strlen($arrayJS2)) == "[")
                    $arrayJS2 .= "{";
                else
                    $arrayJS2 .= ",{";

                foreach ($item2 as $key3 => $item3) {

                    if (is_array($item3)) {

                        $arrayJS2 .= ",'" . $key3 . "': ";
                        $arrayJS2 .= "{";

                        foreach ($item3 as $key4 => $item4) {

                            if (substr($arrayJS2, strlen($arrayJS2) - 1, strlen($arrayJS2)) == "{") {
                                $arrayJS2 .= "'" . $key4 . "': '" . $item4 . "'";
                            } else {
                                $arrayJS2 .= ", '" . $key4 . "': '" . $item4 . "'";
                            }
                        }

                        $arrayJS2 .= "}";
                    } else {

                        if (substr($arrayJS2, strlen($arrayJS2) - 1, strlen($arrayJS2)) == "{") {
                            $arrayJS2 .= "'" . $key3 . "': '" . $item3 . "'";
                        } else {
                            $arrayJS2 .= ", '" . $key3 . "': '" . $item3 . "'";
                        }
                    }
                }

                $arrayJS2 .= "}";
            }
        }

        $arrayJS .= $arrayJS2 . "]";
    } else {
        $arrayJS .= "[{}]";
    }
}

echo "var optionLearnersParameters = {" . $arrayJS . "};\n";

$arrayJS = "";

foreach ($arrayLearnersMethods as $key => $item) {

    if (empty($arrayJS)) {
        $arrayJS = "[" . "\"" . $item . "\"";
    } else {
        $arrayJS .= ", " . "\"" . $item . "\"";
    }
}

echo "var optionArrayLearnerDriftDetect = " . $arrayJS . "];\n";

$arrayJS = "";

foreach ($arrayLearnersMethodsParameters as $key => $item) {

    if (empty($arrayJS)) {
        $arrayJS = "'" . $key . "': ";
    } else {
        $arrayJS .= ", '" . $key . "': ";
    }

    if (is_array($item)) {

        $arrayJS2 = "[";

        foreach ($item as $key2 => $item2) {

            if (is_array($item2)) {

                // if(empty($arrayJS2))
                if (substr($arrayJS2, strlen($arrayJS2) - 1, strlen($arrayJS2)) == "[")
                    $arrayJS2 .= "{";
                else
                    $arrayJS2 .= ",{";

                foreach ($item2 as $key3 => $item3) {

                    if (is_array($item3)) {

                        $arrayJS2 .= ",'" . $key3 . "': ";
                        $arrayJS2 .= "{";

                        foreach ($item3 as $key4 => $item4) {

                            if (substr($arrayJS2, strlen($arrayJS2) - 1, strlen($arrayJS2)) == "{") {
                                $arrayJS2 .= "'" . $key4 . "': '" . $item4 . "'";
                            } else {
                                $arrayJS2 .= ", '" . $key4 . "': '" . $item4 . "'";
                            }
                        }

                        $arrayJS2 .= "}";
                    } else {

                        if (substr($arrayJS2, strlen($arrayJS2) - 1, strlen($arrayJS2)) == "{") {
                            $arrayJS2 .= "'" . $key3 . "': '" . $item3 . "'";
                        } else {
                            $arrayJS2 .= ", '" . $key3 . "': '" . $item3 . "'";
                        }
                    }
                }

                $arrayJS2 .= "}";
            }
        }

        $arrayJS .= $arrayJS2 . "]";
    } else {
        $arrayJS .= "[{}]";
    }
}

echo "var optionArrayLearnerDriftDetectParameters = {" . $arrayJS . "};\n";

$arrayJS = "";

foreach ($arrayDatasets as $key => $item) {

    if (empty($arrayJS)) {
        $arrayJS = "[" . "\"" . $item . "\"";
    } else {
        $arrayJS .= ", " . "\"" . $item . "\"";
    }
}

echo "var optionArrayDatasets = " . $arrayJS . "];\n";
?>
			
			
//http://jsfiddle.net/dandv/9aZQF/2/
//populate(this.id, 'slct2')
//<div id="slct2"></div>
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
							style="float: left; width: 20%; border: 1px solid #fff; display: table-cell">
								
									<?php require_once(PATH_COMPONENTS."/generator/menu_features.php")?>
								
									
								  	
								  	<br>
							<br>

							<ul id="nav" style="width: 200px;">

								<li><a href="#"
									onclick='javascript:createDynamicPopupObjects(this,event,"task",optionArrayTask,"radio","formulario","onchange","setCookieElementSelectValue(this);");'>Evaluation
										strategy</a></li>
								<li><a href="#"
									onclick='javascript:createDynamicPopupObjects(this,event,"learn",optionArrayLearner,"checkbox","formulario","click","showMenu(this, event)");'>Learner</a>
								</li>
								<li><a href="#"
									onclick='javascript:createDynamicPopupObjects(this,event,"driftdetect",optionArrayLearnerDriftDetect,"checkbox","formulario","click","showMenuDriftDetect(this, event)");'>Drift
										Detection Method</a></li>
								<li><a href="#"
									onclick='javascript:createDynamicPopupObjects(this,event,"dataset",optionArrayDatasets,"checkbox","formulario","click","showMenu(this, event)");'>Dataset</a>
								</li>
							</ul>


						</div>

						<div
							style="float: left; width: 80%; border: 1px solid #fff; display: table-cell">




							<form method="POST" name="formulario" id="formulario">

								<div style="width: 58%; background-color: #fff;">

									<div id='content'
										style="float: left; height: auto; width: 820px">





										<div
											style="width: 100%; background-color: #f7f7f7; float: left;">

											<div
												class="selectize-input items not-full has-options has-items">




												<div
													style="float: right; padding-right: 5px; width: auto; padding-top: 20px;">

													<div style="float: right; padding-right: 0px; width: auto;">
														<input type="button" id="submit" name="submit"
															value="Generate Script"
															onclick="javascript:sendGeneratorDatasets();" />

													</div>

													<div style="float: right; width: auto;">
														<input type="button" id="limpar" name="limpar"
															value="Clean"
															onclick="javascript:document.getElementById('consoles').value='';" />
													</div>


												</div>


											</div>

										</div>



										<div style="width: 100%; float: left; height: 10px;"></div>

										<div
											style="width: 100%; background-color: #f7f7f7; text-align: left; float: left">


											<div
												style="float: left; padding-right: 20px; width: auto; margin-left: 5px;">

												<div class="control-group">
													<label for="select-content-type-client" style="float: left">Number
														of instances of dataset</label> <input type="text"
														name="instance_limit" id="instance_limit"
														style="margin-left: 5px; width: 90px; float: left"
														value="" onchange="setCookieElementValue(this);" />

												</div>

											</div>


											<div style="float: left; padding-right: 20px; width: auto;">

												<div class="control-group">
													<label for="select-content-type-client" style="float: left">Sample
														Frequency</label> <input type="text"
														name="sample_frequency" id="sample_frequency"
														style="margin-left: 5px; width: 35px; float: left"
														value="" onchange="setCookieElementValue(this);" />

												</div>

											</div>

											<div style="float: left; padding-right: 20px; width: auto;">

												<div class="control-group">
													<label for="select-content-type-client" style="float: left">Memory
														Check Frequency</label> <input type="text"
														name="mem_check_frequency" id="mem_check_frequency"
														style="margin-left: 5px; width: 35px; float: left"
														value="" onchange="setCookieElementValue(this);" />

												</div>

											</div>

											<div style="float: left; padding-right: 20px; width: auto;">

												<div class="control-group">
													<label for="select-content-type-client" style="float: left">Number
														of concept drifts</label> <input type="text"
														name="drift_length" id="drift_length"
														style="margin-left: 5px; width: 35px; float: left"
														value="" onchange="setCookieElementValue(this);" />

												</div>

											</div>

											<div style="float: left; padding-right: 20px; width: auto;">

												<div class="control-group">
													<label for="select-content-type-client" style="float: left">Drift
														Width</label> <input type="text" name="drift_width"
														id="drift_width"
														style="margin-left: 5px; width: 50px; float: left"
														value="" onchange="setCookieElementValue(this);" />

												</div>

											</div>


											<div style="float: left; padding-right: 20px; width: auto;">

												<div class="control-group">
													<label for="select-content-type-client" style="float: left">Repetition</label>
													<input type="text" name="repetition" id="repetition"
														style="margin-left: 5px; width: 50px; float: left"
														value="" onchange="setCookieElementValue(this);" />

												</div>

											</div>

										</div>




										<div style="width: 100%; float: left; height: 10px;"></div>

										<div
											style="width: 100%; background-color: #f7f7f7; text-align: left; margin-top: 5px; float: left">

											<div style="float: left; width: 100%">

												<div
													style="float: left; padding-left: 5px; width: 100%; margin-top: 5px;">

													<textarea id="consoles" style="width: 100%; height: 400px;"
														name="consoles" data-resizable="true" placeholder=""></textarea>
												</div>
							
							</form>

							<form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>"
								name="saveform" async-form="login"
								class="ng-pristine ng-valid-email ng-invalid ng-invalid-required">
								<input type="hidden"
									value="<?php echo $application->getComponent()?>"
									name="component"> <input type="hidden" value="save"
									name="controller">

								<div style="float: left; padding-left: 5px; width: 500px">

									<input type="hidden" id="console2" name="console2">

									<div style="float: left; padding-left: 0px; width: 400px">
										<input type="text" id="filename" name="filename"
											value="<?php echo "script-".date("Y-m-d-H-i-s_").microtime(true)."";?>"
											class="form-control ng-pristine ng-isolate-scope ng-valid-email ng-invalid ng-invalid-required ng-touched" />
									</div>

									<div style="float: left; padding-left: 0px; width: 400px">
										Save in folder <select name="dirstorage" id="dirstorage">
											<option value=""></option>
													<?php

            $utils = new Utils();

            $files_list = $utils->getListDirectory(Properties::getBase_directory_destine($application) . $application->getUser() . DIRECTORY_SEPARATOR . $dirScriptsName . DIRECTORY_SEPARATOR);

            foreach ($files_list as $key => $element) {

                // if($element["type"]=="dir"){

                echo "<option value=\"" . $element . "\">" . $element . "</option>";
                // }
            }

            ?>
													
												</select>
									</div>

									<div style="float: left; padding-left: 10px">
										<input type="submit" value="Save"
											onclick="javascript: document.getElementById('console2').value=document.getElementById('consoles').value;">


									</div>

								</div>
							</form>


						</div>

						<div style="width: 100%; float: left; height: 10px;"></div>






					</div>
				</div>

				</form>

				<!-- <div id="waiting_process" style="position:fixed;bottom:40%;right:45%;width:200px;height:220px;">
		-->

				<div id="waiting_process"
					style="position: fixed; bottom: 0%; left: 0%; width: auto; height: auto;"></div>






				<script type='text/javascript'>

/*
function detectLearner(element){
	
	var elementOption = document.getElementById('learner_method_selected');
	
	if(element.options[element.selectedIndex].innerHTML=="SingleClassifierDrift"){
		
		elementOption.style.visibility = "visible";//style="visibility: visible;"	
		elementOption.style.height = "auto";			
		elementOption.style.width = "auto";
	}else{
		elementOption.    }else{
    	//popup.innerHTML = "";
    	popup.style.visibility = "visible";
    	
    } style.visibility = "hidden";
		elementOption.style.height = "0px";
		elementOption.style.width = "0px";
	}
}*/





function fecharpop(idName){
    document.getElementById(idName).style.display = 'none';
}

function toggle(elementName) {
	
  checkboxes = document.getElementsByName(elementName);
  checks=false;
  
	for(var i=0, n=checkboxes.length;i<n;i++) {
		if(checkboxes[i].value=="all"){
			checks = checkboxes[i].checked;
		}
	}
  
	for(var i=0, n=checkboxes.length;i<n;i++) {
		checkboxes[i].checked = checks;//source.checked;
	}
}


function showMenu(element, event){
	
	if(element.checked==false)
		return;
	
	
	switch(element.value){
		
		case	'NaiveBayes':{
			
			break;
		}
		case	'SingleClassifierDrift':{
			
			break;
		}
		case	'HoeffdingTree':{
			
			break;
		}
		case	'CDDE':{
			
			
			var key;
			var parameters=[];
			
			for (key in optionLearnersParameters) {
				
			    if (optionLearnersParameters.hasOwnProperty(key)) {
			    	
			    	if(element.value == key){
			    		
				    	for (key2 in optionLearnersParameters[key]) {
				    		
				    		var b = new Object();
				    		
				    		b = {'name':optionLearnersParameters[key][key2]["name"],
				    			'default':optionLearnersParameters[key][key2]["default"],
				    			'label':optionLearnersParameters[key][key2]["label"],
				    			'type':optionLearnersParameters[key][key2]["type"],
				    			'list':optionLearnersParameters[key][key2]["list"]};
				    		
				    		parameters.push(b);
	
				    	}
			    	}
			        
			    }
			}	

			//print_r(parameters);
			
			
			createDynamicPopupObjects2(element,event,element.value,parameters,"text","formulario","","");
			
			
			break;
		}
		case	'DDE':{
			
			
			var key;
			var parameters=[];
			
			for (key in optionLearnersParameters) {
				
			    if (optionLearnersParameters.hasOwnProperty(key)) {
			    	
			    	if(element.value == key){
			    		
				    	for (key2 in optionLearnersParameters[key]) {
				    		
				    		var b = new Object();
				    		
				    		b = {'name':optionLearnersParameters[key][key2]["name"],
				    			'default':optionLearnersParameters[key][key2]["default"],
				    			'label':optionLearnersParameters[key][key2]["label"],
				    			'type':optionLearnersParameters[key][key2]["type"],
				    			'list':optionLearnersParameters[key][key2]["list"]};
				    		
				    		parameters.push(b);
	
				    	}
			    	}
			        
			    }
			}	

			//print_r(parameters);
			
			
			createDynamicPopupObjects2(element,event,element.value,parameters,"text","formulario","","");
			
			
			break;
		}
		/*
		case	'CDDE':{
					
					
					var key;
					var parameters=[];
					
					for (key in optionLearnersParameters) {
						if (optionLearnersParameters.hasOwnProperty(key)) {
														 for (key2 in optionLearnersParameters[key]) {
																 var b = new Object();
																 b = {'name':optionLearnersParameters[key][key2]["name"],'default':optionLearnersParameters[key][key2]["default"],'label':optionLearnersParameters[key][key2]["label"],'type':optionLearnersParameters[key][key2]["type"],'list':optionLearnersParameters[key][key2]["list"]};
																 parameters.push(b);
									 }
													 }
					}	
		
					//print_r(parameters);
					
					
					createDynamicPopupObjects2(element,event,element.value,parameters,"text","formulario","","");
					
					break;
				}*/
		
	}
	
	//createDynamicPopupObjects(element,event,"datasets",optionArrayDatasets,"checkbox","formulario","click","alert('bruno')");

	//alert("this="+element.value);
								
}





function showMenuDriftDetect(element, event){
	
	if(element.checked==false)
		return;
	
		
	switch(element.value){
		<?php

print "\n";
foreach ($arrayLearnersMethodsParameters as $key => $item) {
    print "\t\tcase '" . $key . "':\n";
}
print "\t\t{";
// case 'SeqDrift1ChangeDetector':
// case 'SeqDrift2ChangeDetector':
// case 'ADWINChangeDetector':
// case 'EWMAChartDM':
// case 'EnsembleDriftDetectionMethods':
// case 'HDDM_A_Test':
// case 'HDDM_W_Test':
// case 'PageHinkleyDM':
// case 'GeometricMovingAverageDM':
// case 'ADWINMethod':
// case 'STEPD':
// case 'EDDM':
// case 'DDM':{
//
?>	
			
			var key;
			var parameters=[];
			
			for (key in optionArrayLearnerDriftDetectParameters) {
			    if (optionArrayLearnerDriftDetectParameters.hasOwnProperty(key)) {
			    	
			    	if(element.value == key){
			    	
				    	for (key2 in optionArrayLearnerDriftDetectParameters[key]) {
				    					    		
				    		var b = new Object();
				    		
				    		b = {'name':optionArrayLearnerDriftDetectParameters[key][key2]["name"],
				    		'default':optionArrayLearnerDriftDetectParameters[key][key2]["default"],
				    		'label':optionArrayLearnerDriftDetectParameters[key][key2]["label"],
				    		'type':optionArrayLearnerDriftDetectParameters[key][key2]["type"],
				    		'list':optionArrayLearnerDriftDetectParameters[key][key2]["list"]};
				    		
				    		parameters.push(b);
	
				    	}
			    	}
			        
			    }
			}	

			//print_r(parameters);
			
			
			createDynamicPopupObjects2(element,event,element.value,parameters,"text","formulario","","");
			
			break;
		}
	}
	
	//createDynamicPopupObjects(element,event,"datasets",optionArrayDatasets,"checkbox","formulario","click","alert('bruno')");

	//alert("this="+element.value);
								
}

/*

for (var option in optionLearnersParameters) {
	
	if(option.length>0)
		alert(option+": "+option.length);
}*/


function print_r(obj){
		
	var key;
	var parameters=[];
	
	for (key in obj) {
	    if (obj.hasOwnProperty(key)) {
	    	
	    	for (key2 in obj[key]) {
	    		
	    		//var b = new Object();
	    		
	    		//b = {'parameter':key2,'default':obj[key][key2]};
	    		
	    		//parameters.push(b);
	    		
	    		console.log(obj);//key+"-"+key2+"-"+obj[key][key2]);
	    	}
	        
	    }
	}
}

/*

var key;
var parameters=[];

for (key in optionLearnersParameters) {
    if (optionLearnersParameters.hasOwnProperty(key)) {
    	
    	for (key2 in optionLearnersParameters[key]) {
    		
    		var b = new Object();
    		
    		b = {'name':optionLearnersParameters[key][key2]["name"],'default':optionLearnersParameters[key][key2]["default"],'label':optionLearnersParameters[key][key2]["label"]};
    		
    		parameters.push(b);
    		//console.log(optionLearnersParameters[key][key2]["name"]);
    		//console.log(key+"-"+key2+"-"+optionLearnersParameters[key][key2]);
    	}
        
    }
}
*/


//print_r(parameters);



    		
function createDynamicPopupObjects2(element, event, idName, optionArray, type, formId, eventName, jsCode) {
	var timeOut;
	var prexNameDefault = "bm";
	
	idName = prexNameDefault+idName;
	
    var popup = document.getElementById(idName); 
    
    if(typeof (popup) == undefined || typeof (popup) == null || 
    	typeof (popup) == 'undefined' || popup == null){
    		
    	popup = document.createElement("div"); 
    	popup.id = idName;
    	
    	var X = event.clientX;		
		var Y = event.clientY;
	
		popup.style.position = "absolute";
		popup.style.border = "1px solid #618bd7";
		popup.style.visibility = "visible";
		popup.style.background = "#f7f7f7";
		popup.style.padding = "2px";
		
		popup.style.left = X.toString()	 +"px";
		popup.style.top = Y.toString() +"px";
				
		
		var form = document.getElementById(formId); 
    
    	if(typeof (form) == undefined || typeof (form) == null || 
    		typeof (form) == 'undefined' || form == null){
    		alert("Error form");		
		}else{
			form.appendChild(popup);  
		}		
	    //document.body.appendChild(popup);  	    
	    
	    popup.addEventListener("mouseover", function(){popup.style.display = 'block'; window.clearTimeout(timeOut)});
		popup.addEventListener("mouseout", function(){timeOut=setTimeout(function(){popup.style.display = 'none';},1000)});	    
	       
	        
		for (var option in optionArray) {
			
		    if (optionArray.hasOwnProperty(option)) {
		    	
		    			    		
			    		
		    	//console.log(optionArray[option]);
		    	
		        var pair = optionArray[option]["name"];
		        //var checkbox = document.createElement("input");
		        
		        if(type == "checkbox"){
		        	var checkbox = document.createElement("input");
		        	checkbox.id = "obj"+idName+"[]";
		        	checkbox.name = "obj"+idName+"[]";
		        	checkbox.type = "checkbox";
		        	checkbox.style.width ="24px";
		       		checkbox.style.height = "24px";
		        }		        	
		        else if(type == "radio"){		
		        	var checkbox = document.createElement("input");
		        	checkbox.id = "obj"+idName;        	
		        	checkbox.name = "obj"+idName;
		   			checkbox.type = "radio";
		   			checkbox.style.width ="24px";
		       		checkbox.style.height = "24px";
		        
		        }else if(type == "text"){
		        	
		        	type_element = optionArray[option]["type"];
		        	
		        	if(type_element=="text"){
		        		var checkbox = document.createElement("input");
    		        	checkbox.id = "obj"+idName+"_"+pair;     	
			        	checkbox.name = "obj"+idName+"_"+pair;
			   			checkbox.type = "text";
			   			var w = optionArray[option]["default"];
			   			
			   			if(w.length==1)
			   				checkbox.style.width = (50)+"px";
			   			else
			   				checkbox.style.width = (w.length*13)+"px";
			   			
			   			checkbox.value = optionArray[option]["default"];
			   				
		        	}else{
		        		
		        		list = optionArray[option]["list"];
		        		
		        		var checkbox = document.createElement("select");
		        		checkbox.id =  "obj"+idName+"_"+pair;
		        		checkbox.name = "obj"+idName+"_"+pair;
		        		checkbox.style.width ="auto";
		        		
		        		for (key in list) {
						    if (list.hasOwnProperty(key)) {
						    	
						    	var options = document.createElement("option");
							    options.value = key;
							    options.text = list[key];
							    
							    checkbox.appendChild(options);
						    
						    }
						    
			    		}
			    		
			    		
		        	}
		        			        	
		
		        }        
		        
		        
		        
		        checkbox.style.verticalAlign = "bottom";		        
		        
		        //s2.appendChild(checkbox);
		
		        var label = document.createElement('label');
		        //label.htmlFor = optionArray[option]["label"];
		        label.id = "obj"+idName; 
		        label.style.display ="block";
		        label.style.margin ="2px 0";
		        label.style.height = "0px";
		        label.style.verticalAlign = "top";
		        label.style.lineHeight = "24px";
		        label.style.paddingRight = "5px";
		        
		        label.style.paddingBottom= "3px";
		        
		        //label.addEventListener("click", function(){ toggle("objpopup[]"); });
		        
		        label.style.width = "100%";
		       		        
				label.appendChild(checkbox);
				
				//alert("label.addEventListener(\""+eventName+"\", function(){ "+jsCode+";});");
								
				eval("checkbox.addEventListener(\""+eventName+"\", function(){ "+jsCode+";});");
				
				//label.addEventListener(eventName, function(){ alert("bruno") ;});
				
				label.appendChild(document.createTextNode(" "+optionArray[option]["label"]));
				
		        popup.appendChild(label);
		        popup.appendChild(document.createElement("br"));   
		        
		        //alert(checkbox.id+"="+optionArray[option]["default"]);
			    
			    
			    if(type_element == "list")
			    	SetSelectIndex(checkbox.id,optionArray[option]["default"]);
			    	
			    		
		         
			}
		}   			
	    	
	    //popup.appendChild(document.createElement("br"));   
	    	
    }else{
    	
    	var X = event.clientX;
		var Y = event.clientY;
			
		popup.style.left = X.toString()	 +"px";
		//document.getElementById(idName).style.display = 'none';
		popup.style.top = Y.toString() +"px";
		popup.style.display = 'block';
		
    	//popup.innerHTML = "";
    	popup.style.visibility = "visible";
    	
    } 
	
	


}


    		
function createDynamicPopupObjects(element, event, idName, optionArray, type, formId, eventName, jsCode) {
	var timeOut;
	var prexNameDefault = "bm";
	
	idName = prexNameDefault+idName;
	
    var popup = document.getElementById(idName); 
    
    if(typeof (popup) == undefined || typeof (popup) == null || 
    	typeof (popup) == 'undefined' || popup == null){
    		
    	popup = document.createElement("div"); 
    	popup.id = idName;
    	
    	var X = event.clientX;		
		var Y = event.clientY;
	
		popup.style.position = "absolute";
		popup.style.border = "1px solid #618bd7";
		popup.style.visibility = "visible";
		popup.style.background = "#f7f7f7";
		popup.style.padding = "2px";
		
		popup.style.left = X.toString()	 +"px";
		popup.style.top = Y.toString() +"px";
				
		
		var form = document.getElementById(formId); 
    
    	if(typeof (form) == undefined || typeof (form) == null || 
    		typeof (form) == 'undefined' || form == null){
    		alert("Error form");		
		}else{
			form.appendChild(popup);  
		}		
	    //document.body.appendChild(popup);  	    
	    
	    popup.addEventListener("mouseover", function(){popup.style.display = 'block'; window.clearTimeout(timeOut)});
		popup.addEventListener("mouseout", function(){timeOut=setTimeout(function(){popup.style.display = 'none';},1000)});	    
	       
	        
		for (var option in optionArray) {
			
		    if (optionArray.hasOwnProperty(option)) {
		    	
		        var pair = optionArray[option];
		        var checkbox = document.createElement("input");
		        
		        if(type == "checkbox"){
		        	checkbox.id = "obj"+idName+"[]";
		        	checkbox.name = "obj"+idName+"[]";
		        	checkbox.type = "checkbox";
		        }		        	
		        else if(type == "radio"){		
		        	checkbox.id = "obj"+idName;        	
		        	checkbox.name = "obj"+idName;
		        	checkbox.type = "radio";
		        }else if(type == "text"){
		        	
		        }
		        
		        
		        checkbox.value = pair;
		        
		        checkbox.style.width ="24px";
		        checkbox.style.height = "24px";
		        checkbox.style.verticalAlign = "bottom";		        
		        
		        //s2.appendChild(checkbox);
		
		        var label = document.createElement('label');
		        //label.htmlFor = pair;
		        label.id = "obj"+idName; 
		        label.style.display ="block";
		        label.style.margin ="2px 0";
		        label.style.height = "0px";
		        label.style.verticalAlign = "top";
		        label.style.lineHeight = "24px";
		        label.style.paddingRight = "5px";
		        
		        //label.addEventListener("click", function(){ toggle("objpopup[]"); });
		        
		        label.style.width = "100%";
		        
				label.appendChild(checkbox);
				
				//alert("label.addEventListener(\""+eventName+"\", function(){ "+jsCode+";});");
								
				eval("checkbox.addEventListener(\""+eventName+"\", function(){ "+jsCode+";});");
				
				//label.addEventListener(eventName, function(){ alert("bruno") ;});
				
				label.appendChild(document.createTextNode(pair));
				
		        popup.appendChild(label);
		        popup.appendChild(document.createElement("br"));  
		        
		        
		        
		        
		        		        
		        
		        // var img = document.createElement('img');
// 				
				// img.src = 'images/config.png';
// 						        
		        // label.appendChild(img);
		        // eval("img.addEventListener(\"click\", function(){ "+jsCode+"; });");
		        
		        
	
		        
		        
		        /********/
		       //ordenação
		       
				var pair = optionArray[option];
				var checkbox = document.createElement("input");
				
				checkbox.id = "obj"+idName+"_"+pair+"_order[]";
	        	checkbox.name = "obj"+idName+"_"+pair+"_order[]";
	        	checkbox.type = "text";
		        
		        checkbox.value = "";
		        
		        checkbox.style.width ="24px";
		        checkbox.style.height = "24px";
		        checkbox.style.verticalAlign = "bottom";
		       
		        
				label.appendChild(checkbox);				
		        //popup.appendChild(document.createElement("br"));
		        
		        
		          
			}
		}
	    
	    if(type == "checkbox"){
	    	
		    var pair = "Select All";
	        var checkbox = document.createElement("input");
	        
	
	        checkbox.name = "obj"+idName+"[]";
	        checkbox.type = "checkbox";
		        
	        checkbox.value = "all";
	        
	        checkbox.style.width ="24px";
	        checkbox.style.height = "24px";
	        checkbox.style.verticalAlign = "bottom";		        
	        
	        //s2.appendChild(checkbox);
	
	        var label = document.createElement('label');
	        //label.htmlFor = pair;.style.display = 'block';
	        
	        label.style.display ="block";
	        label.style.margin ="2px 0";
	        label.style.height = "0px";
	        label.style.verticalAlign = "top";
	        label.style.lineHeight = "24px";
	        label.style.paddingRight = "5px";
	        label.style.width = "100%";
	        
			label.appendChild(checkbox);
			
			label.addEventListener("click", function(){ toggle("obj"+idName+"[]"); });
			
			//<a href="#" onclick='javascript:;'>all</a>
			label.appendChild(document.createTextNode(pair));
			
	        popup.appendChild(label);
	        popup.appendChild(document.createElement("br"));  
	        
		}
		
	    
	    	
    }else{
    	
    	var X = event.clientX;
		var Y = event.clientY;
			
		popup.style.left = X.toString()	 +"px";document.getElementById(idName).style.display = 'none';
		popup.style.top = Y.toString() +"px";
		popup.style.display = 'block';
		
    	//popup.innerHTML = "";
    	popup.style.visibility = "visible";
    	
    } 
	
	


}




















function detectLearner2(element) {
	
    //bvar s1 = document.getgetElementByIdElementById(slct1);
    //var s2 = document.getElementById("aaaaa");
    
    var s2 = document.createElement("div");    
    var body = document.getElementsByTagName("body");
    body.appendChild(s2);
    
    
    s2.innerHTML = "";
    
    // /var value = element.options[element.selectedIndex].value;
    
    switch(element.options[element.selectedIndex].value){
    	
    	case	"SingleClassifierDrift":{
     		
    		<?php

    $arrayJS = "";

    foreach ($arrayLearnersMethods as $key => $item) {

        if (empty($arrayJS)) {
            $arrayJS = "[" . "\"" . $item . "\"";
        } else {
            $arrayJS .= ", " . "\"" . $item . "\"";
        }
    }

    echo "var optionArray = " . $arrayJS . "];";
    ?>
			
    		break;
    	}
    	case	"d":{
    		
    		break;
    	}
    	case	"w":{
    		
    		break;
    	}
    	case	"q":{
    		
    		break;
    	}
    }
    
    
	for (var option in optionArray) {
		
	    if (optionArray.hasOwnProperty(option)) {
	    	
	        var pair = optionArray[option];
	        var checkbox = document.createElement("input");
	        
	        checkbox.type = "checkbox";
	        checkbox.name = pair;
	        checkbox.value = pair;
	        
	        //s2.appendChild(checkbox);
	
	        var label = document.createElement('label');
	        //label.htmlFor = pair;
	        
		
			label.appendChild(checkbox);
			
			label.appendChild(document.createTextNode(pair));
			
	        s2.appendChild(label);
	        s2.appendChild(document.createElement("br"));    
		}
	}
}


		

function sendGeneratorDatasets(){


	var parameters ="";
	var method = "POST";
	var strURL = "index.php";
	//var method= elementObj.value.toUpperCase();

	var HttpReq;
	
	if (window.XMLHttpRequest)
	{// code for IE7+, Firefox, Chrome, Opera, Safari
		HttpReq=new XMLHttpRequest();
	}
	else
	{// code for IE6, IE5
		HttpReq=new ActiveXObject("Microsoft.XMLHTTP");
	}
	
	HttpReq.withCredentials = false;
	
	var strParameters = parameters;
	 
	if ( method == 'POST'){//create data
		
		HttpReq.open(method, strURL, true);
		
		
		//strParameters = "parameters="+strParameters;
		
	}else if( method == 'PUT'){//update data
		
		HttpReq.open(method, strURL, true);
		//strParameters = "parameters="+strParameters;
		
	}else if( method == 'DELETE'){//delete data
		
		HttpReq.open(method, strURL, true);
		//strParameters = "parameters="+strParameters;
				
	}else if( method == 'GET'){//delete data
		
		//strParameters = "script="+Base64.encode(strParameters);
		HttpReq.open(method, strURL +'?'+ strParameters, true);
		//HttpReq.open(method, strURL, true);
				
	}else if( method == 'HEAD'){//delete data
		
		HttpReq.open(method, strURL, true);
		//strParameters = "parameters="+strParameters;
				
	}else if( method == 'OPTIONS'){//delete data
		
		HttpReq.open(method, strURL, true);
		//strParameters = "parameters="+strParameters;
				
	}else{
		//default
		
		//strParameters = "parameters="+strParameters;
		HttpReq.open(method, strURL +'?'+ strParameters, true);
	}
    

	HttpReq.setRequestHeader("Content-type","application/x-www-form-urlencoded");

	var content_type="text/html";

	    
    HttpReq.onreadystatechange = function() {
        if (HttpReq.readyState == 4) {
        	
        	switch(HttpReq.status){
        		
        		case	200:
        		//contentResult
        			
        			
					var objectArray = JSON.parse(HttpReq.responseText);
					//alert(objectArray.length);
					//if(objectArray.length==1){
						
					//	document.getElementById("consoles").innerHTML = "---------"+objectArray[0]["script"];
						
					//}else{getElementById
						
						for(i=0; i<objectArray.length;i++){  
							//for(y=0; y<objectArray[i].length;y++){  
							
							if(document.getElementById("consoles").value!="")
								document.getElementById("consoles").value = document.getElementById("consoles").value+"\n\n";
																	
							document.getElementById("consoles").value = document.getElementById("consoles").value+objectArray[i]["script"];														
							
							//}
							//alert(""+objectArray[i].length);
						}
						
						
				//	}
					
					
/*
			        if(objectArray["output"] === undefined ){
			        	
			        }else{
			        	//alert("bruno");
			        }
			        
			        var csv_result="";
			        var z=1;
			        
			        for(i=0; i<objectArray.length;i++){
			        	
			        	if(objectArray[i]['output']=== undefined ){
			        		
			        	}else{
			        		objectArray[i]['output'] = "<pre>"+objectArray[i]['output']+"</pre>";
			        	}
			        	
			        	if(objectArray[i]['Accuracy']=== undefined ){
			        		
			        	}else{
			        		
			        		if(csv_result=="")
			        			csv_resugetElementByIdlt = csv_result + objectArray[i]['Accuracy'];
			        		else
			        			csv_result = csv_result + ", " +objectArray[i]['Accuracy'];
			        			
			        	}
			        	        	
			        	if(objectArray[i]['script']=== undefined ){
			        		
			        	}else{
			        		
			        		objectArray[i]['script'] = "<a href='#' title='"+objectArray[i]['script'].replace("'","\'")+"'>"+z+"</a>";
			        		z++;
			        			
			        	}
			        	
			        }*/

					
			       // var jsonHtmlTable = ConvertJsonToTable(objectArray, 'consolesdiv', null, 'Download');
				
					//document.getElementById("consoles").innerHTML = contentResult;	
            			
            			
            			
        			break;
        		case	401:
        		
        			alert("401 Unauthorized");
        			
        			break;
        	}
        }
        
	
	}
	
	var varParameters=" component=generator";
	var Form = document.getElementById('formulario');
	
	for(I = 0; I < Form.length; I++) {
		
	    var Name = Form[I].getAttribute('name');
	    var Value = Form[I].value;
	    var id = Form[I].id;
	    var type = Form[I].type;
	    
		switch(type){
	    	case	"select-one":
	    		
	    		var optionValue = Form[I].options[Form[I].selectedIndex].value;
	    		
	    		varParameters = varParameters+"&"+id+"="+encodeURIComponent(Base64.encode(optionValue));
	    		
	    		break;
	    	case	"text":
	    	
				
				//if(Value != "")
					varParameters = varParameters+"&"+id+"="+encodeURIComponent(Base64.encode(Value));
						
	    		break;
	    	case	"textarea":

				//
				//strParameters = strParameters+"&"+name+"="+encodeURIComponent(Base64.encode(Value));
	    		break;
	    	case	"checkbox":
				
				
				if(Form[I].checked == true)
					varParameters = varParameters+"&"+id+"="+encodeURIComponent(Base64.encode(Value));
					
				//var checked = parseBool2(Form[I].checked);
				//checked = checked.toString();
				
				//if( Name != null)
				//	varParameters = varParameters+"&"+id+"="+encodeURIComponent(Base64.encode(checked));
					
	    		break;
	    	case	"radio":
				
				if(Form[I].checked == true)
					varParameters = varParameters+"&"+id+"="+encodeURIComponent(Base64.encode(Value));
					
				//var checked = parseBool2(Form[I].checked);
				//checked = checked.toString();
				
				//if( Name != null)
				//	varParameters = varParameters+"&"+id+"="+encodeURIComponent(Base64.encode(checked));
					
	    		break;
	    }
	    
	}
	varParameters = varParameters.substring(1);
	
	//alert(""+varParameters);

	HttpReq.send(varParameters+"&tmpl=tmpl");

}



var cancelViewProcess=true;


function refreshViewProcess(){
	
	setTimeout(function () {
        // Do Something Here
        // Then recall the parent function to
        // create a recursive loop.
        if(cancelViewProcess==true)
        	viewProcess();
        	
    }, 1000);
    
	
}

function viewProcess(){


	var parameters ="";
	var method = "GET";
	var strURL = "ps.php";
	//var method= elementObj.value.toUpperCase();

	
	var HttpReq;
	
	if (window.XMLHttpRequest)
	{// code for IE7+, Firefox, Chrome, Opera, Safari
		HttpReq=new XMLHttpRequest();
	}
	else
	{// code for IE6, IE5
		HttpReq=new ActiveXObject("Microsoft.XMLHTTP");
	}
	
	HttpReq.withCredentials = false;
	
	var strParameters = parameters;
	 
	if ( method == 'POST'){//create data
		
		HttpReq.open(method, strURL, true);
		
		
		//strParameters = "parameters="+strParameters;
		
	}else if( method == 'PUT'){//update data
		
		HttpReq.open(method, strURL, true);
		//strParameters = "parameters="+strParameters;
		
	}else if( method == 'DELETE'){//delete data
		
		HttpReq.open(method, strURL, true);
		//strParameters = "parameters="+strParameters;
				
	}else if( method == 'GET'){//delete data
		
		//strParameters = "script="+Base64.encode(strParameters);
		HttpReq.open(method, strURL +'?'+ strParameters, true);
		//HttpReq.open(method, strURL, true);
				
	}else if( method == 'HEAD'){//delete data
		
		HttpReq.open(method, strURL, true);
		//strParameters = "parameters="+strParameters;
				
	}else if( method == 'OPTIONS'){//delete data
		
		HttpReq.open(method, strURL, true);
		//strParameters = "parameters="+strParameters;
				
	}else{
		//default
		
		//strParameters = "parameters="+strParameters;
		HttpReq.open(method, strURL +'?'+ strParameters, true);
	}
    

	HttpReq.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	
	
	//alert("Content-Type: "+contentType);
	var content_type="text/html";
	var content_type="text/html";
	
	//HttpReq.setRequestHeader('Content-Type', content_type+";charset=UTF-8");
	//HttpReq.setRequestHeader("Accept",accept+";charset=UTF-8");
	    
    HttpReq.onreadystatechange = function() {
        if (HttpReq.readyState == 4) {
        	
        	switch(HttpReq.status){
        		
        		case	200:
        		
        			
        			var objectArray = JSON.parse(HttpReq.responseText);
			         
			        //for(i=0; i<objectArray.length;i++){  
			        	//for(y=0; y<objectArray[i].length;y++){  
			        		
			        		//document.getElementById("console_process").innerHTML = "---------"+objectArray[i]["PID"];
			        	//}
			        	//alert(""+objectArray[i].length);
			        							
					//}
/*
			        if(objectArray["output"] === undefined ){
			        	
			        }else{
			        	//alert("bruno");
			        }
			        
			        var csv_result="";
			        var z=1;
			        
			        for(i=0; i<objectArray.length;i++){
			        	
			        	if(objectArray[i]['output']=== undefined ){
			        		
			        	}else{
			        		objectArray[i]['output'] = "<pre>"+objectArray[i]['output']+"</pre>";
			        	}
			        	
			        	if(objectArray[i]['Accuracy']=== undefined ){
			        		
			        	}else{
			        		
			        		if(csv_result=="")
			        			csv_result = csv_result + objectArray[i]['Accuracy'];
			        		else
			        			csv_result = csv_result + ", " +objectArray[i]['Accuracy'];
			        			
			        	}
			        	        	
			        	if(objectArray[i]['script']=== undefined ){
			        		
			        	}else{
			        		
			        		objectArray[i]['script'] = "<a href='#' title='"+objectArray[i]['script'].replace("'","\'")+"'>"+z+"</a>";
			        		z++;
			        			
			        	}
			        	
			        }*/

					
			        var jsonHtmlTable = ConvertJsonToTable(objectArray, 'jsonTable', null, 'Download');
				
					document.getElementById("console_process").innerHTML = "<br>"+jsonHtmlTable+"<br>";	
            			
            			
            			
        			break;
        		case	401:
        		
        			alert("401 Unauthorized");
        			
        			break;
        	}
        }
        
	
	}

	HttpReq.send();
	refreshViewProcess();

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
	
	
	document.getElementById(elementId).checked = parseBool2(elementCookieChecked);
	
	if( document.getElementById(elementId).id == "ScrollbarFrom" && 
		document.getElementById(elementId).checked == true ){
		setScrollbar('origem');
	}
	
	if( document.getElementById(elementId).id == "screenFrom" && 
		document.getElementById(elementId).checked == true){
		setTextareaScreenAll('containerOrigem');
	}
	
	if( document.getElementById(elementId).id == "ScrollbarTo" && 
		document.getElementById(elementId).checked == true ){
		setScrollbar('destino');
	}
	
	if( document.getElementById(elementId).id == "screenTo" && 
		document.getElementById(elementId).checked == true){
		setTextareaScreenAll2('containerDestino');
	}

}


function detectDataset(element){
	
	var elementOption = document.getElementById('dataset_method_content');
	
	switch(element.options[element.selectedIndex].innerHTML){
		
		case	"AgrawalGenerator":
		case	"LEDGeneratorDrift":
		case	"MixedGenerator":
		case	"RandomRBFGeneratorDrift":
		case	"STAGGERGenerator":
		case	"SineGenerator":
		case	"WaveformGeneratorDrift":	
				
			//var elementOptionMethod = document.getElementById('dataset_'+element.options[element.selectedIndex].innerHTML.toLowerCase());
			//elementOption.innerHTML = elementOptionMethod.innerHTML;
			
			break;
		default:
		
			elementOption.innerHTML ="";
			
			break;
		
	}

}

function detectLearnerMethod(element){
	
	var elementOption = document.getElementById('learner_method_content');
	
	switch(element.options[element.selectedIndex].innerHTML){
		
		case	"ADWINChangeDetector":
		case	"ADWINMethod":
		case	"DDM":
		case	"EDDM":
		case	"EWMAChartDM":
		case	"EnsembleDriftDetectionMethods":
		case	"GeometricMovingAverageDM":
		case	"HDDM_A_Test":
		case	"HDDM_W_Test":
		case	"PageHinkleyDM":
		case	"STEPD":	
				
			//var elementOptionMethod = document.getElementById('learn_method_'
			//+element.options[element.selectedIndex].innerHTML.toLowerCase());
			//elementOption.innerHTML = elementOptionMethod.innerHTML;
			
			break;
		default:
		
			break;
		
	}

}					


function detectLearner(element){
	
	var elementOption = document.getElementById('learner_method_selected');
	
	if(element.options[element.selectedIndex].innerHTML=="SingleClassifierDrift"){
		
		elementOption.style.visibility = "visible";//style="visibility: visible;"	
		elementOption.style.height = "auto";			
		elementOption.style.width = "auto";
	}else{
		elementOption.style.visibility = "hidden";
		elementOption.style.height = "0px";
		elementOption.style.width = "0px";
	}
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

function setCookieElementSelectValue(element){
	
//	alert(" "+element.name);
	//alert("==="+element.options[element.selectedIndex].innerHTML);
	setCookie(element.id,element.options[element.selectedIndex].innerHTML,365);
}


function historicCookieElementValue(elementId, defaultValue){

	var elementCookieHistoric = getCookie(elementId);
	
	if(elementCookieHistoric=="")//{
		elementCookieValue=defaultValue;//=defaultValue;"";
	//}else
	//	elementCookieValue=elementCookieHistoric;
	
	document.getElementById(elementId).value = Base64.decode(elementCookieHistoric);
}


function historicCookieElementInnerHTML(elementId, defaultValue){

	var elementCookieHistoric = getCookie(elementId);
	
	if(elementCookieHistoric=="")
		elementCookieValue=defaultValue;//=defaultValue;"";
	//}else
	//	elementCookieValue=elementCookieHistoric;
	
	//alert(Base64.decode(elementCookieHistoric));
	
	document.getElementById(elementId).innerHTML = Base64.decode(elementCookieHistoric);
}


function setCookieElementValue(element){
	
	setCookie(element.id,Base64.encode(element.value),365);
}

function setCookieElementInnerHTML(element){
	
	setCookie(element.id,Base64.encode(element.innerHTML),365);
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


function resizeTextarea (id) {
  var a = document.getElementById(id);
  a.style.height = 'auto';
  a.style.height = 10+a.scrollHeight+'px';
}

function init() {
  var a = document.getElementsByTagName('textarea');
  for(var i=0,inb=a.length;i<inb;i++) {
     if(a[i].getAttribute('data-resizable')=='true')
      resizeTextarea(a[i].id);
  }
}

//addEventListener('DOMContentLoaded', init);
//window.onload=init();


/*
function attachAutoResizeEvents()
{  	
	// for(i=1;i<=4;i++)
    //{   
    	var txtX=document.getElementById('console');
        var minH=txtX.style.height.substr(0,txtX.style.height.indexOf('px'))
        txtX.onchange=new Function("resize(this,"+minH+")")
        txtX.onkeyup=new Function("resize(this,"+minH+")")
        txtX.onchange(txtX,minH)
    //}
}

function resize(txtX,minH)
{   txtX.style.height = 'auto' // required when delete, cut or paste is performed
    txtX.style.height = txtX.scrollHeight+'px'
    if(txtX.scrollHeight<=minH)
        txtX.style.height = minH+'px'
        
        
        
}
window.onload=attachAutoResizeEvents();

*/

//SetSelectIndex("task", "");
//SetSelectIndex("stream", "");


function loadAllHistoryCookie(){
	
	
	var Form = document.getElementById('formulario');
	for(I = 0; I < Form.length; I++) {
	    var Name = Form[I].getAttribute('name');
	    var Value = Form[I].value;
	    var id = Form[I].id;
	    var type = Form[I].type;
	    
	    switch(type){
	    	case	"select-one":
	    		
	    		defaultValue=0;
	    		
	    		switch(name){
	    			case	"task":
	    			
	    				defaultValue = 1;
	    				break;
	    			case	"learner":
	    			
	    				defaultValue = 1;
	    				break;
	 
	    		}
	    		
	    		historicCookieElementSelectValue(id,defaultValue);
	    		
	    		break;
	    	case	"text":
	
	    		defaultValue="";
	    		
	    		switch(name){
	    			case	"instance_limit":
	    			
	    				defaultValue = 10000;
	    				break;
	    			case	"sample_frequency":
	    			
	    				defaultValue = 10;
	    				break;
	    			case	"mem_check_frequency":
	    			
	    				defaultValue = 10;
	    				break;
	    			case	"drift_length":
	    			
	    				defaultValue = 10;
	    				break;
	    			case	"drift_width":
	    			
	    				defaultValue = 1;
	    				break
	
	    		}
	    
	    		historicCookieElementValue(id,defaultValue);    		
	    		break;
	    	case	"textarea":

				//historicCookieElementValue(id,"")				
				
				//historicCookieElementInnerHTML(id,"");
				
				//alert(getCookie('consoles'));
	    		
	    		//alert(document.getElementById('consoles').innerHTML);
	    		 
	    		break;
	    	case	"checkbox":

				historicCookieCheckbox(id);
	    		 
	    		break;
	    }
	    
	}

}




function getResizeTextarea(elementId){
	
	var w = getCookie(elementId+"-w");
	
	if(w=="")
		w="100px";
			
	document.getElementById(elementId).style.width = w;
	
	var h = getCookie(elementId+"-h");
	
	if(h=="")
		h="100px";
			
	//document.getElementById(elementId).style.height = h;

}


function setResizeTextarea(element){
	
	//setCookie(element.id+"-w",element.style.width,365);
	//setCookie(element.id+"-h",element.style.height,365);
}

function resetResizeTextarea(elementId){
	document.getElementById(elementId).style.width = "auto";
	document.getElementById(elementId).style.height = "auto";
}

function getCoo(elementId){
	
	var elementCookieHistoric = getCookie(elementId);
	
	alert("=="+elementCookieHistoric);//document.getElementById(elementId).value);
	//document.getElementById(elementId).value = elementCookieValue;
	
}

//getResizeTextarea("consoles");

//if(document.getElementById("consoles").innerHTML!="")
//	setCookieElementInnerHTML(document.getElementById("consoles"));

//historicCookieElementValue(id,"");	

loadAllHistoryCookie();

/*
detectLearner(document.getElementById("learner"));

detectLearnerMethod(document.getElementById("learner_method"));

detectDataset(document.getElementById("stream"));*/



function recoveryHistoryCookies(){
	
	var elements = [
				    "ddm_n",
				    "ddm_w",
				    "ddm_o",
				    "eddm_n",
				    "eddm_w",
				    "eddm_o",
				    "stepd_r",
				    "stepd_w",
				    "stepd_m",
				    "adwinmethod_d",
				    "adwinmethod_w",
				    "adwinmethod_r",
				    "adwinchangedetector_a",
				    "adwinchangedetector_r",
				    "ewmachartdm_n",
				    "ewmachartdm_l",
				    "ewmachartdm_r",
				    "ensembledriftdetectionmethods",
				    "ensembledriftdetectionmethods",
				    "ensembledriftdetectionmethods",
				    "hddm_a_test_d",
				    "hddm_a_test_w",
				    "hddm_a_test_t",
				    "hddm_a_test_r",
				    "hddm_w_test_d",
				    "hddm_w_test_w",
				    "hddm_w_test_m",
				    "hddm_w_test_t",
				    "hddm_w_test_r",
				    "pagehinkleydm_n",
				    "pagehinkleydm_d",
				    "pagehinkleydm_l",
				    "pagehinkleydm_a",
				    "pagehinkleydm_r",
				    "geometricmovingaveragedm_n",
				    "geometricmovingaveragedm_l",
				    "geometricmovingaveragedm_a"
				];
					
					
	for (index = 0; index < a.length; ++index) {
    	historicCookieElementValue(a[index],""); 
	}

}



function sendpKill(strURL, method){


	var parameters ="";
	
	document.getElementById('waiting_process').innerHTML='';//'<img src=\'http://gaiaedu.cm-gaia.pt/templates/wait_ax.gif\'>';
	
	//var method= elementObj.value.toUpperCase();

	
	var HttpReq;	
		var d = new Date();
		start_time = formatDate(d);

	
	if (window.XMLHttpRequest)
	{// code for IE7+, Firefox, Chrome, Opera, Safari
		HttpReq=new XMLHttpRequest();
	}
	else
	{// code for IE6, IE5
		HttpReq=new ActiveXObject("Microsoft.XMLHTTP");
	}
	
	HttpReq.withCredentials = false;
	
	var strParameters = content;	 
	 
	if ( method == 'POST'){//create data
		
		HttpReq.open(method, strURL, true);
		
		
		//strParameters = "parameters="+strParameters;
		
	}else if( method == 'PUT'){//update data	//$cmd	=	"pkill -u apache";
	//$output = shell_exec($cmd);
		
		HttpReq.open(method, strURL, true);
		//strParameters = "parameters="+strParameters;
		
	}else if( method == 'DELETE'){//delete data
		
		HttpReq.open(method, strURL, true);
		//strParameters = "parameters="+strParameters;
				
	}else if( method == 'GET'){//delete data
		
		strParameters = "script="+Base64.encode(strParameters);
		HttpReq.open(method, strURL +'?'+ strParameters, true);
		//HttpReq.open(method, strURL, true);
				
	}else if( method == 'HEAD'){//delete data
		
		HttpReq.open(method, strURL, true);
		//strParameters = "parameters="+strParameters;
				
	}else if( method == 'OPTIONS'){//delete data
		
		HttpReq.open(method, strURL, true);
		//strParameters = "parameters="+strParameters;
				
	}else{
		//default
		
		//strParameters = "parameters="+strParameters;
		HttpReq.open(method, strURL +'?'+ strParameters, true);
	}
    

	HttpReq.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	
	
	//alert("Content-Type: "+contentType);
	var content_type="text/html";
	var content_type="text/html";
	
	//HttpReq.setRequestHeader('Content-Type', content_type+";charset=UTF-8");
	//HttpReq.setRequestHeader("Accept",accept+";charset=UTF-8");
	    
    HttpReq.onreadystatechange = function() {
        if (HttpReq.readyState == 4) {
        	
        	switch(HttpReq.status){
        		
        		case	200:
        		
        			
        			/*
					if( document.getElementById('alertmessage').checked ==true )
											alert("Terminou");
										*/
					
        			            			
        			break;
        		case	401:
	
        		
        			alert("401 Unauthorized");
        			
        			break;
        		default:
        			//alert(""+HttpReq.status);
        	}
        }
        
        
	
	}

		HttpReq.send();

	
}


//<div style="float:left;" id="waiting_process"></div>
var start_time;
   
function sendSaveScript(strURL, content, method, email, year){
	
	var parameters ="";
	    
	document.getElementById('waiting_process').innerHTML='<div id="cronometro"></div><img width="160px" src=\'images/cloud_loading_256.gif\'>';
	
	
	keepgoin=true;
	timer();

	//var method= elementObj.value.toUpperCase();

	
	var HttpReq;
	
	if (window.XMLHttpRequest)
	{// code for IE7+, Firefox, Chrome, Opera, Safari
		HttpReq=new XMLHttpRequest();
	}
	else
	{// code for IE6, IE5
		HttpReq=new ActiveXObject("Microsoft.XMLHTTP");
	}
	
	HttpReq.withCredentials = false;
	
	var strParameters = content;	 
	 
	if ( method == 'POST'){//create data
		
		HttpReq.open(method, strURL, true);
		
		
		//strParameters = "parameters="+strParameters;
		
	}else if( method == 'PUT'){//update data
		
		HttpReq.open(method, strURL, true);
		//strParameters = "parameters="+strParameters;
		
	}else if( method == 'DELETE'){//delete data
		
		HttpReq.open(method, strURL, true);
		//strParameters = "parameters="+strParameters;
				
	}else if( method == 'GET'){//delete data
		
		strParameters = "script="+Base64.encode(strParameters);
		HttpReq.open(method, strURL +'?'+ strParameters, true);
		//HttpReq.open(method, strURL, true);
				
	}else if( method == 'HEAD'){//delete data
		
		HttpReq.open(method, strURL, true);
		//strParameters = "parameters="+strParameters;
				
	}else if( method == 'OPTIONS'){//delete data
		
		HttpReq.open(method, strURL, true);
		//strParameters = "parameters="+strParameters;
				
	}else{
		//default
		
		//strParameters = "parameters="+strParameters;
		HttpReq.open(method, strURL +'?'+ strParameters, true);
	}
    

	HttpReq.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	
	
	//alert("Content-Type: "+contentType);
	var content_type="text/html";
	var content_type="text/html";
	
	//HttpReq.setRequestHeader('Content-Type', content_type+";charset=UTF-8");
	//HttpReq.setRequestHeader("Accept",accept+";charset=UTF-8");
	    
    HttpReq.onreadystatechange = function() {
        if (HttpReq.readyState == 4) {
        	
        	switch(HttpReq.status){
        		
        		case	200:
        			
            			
        			break;
        		case	401:
        		
        			alert("401 Unauthorized");
        			
        			break;
        		default:
        			//alert(""+HttpReq.status);
        	}
        }
        
        
	
	}
	


	//if( document.getElementById('message_check').checked ==true )
		HttpReq.send("script="+encodeURIComponent(Base64.encode(strParameters))+'&component=generator&controller=save&tmpl=tmpl');
	//else
	//	HttpReq.send("");
	
	//var aa = HttpReq.getAllResponseHeaders();
		//table_html		

	//var selectize = $('#select-url').selectize();//[].selectize;
	//var aa = selectize.getOption(2)[0];
	
	//alert("ddd="+aa);



				//
}







				function sendMOAREST(strURL, content, method, email, year){
					
					var parameters ="";
					    
					document.getElementById('waiting_process').innerHTML='<div id="cronometro"></div><img width="160px" src=\'images/cloud_loading_256.gif\'>';
					
					
					keepgoin=true;
					timer();

					//var method= elementObj.value.toUpperCase();

					
					var HttpReq;
					
					if (window.XMLHttpRequest)
					{// code for IE7+, Firefox, Chrome, Opera, Safari
						HttpReq=new XMLHttpRequest();
					}
					else
					{// code for IE6, IE5
						HttpReq=new ActiveXObject("Microsoft.XMLHTTP");
					}
					
					HttpReq.withCredentials = false;
					
					var strParameters = content;	 
					 
					if ( method == 'POST'){//create data
						
						HttpReq.open(method, strURL, true);
						
						
						//strParameters = "parameters="+strParameters;
						
					}else if( method == 'PUT'){//update data
						
						HttpReq.open(method, strURL, true);
						//strParameters = "parameters="+strParameters;
						
					}else if( method == 'DELETE'){//delete data
						
						HttpReq.open(method, strURL, true);
						//strParameters = "parameters="+strParameters;
								
					}else if( method == 'GET'){//delete data
						
						strParameters = "script="+Base64.encode(strParameters);
						HttpReq.open(method, strURL +'?'+ strParameters, true);
						//HttpReq.open(method, strURL, true);
								
					}else if( method == 'HEAD'){//delete data
						
						HttpReq.open(method, strURL, true);
						//strParameters = "parameters="+strParameters;
								
					}else if( method == 'OPTIONS'){//delete data
						
						HttpReq.open(method, strURL, true);
						//strParameters = "parameters="+strParameters;
								
					}else{
						//default
						
						//strParameters = "parameters="+strParameters;
						HttpReq.open(method, strURL +'?'+ strParameters, true);
					}
				    

					HttpReq.setRequestHeader("Content-type","application/x-www-form-urlencoded");
					
					
					//alert("Content-Type: "+contentType);
					var content_type="text/html";
					var content_type="text/html";
					
					//HttpReq.setRequestHeader('Content-Type', content_type+";charset=UTF-8");
					//HttpReq.setRequestHeader("Accept",accept+";charset=UTF-8");
					    
				    HttpReq.onreadystatechange = function() {
				        if (HttpReq.readyState == 4) {
				        	
				        	switch(HttpReq.status){
				        		
				        		case	200:
				        		
				        			
				        			if( document.getElementById('alertmessage').checked ==true )
				        				alert("Terminou");
				        			
				        			//returnGetRequestHttp(HttpReq.responseText, 
				            			//idElement,
				            		//	eventRetorn, accept);
				            		
				            		
				            		//alert(HttpReq.statusText);
							        //document.getElementById("contenthtml").innerHTML	=	HttpReq.responseText;
							        var objectArray = JSON.parse(HttpReq.responseText);
							        
							        var pluginArrayArg = new Array();
							        //jsonHtmlTable2="<table>";
							        cols="";
							        rows="";
							          
							        if(objectArray["output"] === undefined ){
							        	
							        }else{
							        	//alert("bruno");
							        }
							        
							        var csv_result="";
							        var z=1;
							        
							        for(i=0; i<objectArray.length;i++){
							        	
							        	
							        	
							        	
							        	if(objectArray[i]['actions']=== undefined ){
							        		
							        	}else{
							        		objectArray[i]['actions'] = "<a href='"+objectArray[i]['actions']+"' target='_blank'><img src='images/raw.png' border='0'></a>";
							        		z++;
							        	}
							        	
							        	
							        	
							        	if(objectArray[i]['Accuracy']=== undefined ){
							        		
							        	}else{
							        		
							        		cols = "<td>"+(i+1)+"</td>"+cols;
							        		rows = "<td>"+objectArray[i]['Accuracy']+"</td>"+rows;
							        		
							        		
							        		var objectArrayInvertConfi = new Object();
							        			objectArrayInvertConfi.script=i;
									        	objectArrayInvertConfi.value = objectArray[i]['Accuracy'];
									        	
									        	pluginArrayArg.push(objectArrayInvertConfi);
							        					        		
							        			//objectArrayInvertConfi = objectArray[i]['Accuracy'];
							        		
							        		//if(csv_result=="")
							        			//csv_result = objectArray[i]['Accuracy'];
							        		//else
							        		//	csv_result = objectArray[i]['Accuracy'] + ";" + csv_result;;
							        			
							        	}
							        	
							        	
							        	
							        	if(objectArray[i]['script']=== undefined ){
							        		
							        	}else{
							        		//objectArray[i]['script'] = "<a href='#' title='"+objectArray[i]['script'].replace("'","\'")+"'>"+z+"</a>";
							        		objectArray[i]['script'] = "<pre>"+objectArray[i]['script']+"</pre>";			        	
							        			
							        	}			        	
							        	
							        }
							        
							        jsonHtmlTable2 = "<table border=1><tr>"+cols+"</tr><tr>"+rows+"</tr></table>";
							        
							        //alert(objectArray.length);	
							        	
							/*
							        var val;
									//var a = ["a", "b", "c"];
									for (val of objectArray) {
										//console.log(val);
										alert(val);
									}	*/
							  
							  
							  		//var jsonHtmlTable2 = ConvertJsonToTable(pluginArrayArg, 'jsonTable2', null, 'Download');
							  		 
							        //alert("json="+jsonHtmlTable2);
							          
							          
							          
							        //[{"script":"EvaluateInterleavedTestThenTrain -l (drift.SingleClassifierDrift -d (STEPD)) -s (ConceptDriftStream -s (ConceptDriftStream  -s  (generators.AgrawalGenerator -f 2) -d  (generators.AgrawalGenerator -f 3) -p 334 -w 1) -d (generators.AgrawalGenerator -f 1) -p 668 -w 1) -i 1000 -f 10 -q 10","Accuracy":"54.20 (+-NaN)","Timer":"0.24 (+-NaN)","Memory":"1.00 (+-NaN)"}]
							        
							        var d = new Date();
									var end_time = formatDate(d);
									
							        var jsonHtmlTable = ConvertJsonToTable(objectArray, 'jsonTable', null, 'Download');
									var contentConsole = document.getElementById("table_html").innerHTML;
									
									document.getElementById("table_html").innerHTML = start_time+"<br>"+jsonHtmlTable+"<br>"+end_time+"<br>"+jsonHtmlTable2+"<br>"+contentConsole;
									
									
				            		
				            		document.getElementById('waiting_process').innerHTML="";
				            		//keepgoin=false;
				            		startover();
				            			
				        			break;
				        		case	401:
				        		
				        			alert("401 Unauthorized");
				        			
				        			break;
				        		default:
				        			//alert(""+HttpReq.status);
				        	}
				        }
				        
				        
					
					}
					
						var d = new Date();
						start_time = formatDate(d);

					//if( document.getElementById('message_check').checked ==true )
						HttpReq.send("script="+encodeURIComponent(Base64.encode(strParameters))+'&email='+encodeURIComponent(Base64.encode(email))+'&year='+encodeURIComponent(Base64.encode(""+year))+"&tmpl=tmpl");
					//else
					//	HttpReq.send("");
					
					//var aa = HttpReq.getAllResponseHeaders();
						//table_html		

					//var selectize = $('#select-url').selectize();//[].selectize;
					//var aa = selectize.getOption(2)[0];
					
					//alert("ddd="+aa);



								//
				}

</script>





				<script type="text/javascript">

//http://www.steamdev.com/zclip/js/ZeroClipboard.swf


/*
$(document).ready(function(){
//http://cin.ufpe.br/~bifm/tools/strings/js/ZeroClipboard.swf


	$('a#copy-destino').zclip({
		path:'http://www.steamdev.com/zclip/js/ZeroClipboard.swf',//'js/ZeroClipboard.swf',
		copy:function(){return $('textarea#destino').val();}
	});

});
*/

$(document).ready(function(){

    $("a#copy-origem").zclip({
        path:'http://www.steamdev.com/zclip/js/ZeroClipboard.swf',//'js/ZeroClipboard.swf',
        //copy:$('textarea#destino').text(),
		copy:function(){return $('textarea#origem').val();},
        beforeCopy:function(){
           
        },
        afterCopy:function(){
           
            $(this).next('.check').show();
        }
    });

});



$(document).ready(function(){

    $("a#copy-consoles").zclip({
        path:'http://www.steamdev.com/zclip/js/ZeroClipboard.swf',//'js/ZeroClipboard.swf',
        //copy:$('textarea#destino').text(),
		copy:function(){return $('textarea#consoles').val();},
        beforeCopy:function(){
           
        },
        
        afterCopy:function(){
           
            $(this).next('.check').show();
        }
    });

});


</script>


				<script>
			var currenthor = 0;
			var currentsec = 0;
			var currentmin = 0;
			var currentmil = 0;
			var keepgoin = false;

			function timer() {

				if (keepgoin) {
					currentmil += 1;

					if (currentmil == 10) {
						currentmil = 0;
						currentsec += 1;
					}

					if (currentsec == 60) {
						currentsec = 0;
						currentmin += 1;
					}

					if (currentmin == 60) {
						currentmin = 0;
						currenthor++;
					}

					Strsec = "" + currentsec;
					Strmin = "" + currentmin;
					Strmil = "" + currentmil;

					if (Strsec.length != 2) {
						Strsec = "0" + currentsec;
					}
					if (Strmin.length != 2) {
						Strmin = "0" + currentmin;
					}

					if (Strmil.length != 2) {
						Strmil = "0" + currentmil;
					}

					if (currenthor.length != 2) {
						Strhor = "0" + currenthor;
					}

					document.getElementById("cronometro").innerHTML = Strhor + ":" + Strmin + ":" + Strsec + ":" + Strmil;

					/*
					 document.display.seconds.value=Strsec
					 document.display.minutes.value=Strmin;
					 document.display.milsecs.value=Strmil;*/

					setTimeout("timer()", 100);
				}
			}

			function startover() {
				keepgoin = false;
				currenthor = 0;
				currentsec = 0;
				currentmin = 0;
				currentmil = 0;
				Strsec = "00";
				Strmin = "00";
				Strmil = "00";
			}

			startover();
			/*
			function ShowDate() {
			var now = new Date();
			var then = now.getFullYear()+'-'+(now.getMonth()+1)+'-'+now.getDay();
			then += ' '+now.getHours()+':'+now.getMinutes();

			alert(now+'\n'+then);
			}
			*/

			//ShowDate();

			function formatDate(date) {
				var year = date.getFullYear(),
				    month = date.getMonth() + 1, // months are zero indexed
				    day = date.getDate(),
				    hour = date.getHours(),
				    minute = date.getMinutes(),
				    second = date.getSeconds(),
				    hourFormatted = hour % 12 || 12, // hour returned in 24 hour format
				    minuteFormatted = minute < 10 ? "0" + minute : minute,
				    morning = hour < 12 ? "am" : "pm";

				return month + "/" + day + "/" + year + " " + hourFormatted + ":" + minuteFormatted + morning;
			}

		</script>




			</div>

		</div>

	</div>
</div>
</div>
</div>
</div>

