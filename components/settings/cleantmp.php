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


if (! class_exists('Application')) {
    $application = Framework::getApplication();
}

if (! $application->is_authentication()) {
    $application->alert("Error: you do not have credentials.");
}

Framework::import("Utils", "core/utils");

$utils = new Utils();

$task = $application->getParameter("task");


if ($task == "clean") {

    
    $tmp_dir = Properties::getBase_directory_destine_exec($application) . $application->getUser();
    
    $files_list = $utils->getListElementsDirectory1($tmp_dir, 
        // .DIRECTORY_SEPARATOR
        array(
            "txt"
        ));

}

?>


<script>

function ConfirmDelete()
{
  var x = confirm("Are you sure you want to delete?");
  if (x)
     return true;
  else
    return false;
}

</script>



<div class="content content-alt">
	<div class="container" style="width: 90%">
		<div class="row">
			<div class="">

				<div class="card" style="width: 100%">
					<div class="page-header">
						<h1>
							<a href="<?php echo $_SERVER['REQUEST_URI']?>">Clean Temporary Files</a>
						</h1>
					</div>

					<div style="width: 100%; padding-bottom: 15px; display: table">

						<div
							style="float: left; width: 1%; border: 1px solid #fff; display: table-cell">


						</div>

						<div
							style="float: left; width: 98%; border: 1px solid #fff; display: table-cell">

								<h2>Were removed.</h2>
								
<?php 


if ($task == "clean") {   
    
    
    if(count($files_list) > 0){
        
        $body = "<table border=1>";
        $body .= "<tr><td>File</td><td>Size</td><td>Datetime</td></tr>";
        
        foreach($files_list as $item){
            $body .= "<tr>";
            $body .= "<td>".$item['name']."</td>";
            $body .= "<td>".$item['size']."</td>";
            $body .= "<td>".$item['datetime']."</td>";
            $body .= "</tr>";
            
            if (is_file($tmp_dir . DIRECTORY_SEPARATOR . $item['name'])) {
                unlink($tmp_dir . DIRECTORY_SEPARATOR . $item['name']);
            }
        }
        
        $body .= "</table>";
        //     var_dump($files_list);
        
        echo $body;
        
    }else{
        echo "No file.";
    }
    

    
}



?>

									<div style="text-align: right; display: block;">
										<br> <input type="button"
											onclick="javascript: window.location.href='?component=settings';"
											name="cancel" value="Return" />

									</div>
							
							</form>
	
	

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
									<?php

        /*
         * for($i=0; $i<count($files_list); $i++){
         *
         * echo "<span style='margin-left:65px;' data-reactid=\".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1.0\">".$files_list[$i]."</span><br>\n";
         *
         * }
         */

        ?>
								
								</div>

					</div>

				</div>
			</div>
		</div>
	</div>
</div>