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



?>


							<div class="page-header">
        						<h1>
        							<a href="<?php echo $_SERVER['REQUEST_URI']?>"><?php echo TITLE_COMPONENT_BUILD_MOA;?></a>
        						</h1>
        					</div>
        					
        					
<pre style="font-family: monospace,monospace;font-size: 11px;text-aling:left;overflow-wrap: break-word;padding:0px;">

click to new build MOA binary version.

</pre>

<input type="button" class="btn btn-success" id="buttonubuild" value="Automatically Build MOA and Deploy"
onclick="javascript: automaticBuild(this); " />


					
					
<div id="console_process2" style="width: 100%; max-width: 100%; border: 0px solid #000; text-align:left;">
	<pre id="console_process" style="width: 100%; max-width: 90%; border: 0px solid #000; font-family: monospace,monospace;font-size: 11px;text-aling:left;overflow-wrap: break-word;padding:0px;"></pre>
</div>	


<br>

									<div style="float: right; padding-left: 10px">
									
											<input type="button" class="btn btn-default"
                							onclick="javascript: window.location.href='?component=settings';"
                							name="cancel" value="Return" />
									</div>
									

		
<script>


function automaticBuild(elementId)
{
         
 	url = 'index.php?component=settings&controller=buildmoa_tmpl&task=build&tmpl=tmpl';
    method = 'POST';
    id = 'console_process';
    document.getElementById(id).innerHTML = "Wait. It may take a few minutes."; 
 	sendAjaxRequest(url, method, id);
 
	
}


function sendAjaxRequest(url, method, id){


	var parameters ="";
	var method = method;//"GET";

	var strURL = url;//'index.php?component=home&controller=cpu_tmpl&task=view&tmpl=true';

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
	
	//HttpReq.setRequestHeader('Content-Type', content_type+";charset=UTF-8");
	//HttpReq.setRequestHeader("Accept",accept+";charset=UTF-8");
	    
    HttpReq.onreadystatechange = function() {
        if (HttpReq.readyState == 4) {
        	
        	switch(HttpReq.status){
        		
        		case	200:
        		
        			
        			var html = HttpReq.responseText;
			    
					document.getElementById(id).innerHTML = html;
            			
        			break;
        		case	401:
        		
        			alert("401 Unauthorized");
        			
        			break;
        	}
        }
        
	
	}

	HttpReq.send();

}


</script>
