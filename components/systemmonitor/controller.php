<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\components\home;

defined('_EXEC') or die();

use moam\core\Framework;
// use moam\core\Application;
// use moam\libraries\core\menu\Menu;
use moam\core\Template;
use moam\core\Properties;
use moam\libraries\core\utils\Utils;
use moam\libraries\core\sys\CPULoad;


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

Template::addHeader(array("tag"=>"script",
    "type"=>"text/javascript",
    "src"=>""
    . $application->getPathTemplate()
    . "/javascript/base64.js"));


Template::addHeader(array(
    "tag" => "script",
    "type" => "text/javascript",
    "src" => "" . PATH_WWW . "templates/default/javascript/json-to-table.js"
));

Framework::import("Utils", "core/utils");
Framework::import("class_CPULoad", "core/sys");

$utils = new Utils();

$df = $utils->getFreeSpace(Properties::getBase_directory_destine($application));
$dt = disk_total_space(Properties::getBase_directory_destine($application));
$du = $dt - $df;

$dp = sprintf('%.2f', ($du / $dt) * 100);

$df = $utils->formatSize($df);
$du = $utils->formatSize($du);
$dt = $utils->formatSize($dt);

$cpuload = new CPULoad();
$cpuload->get_load();
// $cpuload->print_load();
$cpu_du = 0;
$cpu_df = 0;
$cpu_dt = 0;

// echo "CPU load is: ".$cpuload->load["cpu"]."%";

$cpu_dp = round($cpuload->load["cpu"], 2);

function get_memory()
{
    foreach (file('/proc/meminfo') as $ri)
        $m[strtok($ri, ':')] = strtok('');
    return 100 - @round(($m['MemFree'] + $m['Buffers'] + $m['Cached']) / $m['MemTotal'] * 100);
}

$memory_dp = get_memory();

$utils = new Utils();

// $cmd = "TERM=xterm /usr/bin/top n 1 b i";//"ps -aux";
// $cmd = "ps aux";//"ps -aux";
$cmd = "w";
// $cmd = "uptime| sed 's/,//g'| awk '{print $3\" \"$4\" e \"$5\"h\"}'";

// $output = shell_exec($cmd);
$result = $utils->runExternal($cmd);
$output = $result["output"];

// $array_w = explode("\n", $output);

// var_dump($array_w);

// $array_usage = explode(" ", $array_w[0]);

// var_dump($array_usage);

// $data["time"] = $array_usage[1];
// $data["up"] = $array_usage[3]." ".$array_usage[4];
// $data["users"] = $array_usage[6];

// $array_users = explode("\t", $array_w[2]);

// var_dump($array_users);

// exit();

// uptime| sed 's/,//g'| awk '{print $3" "$4" e "$5"h"}'

?>

<pre style="font-family: monospace;font-size: 11px;text-aling:left;max-width: 90%;padding:0px;margin:0px;"><?php 
						
						$cmd = "w";
						// $cmd = "uptime| sed 's/,//g'| awk '{print $3\" \"$4\" e \"$5\"h\"}'";
						
						// $output = shell_exec($cmd);
						$result = $utils->runExternal($cmd);
						$output = $result["output"];
						$output = explode("\n", $output);
						
						echo wordwrap(trim($output[0])."\n".$output[1]."\n".$output[2], 80, "\n");
						

						echo "\n\n";
						
$sysinfo = $utils->getHardwareInfo();

echo wordwrap($sysinfo, 80, "\n");

echo "\n\n";

?></pre>
				

							

							


							<input type="button" class="btn btn-default" value="Usage Summary"
								onclick="javascript:updateRefreshpp(this, 0);" />
							
							<input type="button" class="btn btn-default" value="Process"
								onclick="javascript:updateRefreshpp(this, 1);" />
							
							<input type="button" class="btn btn-default" value="Temp files"
								onclick="javascript:updateRefreshpp(this, 2);" />

							<input type="button" class="btn btn-default" id="buttonrefresh" value="RAM & CPU by cores"
								onclick="javascript: updateRefreshpp(this, 3);" />
					
							
							
							
<div id="containerbody" style="border:0px solid #000000;height:100%;margin-left: -15px;
margin-right: -15px;list-style-type: none;
margin: 0;
overflow-y: scroll;max-height: 400px;" >

							<div id="console_cpu"></div>
							<div id="temp_files"></div>
							<div id="console_process"></div>
		<div id="console_process2" style="width: 100%; max-width: 100%; border: 0px solid #000; text-align:center;">
        	<div id="moamram" style="float:left;width: 20%; max-width: 90%; border: 0px solid #000; text-align:center;"></div>
        	<div id="moamcpu" style="float:left;width: 80%; max-width: 90%; border: 0px solid #000; text-align:center;"></div>
        </div>


</div>







					
					
<script type='text/javascript'>


var buttonCancelTimeOut =  [];
buttonCancelTimeOut.push(true, true, true, true);

var buttonController =  [];
buttonController.push('cpu_tmpl', 'process_tmpl', 'temp_files_tmpl', 'hardwareinfo_tmpl');

