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

if($downloadfile == null)
{
	$downloadfile = "png";	
}

$data_rank = "";

$data_result = "";
$data_diff_statistical = "";
$src_img = "";
    
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
				
				if($cols_names != "")
				{
					$cols_names .= "\t";
				}
				
                if (is_numeric(trim($cols_))) 
                {
                    $cols_names .= "A" . $i;
                    $i ++;
                } 
                else 
                {
                    $cols_names .= $cols;// . "\t";
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

    $data_values_wins = @$utils->winsColsArray($rank_avg);
    //var_dump($rank_avg);exit();
    
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
													$aux .= "\t";
												}
												
												$data_rank_view .= $value;
												$custom[$index] = array("key"=>$value,"sum"=>0,"avg"=>0, 
																		"wins"=>0, "ties"=>0,"losses"=>0,
																		"lossesorder"=>0, "lossesorder2"=>0);
												$index++;
												$aux .= $index;
											}
											$data_rank_view .=  "\n" . $aux . "\n";	
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
											
											/*$data_rank_view .= "\n";	
											$index = 0;				
																	
											foreach($data_values_sum as $value)
											{
												if($index != 0)
												{
													$data_rank_view .= "\t";
												}
												
												$data_rank_view .= $value;
												$custom[$index]["sum"] = $value;
												$index++;
											}*/
											$data_rank_view .= "\n";	
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
											
											//$rank_losses_order = $utils->avgColsArray($custom[$index]["losses"]);
											//var_dump($rank_losses_order);exit("=");
											
											$index = 0;	
											foreach($losses as $value)
											{												
												$losses2[$index][] = $utils->rank_avg($value, $losses, 1);
												$index++;
											}
											
											
											
											$data_rank_view .= "\n";	
											$index = 0;	
											
											foreach($losses2 as $item)
											{
												/*if($index != 0)
												{
													$data_rank_view .= "\t";
												}*/
												
												foreach($item as $key=>$value)
												{
													//$data_rank_view .= $value;
													$custom[$index]["lossesorder"] = $value;
												}
																								
												$index++;
											}
											
											
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
													/*if($index != 0)
													{
														$data_rank_view .= "\t";
													}
													
													$data_rank_view .= $value;*/
													
													$custom[$index]["lossesorder2"] = intval($value);
												}
																								
												$index++;
											}											
											

											$index = 0;
											$losses = array();
											
											foreach($custom as $item)
											{												
												$losses[] = $custom[$index]["lossesorder2"] + $custom[$index]["lossesorder"];																								
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
											
											foreach($losses2 as $item)
											{
												if($index != 0)
												{
													$data_rank_view .= "\t";
												}
												
												foreach($item as $key=>$value)
												{
													$data_rank_view .= $value;
													$custom[$index]["lossesorder"] = $value;
												}
																								
												$index++;
											}
											
											//var_dump($losses2);exit();
											
											//var_dump($custom);exit();
											
											
											$data_rank_view .= "\n";// . $aux;
											$index = 1;
											
											echo "<div style='float:left;width:auto;border: 1px solid #999999;font-size:12px;text-aling:center;padding: 3px 3px;'>";
											echo "<div style='text-align: center; border-bottom:1px solid #eeeeee;font-weight: bold;width: inherit;'>Named</div>";
											echo "<div style='text-align: center; width: inherit;border-bottom:1px solid #eeeeee;font-weight: bold;'>Order</div>";
											echo "<div style='text-align: center; width: inherit;border-bottom:1px solid #eeeeee;font-weight: bold;'>Average</div>";
											//echo "<div style='text-align: center; width: inherit;border-bottom:1px solid #eeeeee;'>Sum</div>";											
											echo "<div style='text-align: center; width: inherit;font-weight: bold;border-bottom:1px solid #eeeeee;'>Wins</div>";
											echo "<div style='text-align: center; width: inherit;font-weight: bold;border-bottom:1px solid #eeeeee;'>Ties</div>";
											echo "<div style='text-align: center; width: inherit;font-weight: bold;border-bottom:1px solid #eeeeee;'>Losses</div>";
											//echo "<div style='text-align: center; width: inherit;font-weight: bold;border-bottom:1px solid #eeeeee;'>Order</div>";
											echo "<div style='text-align: center; width: inherit;font-weight: bold;'>Order</div>";
											echo "</div>";
												
											foreach($custom as $item)
											{
												
												echo "<div style='float:left;width:auto;border: 1px solid #999999;font-size:12px;text-aling:center;padding: 3px 3px;'>";
												echo "<div style='text-align: center; border-bottom:1px solid #eeeeee;font-weight: bold;width: inherit;'>" . $item['key'] . "</div>";
												echo "<div style='text-align: center; width: inherit;border-bottom:1px solid #eeeeee;'>" . $index . "</div>";
												echo "<div style='text-align: center; width: inherit;border-bottom:1px solid #eeeeee;'>" . $item['avg'] . "</div>";
												//echo "<div style='text-align: center; width: inherit;border-bottom:1px solid #eeeeee;'>" . $item['sum'] . "</div>";												
												echo "<div style='text-align: center; width: inherit;border-bottom:1px solid #eeeeee;'>" . $item['wins'] . "</div>";
												echo "<div style='text-align: center; width: inherit;border-bottom:1px solid #eeeeee;'>" . $item['ties'] . "</div>";
												echo "<div style='text-align: center; width: inherit;border-bottom:1px solid #eeeeee;'>" . $item['losses'] . "</div>";
												//echo "<div style='text-align: center; width: inherit;border-bottom:1px solid #eeeeee;'>" . $item['lossesorder'] . "</div>";
												echo "<div style='text-align: center; width: inherit;'>" . $item['lossesorder'] . "</div>";
												echo "</div>";
												$index++;
											}
											
											
											
											
											//echo "<table border=1 class='tableviewrank'>";
											//echo "<tr>";
											/*echo '
											<div class="divTable" style="border: 1px solid #000;" >
											<div class="divTableBody">
												<div class="divTableRow">';
												
											foreach($data_names as $value)
											{
												echo '<div class="divTableCell">' . $value . "</div>";
											}
											echo "</div>";
											echo '<div class="divTableRow">';
											
											foreach($data_values as $value)
											{
												echo '<div class="divTableCell">' . $value . "</div>";
											}
											echo "</div>
													</div>
												</div>";*/
										}
										?>
										

											<br />

<?php
	if(!empty($src_img)){
?>

	<a href="?component=statistical&controller=figure&attachment=&filename=<?php echo urlencode($filename2);?>">Download file</a>&nbsp;
<?php
}
?>
	<a target="_blank" href="?component=statistical&controller=printpreview&attachment=false&tmpl=tmpl&filename=<?php echo urlencode($filename);?>&datasource=<?php echo urlencode(base64_encode($data_rank));?>">Print Preview</a>
										
										
										</div>
</div>



									<div style="float: left;width: 100%; margin-top: 5px;">
										 <label>Caption <input type="text" value="<?php echo $caption;?>"
									name="caption" id="caption"></label>
									
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

										<textarea  class="dataview" id="rankview" style="width: 100%; height: 50px;"
											name="rankview"><?php echo $data_rank_view?></textarea>
											
										<textarea  class="dataview" id="data_source" style="width: 100%; height: 200px;"
											name="data_source"><?php echo $data_source?></textarea>



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
SetSelectIndexRadio("downloadfile", "<?php echo $downloadfile?>");

</script>
