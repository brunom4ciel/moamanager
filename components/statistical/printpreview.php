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


Framework::import("Utils", "core/utils");
$utils = new Utils();

$data_source = $application->getParameter("datasource");
$filename = $application->getParameter("filename");
$task = $application->getParameter("task");
$data_rank = "";

if(!empty($filename))
{
	if(is_readable(PATH_USER_WORKSPACE_PROCESSING . $filename))
	{		
		$src_img = "?component=statistical&controller=figure&filename=" . urlencode($filename);
	}
}

if (!empty($data_source) )
{    
    $data_rank = base64_decode(urldecode($data_source));    
    
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

<img style="width:100%" src="<?php echo $src_img;?>" />
<br />


									
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
										

											</div>
											
</div>


