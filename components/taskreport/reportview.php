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
// use moam\core\Application;
use moam\core\Properties;
use moam\core\Template;
use moam\libraries\core\utils\Utils;
// use moam\libraries\core\menu\Menu;
use moam\libraries\core\json\JsonFile;
use moam\libraries\core\date\DateTimeFormats;
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

Framework::import("Utils", "core/utils");
Framework::import("JsonFile", "core/json");
Framework::import("DateTimeFormats", "core/date");

Template::addHeader(array(
    "tag" => "script",
    "type" => "text/javascript",
    "src" => "" . $application->getPathTemplate() . "/javascript/base64.js"
));

$utils = new Utils();

$filename = $application->getParameter("filename");
$folder = $application->getParameter("folder");
$id = $application->getParameter("id");
// $dirScriptsName = "scripts";

// exit("error");
$data = "";

if ($filename != null) {

    $utils = new Utils();

    $filename = PATH_USER_WORKSPACE_STORAGE . $folder . 
    // .DIRECTORY_SEPARATOR
    $filename;
    // .$extension_scripts
    

    $task = $application->getParameter("task");

    if ($task == "save") {

        /*
         * $data = $application->getParameter("data");
         * $utils->setContentFile($filename, $data);
         *
         * $filenamenew = Properties::getBase_directory_destine($application)
         * .$application->getUser()
         * //.DIRECTORY_SEPARATOR
         * //.$dirScriptsName
         * .DIRECTORY_SEPARATOR
         * .$folder
         * //.DIRECTORY_SEPARATOR
         * .$application->getParameter("filenamenew");
         *
         * if($application->getParameter("filenamenew")
         * != $application->getParameter("filename")){
         *
         * if(file_exists($filename)){
         *
         * if(file_exists($filenamenew.$extension_scripts)){
         *
         * while(file_exists($filenamenew.$extension_scripts)){
         * $filenamenew = "copy-".$filenamenew;
         * }
         *
         * }
         *
         * rename($filename, $filenamenew.$extension_scripts);
         *
         * App::setParameter("filename", substr($filenamenew,strrpos($filenamenew,"/")+1,strrpos($filenamenew,".")));
         *
         * }
         *
         * }
         */
    } else {

        if ($task == "remove") {

            // Framework::includeLib("JsonFile.php");

            $jsonfile = new JsonFile();
            $jsonfile->open($filename);

            $data = $jsonfile->getDataKeyValue("id", $id);

            if (isset($data["id"])) {

                $jsonfile->removeDataKeyValue("id", $id);

//                 $jsonfile->save();

                $application->redirect(PATH_WWW . "?component=" . $application->getComponent()
                    . "&controller=" . $application->getController() 
                    . "&filename=" . basename($filename) 
                    . "&folder=" . $folder . "");
            }

            // if(file_exists($filename)){

            // unlink($filename);
            // header("Location: ".PATH_WWW."?component="
            // .App::getComponent()
            // ."&controller=files");

            // }
        } else {

            if ($task == "new") {

                // exit($filename);

                $data = "";
                $utils->setContentFile($filename, $data);
            } else {

                // $data = $utils->getContentFile($filename);
            }
        }
    }
}

?>


							<div class="page-header">
        						<h1>
        							<a href="<?php echo $_SERVER['REQUEST_URI']?>">Read Report</a>
        						</h1>
        					</div>
        					

								<form method="POST"
								action="<?php echo $_SERVER['PHP_SELF'];?>#save" name="saveform"
								>								
								<input type="button"  class="btn btn-default"
									value="Script All" name="script"
									onclick="javascript: ScriptView('all');" /> <input
									type="button"  class="btn btn-default" value="Script Processed" name="script2"
									onclick="javascript: ScriptView('processed');" /> <input
									type="button"  class="btn btn-default" value="Script Unprocessed" name="script3"
									onclick="javascript: ScriptView('unprocessed');" /><br>

  <?php echo $folder. $application->getParameter("filename");?><br>
								<table border='1' id="report_view" style="width: 100%;">
									<tr>
										<th style="width: 40px;">Id</th>
										<th style="width: 40%;">Script
										
										<th>Process</th>
										<th>Start Time</th>
										<th>End Time</th>
										<th>Diff</th>
									</tr>  
    									
	
							

