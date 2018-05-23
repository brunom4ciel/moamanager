<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\components\moa;

defined('_EXEC') or die();

use moam\core\Framework;
use moam\core\Application;
use moam\core\Properties;
use moam\core\Template;
use moam\libraries\core\utils\Utils;
use moam\libraries\core\menu\Menu;
use moam\libraries\core\json\JsonFile;
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
Framework::import("JsonFile", "core/json");

Template::addHeader(array(
    "tag" => "script",
    "type" => "text/javascript",
    "src" => "" . $application->getPathTemplate() . "/javascript/base64.js"
));

$utils = new Utils();

$filename = $application->getParameter("filename");
$folder = $application->getParameter("folder");

$data = "";

if ($filename != null) {

    $filename = Properties::getBase_directory_destine($application) . $application->getUser() . DIRECTORY_SEPARATOR . $folder . 
    // .DIRECTORY_SEPARATOR
    $filename; // .$extension_scripts;

    $task = $application->getParameter("task");

    if ($task == "remove") {

        // exit($filename);
        if (is_file($filename)) {
            // exit("fff");
            unlink($filename);

            header("Location: " . PATH_WWW . "?component=" . $application->getComponent() . "&controller=" . $application->getController() . "&folder=" . substr($folder, 0, strrpos($folder, "/")) . "/");
        }
    }
}

if ($folder == null) {

    $files_list = $utils->getListElementsDirectory1(Properties::getBase_directory_destine($application) . $application->getUser() . DIRECTORY_SEPARATOR, array(
        "txt",
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
            "log"
        ));
}