var buttonControllerElement =  [];
buttonControllerElement.push('console_cpu', 'console_process', 'temp_files', 'console_process2');

var buttonInterval =  [];
buttonInterval.push(false, false, false, false);

var cancelPOST = false;

function updateRefreshpp(objElement, indexButton)
{
	var label = objElement.value;
	label_pre = label.substring(0,4);
			
	if(label_pre == 'stop')
	{
		objElement.value = label.substring(5);
		buttonCancelTimeOut[indexButton] = true;
	}
	else
	{ 
		objElement.value = 'stop ' + label;
		buttonCancelTimeOut[indexButton] = false;
		refreshPOST(indexButton);
	}
}


function refreshPOST(indexButton)
{

    if(buttonCancelTimeOut[indexButton] == true)
    {    	
    	document.getElementById(buttonControllerElement[indexButton]).innerHTML = '';    
    	cancelPOST = true;
    	    	    		
//     	clearInterval(buttonInterval[indexButton]);
    }
    else
    {	
    	controller = buttonController[indexButton];
        url = 'index.php?component=<?php echo $application->getComponent()?>&controller='+controller+'&task=view&tmpl=tmpl';
        method = 'POST';
        id = buttonControllerElement[indexButton];
        cancelPOST = false;
        
        if(indexButton == 3)
        {
        	sendAjaxRequest2(url, method, id);
        }
        else
        {
        	sendAjaxRequest(url, method, id);
        }
        
//     	buttonInterval[indexButton] = window.setInterval(function() {
		setTimeout(function () {    		
    		refreshPOST(indexButton);
    	}, 2000);
    }
     
}



function sendAjaxRequest2(url, method, id){


	var parameters ="";
	var method = method;//"GET";

	var strURL = url;//'index.php?component=home&controller=cpu_tmpl&task=view&tmpl=true';

	
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

        	if(cancelPOST == true)
        	{
				return;
        	}
        	
        	switch(HttpReq.status){
        		
        		case	200:
        		
        			
        			var html = HttpReq.responseText;
			         

        			var objectArray = JSON.parse(HttpReq.responseText);
        			var imgs = "";
					var create = false;
					var keyname = 'moamram';
					
					for(var i=0; i < objectArray[keyname].length; i++)
					{
						var element =  document.getElementById(keyname+'graph' + i);
						var url = Base64.decode(objectArray[keyname][i]);
						
						if (typeof(element) != 'undefined' && element != null)
						{
						  // exists.
							document.getElementById(keyname+"graph" + i).src = url;
						}else
							{
							create = true;
							imgs = imgs + '<img id="'+keyname+'graph' + i + '" src="' + url + '" />';
						}						
					}
					

					if(create == true)
        			{
						document.getElementById(keyname).innerHTML = imgs;
        			}
        			
					var imgs = "";
					var create = false;
					var keyname = 'moamcpu';
					
					for(var i=0; i < objectArray[keyname].length; i++)
					{
						var element =  document.getElementById(keyname+'graph' + i);
						var url = Base64.decode(objectArray[keyname][i]);
						
						if (typeof(element) != 'undefined' && element != null)
						{
						  // exists.
							document.getElementById(keyname+"graph" + i).src = url;
						}else
							{
							create = true;
							imgs = imgs + '<img id="'+keyname+'graph' + i + '" src="' + url + '" />';
						}						
					}
					

					if(create == true)
        			{
						document.getElementById(keyname).innerHTML = imgs;
        			}

            			
        			break;
        		case	401:
        		
        			alert("401 Unauthorized");
        			
        			break;
        	}
        }
        
	
	}

	HttpReq.send();
	//refreshViewProcess();
// 	eval(callback);

}




function sendAjaxRequest(url, method, id){


	var parameters ="";
	var method = method;//"GET";

	var strURL = url;//'index.php?component=home&controller=cpu_tmpl&task=view&tmpl=true';

	
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

        	if(cancelPOST == true)
        	{
				return;
        	}
        	
        	switch(HttpReq.status){
        		
        		case	200:
        		
        			
        			var html = HttpReq.responseText;
			         

        			var objectArray = JSON.parse(HttpReq.responseText);
			         
					
			        var jsonHtmlTable = ConvertJsonToTable(objectArray, 'jsonTable', null, 'Download');
			        
					document.getElementById(id).innerHTML = "<br>"+jsonHtmlTable+"<br>";	
            			
            			
            			
        			break;
        		case	401:
        		
        			alert("401 Unauthorized");
        			
        			break;
        	}
        }
        
	
	}

	HttpReq.send();
	//refreshViewProcess();
// 	eval(callback);

}




function resizeImage()
{
  // browser resized, we count new width/height of browser after resizing
    var height = window.innerHeight - 370;// || $(window).height();
    
    document.getElementById("containerbody").setAttribute(
	   "style", "border:1px solid #ffffff;margin-left: -15px;  margin-right: -15px;list-style-type: none;  margin: 0;  overflow-y: scroll;max-height: "+height+"px");
}

window.addEventListener("resize", resizeImage);

resizeImage();




</script>
            	
					




