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

Template::addHeader(array(
    "tag" => "script",
    "type" => "text/javascript",
    "src" => "" . $application->getPathTemplate() . "/javascript/base64.js"
));

$utils = new Utils();

$filename = $application->getParameter("filename");
$folder = $application->getParameter("folder");
$data = "";
$task = $application->getParameter("task");

if ($filename != null) {

    $filename = Properties::getBase_directory_destine($application) . $application->getUser() . DIRECTORY_SEPARATOR . $folder . 
    // .DIRECTORY_SEPARATOR
    $filename; // .$extension_scripts;

    if($task == "clean")
    {        
        $dir = PATH_USER_WORKSPACE_STORAGE . $folder;
        
        $element = $application->getParameter("element");

        foreach ($element as $key => $item) 
        {       
            if(file_exists($dir . $item))
            {
                unlink($dir . $item);
            }
        }
        
//         $files_list = detectProblemsFiles($filename);
        
//         foreach ($files_list as $element) {
            
//             if(file_exists($element['name']))
//             {
//                 unlink($element['name']);
//             }
//         }        
    }
}

// Framework::includeLib("JsonFile.php");
function detectProblemsFiles($filename)
{
    $result = array();
    $jsonfile = new JsonFile();
    $jsonfile->open($filename);
    $data = $jsonfile->getData();
    $utils = new Utils();

    if (count($data) > 0) 
    {
        foreach ($data as $key => $item) 
        {
            if(isset($item['filename']))
            {
                if(file_exists($item['filename']))
                {
                    $size = filesize($item['filename']) /1024;
                    
                    if($size > 3000)
                    {
                        $bparted = $size/2;
                    }
                    else
                    {
                        if($size > 2000)
                        {
                            $bparted = 1500;
                        }
                        else
                        {
                            $bparted = 500;
                        }
                    }
                    
                    $content = $utils->getContentFilePart($item['filename'], ($bparted * 1024));
                    
                    if(strrpos($content["data"], "Accuracy:") === FALSE)//if(filesize($item['filename']) < 1000)
                    {
                        $result[] = array("name"=>$item['filename'],
                            "size"=>$utils->filesize_formatted($item['filename']),
                            "datetime"=>date("Y/m/d H:i:s", filemtime($item['filename']))
                        );
                    }
                }
            }
           
        }
    }
 
    return $result;
}


?>

					<div class="page-header">
						<h1>
							<a href="<?php echo $_SERVER['REQUEST_URI']?>">Detect Problems</a>
						</h1>
					</div>




							<form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>"
								name="saveform" 
								class="ng-pristine ng-valid-email ng-invalid ng-invalid-required">
								<input type="hidden"
									value="<?php echo $application->getComponent()?>"
									name="component"> <input type="hidden" value="<?php echo $application->getController()?>"
									name="controller"> <input type="hidden" value="clean"
									name="task" id="task"> <input type="hidden"
									value="<?php echo $application->getParameter("filename");?>"
									name="filename"> <input type="hidden"
									value="<?php echo $application->getParameter("folder");?>"
									name="folder">






										
										<input type="submit" class="btn btn-danger" value="Delete" name="return"
										onclick="javascript: return confirmfixedBugs();" />
										
										<br> 
	
	<?php 
	
	$files_list = detectProblemsFiles($filename);
	
	if(count($files_list) > 0){			
	    if(count($files_list) > 1){?>						
		<h2>Problems were encountered in <?php echo count($files_list) ;?> files. See below.</h2>
		<?php }else{?>
		<h2>Problems were encountered in <?php echo count($files_list) ;?> file. See below.</h2>
		<?php }?>
	<?php }else{?>
	<h2>No problems detected in the files.</h2>	
	<?php }?>
	<table border='1' id="temporary_files" style="width: 100%;">
										<tr>
											<th style="width: 70%;"><label><input type="checkbox"
													id="checkall" onClick="do_this2()" value="select" />Name</label></th>
											<th>Size</th>
											<th>DateTime</th>
										</tr>
<?php




foreach ($files_list as $element) {

    $basep = Properties::getBase_directory_destine($application) . $application->getUser() . DIRECTORY_SEPARATOR;
    
    
    $f_dirname = substr($element['name'], strlen($basep));
    $f_dirname = substr($f_dirname, 0, strrpos($f_dirname, DIRECTORY_SEPARATOR)+1);
    
    $f_name = substr($element['name'], strrpos($element['name'], DIRECTORY_SEPARATOR)+1);
    
    echo "<tr><td> "    
     . "<label><input type='checkbox' name='element[]' value='" . $f_name . "' />"
    . "<a href='?component=" . $application->getComponent() . "&controller=openreadonly&filename=" . $f_name 
    . "&folder=" . $f_dirname 
    . "&filename2=" . $application->getParameter("filename")
    . "'"
    ." >"
    . "<img width='16px' align='middle' src='" . $application->getPathTemplate() . "/images/icon-view.png' title='View contents'/></a>"
    . "" .  $f_dirname . $f_name  ."</label> " 
    ;

        echo "</td><td>" . $element["size"] . "</td><td>" . $element["datetime"] . "</td></tr>";
//     }
}

?>		
	</table>
							
							</form>



								<div style="float: right; padding-top: 10px">
									<input type="button" class="btn btn-default" value="Return" name="return"
										onclick="javascript: returnPage();" />
								</div>

<script type='text/javascript'>



function verificaChecks() 
{	
	var aChk = document.getElementsByName("element[]");  
	var nenhum = false;
	
	for (var i=0;i<aChk.length;i++)
	{  
		if (aChk[i].checked == true)
		{  
			// CheckBox Marcado... Faça alguma coisa... Ex:
			//alert(aChk[i].value + " marcado.");
			nenhum = true;
			break;
		//}  else {
			// CheckBox Não Marcado... Faça alguma outra coisa...
		}
	}

	if(nenhum == false)
	{		
		alert('You need to select a file.');
	}
	
	return nenhum;
	
} 


function confirmfixedBugs()
{

	if(verificaChecks() == true)
	{ 

	    var x = confirm("Are you sure you want to delete the files?");
	    
	    if (x)
	    {
	    	return true;
	    	
	    }
	    else
	    {        
	    	return false;
	    }
	    
	}
	else
	{
		return false;
	}


}

function returnPage(){
	//window.history.go(-1);

	<?php

$folder = $application->getParameter("folder");


?>
			
	window.location.href='?component=<?php echo $application->getParameter("component");?>'
			+'&controller=report'
			+'&task=open'
			+'&folder=<?php echo $folder;?>';
			
}




function do_this2()
{

    var checkboxes = document.getElementsByName('element[]');
    var button = document.getElementById('checkall');
    
    if(button.checked ==  true)
    {
        for (var i in checkboxes)
        {
            checkboxes[i].checked = 'FALSE';
        }
        //button.value = 'deselect'
    }
    else
    {
        for (var i in checkboxes)
        {
            checkboxes[i].checked = '';
        }
       // button.value = 'select';
        button.checked == false;
    }
}

</script>


