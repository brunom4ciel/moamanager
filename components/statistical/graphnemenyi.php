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
use moam\core\Template;
use moam\core\Properties;
use \DirectoryIterator;

if (! class_exists('Application')) {
    $application = Framework::getApplication();
}

if (! $application->is_authentication()) {
    $application->alert("Error: you do not have credentials.");
}

Template::setDisabledMenu();

/*
// Optionally Disable browser caching on "Back"
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Expires: Sun, 1 Jan 2000 12:00:00 GMT' );
header( 'Last-Modified: ' . gmdate('D, d M Y H:i:s') . 'GMT' );

$post_hash = md5( json_encode( $_POST ) );

if( session_start() )
{
    $post_resubmitted = isset( $_SESSION[ 'post_hash' ] ) && $_SESSION[ 'post_hash' ] == $post_hash;
    $_SESSION[ 'post_hash' ] = $post_hash;
    session_write_close();
}
else
{
    $post_resubmitted = false;
}

if ( $post_resubmitted ) {
  // POST was resubmitted
}
else
{
  // POST was submitted normally
}

*/

// Framework::import("menu", "core/menu");

// if (! class_exists('Menu')) {
//     $menu = new Menu();
// }

Framework::import("Utils", "core/utils");
$utils = new Utils();

$data_source = $application->getParameter("data_source");
$task = $application->getParameter("task");
$filename_autoload = $application->getParameter("filename");
$decimalprecision = $application->getParameter("decimalprecision");
$order = $application->getParameter("order");
$caption = $application->getParameter("caption");
$source = $application->getParameter("source");
$folder = $application->getParameter("folder");
$downloadfile = $application->getParameter("downloadfile");
$columns = $application->getParameter("columns");

if($downloadfile == null)
{
	$downloadfile = "png";	
}

if($columns == null)
{
	$columns = "0";
}

$data_rank = "";

$data_result = "";
$data_diff_statistical = "";
$src_img = "";
$body_rank_postos =  "";
    
$statistical_test_array = array(
    "Nemenyi"
);

if($decimalprecision == null)
{
	$decimalprecision = 2;
}
else
{
	$decimalprecision = intval($decimalprecision);
	
	if(!is_int($decimalprecision))
	{
		$decimalprecision = 2;
	}
}

if($order == null)
{
	$order = 0;
}
else
{
	$order = intval($order);
	
	if($order == 0)
	{
		$order = 0;
	}
	else
	{
		$order = 1;
	}
}
	
if(!empty($filename_autoload))
{
	if(is_readable(PATH_USER_WORKSPACE_PROCESSING . $filename_autoload))
	{		
		$data_source = $utils->getContentFile(PATH_USER_WORKSPACE_PROCESSING . $filename_autoload);
		unlink(PATH_USER_WORKSPACE_PROCESSING . $filename_autoload);
		$task = "Nemenyi";
	}
}

