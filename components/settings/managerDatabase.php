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

if (! $application->is_authentication() || $application->getUserType() != 1) {
    $application->alert("Error: you do not have credentials.");
}

Template::setDisabledMenu();

Framework::import("Utils", "core/utils");
Framework::import("DBPDO", "core/db");

$utils = new Utils();

$DB = new DBPDO(Properties::getDatabaseName(), Properties::getDatabaseHost(), Properties::getDatabaseUser(), Properties::getDatabasePass());

$error_msg = "";
$result_query = "";

$query = $application->getParameter("query");

if (isset($_POST['query'])) {

    $query_lines = explode(";", $query);

    for ($i = 0; $i < count($query_lines); $i ++) {

        $querys = explode("\n", $query_lines[$i]);

        $query_lines[$i] = "";

        for ($z = 0; $z < count($querys); $z ++) {

            if (substr($querys[$z], 0, 2) != "--") {

                $query_lines[$i] .= $querys[$z];
            }
        }

        $query_lines[$i] = trim(str_replace("\n", "", $query_lines[$i]));

        if (trim($query_lines[$i] != ""))
            $query_lines[$i] .= ";";
        else
            unset($query_lines[$i]);
    }

    // var_dump($query_lines);

    // exit("--------------------");

    // $query_verbose = "";

    for ($i = 0; $i < count($query_lines); $i ++) {
        // $query_verbose .= $query_lines[$i]."<br><br>";

        $rs = $DB->prep_query($query_lines[$i]);
        // $data_db = $DB->execute($query_lines[$i]);

        try {

            // open transaction
            $DB->beginTransaction();

            // execute query
            if ($rs->execute()) {

                if ($rs->rowCount() > 0) {

                    $data_db = $rs->fetchAll(PDO::FETCH_ASSOC);

                    $result_query .= "Query execute (" . $i . "): ";
                    $result_query .= $query_lines[$i] . "\n\n";
                    $z = 1;
                    $column_header = true;
                    $header = "";

                    $result_query .= "<table border='1px'>";

                    foreach ($data_db as $key => $item) {

                        $body = "";

                        foreach ($item as $key2 => $item2) {

                            if ($column_header) {
                                $header .= "<td>" . $key2 . "</td>";
                            }

                            $body .= "<td>" . $item2 . "</td>";
                        }

                        if ($column_header) {
                            $result_query .= "<tr>" . $header . "</tr><tr>" . $body . "</tr>";
                        } else {
                            $result_query .= "<tr>" . $body . "</tr>";
                        }

                        $column_header = false;
                    }

                    $result_query .= "</table>";
                    $result_query .= "\n\n";
                }
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
    }
}

?>




							<div class="page-header">
        						<h1>
        							<a href="<?php echo $_SERVER['REQUEST_URI']?>">Dataset Manager</a>
        						</h1>
        					</div>
							
							<?php

    if (! empty($error_msg)) {

        echo "<pre style='  display:block;

  width:100%; 
  top:20px;
  left:0;
  font-size: 12px;
  padding:5px;
  border:1px solid #999;
  background:#F6CECE; 
  color:#000;'><b>Warning</b>\n" . $error_msg . "</pre>";
    }

    ?>
							
							
							
            				<form method="POST"
						action="<?php echo $_SERVER['PHP_SELF'];?>" name="loginForm"
						enctype="multipart/form-data">
						<input type="hidden"
							value="<?php echo $application->getComponent()?>"
							name="component"> <input type="hidden"
							value="<?php echo $application->getController()?>"
							name="controller"> <input type="hidden"
							value="<?php echo $application->getParameter("task")?>"
							name="task">

						<textarea id="data" style="width: 100%; height: 400px;"
							name="query"><?php echo $query?></textarea>
						<br>

							<div style="float: right; padding-left: 10px">
									
								<input type="submit" class="btn btn-success" name="Execute" value="Execute" />
						
								<input type="button" class="btn btn-default"
    							onclick="javascript: window.location.href='?component=settings';"
    							name="cancel" value="Return" />
						</div>
						
						
					</form>
							
							
							<?php

    if (! empty($result_query)) {

        echo "<pre style='  display:block;
								
  width:100%;
  top:20px;
  left:0;
  font-size: 12px;
  padding:5px;
  border:1px solid #999;
  background:#F2F5A9;
  color:#000;'>" . $result_query . "</pre>";
    }

    ?>
							



