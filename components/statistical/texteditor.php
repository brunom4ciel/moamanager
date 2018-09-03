<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\components\statistical;

defined('_EXEC') or die();

use moam\core\Framework;
use moam\libraries\core\utils\Utils;
// use moam\libraries\core\menu\Menu;
use moam\core\Properties;

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
$utils = new Utils();

$data_source = $application->getParameter("data_source");
$task = $application->getParameter("task");
$filename_autoload = $application->getParameter("filename");
$data_result = "";
$data_diff_statistical = "";

$statistical_test_array = array(
    "Bonferroni-Dunn",
    "Nemenyi",
    "Holm",
    "Shaffer",
    "Bergmann-Hommel"
);

if(!empty($filename_autoload))
{
	$data_source = $utils->getContentFile(PATH_USER_WORKSPACE_PROCESSING . $filename_autoload);
	unlink(PATH_USER_WORKSPACE_PROCESSING . $filename_autoload);
	$task = "Shaffer";
}

if (in_array($task, $statistical_test_array)) {

    $friedman_bin = Properties::getbase_directory_statistical() . "friedman-test/bin/friedman_run";

    $data_source2 = $data_source;
    $data_source2 = str_replace(",", ".", $data_source);

    $data_s = explode("\n", $data_source2);

    $countRows = 0;
    $countCols = 0;
    $cols_names = "";
    $letter = false;

    foreach ($data_s as $rows) {

        if ($countRows == 0) {
            $rows_s = explode("\t", $rows);

            $i = 1;
            foreach ($rows_s as $cols) {
                if (is_numeric(trim($cols))) {
                    $cols_names .= " A" . $i;
                    $i ++;
                } else {
                    $cols_names .= $cols . "\t";
                    $letter = true;
                }

                $countCols ++;
            }
        }

        if (trim($rows) != "")
            $countRows ++;
    }

    if ($letter == true) {
        $countRows --;

        $data_s = explode("\n", $data_source2);

        // var_dump($data_s);
        // exit("fim");

        $start = true;
        $data_source2 = "";

        foreach ($data_s as $rows) {
            if ($start == true)
                $start = false;
            else
                $data_source2 .= $rows . "\n";
        }
    }

    $cols_names = trim($cols_names);

    $data_destine = $countRows . "\t" . $countCols . "\n" . $cols_names . "\n" . $task . "\n" . // .ucfirst($task)."\n"
    "0.95" . "\n" . $data_source2;

    $filename = PATH_USER_WORKSPACE_PROCESSING . "tmp" . str_replace(" ", "-", microtime()) . "";

    $utils->setContentFile($filename . ".tmp", $data_destine);

    if (is_file($filename . ".tmp")) {
        $command = $friedman_bin . " < " . $filename . ".tmp > " . $filename . "-output.tmp";
        // echo $command;
        exec($command);
        sleep(1);
    }

    if (is_file($filename . "-output.tmp")) {
        $data_result = $utils->getContentFile($filename . "-output.tmp");

        $s = explode("\n\n", $data_result);

        $data_postos = trim($s[4]);

        $data_postos = str_replace(".", ",", $data_postos);
        
        $data_diff_statistical  = trim($s[6])."\n\n";
		$data_diff_statistical  .= trim($s[7])."\n\n";
		$data_diff_statistical  .= trim($s[8]);
		
        $data_rank = trim($s[5]);

        $data_rank = explode("\n", $data_rank);

        $data_rank = $data_rank[1];

        $data_order = explode("\t", $data_rank);
        $data_order2 = $data_order;

        sort($data_order);

        $test = array();
        $index = 1;

        foreach ($data_order as $item) {
            foreach ($data_order2 as $key2 => $item2) {
                if ($item == $item2) {
                    $data_order2[$key2] = array(
                        "order" => $index,
                        "value" => $item2
                    );

                    // array_push($test, array("order"=>$index, "value"=>$item2));
                }
            }

            $index ++;
        }

        $data_rank = "";
        $data_rank2 = "";

        foreach ($data_order2 as $row) {
            $row["value"] = str_replace(".", ",", $row["value"]);

            if (empty($data_rank)) {
                $data_rank = $row["value"];
                $data_rank2 = $row["order"];
            } else {
                $data_rank .= "\t" . $row["value"];
                $data_rank2 .= "\t" . $row["order"];
            }
        }

        $data_rank = $data_rank . "\n" . $data_rank2;

        // var_dump($data_order2);
        // echo $data_rank;
        // exit();

        // $data_order = explode("\t", $data_rank);

        // sort($data_order);

        // $data_rank = str_replace(".", ",", $data_rank);

        // $data_rank .= "\n" . implode("\t", $data_order);

        unlink($filename . "-output.tmp");
        unlink($filename . ".tmp");
    }
}

?>



							<form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>">
								<input type="hidden"
									value="<?php echo $application->getComponent()?>"
									name="component"> <input type="hidden"
									value=<?php echo $application->getController()?>
									name="controller"> <input type="hidden" value="Shaffer"
									name="task" id="task">

								

									<div
										style="float: left; padding-left: 5px; width: 100%; margin-top: 5px;">


										<textarea id="data_source" style="width: 100%; height: 400px;"
											name="data_source"><?php echo $data_source?></textarea>



									</div>


									<div style="float: left; padding-left: 10px">
										Separador decimal <input type="text" name="decimalformat"
											id="decimalformat" value="," style="width: 40px;" /> <input
											type="submit" class="btn btn-default" value="Shaffer"
											onclick="document.forms[0].task.value=this.value"> <input
											type="submit" class="btn btn-default" value="Nemenyi"
											onclick="document.forms[0].task.value=this.value"> <input
											type="submit" class="btn btn-default" value="Holm"
											onclick="document.forms[0].task.value=this.value"> <input
											type="submit" class="btn btn-default" value="Bonferroni-Dunn"
											onclick="document.forms[0].task.value=this.value"> <input
											type="submit" class="btn btn-default" value="Bergmann-Hommel"
											onclick="document.forms[0].task.value=this.value">
									</div>
											
											<?php

        if (! empty($data_result)) {
            ?>
											<div
										style="float: left; padding-left: 5px; width: 100%; margin-top: 5px;">

										<textarea id="data_rank" style="width: 100%; height: 70px;"
											name="data_rank"><?php echo $data_rank?></textarea>

										<textarea id="data_postos" style="width: 100%; height: 400px;"
											name="data_postos"><?php echo $data_postos?></textarea>

										<textarea id="data_diff_statistical" style="width: 100%; height: 400px;"
											name="data_diff_statistical"><?php echo $data_diff_statistical?></textarea>

										
										<textarea id="data_result" style="width: 100%; height: 400px;"
											name="data_result"><?php echo $data_result?></textarea>
											
									</div>
											
											<?php }?>
					

							</form>