if (in_array($task, $statistical_test_array)) {

    
    $nemenyi_bin = Properties::getbase_directory_statistical() . "orange3/nemenyi.py";

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
	
	if($columns == "1")
	{
		$original_columns = $cols_names1;
	}
	
	$cols_names = implode("\t", $cols_names1);
	
    $data_source = trim($cols_names) . "\n" . trim($aux1);
    
    
    /*$rank_avg = array();
    
    for($i = 1; $i <= count($data_values); $i++)
    {
        for($z = 0; $z < count($data_values[$i]); $z++)
        {
            $rank_avg[$i][$z] = $utils->rank_avg($data_values[$i][$z], $data_values[$i], 0);
        }        
    }*/
    

    $rank_avg = $utils->friedman_postos($data_values, $order);

    $body_rank_postos = "";
    
    foreach($rank_avg as $item)
    {
        $newline = "";        
        
        foreach($item as $key=>$value)
        {
            if($newline != "")
            {
                $newline .= "\t";
            }
            
            $newline .= $value;
        }
        
        if($body_rank_postos != "")
        {
            $body_rank_postos .= "\n";
        }
        
        $body_rank_postos .= $newline;
    }
    
    
    $body_rank_postos = $cols_names . "\n" . $body_rank_postos;
    
    
    $data_values_wins = @$utils->winsColsArray($rank_avg);
    
    
    $data_values_ties = @$utils->tiesColsArray($rank_avg);
    $data_values_losses = @$utils->lossesColsArray($rank_avg);
    
    $data_values = @$utils->avgColsArray($rank_avg, $decimalprecision);
    //$data_values_sum = @$utils->sumColsArray($rank_avg, $decimalprecision);
                
        
    $cols_names = trim($cols_names);
    
    $s = explode("\t", $cols_names);
    
    $d = array();
    //$d2 = array();
    $d3 = array();
    $d4 = array();
    $d5 = array();
    
    for($i = 0; $i < count($data_values); $i++)
    {
        $d[$s[$i]] = $data_values[$i];
		//$d2[$s[$i]] = $data_values_sum[$i];
		$d3[$s[$i]] = (isset($data_values_wins[$i])?$data_values_wins[$i]:0);
		$d4[$s[$i]] = (isset($data_values_ties[$i])?$data_values_ties[$i]:0);
		$d5[$s[$i]] = (isset($data_values_losses[$i])?$data_values_losses[$i]:0);
    }
        
        //var_dump($d3);exit();
        
    $data_values_original = $data_values;    
    $data_values = $d;
    //$data_values_sum = $d2;
    $data_values_wins = $d3;
    $data_values_ties = $d4;
    $data_values_losses = $d5;
    
    asort($data_values);  
    
    /*$d = array();
    
    foreach($data_values as $key=>$value)
    {
        $d[$key] = $data_values_sum[$key];
    }
    $data_values_sum = $d;*/
    
    $d = array();
    
    foreach($data_values as $key=>$value)
    {
        $d[$key] = $data_values_wins[$key];
    }
    $data_values_wins = $d;
    
    $d = array();
    
    foreach($data_values as $key=>$value)
    {
        $d[$key] = $data_values_ties[$key];
    }
    $data_values_ties = $d;
        
	$d = array();
    
    foreach($data_values as $key=>$value)
    {
        $d[$key] = $data_values_losses[$key];
    }
    $data_values_losses = $d;
    
    if($columns == "0")
    {
		$data_source2 = implode("\t", $data_values);
		//$data_source2_sum = implode("\t", $data_values_sum);
		$data_source2_wins = implode("\t", $data_values_wins);
		$data_source2_ties = implode("\t", $data_values_ties);
		$data_source2_losses = implode("\t", $data_values_losses);
        
		//$cols_names = trim($cols_names);
		$aux = "";
		
		foreach($data_values as $key=>$item)
		{
			if($aux != "")
			{
				$aux .= "\t";
			}
			
			$aux .= $key;
		}
		
		$cols_names = $aux;
		
		$data_source2 = str_replace(".", ",", $data_source2);
		//$data_source2_sum = str_replace(".", ",", $data_source2_sum);
		
		$data_rank = $cols_names 
					. "\n" . $data_source2 
					//. "\n" . $data_source2_sum 
					. "\n" . $data_source2_wins
					. "\n" . $data_source2_ties
					. "\n" . $data_source2_losses;
		
	}

	if($columns == "1")
	{
		
		$data_values_wins1 = array();
		
		foreach($original_columns as $item)
		{
			foreach($data_values_wins as $key=>$value)
			{
				if($key == $item)
				{
					$data_values_wins1[] = $value;
				}					
			}						
		}
		
		$data_values_ties1 = array();
		
		foreach($original_columns as $item)
		{
			foreach($data_values_ties as $key=>$value)
			{
				if($key == $item)
				{
					$data_values_ties1[] = $value;
				}					
			}						
		}	
		
		$data_values_losses1 = array();
		
		foreach($original_columns as $item)
		{
			foreach($data_values_losses as $key=>$value)
			{
				if($key == $item)
				{
					$data_values_losses1[] = $value;
				}					
			}						
		}		
		
		$data_source2 = implode("\t", $data_values_original);
		$data_source2_wins = implode("\t", $data_values_wins1);
		$data_source2_ties = implode("\t", $data_values_ties1);
		$data_source2_losses = implode("\t", $data_values_losses1);
		$cols_names = implode("\t", $original_columns);
    
		$data_rank = $cols_names 
				. "\n" . $data_source2 
				//. "\n" . $data_source2_sum 
				. "\n" . $data_source2_wins
				. "\n" . $data_source2_ties
				. "\n" . $data_source2_losses;
		
		$data_rank = trim($data_rank);
		
	}
	
	
	
	
	
	
	
	
	
    
	if(empty($filename_autoload))
	{
		$filename_autoload = str_replace("/","-",$caption);
		$filename_autoload = str_replace(" ", "", $filename_autoload);
	}

	$filename_img = $filename_autoload;
	
	if(strrpos($filename_img, ".") !== false)
	{		
		$filename_img = substr($filename_img, 0, strrpos($filename_img, "."));
		
		if(substr($filename_img, strlen($filename_img)-1) == "-")
		{
			$filename_img = substr($filename_img, 0, strlen($filename_img)-1);
		}
		
		$filename_img = $source . "-" . $filename_img;
		$filename_img = str_replace(" ", "-", $filename_img);
	}
	
	$filename_img2 = $filename_img;
	
	
	
	if(empty($caption))
	{
		$caption = $filename_img;
		$caption2 = $filename_img;
	}else
	{
		$caption2 = $caption;
		$caption2 = str_replace(" ", "", $caption2);
	}
	
		
		
		//$caption = $source . " - " .$caption;
		
		//var_dump($caption2);exit();
		
    $data_destine = $cols_names . "\n"  // .ucfirst($task)."\n"
        .  $data_source2 . "\n"
        . $caption . "\t" . $countRows;
        
               

    $filename = PATH_USER_WORKSPACE_PROCESSING . "tmp" . str_replace(" ", "-", microtime()) . "";
//     $filename = sys_get_temp_dir() . "/tmp" . str_replace(" ", "-", microtime()) . "";
    
   # $f2 = Properties::getbase_directory_statistical() . "orange3/tmp";
    
//     $data_destine = "svdd\tparzen	knndd	bagging	occluste	OCDCS	ocDESind
// 5,74359	5,051282	4,24359	4,910256	4,384615	4,730769	3,320513
// rene	39";
    
    $utils->setContentFile($filename . ".tmp", $data_destine);

    if (is_file($filename . ".tmp")) {
        
        /*$filename_img = $filename_autoload;
        $filename_img = substr($filename_img, 0, strrpos($filename_img, "."));
        
        if(substr($filename_img, strlen($filename_img)-1) == "-")
        {
			$filename_img = substr($filename_img, 0, strlen($filename_img)-1);
		}*/
        
        $filename_img .= time() . ".png";
        $filename_img = PATH_USER_WORKSPACE_PROCESSING . $filename_img;
        
        if(is_file($filename_img))
        {
			unlink($filename_img);
		}
		
        $command = "python3 " . $nemenyi_bin . "  " . $filename . ".tmp  " . $filename_img . " png";        
        $command = escapeshellcmd($command);
        
        $filename_img2 .= time() . "." . $downloadfile;
        $filename_img2 = PATH_USER_WORKSPACE_PROCESSING . $filename_img2;
        
        if(is_file($filename_img2))
        {
			unlink($filename_img2);
		}
		
        $command2 = "python3 " . $nemenyi_bin . "  " . $filename . ".tmp  " . $filename_img2 . " " . $downloadfile;        
        $command2 = escapeshellcmd($command2);
        
//         $output = $utils->runExternal($command);
        exec($command);
        sleep(1);
        exec($command2);
        sleep(1);        
        
		if(is_readable($filename . ".tmp"))
		{
			unlink($filename . ".tmp");
		}
    }

    $files_tmp = array("png","pdf","eps");
	$dir = new DirectoryIterator(PATH_USER_WORKSPACE_PROCESSING);
	$now = time();
	foreach ($dir as $file) 
	{
		if ($file->isFile()) 
		{		
			if(in_array($file->getExtension(), $files_tmp))
			{			
				//echo $file->getBasename() . "\n";
				//echo "now=".$now ."\ntime=". $file->getCTime() . "\ndiff=". ($now - $file->getCTime()) . "\n\n";
				
				if ($now - $file->getCTime() >= 60 * 60) // 1 hour 
				{
					//echo $file->getBasename() . "\n";
					unlink(PATH_USER_WORKSPACE_PROCESSING . $file->getBasename());
				}
			}
		}
	}

    
    if (is_file($filename_img)) {
		$filename = substr($filename_img, strrpos($filename_img, "/")+1);
		$filename2 = substr($filename_img2, strrpos($filename_img2, "/")+1);
        $src_img = "?component=statistical&controller=figure&filename=" . urlencode($filename);
    }
}