foreach ($files_list as $key => $element) {

    if ($element["type"] == "dir") {
        if ($element["name"] == "scripts") {
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

// Framework::includeLib("JsonFile.php");
function getPercentProcessFile($filename, $folder = "")
{
    global $application;

    $jsonfile = new JsonFile();

    $jsonfile->open($filename);

    $data = $jsonfile->getData();

    $dirStorage = Properties::getBase_directory_destine($application) . $application->getUser() . DIRECTORY_SEPARATOR . $folder;

    $length_data = count($data);
    $length_process = 0;

    if ($length_data > 0) {

        foreach ($data as $key => $item) {

            if (is_array($item)) {

                foreach ($item as $key2 => $item2) {

                    /*
                     * if($key2 == "process"){
                     *
                     * if($item2 == "true"){
                     * $length_process++;
                     * }
                     *
                     * }
                     */

                    if ($key2 == "command") {

                        $command = $item2;
                        $filename = substr($command, strrpos($command, ">") + 1);
                        $filename = trim($filename);

                        $filename = substr($filename, strrpos($filename, "/") + 1);
                        $filename = trim($filename);

                        // echo $dirStorage.$filename."<br>";

                        if (file_exists($dirStorage . $filename)) {
                            // echo $dirStorage.$filename."\n";
                            $length_process ++;
                        } else {}
                    }
                }
            }
        }

        $result = ($length_process * 100) / $length_data;
    } else {

        $result = false;
    }

    return array(
        "percent" => $result,
        "fail" => ($length_data - $length_process),
        "length" => $length_data
    );
}

$dirstorage = Properties::getBase_directory_destine($application) . $application->getUser() . DIRECTORY_SEPARATOR . $folder;

?>


<div class="content content-alt">
	<div class="container" style="width: 90%">
		<div class="row">
			<div class="">

				<div class="card" style="width: 100%">
					<div class="page-header">
						<h1>
							<a href="<?php echo $_SERVER['REQUEST_URI']?>"><?php echo TITLE_CONTROLLER_REPORT?></a>
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
								<input type="hidden" name="folder" value="<?php echo $folder;?>" />




								<div id="container">


									<input type="button" value="Return" name="return"
										onclick="javascript: returnPage();" /><br> <a
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
											<th style="width: 70%;">Name</th>
											<th>Size</th>
											<th>DateTime</th>
										</tr>
<?php

foreach ($files_list as $key => $element) {

    if ($element["type"] == "dir") {

        echo "<tr><td><img width='24px' align='middle' src='" . $application->getPathTemplate() . "/images/icon-folder.png'/> " . " " . "<a href='?component=" . $application->getParameter("component") . "&controller=" . $application->getParameter("controller") . "&folder=" . (empty($folder) ? "" : $folder) . $element["name"] . "/&task=open'>" . $element["name"] . "</a> </td><td>" . $element["size"] . "</td><td>" . $element["datetime"] . "</td></tr>";
    }
}

foreach ($files_list as $key => $element) {

    // $element["name"] = substr($element["name"],0,strrpos($element["name"], "."));

    if ($element["type"] != "dir") {

        $result = getPercentProcessFile(Properties::getBase_directory_destine($application) . $application->getUser() . DIRECTORY_SEPARATOR . $folder . $element["name"], $folder);

        // var_dump($result);
        // exit();

        $percentProcess = $result["percent"];

        $fail = $result["fail"];

        $processados = $result["length"] - $result["fail"];

        $percentLength = $result["length"];

        // if($percentProcess==false){
        // $percentProcess_msg = "error";
        // }else{

        if ($fail > 0) {
            $percentProcess_msg = $percentProcess . "% " . $processados . " of " . $percentLength;
        } else {
            $percentProcess_msg = $percentProcess . "%";
        }

        // }

        echo "<tr><td> <img align='middle' width='32px' src='" . $application->getPathTemplate() . "/images/icon-report.png'/>  " . "<a href='?component=" . $application->getParameter("component") . "&controller=reportview&filename=" . $element["name"] . "&folder=" . $application->getParameter("folder") . "'>" . $element["name"] . "</a> [" . $percentProcess_msg . "]";

        // $percentProcess=80;

        if ($percentProcess != 100) {

            // if($percentProcess==false){
            echo "<a title='Continue process' href='" . "?component=" . $application->getParameter("component") . "&controller=" . $application->getParameter("controller") . "&filename=" . $element["name"] . "&folder=" . $application->getParameter("folder") . "&task=remove'>" . "<img align='middle' width='24px' src='" . $application->getPathTemplate() . "/images/icon-remove.gif' border='0'></a> ";

            // }else{
            echo "<a title='Continue process' href='" . "?component=" . $application->getParameter("component") . "&controller=run" . "&filename=" . $element["name"] . "&folder=" . $application->getParameter("folder") . "&task=continue' onclick='javascript: //sendContinue(\"\");'>" . "<img align='middle' width='24px' src='" . $application->getPathTemplate() . "/images/icon-restart.png' border='0'></a> ";

            // }
        }

        echo "</td><td>" . $element["size"] . "</td><td>" . $element["datetime"] . "</td></tr>";
    }
}

?>		
	</table>
							
							</form>



						</div>

					</div>

				</div>
			</div>
		</div>
	</div>
</div>



<script type='text/javascript'>

function returnPage(){
	//window.history.go(-1);

	<?php

$folder = $application->getParameter("folder");

$levels = explode("/", $folder);

$folder_ = "";

for ($i = 0; $i < (count($levels) - 2); $i ++) {
    $folder_ .= $levels[$i] . "/";
}

// echo $folder_;
?>
			
	window.location.href='?component=<?php echo $application->getParameter("component");?>'
			+'&controller=report'
			+'&task=open'
			+'&folder=<?php echo $folder_;?>';
			
}

function sendContinue(filename){
	
	sendMOAREST('<?php echo PATH_WWW?>index.php',filename,'POST');
	
}

   
function sendMOAREST(strURL, filename_from, method){
	
	var parameters ="";
	    


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

	var content='';
	
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

    	    
	
	var component = '<?php echo $application->getParameter("component")?>';
	var controller = 'run';
	var task = 'continue';
	var filename = filename_from;//
	var folder = '<?php echo $application->getParameter("folder")?>';
	
	var parallel_process = 1;
	
	var dirstorage = '<?php echo $dirstorage;?>';

	//if( document.getElementById('message_check').checked ==true )
	HttpReq.send(""
				+'&parallel_process='+encodeURIComponent(Base64.encode(parallel_process))
				+'&folder='+encodeURIComponent(Base64.encode(folder))
				+'&dirstorage='+encodeURIComponent(Base64.encode(dirstorage))
				+'&component='+encodeURIComponent(component)
				+'&controller='+encodeURIComponent(controller)
				+'&task='+encodeURIComponent(task)
				+'&filename='+encodeURIComponent(Base64.encode(filename)));
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

