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
use moam\core\Properties;
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

$dirProcess = Properties::getBase_directory_destine_exec()
//.$application->getUser()
//.DIRECTORY_SEPARATOR
;

/* gets the data from a URL */
function get_data($url) {
    $ch = curl_init();
    $timeout = 5;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

function getKeyValue($key, $str)
{
    $result = "";
    
    if(strpos($str, $key) === FALSE)
    {
        
    }
    else 
    {
        $str = substr($str, strrpos($str, $key)+strlen($key));
        $str = substr($str, strpos($str, "'")+1);
        $str = substr($str, strpos($str, "'")+1);       
        $result = substr($str, 0, strpos($str, "'"));
    }
    
      
    return $result;
}

$url = "https://raw.githubusercontent.com/brunom4ciel/moamanager/master/index.php";

$html_page_remote_github = get_data($url);

$moamanager_remote_version = getKeyValue('MOAMANAGER_VERSION', $html_page_remote_github);
$moamanager_remote_releases = getKeyValue('MOAMANAGER_RELEASES', $html_page_remote_github);
$moa_remote_version = getKeyValue('MOA_VERSION', $html_page_remote_github);
$statistical_tests_remote_version = getKeyValue('STATISTICAL_TESTS_VERSION', $html_page_remote_github);

$button_show_moamanager = false;

if($moamanager_remote_version == "")
{
    $moamanager_remote_version = "Error while querying remote version. Try again later.";
}
else 
{    
    $button_show_moamanager = true;//$utils->compareVersion(MOAMANAGER_VERSION, $moamanager_remote_version);
}


$button_show_moa = false;

if($moa_remote_version == "")
{
    $moa_remote_version = "Error while querying remote version. Try again later.";
}
else
{
    $button_show_moa = true;//$utils->compareVersion(MOA_VERSION, $moa_remote_version);
}


$button_show_statistical_version = false;

if($statistical_tests_remote_version == "")
{
    $statistical_tests_remote_version = "Error while querying remote version. Try again later.";
}
else
{
    $button_show_statistical_version = true;//$utils->compareVersion(STATISTICAL_TESTS_VERSION, $statistical_tests_remote_version);
}

?>


					<div class="page-header">
						<h1><a href="<?php echo $_SERVER['REQUEST_URI']?>"><?php echo TITLE_COMPONENT_SOFTWARE_UPDATE;?></a></h1>
					</div>

<fieldset style="border:1px solid #ccc;padding:5px;margin:5px;">
<pre style="font-family: monospace,monospace;font-size: 11px;text-aling:left;overflow-wrap: break-word;padding:0px;">
MOAManager Local Version: <?php echo MOAMANAGER_VERSION;?>

MOAManager Remote Version: <?php echo $moamanager_remote_version;?>

MOAManager Remote Releases: <?php echo $moamanager_remote_releases;?>


MOA Local Version: <?php echo MOA_VERSION;?>

MOA Remote Version: <?php echo $moa_remote_version;?>


Statistical Tests Local Version: <?php echo STATISTICAL_TESTS_VERSION;?>

Statistical Tests Remote Version: <?php echo $statistical_tests_remote_version;?>


Download details:
*only download repository.

For more detail about updates, please visit page <a href="https://github.com/brunom4ciel/moamanager/" target="_blank">https://github.com/brunom4ciel/moamanager/</a>

</pre>

<?php 
if(is_dir($dirProcess . "repository" . DIRECTORY_SEPARATOR . "moamanager"))
{
    if(file_exists($dirProcess . "repository" . DIRECTORY_SEPARATOR . "moamanager" . DIRECTORY_SEPARATOR . "index.php"))
    {
        $content = file_get_contents($dirProcess . "repository" . DIRECTORY_SEPARATOR . "moamanager" . DIRECTORY_SEPARATOR . "index.php");
        
        $moamanager_local_repository_version = getKeyValue('MOAMANAGER_VERSION', $content);
        $moamanager_local_repository_releases = getKeyValue('MOAMANAGER_RELEASES', $content);        
        
    ?>
    <input type="button" class="btn btn-danger" id="buttonclean" value="Clean Local Repository [Release <?php echo $moamanager_local_repository_releases?>]"
    onclick="javascript: automaticCleanRepository(this); " /> * Repository exists in disk local. Clean repository local before to download of latest repository.
<?php 
   }   
}else 
{
?>
<input type="button" class="btn btn-success" id="buttondownload" value="Download Repository"
onclick="javascript: automaticDownload(this); " />
<?php }?>

</fieldset>
<div id="console_process2" style="width: 100%; max-width: 100%; border: 0px solid #000; text-align:left;">
	<pre id="console_process_download" style="width: 100%; max-width: 90%; border: 0px solid #000; font-family: monospace,monospace;font-size: 11px;text-aling:left;overflow-wrap: break-word;padding:0px;"></pre>
</div>


<fieldset style="border:1px solid #ccc;padding:5px;margin:5px;">
<pre style="font-family: monospace,monospace;font-size: 11px;text-aling:left;overflow-wrap: break-word;padding:0px;">
MOAManager Local Version: <?php echo MOAMANAGER_VERSION;?>

MOAManager Remote Version: <?php echo $moamanager_remote_version;?>


MOA Local Version: <?php echo MOA_VERSION;?>

MOA Remote Version: <?php echo $moa_remote_version;?>


Statistical Tests Local Version: <?php echo STATISTICAL_TESTS_VERSION;?>

Statistical Tests Remote Version: <?php echo $statistical_tests_remote_version;?>


Update details:
*does not remove the files  core/properties.php and includes/defines.php in path moamanager of /var/www/html/moamanager/
*does not change the data of datasets.
*does not change files in workspace of users.
<b>*change files in path moa of /opt/moamanager/moa/src.</b>
<b>*change files in path moa of /opt/moamanager/moa/lib.</b>
<b>*change files in path moa of /opt/moamanager/moa/MANIFEST.MF.</b>
*does not change files in path bin of /opt/moamanager/moa/bin/.
<b>*change files in path statistical of /opt/moamanager/statistical/.</b>
</pre>
<?php 
if(is_dir($dirProcess . "repository" . DIRECTORY_SEPARATOR . "moamanager"))
{
?>
<input type="button" class="btn btn-info" id="buttonupdate" value="Update All Softwares"
onclick="javascript: automaticDownload(this); " />
<?php }?>

</fieldset>
<div id="console_process2" style="width: 100%; max-width: 100%; border: 0px solid #000; text-align:left;">
	<pre id="console_process_update" style="width: 100%; max-width: 90%; border: 0px solid #000; font-family: monospace,monospace;font-size: 11px;text-aling:left;overflow-wrap: break-word;padding:0px;"></pre>
</div>

<?php 
if($button_show_moamanager)
{
?>
<fieldset style="border:1px solid #ccc;padding:5px;margin:5px;">
<pre style="font-family: monospace,monospace;font-size: 11px;text-aling:left;overflow-wrap: break-word;padding:0px;">
MOAManager Local Version: <?php echo MOAMANAGER_VERSION;?>

MOAManager Remote Version: <?php echo $moamanager_remote_version;?>


Update details:
*does not remove the files  core/properties.php and includes/defines.php in path moamanager of /var/www/html/moamanager/
*does not change the data of datasets.
*does not change files in workspace of users.
</pre>
<?php 
if(is_dir($dirProcess . "repository" . DIRECTORY_SEPARATOR . "moamanager"))
{
?>
<input type="button" class="btn btn-warning" id="buttonupdate1" value="Update Only MOAManager"
onclick="javascript: automaticUpdateMOAManager(this); " />
<?php }?>
</fieldset>
<div id="console_process2" style="width: 100%; max-width: 100%; border: 0px solid #000; text-align:left;">
	<pre id="console_process_update_moam" style="width: 100%; max-width: 90%; border: 0px solid #000; font-family: monospace,monospace;font-size: 11px;text-aling:left;overflow-wrap: break-word;padding:0px;"></pre>
</div>
<?php     
}
?>

<?php 
if($moa_remote_version)
{
?>
<fieldset style="border:1px solid #ccc;padding:5px;margin:5px;">
<pre style="font-family: monospace,monospace;font-size: 11px;text-aling:left;overflow-wrap: break-word;padding:0px;">
MOA Local Version: <?php echo MOA_VERSION;?>

MOA Remote Version: <?php echo $moa_remote_version;?>


Update details:
*does not change files in path moamanager of /var/www/html/moamanager/.
*does not change files in path statistical of /opt/moamanager/statistical/.
*does not change the data of datasets.
*does not change files in workspace of users.
<b>*change files in path moa of /opt/moamanager/moa/src.</b>
<b>*change files in path moa of /opt/moamanager/moa/lib.</b>
<b>*change files in path moa of /opt/moamanager/moa/MANIFEST.MF.</b>
*does not change files in path bin of /opt/moamanager/moa/bin/.
</pre>
<?php 
if(is_dir($dirProcess . "repository" . DIRECTORY_SEPARATOR . "moamanager"))
{
?>
<input type="button" class="btn btn-warning" id="buttonupdate2" value="Update Only MOA"
onclick="javascript: automaticUpdateMOA(this); " />
<?php }?>
</fieldset>
					
<div id="console_process2" style="width: 100%; max-width: 100%; border: 0px solid #000; text-align:left;">
	<pre id="console_process_update_moa" style="width: 100%; max-width: 90%; border: 0px solid #000; font-family: monospace,monospace;font-size: 11px;text-aling:left;overflow-wrap: break-word;padding:0px;"></pre>
</div>	
<?php     
}
?>

<?php 
if($statistical_tests_remote_version)
{
?>
<fieldset style="border:1px solid #ccc;padding:5px;margin:5px;">
<pre style="font-family: monospace,monospace;font-size: 11px;text-aling:left;overflow-wrap: break-word;padding:0px;">
Statistical Tests Local Version: <?php echo STATISTICAL_TESTS_VERSION;?>

Statistical Tests Remote Version: <?php echo $statistical_tests_remote_version;?>


Update details:
*does not change files in path moamanager of /var/www/html/moamanager/.
*does not change files in path moa of /opt/moamanager/moa/.
*does not change the data of datasets.
*does not change files in workspace of users.
<b>*change files in path statistical of /opt/moamanager/statistical/.</b>
</pre>
<?php 
if(is_dir($dirProcess . "repository" . DIRECTORY_SEPARATOR . "moamanager"))
{
?>
<input type="button" class="btn btn-warning" id="buttonupdate3" value="Update Only Statistical Tests"
onclick="javascript: automaticUpdateStatisticalTests(this); " />
<?php }?>
</fieldset>
<div id="console_process2" style="width: 100%; max-width: 100%; border: 0px solid #000; text-align:left;">
	<pre id="console_process_update_statistical" style="width: 100%; max-width: 90%; border: 0px solid #000; font-family: monospace,monospace;font-size: 11px;text-aling:left;overflow-wrap: break-word;padding:0px;"></pre>
</div>
<?php     
}
?>
					
					
	


					<div style="text-align: right; display: block;">

						<input type="button" class="btn btn-default"
							onclick="javascript: window.location.href='?component=settings';"
							name="cancel" value="Return" />

					</div>
					

		
<script>



function automaticCleanRepository(elementId)
{
         
 	url = 'index.php?component=settings&controller=softwaredownloadclean_tmpl&tmpl=tmpl';
    method = 'POST';
    id = 'console_process_download';
    this.disabled = true;
    //disabledButtons();
    
    document.getElementById(id).innerHTML = "Wait. It may take a few minutes."; 
 	sendAjaxRequest(url, method, id);
	
}

function automaticUpdateMOAManager(elementId)
{
         
 	url = 'index.php?component=settings&controller=softwareupdatemoam_tmpl&tmpl=tmpl';
    method = 'POST';
    id = 'console_process_update_moam';
    
    this.disabled = true;
    
    document.getElementById(id).innerHTML = "Wait. It may take a few minutes."; 
 	sendAjaxRequest(url, method, id);
	
}
function automaticUpdateMOA(elementId)
{
         
 	url = 'index.php?component=settings&controller=softwareupdatemoa_tmpl&tmpl=tmpl';
    method = 'POST';
    id = 'console_process_update_moa';
    
//     disabledButtons();
this.disabled = true;
    
    document.getElementById(id).innerHTML = "Wait. It may take a few minutes."; 
 	sendAjaxRequest(url, method, id);
	
}
function automaticUpdateStatisticalTests(elementId)
{
         
 	url = 'index.php?component=settings&controller=softwareupdatestatisticaltests_tmpl&tmpl=tmpl';
    method = 'POST';
    id = 'console_process_update_statistical';
    this.disabled = true;
//     disabledButtons();
    
    document.getElementById(id).innerHTML = "Wait. It may take a few minutes."; 
 	sendAjaxRequest(url, method, id);
	
}

function automaticUpdate(elementId)
{
         
 	url = 'index.php?component=settings&controller=softwareupdate_tmpl&tmpl=tmpl';
    method = 'POST';
    id = 'console_process_update';
    this.disabled = true;
//     disabledButtons();
    
    document.getElementById(id).innerHTML = "Wait. It may take a few minutes."; 
 	sendAjaxRequest(url, method, id);
	
}

function automaticDownload(elementId)
{
         
 	url = 'index.php?component=settings&controller=softwaredownload_tmpl&tmpl=tmpl';
    method = 'POST';
    id = 'console_process_download';
    this.disabled = true;
    //disabledButtons();
    
    document.getElementById(id).innerHTML = "Wait. It may take a few minutes."; 
 	sendAjaxRequest(url, method, id);
	
}

// function disabledButtons()
// {
// 	document.getElementById('buttondownload').disabled = true;
// 	document.getElementById('buttonupdate0').disabled = true;
// 	document.getElementById('buttonupdate1').disabled = true;
//     document.getElementById('buttonupdate2').disabled = true;
//     document.getElementById('buttonupdate3').disabled = true;
// }


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