?>

<style>

.dataview{font-size:10px;}
.tableviewrank td{font-size:11px;padding:4px;border-spacing: 4px;text-align:center;}

/* DivTable.com */
.divTable{
	display: table;
	width: 100%;
}
.divTableRow {
	display: table-row;
}
.divTableHeading {
	background-color: #EEE;
	display: table-header-group;
}
.divTableCell, .divTableHead {
	border: 1px solid #999999;
	display: table-cell;
	padding: 3px 10px;
	font-size:11px;
}
.divTableHeading {
	background-color: #EEE;
	display: table-header-group;
	font-weight: bold;
}
.divTableFoot {
	background-color: #EEE;
	display: table-footer-group;
	font-weight: bold;
}
.divTableBody {
	display: table-row-group;
}

#inner {
  display: table;
  margin: 0 auto;
}

</style>

<h1><?php echo $source . " - /" . $folder;?></h1>


<?php
	if(!empty($src_img)){
?>
<div id="outer" style="width:100%">
	<div id="inner">	
		<img style="width:100%" src="<?php echo $src_img;?>" />
	</div>
</div>	
<?php
}
?>
							<form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>">
								<input type="hidden"
									value="<?php echo $application->getComponent()?>"
									name="component"> <input type="hidden"
									value=<?php echo $application->getController()?>
									name="controller"> <input type="hidden" value="Nemenyi"
									name="task" id="task">
									<input type="hidden" value="<?php echo $source;?>"
									name="source" id="source">
									<input type="hidden" value="<?php echo $folder;?>"
									name="folder" id="folder">
																	

