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

if (! $application->is_authentication()) {
    $application->alert("Error: you do not have credentials.");
}

Template::setDisabledMenu();

Framework::import("Utils", "core/utils");

$utils = new Utils();

$task = $application->getParameter("task");


// if ($task == "clean") {

    
    $tmp_dir = Properties::getBase_directory_destine_exec($application) . $application->getUser();
    
    $files_list = $utils->getListElementsDirectory1($tmp_dir, 
        // .DIRECTORY_SEPARATOR
        array(
            "txt", "zip", "data"
        ));

// }

?>


<script>

function confirmCleanDirectory(){

 	var x = confirm("Are you sure you want to delete your temporary files?");

 	if (x){
 	 	var x = confirm("If the temporary files is delete all files in processing will cease to function .\nAre you sure you want to delete your temporary files?");
		if(x)
			return true;
	  	else
			return false;
 	}else
    	return false;
	
}

function ConfirmDelete()
{
  var x = confirm("Are you sure you want to delete?");
  if (x)
     return true;
  else
    return false;
}

</script>


							<div class="page-header">
        						<h1>
        							<a href="<?php echo $_SERVER['REQUEST_URI']?>">Clean temporary files</a>
        						</h1>
        					</div>

							<form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>"
						name="loginForm">
						<input type="hidden"
							value="<?php echo $application->getComponent()?>"
							name="component"> <input type="hidden"
							value="<?php echo $application->getController()?>"
							name="controller">
							<input type="hidden"
							value="clean"
							name="task">
<?php 


if ($task == "clean") {   
    

    if(count($files_list) > 0){
        
        $body = "<h2>Were removed.</h2><table border=1>";
        $body .= "<tr><td>File</td><td>Size</td><td>Datetime</td></tr>";
        
        foreach($files_list as $item){
            $body .= "<tr>";
            $body .= "<td style='text-decoration: line-through;'>".$item['name']."</td>";
            $body .= "<td>".$item['size']."</td>";
            $body .= "<td>".$item['datetime']."</td>";
            $body .= "</tr>";
            
            if (is_file($tmp_dir . DIRECTORY_SEPARATOR . $item['name'])) {
                unlink($tmp_dir . DIRECTORY_SEPARATOR . $item['name']);
            }
        }
        
        $body .= "</table><h2>Successful.</h2>";
        //     var_dump($files_list);
        
        echo $body;
        
    }else{
        echo "No file.";
    }
       
}
else 
{
    
    if ($task == "confirm") {
        
        
        if(count($files_list) > 0){
            
            $body = "
<input type=\"submit\" class=\"btn btn-danger\" id=\"buttonclean\" value=\"Click to confirm the deletion of the files\"
onclick=\"javascript: return confirmCleanDirectory(); \" />"; 
            $body .= "<h2>Files.</h2><table border=1>";
            $body .= "<tr><td>File</td><td>Size</td><td>Datetime</td></tr>";
            
            foreach($files_list as $item){
                $body .= "<tr>";
                $body .= "<td>".$item['name']."</td>";
                $body .= "<td>".$item['size']."</td>";
                $body .= "<td>".$item['datetime']."</td>";
                $body .= "</tr>";
                
//                 if (is_file($tmp_dir . DIRECTORY_SEPARATOR . $item['name'])) {
//                     unlink($tmp_dir . DIRECTORY_SEPARATOR . $item['name']);
//                 }
            }
            
            $body .= "</table>";
            //     var_dump($files_list);
            
            echo $body;
            
        }else{
            echo "No file.";
        }
        
        
        
    }
}



?>
</form>
									
									<div style="float: right; padding-left: 10px">
									
											<input type="button" class="btn btn-default"
                							onclick="javascript: window.location.href='?component=settings';"
                							name="cancel" value="Return" />
									</div>
									
													
									
									
									
									
									