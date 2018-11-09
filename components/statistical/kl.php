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
    "kl"
);

if(!empty($filename_autoload))
{
	$data_source = $utils->getContentFile(PATH_USER_WORKSPACE_PROCESSING . $filename_autoload);
	unlink(PATH_USER_WORKSPACE_PROCESSING . $filename_autoload);
	$task = "kl";
}

if (in_array($task, $statistical_test_array)) {

    $data_source2 = $data_source;
    $data_source2 = str_replace(",", ".", $data_source);

    $data_s = explode("\n", $data_source2);
    
    $countRows = 0;
    $countCols = 0;
    $cols_names = "";
    $letter = false;
    $cols_names1 = array();

    $data_values = array();
    $index_row = 0;
    
    foreach ($data_s as $rows) 
    {

        if ($countRows == 0) 
        {
            $rows_s = explode("\t", $rows);

            $i = 1;
            foreach ($rows_s as $cols) 
            {
				$cols_ = str_replace(",", ".", $cols);
				$cols_ = str_replace("\n", "", $cols_);
				
                if (is_numeric(trim($cols_))) 
                {
                    $cols_names1[] = "A" . $i;//$cols_names .= "A" . $i;
                    $i ++;
                } 
                else 
                {
                    $cols_names1[] = trim($cols);//$cols_names .= $cols;// . "\t";
                    $letter = true;
                }

                $countCols ++;
            }
        }

        if (trim($rows) != "")
        {
            $rows_v = explode("\t", $rows);
            
            $index_col = 0;
            foreach ($rows_v as $cols)
            {
                if (is_numeric(trim($cols)))
                {
                    $data_values[$index_row][$index_col] = floatval($cols);
                }
                
                $index_col++;
            }
            
            $index_row++;
            $countRows ++;
        }
    }
    
    //$data_source = $cols_names . "\n" ;
    
    $aux1 = "";    
    foreach($data_values as $item)
    {
		$aux2 = "";
		
		foreach($item as $key=>$value)
		{
			if($aux2 != "")
			{
				$aux2 .= "\t";
			}
			
			$aux2 .= $value;
		}
        $aux1 .= $aux2 . "\n";
    }    

	$original_columns = array();
		
	$cols_names = implode("\t", $cols_names1);
		
    $data_source = trim($cols_names) . "\n" . trim($aux1);
        
    $d1 = array();
    $d2 = array();
    
    $data_values_cols = 0;
    
    foreach($data_values as $item)
    {
		foreach($item as $key=>$value)
		{
			$data_values_cols++;
		}		
		break;
	}

	$str_equals = "";
	$str_not_equals = "";
	$kl_data = array();
	$kl_rank = array();
	
    for($col_ref = 0; $col_ref < $data_values_cols; $col_ref++)
    {
		$d1 = get_data_col($data_values, $col_ref);
		$d1_colname = $cols_names1[$col_ref];
		
		for($col_next = $col_ref + 1; $col_next < $data_values_cols; $col_next++)
		{
			$d2 = get_data_col($data_values, $col_next);
			$d2_colname = $cols_names1[$col_next];
			
			$kl = kl($d1, $d2);
			
			if($kl >= -0.5 && $kl <= 0.5)
			{
				$str_equals .= "KL Divergence between " . $d1_colname . " and " . $d2_colname . "  = " . $kl . "<br>";
			}
			else
			{
				$str_not_equals .= "KL Divergence between " . $d1_colname . " and " . $d2_colname . "  = " . $kl . "<br>";
			}
			
			$kl_data[$d1_colname][$d2_colname] = $kl;
			$kl_rank[] = $kl;
		}
    }   
    
    sort($kl_rank);
    
    //var_dump($kl_rank);exit();
    for($i = 0; $i < count($kl_rank); $i++)
    {
		foreach($kl_data as $key=>$item)
		{
			foreach($item as $key2=>$item2)
			{
				//var_dump($item2);exit();
				if($item2 == $kl_rank[$i])
				{
					echo $key. " and " . $key2 . " = " . $item2 . "<br>";
				} 
			}			
		}
    }
    
//    echo "equals<br>";
//    echo $str_equals;
    
//    echo "<br><br>";
    
//    echo "are not equals<br>";
//    echo $str_not_equals;
    
}

function get_data_col($data, $col)
{
	$result = array();
	
	foreach($data as $item)
	{
		foreach($item as $key=>$value)
		{
			if($key == $col)
			{
				$result[] = $value;
			}
		}        
	}
	
	return $result;
}
    
function log2($d)
{
	return floor(log($d,2)) / floor(log(2,2));
}

function kl($p1, $p2)
{
	$klDiv = 0.0;
	
	for ($i = 0; $i < count($p1); ++$i) 
	{
		if ($p1[$i] == 0) { continue; }
		if ($p2[$i] == 0.0) { continue; } 

		$klDiv += $p1[$i] * log($p1[$i] / $p2[$i]);//log2($p1[$i] / $p2[$i]);
	}

	return $klDiv / log(2);
}

?>

<style>

.dataview{font-size:10px}

</style>

							<form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>">
								<input type="hidden"
									value="<?php echo $application->getComponent()?>"
									name="component"> <input type="hidden"
									value=<?php echo $application->getController()?>
									name="controller"> 
									<input type="hidden" value="kl"	name="task" id="task">

								

									<div
										style="float: left; padding-left: 5px; width: 100%; margin-top: 5px;">


										<textarea  class="dataview" id="data_source" style="width: 100%; height: 400px;"
											name="data_source"><?php echo $data_source?></textarea>



									</div>


									<div style="float: left; padding-left: 10px">
										View decimal separator <input type="text" name="decimalformat"
											id="decimalformat" value="," style="width: 40px;" /> <input
											type="submit" class="btn btn-default" value="Kullback-Leibler Divergence">
											
									</div>
											
											<?php

        if (! empty($data_result)) {
            ?>
											<div
										style="float: left; padding-left: 5px; width: 100%; margin-top: 5px;">

										<textarea class="dataview" id="data_rank" style="width: 100%; height: 70px;"
											name="data_rank"><?php echo $data_rank?></textarea>

										<textarea class="dataview" id="data_diff_statistical" style="width: 100%; height: 400px;"
											name="data_diff_statistical"><?php echo $data_diff_statistical?></textarea>
											
											
										<textarea class="dataview" id="data_postos" style="width: 100%; height: 400px;"
											name="data_postos"><?php echo $data_postos?></textarea>
										
										<textarea class="dataview" id="data_result" style="width: 100%; height: 400px;"
											name="data_result"><?php echo $data_result?></textarea>
											
									</div>
											
											<?php }?>
					

							</form>