<div id="outer" style="width:100%">
  


									<div
										id="inner">	
										


										<?php
										
										if(!empty($data_rank))
										{
											$data = explode("\n",$data_rank);
											$data_names = explode("\t",$data[0]);
											$data_values_avg = explode("\t",$data[1]);
											//$data_values_sum = explode("\t",$data[2]);
											$data_values_wins = explode("\t",$data[2]);
											$data_values_ties = explode("\t",$data[3]);
											$data_values_losses = explode("\t",$data[4]);
												
											$custom = array();
											$index = 0;
											$data_rank_view = "";
											$aux = "";
											
											foreach($data_names as $value)
											{
												if($index != 0)
												{
													$data_rank_view .= "\t";
													//$aux .= "\t";
												}
												
												$data_rank_view .= $value;
												$custom[$index] = array("key"=>$value,"sum"=>0,"avg"=>0, 
																		"wins"=>0, "ties"=>0,"losses"=>0,
																		"lossesorder"=>0, "lossesorder2"=>0, "avgrank"=>0, "allwins"=>0);
												$index++;
												//$aux .= $index;
											}
											$data_rank_view .=  "\n";// . $aux . "\n";	
											
											
								
											// *********
											// wins
											// 
											$index = 0;	
											
											foreach($data_values_wins as $value)
											{
												if($index != 0)
												{
													$data_rank_view .= "\t";
												}
												
												$data_rank_view .= $value;
												$custom[$index]["wins"] = $value;
												$index++;
											}
											
											$data_rank_view .= "\n";
											
											// *********
											// wins
											// *****************
											
											
											// *********
											// ties
											//
											
											$index = 0;	
											
											foreach($data_values_ties as $value)
											{
												if($index != 0)
												{
													$data_rank_view .= "\t";
												}
												
												$data_rank_view .= $value;
												$custom[$index]["ties"] = $value;
												$index++;
											}
											
											$data_rank_view .= "\n";	
											
											// *********
											// ties
											// *****************
											
											
											
											// *********
											// losses
											//
											
											$index = 0;	
											$losses = array();
											
											foreach($data_values_losses as $value)
											{
												if($index != 0)
												{
													$data_rank_view .= "\t";
												}
												
												$data_rank_view .= $value;
												$custom[$index]["losses"] = $value;
												$losses[] = $value;
												$index++;
											}
											
											$data_rank_view .= "\n";
											
											// *********
											// losses
											// *****************
											
											
											// *********
											// wins + ties
											//
											
											$index = 0;	
											
											foreach($custom as $key=>$item)
											{
												$custom[$key]["allwins"] = $custom[$key]["wins"] + $custom[$key]["ties"];
												
												if($index != 0)
												{
													$data_rank_view .= "\t";
												}
												
												$data_rank_view .= $custom[$key]["allwins"];
												
												$index++;
											}
														
											$data_rank_view .= "\n";
																						
											// *********
											// wins + ties
											// *****************
											
											
											// *********
											// average rank
											// 
											$index = 0;
																	
											foreach($data_values_avg as $value)
											{
												if($index != 0)
												{
													$data_rank_view .= "\t";													
												}
												
												$data_rank_view .= $value;
												$custom[$index]["avg"] = $value;
												$index++;												
											}
											
											$data_rank_view .= "\n";
											
											// *********
											// average rank
											// *****************
												
											
											// *********
											// position average 
											// 
											$index = 0;
											$position_avg = array();
																	
											foreach($custom as $index=>$item)
											{
												foreach($item as $key=>$value)
												{
													if($key == "avg")
													{
														$position_avg[$index] = $value;
													}
												}
										
											}
											
											$aux_position_avg = $position_avg;
											
											asort($position_avg);
												
											$index = 1;
											$aux_avg = array();
											
											foreach($position_avg as $key=>$value)
											{
												foreach($aux_position_avg as $key2=>$value2)
												{
													if($value == $value2)
													{
														//$aux_avg[$key] = $index;
														$custom[$key]["avgrank"] = $index;
														$index++;
													}
												}
												
											}
											

											/*foreach($aux_avg as $key=>$value)
											{
												$custom[$key]["avgrank"] = $value;
											}*/
											
											$index = 0;
											
											for($i = 0; $i < count($custom); $i++)
											{
												if($index != 0)
												{
													$data_rank_view .= "\t";													
												}
												
												$data_rank_view .= $custom[$i]["avgrank"];
												$index++;
											}											
											
																						
											$data_rank_view .= "\n";
											
											// *********
											// position average
											// *****************
											
											
											
											
											/*$index = 0;	
											$losses = array();
											
											foreach($data_values_losses as $value)
											{
												if($index != 0)
												{
													$data_rank_view .= "\t";
												}
												
												$data_rank_view .= $value;
												$custom[$index]["losses"] = $value;
												$losses[] = $value;
												$index++;
											}*/
											
											//$rank_losses_order = $utils->avgColsArray($custom[$index]["losses"]);
											//var_dump($rank_losses_order);exit("=");
											
											/*$index = 0;	
											foreach($losses as $value)
											{												
												$losses2[$index][] = $utils->rank_avg($value, $losses, 1);
												$index++;
											}*/
											
											
											
											//$data_rank_view .= "\n";	
											/*$index = 0;	
											
											foreach($losses2 as $item)
											{												
												foreach($item as $key=>$value)
												{
													//$data_rank_view .= $value;
													$custom[$index]["lossesorder"] = $value;
												}
																								
												$index++;
											}*/
											
											
											//var_dump($custom);exit("==");
											
											$index = 0;
											$losses = array();
											
											foreach($custom as $item)
											{
												
												foreach($item as $key=>$value)
												{
													if($key == 'lossesorder2')
													{
														$losses[] =  
															$custom[$index]["wins"]
															+ ($custom[$index]["ties"]/2);
													}
												}
												
												$index++;
											}
											
											$index = 0;	
											foreach($losses as $value)
											{												
												$losses2[$index][] = $utils->rank_avg($value, $losses, 0);
												$index++;
											}
											
											$index = 0;	
											
											foreach($losses2 as $item)
											{
												foreach($item as $key=>$value)
												{													
													$custom[$index]["lossesorder2"] = intval($value);
												}
																								
												$index++;
											}											
											

											$index = 0;
											$losses = array();
											
											foreach($custom as $item)
											{												
												$losses[] = intval($custom[$index]["lossesorder2"]) + intval($custom[$index]["lossesorder"]);																								
												$index++;
											}
											
											$index = 0;	
											$losses2 = array();
											
											foreach($losses as $value)
											{												
												$losses2[$index][] = $utils->rank_avg($value, $losses, 1);
												$index++;
											}
											
											$index = 0;
											$aux_losses = array();
											
											foreach($losses2 as $item)
											{												
												foreach($item as $key=>$value)
												{
													$custom[$index]["lossesorder"] = $value;
													$aux_losses[] = $value;
												}
																								
												$index++;
											}
											
											$index = 1;
											$index_aux = 0;
											$aux = array();
											$aux2 = array();
											
											
											asort($aux_losses);											
											
											
											foreach($aux_losses as $key=>$item)
											{
												if(count($aux) == 0)
												{
													$aux[$key] = $index;	
													$aux2[] = $item;																																																
												}	
												else
												{
													if($aux2[count($aux2)-1] == $item)
													{
														$aux[$key] = $index;
														$aux2[] = $item;	
													}
													else
													{
														$index++;
														$aux[$key] = $index;	
														$aux2[] = $item;														
													}														
													
													$index_aux++;												
												}
																								
											}
											
															//var_dump($aux_losses);							
											//var_dump($aux);exit();
											
											$index = 0;
											$data_rank_view_s = array();
											
											foreach($aux as $key=>$item)
											{
												$custom[$key]["lossesorder"] = $item;
												$data_rank_view_s[$key] = $item;
											}
											
											$index = 0;
											
											foreach($data_rank_view_s as $key=>$item)
											{
												if($index != 0)
												{
													$data_rank_view .= "\t";
												}
												$data_rank_view .= $data_rank_view_s[$index];
												$index++;
											}

											
											
											
											//var_dump($losses2);exit();
											
											//var_dump($custom);exit();
											
										


											$data_rank_view .= "\n";// . $aux;
											$index = 1;
											
											echo "<div style='float:left;width:auto;border: 1px solid #999999;font-size:12px;text-aling:center;padding: 3px 3px;'>";
											echo "<div style='text-align: center; border-bottom:1px solid #eeeeee;font-weight: bold;width: inherit;'>Named</div>";											
											//echo "<div style='text-align: center; width: inherit;border-bottom:1px solid #eeeeee;'>Sum</div>";											
											echo "<div style='text-align: center; width: inherit;font-weight: bold;border-bottom:1px solid #eeeeee;'>Wins</div>";
											echo "<div style='text-align: center; width: inherit;font-weight: bold;border-bottom:1px solid #eeeeee;'>Ties</div>";
											echo "<div style='text-align: center; width: inherit;font-weight: bold;border-bottom:1px solid #eeeeee;'>Losses</div>";
											echo "<div style='text-align: center; width: inherit;font-weight: bold;border-bottom:1px solid #eeeeee;'>All Wins</div>";
											//echo "<div style='text-align: center; width: inherit;font-weight: bold;border-bottom:1px solid #eeeeee;'>Order</div>";
											echo "<div style='text-align: center; width: inherit;border-bottom:1px solid #eeeeee;font-weight: bold;'>Average Rank</div>";
											echo "<div style='text-align: center; width: inherit;border-bottom:1px solid #eeeeee;font-weight: bold;'>Position Average</div>";											
											echo "<div style='text-align: center; width: inherit;font-weight: bold;'>Position Wins</div>";
											echo "</div>";
												
											foreach($custom as $item)
											{
												
												echo "<div style='float:left;width:auto;border: 1px solid #999999;font-size:12px;text-aling:center;padding: 3px 3px;'>";
												echo "<div style='text-align: center; border-bottom:1px solid #eeeeee;font-weight: bold;width: inherit;'>" . $item['key'] . "</div>";		
												
												//echo "<div style='text-align: center; width: inherit;border-bottom:1px solid #eeeeee;'>" . $item['sum'] . "</div>";												
												echo "<div style='text-align: center; width: inherit;border-bottom:1px solid #eeeeee;'>" . $item['wins'] . "</div>";
												echo "<div style='text-align: center; width: inherit;border-bottom:1px solid #eeeeee;'>" . $item['ties'] . "</div>";
												echo "<div style='text-align: center; width: inherit;border-bottom:1px solid #eeeeee;'>" . $item['losses'] . "</div>";
												echo "<div style='text-align: center; width: inherit;border-bottom:1px solid #eeeeee;'>" . $item['allwins'] . "</div>";
												//echo "<div style='text-align: center; width: inherit;border-bottom:1px solid #eeeeee;'>" . $item['lossesorder'] . "</div>";
												echo "<div style='text-align: center; width: inherit;border-bottom:1px solid #eeeeee;'>" . $item['avg'] . "</div>";
												echo "<div style='text-align: center; width: inherit;border-bottom:1px solid #eeeeee;'>" . $item['avgrank'] . "</div>";
												echo "<div style='text-align: center; width: inherit;'>" . $item['lossesorder'] . "</div>";
												echo "</div>";
												$index++;
											}
											

										}
										?>
										

											<br />