<?php

$files_list = $utils->getListElementsDirectory1(PATH_USER_WORKSPACE_STORAGE . $folder, 
    // .DIRECTORY_SEPARATOR
    array(
        "txt"
    ));

$dir = PATH_USER_WORKSPACE_STORAGE . $folder;

foreach ($files_list as $key => $element) {

    if ($element["type"] == "dir") {
        if ($element["name"] == "scripts") {
            unset($files_list[$key]);
        }
    } else {

        $filename_ = $dir . $files_list[$key]["name"];

        // $filename_ = substr($command,strrpos($command,">")+1);
        // $filename_ = trim($filename_);

        $filename_ = substr($filename_, strrpos($filename_, "/") + 1);
        $filename_ = trim($filename_);

        // echo $dir.$filename_."<br>";

        if (file_exists($dir . $filename_)) {

            // echo $filename_." ---sim<br>";

            $id = substr($filename_, 0, 4); // strrpos($filename_,"-"));
            $id = trim($id);

            $jsonfile = new JsonFile();

            $jsonfile->open($filename);

            // if(is_writable($filename)){

            // echo "sim";
            // }else{
            // echo "nao";
            // }

            // echo $filename."<br>";
            $data = $jsonfile->getDataKeyValue("filename", $filename);

            $data['process'] = true;
            // echo "<br>id=".$id."<br><br>";
            // var_dump($data);
            // exit();
            // $jsonfile->setData($data);

            $jsonfile->setDataKeyValue("filename", $filename, $data);

            $jsonfile->save();

            // $jsonfile->load();

            $jsonfile = null;
        } else {}

        
        
        
        
        /*
         * echo substr($element["name"],strrpos($element["name"],".")+1);
         * if(substr($element["name"],strrpos($element["name"],".")+1)=="log"){exit("bruno");
         * unset($files_list[$key]);
         * }
         */
    }
}

// exit("fim");

$jsonfile = null;

$jsonfile = new JsonFile();

$jsonfile->open($filename);

$data = $jsonfile->getData();

$length_data = count($data);
$length_process = 0;

