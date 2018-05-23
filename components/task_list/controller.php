<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\components\task_list;

defined('_EXEC') or die();

use moam\core\AppException;
use moam\core\Framework;
use moam\core\Properties;
use moam\libraries\core\menu\Menu;
use moam\libraries\core\db\DBPDO;
use moam\libraries\core\utils\Utils;
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

Framework::import("DBPDO", "core/db");
Framework::import("Utils", "core/utils");

$DB = new DBPDO(Properties::getDatabaseName(), Properties::getDatabaseHost(), Properties::getDatabaseUser(), Properties::getDatabasePass());

require_once ($application->getComponent() . ".php");

$taskList = new TaskList($DB);

$element = $application->getParameter("element");
$user_id = $application->getUserId();
$task = $application->getParameter("task");

// function isRunning($pid) {
// $isRunning = false;
// if(strncasecmp(PHP_OS, "win", 3) == 0) {
// $out = [];
// exec("TASKLIST /FO LIST /FI \"PID eq $pid\"", $out);
// if(count($out) > 1) {
// $isRunning = true;
// }
// }
// elseif(posix_kill(intval($prevPid), 0)) {
// $isRunning = true;
// }
// return $isRunning;
// }

try {

    if ($task == "stop") {
        if (is_array($element)) {
            foreach ($element as $pid) {
                if ($application->getUserType() == 1) { // super user

                    exec("kill $pid");
                } else {
                    if ($taskList->is_pid_from_user($pid, $user_id)) {

                        exec("kill $pid");
                    }
                }

                // $command = "kill ". $pid;

                // $utils->runExternal($command);
                // echo $command;

                // $running=posix_kill($pid, 15);

                // if(posix_get_last_error()==1) /* EPERM */
                // {
                // echo "Sucess kill PID ".$pid;

                // }else
                // {
                // echo "Fail kill PID ".$pid;
                // }

                // $learner->remove($learner_id, $user_id);
            }
        }
    } else {}
} catch (AppException $e) {
    throw new AppException($e->getMessage());
}

?>

<script>


function goEdit(id){

	var component = document.getElementById("component").value;
	var controller = 'edit';

	url = "?component="+component
		+"&controller="+controller
		+"&learner_base_id="+id
		+"&task=edit";

	window.location.href = url;
	
}


function newData(){
	
	var template = prompt("Please enter new name", "New learner_base");

	var component = document.getElementById("component").value;

	
	if (template != null) {
		
    	window.location.href='?component='+component
        					+'&controller=edit'
        					+'&learner_base='+template
        					+'&task=new';
    	
	}
	
}



function sendAction(task){

	if(task == 'remove'){

	  var x = confirm("Are you sure you want to delete?");
	  if (!x)
	     return;

	}

	if(task == 'import'){

		document.getElementById('controller').value = "import";

	}
	
	document.getElementById('task').value = task;
	document.getElementById('form_data').submit();
	
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

						<div style="float: left; width: auto; border: 1px solid #fff">
																
							<?php echo $application->showMenu($menu);?>									

						</div>

						<div
							style="float: left; width: 100%; max-width: 80%; border: 1px solid #fff">

							<form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>"
								name="form_data" id="form_data"
								class="ng-pristine ng-valid-email ng-invalid ng-invalid-required">
								<input type="hidden"
									value="<?php echo $application->getComponent()?>"
									name="component" id="component"> <input type="hidden"
									value="<?php echo $application->getController()?>"
									name="controller" id="controller"> <input type="hidden"
									value="next" name="task" id="task">


								<div style="float: left; padding-left: 20px; width: 100%">

									<div
										style="float: left; padding-left: 5px; width: 100%; margin-top: 5px;">


										<div class="div_table" style="width: 100%">


											<div id="container">

												<div style="float: left; padding: 10px; width: 100%;"></div>

												Last 20 <input type="button" value="Kill Process"
													name="stop" onclick="javascript: sendAction('stop');" /> <br>
												<br>

												<table border='1' id="temporary_files" style="width: 100%;">
													<tr>
														<th>PID</th>
														<th style="width: 10%;"><label><input type="checkbox"
																id="checkall" onClick="do_this2()" value="select" />Type</label></th>
														<th style="width: 70%;">Command</th>
														<th style="width: 10%;">Initialized</th>
														<th style="width: 10%;">Closed</th>
														<th style="width: 10%;">Status</th>

													</tr>
<?php

try {

    $utils = new Utils();

    if ($application->getUserType() == 1) { // super user
        $rs = $taskList->selectFromSuperUser();
    } else {
        $rs = $taskList->selectFromUser($user_id);
    }

    $i = 0;

    while ($row = $rs->fetch()) {
        $i ++;

        $pid = $row["pid"];

        if ($row["process_closed"] == "") {
            $realtime_status = $utils->proc_get_status($pid);
        } else {
            $realtime_status = "closed";
        }

        if ($realtime_status == "closed") {
            $bgcolor = "#cccccc";
        } else {
            $bgcolor = "#505050";
        }

        echo "<tr style='color:" . $bgcolor . "'><td>" . $pid . "</td><td>" . 

        // ."<a onclick='javascript: renameFolder(this);' name='".$element["name"]."' title='Rename' href='#'>"
        // ."<img align='middle' width='24px' src='".App::getDirTmpl()."images/icon-rename.png' border='0'></a> "
        // "<a href='#'
        // onclick=\"javascript: goEdit('" . $row["execution_history_id"] . "');\">"
        // . "<img width='24px' align='middle' src='" . $application->getPathTemplate()
        // . "/images/icon-folder.png' title='Open'/></a> " .

        // ."<a title='Remove' onclick='javascript: return ConfirmDelete();' href='?component=home&controller=files&task=remove&folder=".$folder.$element["name"]."'>"
        // ."<img src='".App::getDirTmpl()."images/icon-remove.gif' border='0'></a>

        "<label><input type='checkbox' name='element[]' value='" . $pid . "' />" . $row["process_type"] . "</label> " . "</td><td>";

        if ($row["process_type_id"] == "1") {
            echo $row["source"];
        } else {
            echo $row["command"];
        }

        echo " " . "</td><td>" . $row["process_initialized"] . "</td><td>" . $row["process_closed"] . "</td><td>" . $realtime_status . "</td></tr>";
    }
} catch (AppException $e) {
    throw new AppException($e->getMessage());
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

</div>
</div>
</div>