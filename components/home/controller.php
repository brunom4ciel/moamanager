<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\components\home;

use moam\core\Framework;
use moam\core\Application;
use moam\libraries\core\menu\Menu;
use moam\core\Template;
use moam\libraries\core\utils\Utils;

if (! class_exists('Application')) {
    $application = Framework::getApplication();
}

if (! $application->is_authentication()) {
    $application->alert("Error: you do not have credentials.");
}

Framework::import("menu", "core/menu");

Framework::import("Utils", "core/utils");
// Framework::import("class_CPULoad", "core/sys");

Template::addHeader(array("tag"=>"script",
    "type"=>"text/javascript",
    "src"=>""
    . $application->getPathTemplate()
    . "/javascript/base64.js"));

$utils = new Utils();

if (! class_exists('Menu')) {
    $menu = new Menu();
}

Template::addHeader(array(
    "tag" => "script",
    "type" => "text/javascript",
    "src" => "" . PATH_WWW . "templates/default/javascript/json-to-table.js"
));


// Template::addHeader(array("tag"=>"link",
// "type"=>"text/css",
// "rel"=>"stylesheet",
// "href"=>"" . PATH_WWW . "templates/default/css/style2.css"));

// Template::setTitle("Teste");

// $menu = Framework::getInstance("Menu");

// $application = Framework::getApplication();

$time = $application->getParameter("time");

if (! empty($time))
    sleep($time);

?>
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
			
																
																
						<div id="usage_machine" style="text-align: left;">
								<pre style="font-family: monospace,monospace;font-size: 11px;text-aling:left;">

<?php 
						
						$cmd = "w";
						// $cmd = "uptime| sed 's/,//g'| awk '{print $3\" \"$4\" e \"$5\"h\"}'";
						
						// $output = shell_exec($cmd);
						$result = $utils->runExternal($cmd);
						$output = $result["output"];
						$output = explode("\n", $output);
						
						echo trim($output[0])."\n".$output[1]."\n".$output[2];
						

						
						?>	
						
</pre>

<pre style="font-family: monospace,monospace;font-size: 11px;text-aling:left;">

<?php 
$sysinfo = $utils->getHardwareInfo();

echo $sysinfo;

$utils->getHardwareKernelVersion();
?>	

</pre>
							</div>
							
						
					
					
						
<div id="console_process" style="width: 100%; max-width: 100%; border: 0px solid #000; text-align:center;">
	<div id="ram" style="float:left;width: 20%; max-width: 90%; border: 0px solid #000; text-align:center;"></div>
	<div id="cpu" style="float:left;width: 80%; max-width: 90%; border: 0px solid #000; text-align:center;"></div>
</div>


<br>
<input type="button" id="buttonrefresh" value="Auto Refresh"
								onclick="javascript:if(this.value=='Stop Auto Refresh'){this.value='Auto Refresh';cancelViewHardwareInfo=false; }else{ this.value='Stop Auto Refresh';cancelViewHardwareInfo=true;refreshViewHardwareinfo(); }" />
					
					
					
<script type='text/javascript'>




// var cancelViewCPU=true;
// var cancelViewProcess=true;
var cancelViewHardwareInfo=true;
var timeoutajust = 500;

function refreshViewHardwareinfo(){
	
	setTimeout(function () {
        // Do Something Here
        // Then recall the parent function to
        // create a recursive loop.
        if(cancelViewHardwareInfo==true){
            
        	url = 'index.php?component=home&controller=hardwareinfo_tmpl&task=view&tmpl=tmpl';
            method = 'POST';
            id = 'console_process';
            
        	sendAjaxRequest(url, method, id, 'refreshViewHardwareinfo();');
        	timeoutajust = 1000;
        }
        	
    }, timeoutajust);
    
	
}



function sendAjaxRequest(url, method, id, callback){


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
        	
        	switch(HttpReq.status){
        		
        		case	200:
        		
        			
        			var html = HttpReq.responseText;
			         

        			var objectArray = JSON.parse(HttpReq.responseText);
        			var imgs = "";
					var create = false;
					var keyname = 'ram';
					
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


					if(create == true)
        			{
						document.getElementById('ram').innerHTML = imgs;
        			}
        			
					var imgs = "";
					var create = false;
					var keyname = 'cpu';
					
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
        			
//         			if(typeof(objectArray[0]) == 'object')
//                     {
//                         headers = array_keys(objectArray[0]);


//                         alert("ok");
                        
//                         for (i = 0; i < headers.length; i++){
//                             alert(Base64.decode(headers[i]));
//                         }
                        
//                     }

                    
			         
					
			       // var jsonHtmlTable = ConvertJsonToTable(objectArray, 'jsonTable', null, 'Download');
			        
// 					document.getElementById(id).innerHTML = "<br>"+html+"<br>";	
            			
            			
            			
        			break;
        		case	401:
        		
        			alert("401 Unauthorized");
        			
        			break;
        	}
        }
        
	
	}

	HttpReq.send();
	//refreshViewProcess();
	eval(callback);

}




refreshViewHardwareinfo();
cancelViewHardwareInfo=true;
document.getElementById("buttonrefresh").value='Stop Auto Refresh';

</script>

										
										
										
										</div>

					</div>

				</div>
			</div>
		</div>
	</div>
</div>

