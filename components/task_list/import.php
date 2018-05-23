<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\components\learner_base;

defined('_EXEC') or die();

use moam\core\AppException;
use moam\core\Framework;
use moam\core\Application;
use moam\core\Properties;
use moam\libraries\core\utils\Utils;
use moam\libraries\core\menu\Menu;
use moam\libraries\core\db\DBPDO;
use PDO;
if (! class_exists('Application')) {
    $application = Framework::getApplication();
}

if (! $application->is_authentication()) {
    $application->alert("Error: you do not have credentials.");
}

Framework::import("menu", "core/menu");

if (! class_exists('Menu')) {
    $menu = new Menu();

    $menu->add(PATH_WWW, "Home");
    $menu->add(PATH_WWW . "?component=evaluation", "Evaluation");
    $menu->add(PATH_WWW . "?component=analyze", "Analyze");
    $menu->add(PATH_WWW . "?component=methodology_type", "Methodology Type");
    $menu->add(PATH_WWW . "?component=methodology", "Methodology");
    $menu->add(PATH_WWW . "?component=method", "Method");
    $menu->add(PATH_WWW . "?component=stream", "Stream");
    $menu->add(PATH_WWW . "?component=stream_type", "Stream Type");
    $menu->add(PATH_WWW . "?component=learner", "Learner");
    $menu->add(PATH_WWW . "?component=learner_base", "Base Learner");
    $menu->add(PATH_WWW . "?component=template_method", "Template Method");
    $menu->add(PATH_WWW . "?component=template_stream", "Template Stream");
}

Framework::import("Utils", "core/utils");
Framework::import("DBPDO", "core/db");

$utils = new Utils();

$DB = new DBPDO(Properties::getDatabaseName(), Properties::getDatabaseHost(), Properties::getDatabaseUser(), Properties::getDatabasePass());

$settings = $application->getParameter("settings");
$user_id = $application->getUserId();
$element = $application->getParameter("element");

$task = $application->getParameter("task");

if ($task == "upload") {

    if (isset($_FILES['jsonfile'])) {

        try {

            $handle = fopen($_FILES['jsonfile']['tmp_name'], "rb") or die("Unable to open file!");
            $data = "";

            while (! feof($handle))
                $data .= fread($handle, 1024);

            fclose($handle);

            $data = json_decode($data);
        } catch (AppException $e) {
            throw new AppException($e->getMessage());
        }

        // list all elements
        foreach ($data as $element) {

            foreach ($element as $item) {

                if ($item->type == "learner_base") {

                    foreach ($item->list as $value) {

                        $learner_base_id = $value->learner_base_id;
                        $learner_base = $value->learner_base;
                        $alias = $value->alias;

                        if ($settings == 1) {

                            $data_db = $DB->prep_query("REPLACE INTO
													 learner_base
											(learner_base,alias,user_id,learner_base_id)
									VALUES 
											(?,?,?,?)");

                            $data_db->bindParam(1, $learner_base, PDO::PARAM_STR);
                            $data_db->bindParam(2, $alias, PDO::PARAM_STR);
                            $data_db->bindParam(3, $user_id, PDO::PARAM_INT);
                            $data_db->bindParam(4, $learner_base_id, PDO::PARAM_INT);
                        } else {

                            if ($settings == 2) {

                                $data_db = $DB->prep_query("INSERT INTO
													 learner_base
											(learner_base,alias,user_id)
									VALUES
											(?,?,?)");

                                $data_db->bindParam(1, $learner_base, PDO::PARAM_STR);
                                $data_db->bindParam(2, $alias, PDO::PARAM_STR);
                                $data_db->bindParam(3, $user_id, PDO::PARAM_INT);
                            }
                        }

                        try {

                            // open transaction
                            $DB->beginTransaction();

                            // execute query
                            $data_db->execute();

                            // confirm transaction
                            $DB->commit();
                        } catch (AppException $e) {
                            throw new AppException($e->getMessage());
                        }
                    }
                }
            }
        }

        header("Location: ?component=" . $application->getParameter("component") . "");
    }
}

?>

<script>

function sendAction(task){

	if(task == 'remove'){

	  var x = confirm("Are you sure you want to delete?");
	  if (!x)
	     return;

	}
	
	
	document.getElementById('task').value = task;
	document.getElementById('form_data').submit();
	
}



</script>
<div class="content content-alt">
	<div class="container" style="width: 90%">
		<div class="row">
			<div class="">

				<div class="card" style="width: 100%">
					<div class="page-header">
						<h1>
							<a href="<?php echo $_SERVER['REQUEST_URI']?>">Import
								learner_base</a>
						</h1>
					</div>

					<div style="width: 100%; padding-bottom: 15px; display: table">

						<div style="float: left; width: auto; border: 1px solid #fff">
																
							<?php echo $application->showMenu($menu);?>								

						</div>

						<div
							style="float: left; width: 100%; max-width: 80%; border: 1px solid #fff">

							<form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>"
								name="loginForm" enctype="multipart/form-data">
								<input type="hidden"
									value="<?php echo $application->getComponent()?>"
									name="component"> <input type="hidden"
									value="<?php echo $application->getController()?>"
									name="controller"> <input type="hidden" value="upload"
									name="task">

								<div class="boxlimit"
									style="margin-top: 5px; width: 100%; border: 1px solid #cccccc;">

									<div class="div_table" style="width: 100%; float: left;">

										<div class="div_row" style="width: 100%; float: left;">
											<div class="div_cell" style="width: 100%; float: left;">
												Settings</div>
											<div class="div_cell">

												<div class="boxlimit"
													style="margin-top: 5px; width: 150px; border: 1px solid #cccccc;">
													<label><input type="radio" name="settings" id="settings"
														value="1" checked>Replece</label> <label><input
														type="radio" name="settings" id="settings" value="2">Insert</label>
												</div>

											</div>
										</div>

										<div class="div_row" style="width: 100%; float: left;">
											<div class="div_cell">Upload</div>
											<div class="div_cell" style="width: 60%; float: left;">

												File (*.json): <input type="file" name="jsonfile" />

											</div>
										</div>


									</div>
									<div style="float: right;">
										<input type="submit" name="default" value="import" />
									</div>

								</div>


							</form>


						</div>
					</div>
				</div>

			</div>
		</div>
	</div>