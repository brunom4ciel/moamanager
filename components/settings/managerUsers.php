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
use moam\core\Template;
use moam\libraries\core\db\DBPDO;
use moam\libraries\core\utils\Utils;
use moam\core\Properties;
use PDO;
use moam\core\AppException;
if (! class_exists('Application')) {
    $application = Framework::getApplication();
}

if (! $application->is_authentication()) {
    $application->alert("Error: you do not have credentials.");
}

Framework::import("Utils", "core/utils");
Framework::import("DBPDO", "core/db");

$utils = new Utils();

$DB = new DBPDO(Properties::getDatabaseName(), Properties::getDatabaseHost(), Properties::getDatabaseUser(), Properties::getDatabasePass());

$task = $application->getParameter("task");

if ($task == "move") {

    $element = $application->getParameter("element");
    $movedestine = $application->getParameter("moveto");

    if (is_dir($movedestine)) {
        // verifica se existe o diretório

        if (is_readable($movedestine)) {

            if (is_writable($movedestine)) {

                foreach ($element as $email) {

                    $rs = $DB->prep_query("SELECT					
                    			workspace
                    					
                    			FROM user
                    					
                    			WHERE email=?");

                    $rs->bindParam(1, $email, PDO::PARAM_STR);

                    $error = "";

                    try {

                        // open transaction
                        $DB->beginTransaction();

                        // execute query
                        if ($rs->execute()) {
                            $data = $rs->fetch();

                            $workspace_source = $data["workspace"];
                        } else {

                            throw new AppException($DB->getErrorMessage($rs));
                        }

                        // confirm transaction
                        $DB->commit();
                    } catch (AppException $e) {

                        // back transaction
                        $DB->rollback();

                        throw new AppException($e->getMessage());
                    }

                    if ($workspace_source != $movedestine) {

                        if (! $utils->folder_exist($movedestine . $email)) { // verify if not exists

                            if ($utils->folder_exist($workspace_source . $email)) { // verify if exists

                                //
                                // not error

                                // chmod($workspace_source.$email, 0777);
                                // chmod($movedestine.$email, 0777);

                                exec("mv " . escapeshellarg($workspace_source . $email) . " " . escapeshellarg($movedestine . $email));

                                // if(rename($workspace_source.$email, $movedestine.$email)){

                                $data_db = $DB->prep_query("UPDATE user SET
					
				workspace=?
					
				WHERE email=?");

                                $data_db->bindParam(1, $movedestine, PDO::PARAM_STR);
                                $data_db->bindParam(2, $email, PDO::PARAM_STR);

                                $error = "";

                                try {

                                    // open transaction
                                    $DB->beginTransaction();

                                    // execute query
                                    if ($rs->execute()) {} else {

                                        throw new AppException($DB->getErrorMessage($rs));
                                    }

                                    // confirm transaction
                                    $DB->commit();
                                } catch (AppException $e) {

                                    // back transaction
                                    $DB->rollback();

                                    throw new AppException($e->getMessage());
                                }

                                if (empty($error)) {} else {

                                    $error_msg .= "Error: save dataset.<br>";
                                }

                                // }else{

                                // }
                            } else {

                                $error_msg .= "Error: the directory " . $movedestine . $email . " not exists.<br>";
                            }
                        } else {

                            $error_msg .= "Error: the directory " . $movedestine . $email . " exists.<br>";
                        }
                    } else {

                        $error_msg .= "Error: the directories " . $movedestine . " and " . $workspace_source . " are equal.<br>";
                    }
                }
            } else {

                $error_msg = "The directory is not writable.";
            }
        } else {

            $error_msg = "The directory is not readable.";
        }
    } else {

        $error_msg = "Folder not exists: " . $movedestine;
    }
} else {

    if ($task == "remove") {}
}

?>

<script>


function sendAction(task){


	if(task == 'move'){

	  var x = confirm("Are you sure you want to move?");
	  if (!x)
	     return;

	}


	if(task == 'remove'){

		  var x = confirm("Are you sure you want to delete?");
		  if (!x)
		     return;

		}
	
	
	document.getElementById('task').value = task;
	document.getElementById('formulario').submit();
	
}


function do_this2(){

    var checkboxes = document.getElementsByName('element[]');
    var button = document.getElementById('checkall');
    
    if(button.checked ==  true){
        for (var i in checkboxes){
            checkboxes[i].checked = 'FALSE';
        }
        //button.value = 'deselect'
    }else{
        for (var i in checkboxes){
            checkboxes[i].checked = '';
        }
       // button.value = 'select';
        button.checked == false;
    }
}

</script>

<div class="content content-alt">
	<div class="container" style="width: 70%">
		<div class="row">
			<div class="">
				<div class="card" style="width: 100%">



					<div class="page-header">
						<h1>
							<a href="<?php echo $_SERVER['REQUEST_URI']?>">Manager Users</a>
						</h1>
					</div>
							
							<?php

    if (! empty($error_msg)) {

        echo $error_msg;
    }
    ?>
							
							
							<div style="width: 100%; padding-bottom: 15px; display: table">


						<div
							style="float: left; width: 100%; max-width: 99%; border: 1px solid #fff">

							<form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>"
								name="formulario" id="formulario" enctype="multipart/form-data">
								<input type="hidden"
									value="<?php echo $application->getComponent()?>"
									name="component"> <input type="hidden"
									value="<?php echo $application->getController()?>"
									name="controller"> <input type="hidden" name="task" id="task"
									value="" />

								<div id="container">

									<input type="button" value="Remove" name="remove"
										onclick="javascript: sendAction('remove');" />
||

Move to:  
<?php

$rs = $DB->prep_query("SELECT
	
	email, workspace
	
	FROM  user 

	ORDER by email asc");

try {

    // execute query
    if ($rs->execute()) {
        $data = $rs->fetchAll();
    } else {

        throw new AppException($DB->getErrorMessage($rs));
    }
} catch (AppException $e) {
    throw new AppException($e->getMessage());
}

$data_db2 = array();

$dirs = Properties::getOutput_directorys();

foreach ($dirs as $item) {

    $data_db2[] = array(
        "id" => $item,
        "name" => $item . " - Free " . $utils->formatSize($utils->getFreeSpace($item)) . ""
    );
}

$cmb = $utils->createSelectList("moveto", "moveto", $data_db2, "", "", "", "");

?><?php echo $cmb;?>

													
												</select> <input type="button" value="Move" name="move"
										id="move" onclick="javascript: sendAction('move');" /> <br>



									<table border='1' id="temporary_files" style="width: 100%;">
										<tr>
											<th>#</th>
											<th style="width: 40%;"><label><input type="checkbox"
													id="checkall" onClick="do_this2()" value="select" />Name</label></th>
											<th>Disk</th>
											<th>Size Usage</th>
											<th>Free Space</th>
										</tr>
<?php
$i = 0;
foreach ($data as $key => $element) {
    $i ++;

    $dir_ = $element["workspace"] . $element["email"] . DIRECTORY_SEPARATOR;

    echo "<tr><td>" . $i . "</td><td>" . "" . "<img width='24px' align='middle' src='" . $application->getPathTemplate() . "/images/icon-folder.png' title='Open'/> " . 

    // ."<a title='Remove' onclick='javascript: return ConfirmDelete();' href='?component=home&controller=files&task=remove&folder=".$folder.$element["name"]."'>"
    // ."<img src='".App::getDirTmpl()."images/icon-remove.gif' border='0'></a>

    "<label><input type='checkbox' name='element[]' value='" . $element["email"] . "' />" . $element["email"] . "</label></td>" . "<td><a href='?component=settings&controller=files&folder=" . $element["workspace"] . "/&task=open'>" . $element["workspace"] . "</a></td>" . "<td>" . $utils->getDirSize($dir_) . "</td><td>" . $utils->formatSize($utils->getFreeSpace($dir_)) . "</td></tr>";
}

?>		
	</table>







								</div>
						
						</div>


					</div>










					<div style="text-align: right; display: block;">

						<input type="button"
							onclick="javascript: window.location.href='?component=settings';"
							name="cancel" value="Return" />

					</div>

					</form>


				</div>

			</div>
		</div>
	</div>
</div>
</div>