<?php
	if(!empty($src_img)){
		
		$file_ext = substr($filename2, strrpos($filename2, ".")+1);
		if($file_ext == "png"){
?>	
	<a target="_blank" href="?component=statistical&controller=figure&attachment=&filename=<?php echo urlencode($filename2);?>">Download file</a>&nbsp;
<?php
	}else{
?>
	<a href="?component=statistical&controller=figure&attachment=true&filename=<?php echo urlencode($filename2);?>">Download file</a>&nbsp;
<?php		
	}
}
?>
	<a target="_blank" href="?component=statistical&controller=printpreview&attachment=false&tmpl=tmpl&filename=<?php echo urlencode($filename);?>&datasource=<?php echo urlencode(base64_encode($data_rank));?>">Print Preview</a>
										
										
										</div>
</div>



									<div style="float: left;width: 100%; margin-top: 5px;">
										 <label>Caption <input type="text" value="<?php echo $caption;?>"
									name="caption" id="caption" style="width:500px"></label><br>
									
									<label>Order View Columns <select name="columns" id="columns">
													<option value="0">Average Rank</option>
													<option value="1">Original</option>
												</select>
									</label>
									
									<label>Precision<input type="text" name="decimalprecision" id="decimalprecision" value="<?php echo $decimalprecision;?>" style="width:40px;" /></label>
					
									<label>Rank order <select name="order" id="order">
													<option value="0">ASC</option>
													<option value="1">DESC</option>
												</select>
									</label>
									
									
										<span style="width:100%;border-bottom:1px solid #cccccc">Download file format</span>										
										<label><input type="radio" name="downloadfile" id="downloadfile" value="png"/>png</label>
										<label><input type="radio" name="downloadfile" id="downloadfile" value="eps"/>eps</label>
										<label><input type="radio" name="downloadfile" id="downloadfile" value="pdf"/>pdf</label>
										
										<input
											type="submit" class="btn btn-success" value="Execute"> 
											
									</div>
											
									<div
										style="float: left;  width: 100%; margin-top: 5px;">

										<textarea  class="dataview" id="data_source" style="width: 100%; height: 200px;"
											name="data_source"><?php echo $data_source?></textarea>
											
										<textarea  class="dataview" id="rankview" style="width: 100%; height: 150px;"
											name="rankview"><?php echo $data_rank_view?></textarea>
																					
<?php if($body_rank_postos != ""){?>
										<textarea  class="dataview" id="body_rank_postos" style="width: 100%; height: 200px;"
											name="body_rank_postos"><?php echo $body_rank_postos?></textarea>

<?php }?>
									</div>		
					

							</form>

<script>


function SetSelectIndexRadio(idElement, elementText)
{
	//var elementObj = document.getElementById(idElement);
	var elementObj = document.getElementsByName(idElement);
	
	for (var i = 0; i < elementObj.length; i++) 
	{		
		if (elementObj[i].value == elementText) 
		{
			elementObj[i].checked = true;
			break;
		}
	}
}

function SetSelectIndex(idElement, elementText)
{
    var elementObj = document.getElementById(idElement);
//alert("id"+elementObj.id);

    for(i = 0; i < elementObj.length; i++)
    {
      // check the current option's text if it's the same with the input box
      if (elementObj.options[i].value == elementText)
      {
         elementObj.selectedIndex = i;
         break;
      }     
    }
}

SetSelectIndex("order","<?php echo $order?>");
SetSelectIndex("columns","<?php echo $columns?>");
SetSelectIndexRadio("downloadfile", "<?php echo $downloadfile?>");

</script>