if ($length_data > 0) {

    foreach ($data as $key => $element) {

        if (is_array($element)) {

            // foreach($item as $key2=>$item2){

            // if($key2 == "process"){
            $element["process"] = ($element["process"] == 1 ? "true" : "false");

            if (empty($element["endtime"]))
                $element["endtime"] = "";
            else
                $element["endtime"] = date("Y-m-d H:i:s", $element["endtime"]);

            if (empty($element["starttime"]))
                $element["starttime"] = "";
            else
                $element["starttime"] = date("Y-m-d H:i:s", $element["starttime"]);

            $datetime = new DateTimeFormats();

            $result = $datetime->date_diff($element["starttime"], $element["endtime"], array(
                "d",
                "h",
                "i",
                "s"
            )); // i-minutes

            $diff_dates = $datetime->date_diff_format($result, array(
                "d",
                "h",
                "i",
                "s"
            ));

            $file_real = "";
            $isMedatata = false;
            $checksum = false;
            
            if(isset($element["filename"]))
            {
                $file_real = $element["filename"] ;//$application->getParameter("folder") . substr($application->getParameter("filename"), 0, strrpos($application->getParameter("filename"), ".")) . "-" . $element["id"] . ".txt";

                if(file_exists($file_real))
                {
                    if($utils->isMetadataFileScript($file_real))
                    {
                        $isMedatata = true;
                
                        $hash_file = $utils->checksumFileScriptMOA($file_real,
                            Properties::getBase_directory_destine_exec() .
                            $application->getUser() . DIRECTORY_SEPARATOR, $element['script']);
                        
                        $metadata_hmac = $utils->getMetadataValueScript($file_real,
                            "security-hash-hmac-file");
                               
                        if($metadata_hmac == $hash_file)
                        {
                            $checksum = true;
                        }
                    }
                }
            
            }
            
            
            //$file_real = PATH_USER_WORKSPACE_STORAGE . $file_real;
            
            //exit($file_real);
            
            $bgcolor = "#ffffff";
            
            if ($element["running"] == 'true') {
                $status = "Running";
            } else {

                // if($element["process"]=="true"){
                // $status = "Proccessed";
                // $bgcolor="#ffffff";
                // }else {

                if (file_exists($file_real)) {
                    $bgcolor = "#fff";
                    $status = "Finish";
                } else {
                    $bgcolor = "#E77471";
                    $status = "File not exists";
                }

                // $status = "Finish";
                // $bgcolor="#F8E0E6";
                // }
            }            

            
            
            echo "<tr style='background-color:" . $bgcolor . "'><td>  " . $element["id"] 
            . "</td><td>" . "<a title='Remove' href='javascript:ConfirmDelete(\"" . "?component=" 
									    . $application->getParameter("component") 
            . "&controller=" . $application->getParameter("controller") . "&folder=" . $application->getParameter("folder") 
            . "&id=" . $element["id"] . "&filename=" . $application->getParameter("filename") . "&task=remove\");'>";
            
            if($isMedatata)
            {
                if($checksum)
                {
                    echo "<img align='middle' width='24px' src='" . $application->getPathTemplate()
                    . "/images/icon-security.png' border='0' title='Trusted signature: authenticated data integrity'>";
                }
            }
            
            echo ""
								    . "<img align='middle' width='24px' src='" . $application->getPathTemplate() 
            . "/images/icon-remove.gif' border='0'></a> " . "<a title='Edit' href='" 
										    . "?component=" . $application->getParameter("component") 
            . "&controller=reportedit" . "&folder=" . $application->getParameter("folder") . "&id=" . $element["id"] 
            . "&filename=" . $application->getParameter("filename") . "&task=edit'>" 
            . "<img align='middle' width='24px' src='" . $application->getPathTemplate() . "/images/icon-edit.png' border='0'></a> " 
        . "<a href='#' title='" . $element["script"] . "' alt='' onclick=\"javascript: expand(this);\"'>" 
                . substr($element["script"], 0, 40) . " ...</a> " . "</td><td>" . $status . "</td>" . "</td><td>" . $element["starttime"] . "</td><td>"
            . $element["endtime"] . "</td>" . "<td>" . $diff_dates . "</td>" . "</tr>";

            // }
            // }
        }
    }

    $result = ($length_process * 100) / $length_data;
}

?>
			
	</table>



								</form>


<?php if(!empty($application->getParameter('folder'))){?>
								<div style="float: right; padding-top: 10px">
									<input type="button" class="btn btn-default" value="Return" name="return"
										onclick="javascript: returnPage();" />
								</div>
									
									
									<br>
										
<?php }?>	

	<script>

function ConfirmDelete(url)
{
  var x = confirm("Are you sure you want to delete?");
  if (x){
	  window.location.href=url;
     return true;
  }else
    return false;
}

function expand(id){

	if(id.alt=='' || id.alt === undefined){

		id.alt=id.text; 
		id.innerHTML=id.title;
	}else{
		id.innerHTML=id.alt; 
		id.alt='';
	}
}

function returnPage(){
	//window.history.go(-1);

	//http://localhost/iea/?component=moa&controller=report&folder=New%20Folder/&task=open

	window.location.href='?component=<?php echo $application->getParameter("component");?>'
			+'&controller=report'
			+'&task=open'
			+'&folder=<?php echo $application->getParameter("folder");?>';
			
}

function ScriptView(command){

	
	var url = '?component=<?php echo $application->getParameter("component");?>'
						+'&controller=reportscript'
						+'&filename=<?php echo $application->getParameter("filename");?>'
						+'&command='+command
						+'&folder=<?php echo $application->getParameter("folder");?>';

// 	window.location.href = url;
	window.open(
			  url,
			  '_blank' // <- This is what makes it open in a new window.
			);
	
}



</script>
