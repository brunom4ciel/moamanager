<?php
/**
 * @package    MOAM.Application
*
* @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/


namespace moam\components\extract;

defined('_EXEC') or die;

use moam\core\Framework;
// use moam\core\Application;
use moam\core\Properties;
use moam\core\Template;
use moam\libraries\core\utils\Utils;
// use moam\libraries\core\menu\Menu;
use moam\libraries\core\mining\Mining;


if (!class_exists('Application'))
{
    $application = Framework::getApplication();
}

if(!$application->is_authentication())
{
    $application->alert ( "Error: you do not have credentials." );
}

// Template::setDisabledMenu();

// Framework::import("menu", "core/menu");

// if (!class_exists('Menu'))
// {
//     $menu = new Menu();
    
// }

Framework::import("Utils", "core/utils");
Framework::import("Mining", "core/mining");


Template::addHeader(array("tag"=>"link",
    "type"=>"text/css",
    "rel"=>"stylesheet",
    "href"=>""
    . $application->getPathTemplate()
    . "/css/table-excel.css"));

Template::addHeader(array("tag"=>"script",
    "type"=>"text/javascript",
    "src"=>""
    . $application->getPathTemplate()
    . "/javascript/base64.js"));

Template::addHeader(array("tag"=>"script",
    "type"=>"text/javascript",
    "src"=>""
    . $application->getPathTemplate()
    . "/javascript/edit_area/edit_area_full.js"));

$utils = new Utils();


$csv = "";
$result_view = "";

Template::setDisabledMenu();

$task = $application->getParameter("task");
$folder = $application->getParameter("folder");

if($folder != null){
    if(substr($folder, strlen($folder)-1)!="/"){
        $folder .= DIRECTORY_SEPARATOR;
    }
}

$scripts = "";

/*
 function htmlCSV($csv){
 
 $elements_classifieds_colors = array("#00FF00", "#00FFFF", "#FFFF00", "#FF7F24", "#FF00FF");
 $elements_classfieds_order = array("First", "second", "Third", "Fourth", "Fifth");
 
 
 
 $output="";
 $z=1;
 $table1 = "";
 $table2 = "";
 $itens = explode("\n",$csv);
 $avg_count=0;
 
 foreach($itens as $key=>$item){
 
 $cols = explode("\t", $item);
 
 if(count($cols)>0){
 
 if(empty($avg)){
 
 $bg_color="#ffffff";
 
 }else{
 
 if($avg==$avg_count){
 $bg_color="#cccccc";
 $avg_count=0;
 }else{
 $bg_color="#ffffff";
 $avg_count++;
 }
 
 }
 
 //echo "avg=".$avg.", avg_count=".$avg_count."<br>";
 
 $cols_v=false;
 foreach($cols as $key=>$item){
 if(trim($item)!=""){
 $cols_v = true;
 break;
 }
 }
 
 if($cols_v==true){
 
 $table1 .= "<tr>";
 $table1 .= "<th>".$z."</th>";
 
 $table2 .= "<tr style='background-color:$bg_color'>";
 // /classify_max
 
 foreach($cols as $key=>$item){
 if(trim($item)!=""){
 
 //if($classify_max==1){
 //	$table2 .= "<td bgcolor='{$elements_classifieds_colors[0]}'>".$item."</td>";
 //}else{
 $table2 .= "<td>".$item."</td>";
 //}
 
 
 }
 
 }
 
 $table2 .= "</tr>";
 $table1 .= "</tr>";
 $z++;
 
 }
 
 }
 
 
 }
 
 
 $result = 	"<table class=\"excel\" style=\"float:left;width:auto;display:table-cell;\">"
 .$table1."
 </table>
 <table class=\"excel\" style=\"float:left;width:auto;display:table-cell;\">
 <tbody>".$table2."
 </tbody>
 </table>";
 
 return $result;
 }
 */




$type_extract = $application->getParameter("type_extract");
$statisticaltest = $application->getParameter("statisticaltest");



if($task == "folder"){
    
    
    
}else{
    
    $filename = Properties::getBase_directory_destine($application)
    .$application->getUser()
    .DIRECTORY_SEPARATOR
    .$application->getParameter("folder")
    .$application->getParameter("filename");
    
    if($task == "preview"){
        
        $csv = $utils->getContentFile($filename);
        //echo $csv;
        
        //exit();
        
    }else if($task == "download"){
        
        
        //Header('Content-Disposition: attachment; filename=pedidos.csv');
        
        ob_end_clean();
        
        Header('Content-Description: File Transfer');
        
        
        $extension = $application->getParameter("filename");
        $extension = substr($extension, strrpos($extension, ".")+1);
        
        switch($extension){
            
            case 'tex':
                
                header( 'Content-Type: application/x-tex' );
                break;
            case 'csv':
                
                header( 'Content-Type: text/csv' );
                
                break;
            case 'html':
                
                header( 'Content-Type: text/html' );
                break;
                
        }
        
        
        //Header('Content-Type: application/force-download');
        header( 'Content-Disposition: attachment;filename='
            .$application->getParameter("filename"));
        
        echo $utils->getContentFile($filename);
        
        $contLength = ob_get_length();
        header( 'Content-Length: '.$contLength);
        
        exit();
        
    }else if($task == "view"){
        
        //Header('Content-Description: File Transfer');
        //Header('Content-Type: application/force-download');
        //Header('Content-Disposition: attachment; filename=pedidos.csv');
        
        //ob_end_clean();
        
        //echo "<pre>";
        $scripts = $utils->getContentFile($filename);
        //echo "</pre>";
        
        //$contLength = ob_get_length();
        //header( 'Content-Length: '.$contLength);
        
        //exit();
        
        
    }else if($task == "extract"){
        
        
        $element = $application->getParameter("element");
        
        $metricstract = $application->getParameter("metricstract");
        
        $metricstracts = $application->getParameter("metricstracts");
        $descriptivestatistics = $application->getParameter("descriptivestatistics");
                
        
        //$fp = $application->getParameter("fp");
        //	$fn = $application->getParameter("fn");
        $interval = $application->getParameter("interval");
        $breakline = $application->getParameter("breakline");
        $resume = $application->getParameter("resume");
        $detector = $application->getParameter("detector");
        $detectorsum = $application->getParameter("detectorsum");
        
//         $metrics = array();
//         $metrics["accuracy"] = null;
//         $metrics["timer"] = null;
//         $metrics["memory"] = null;
//         $metrics["dissimilarity"] = null;
//         $metrics["dist"] = null;
//         $metrics["fn"] = null;
//         $metrics["fp"] = null;
//         $metrics["tn"] = null;
//         $metrics["tp"] = null;
//         $metrics["precision"] = null;
//         $metrics["recall"] = null;
//         $metrics["mcc"] = null;
//         $metrics["f1"] = null; 
//         $metrics["resume"] = null; 
//         $metrics["mdr"] = null;
//         $metrics["mtfa"] = null;
//         $metrics["mtd"] = null;
//         $metrics["mtr"] = null; 
// //         $metrics["mcclist"] = null; 
        
        
//         if(!empty($metricstract))
//         {
//             $metrics[$metricstract] = 1;     
//         }
        
        
        
        $column = $application->getParameter("column");
        
        // 			$process_type = $application->getParameter("process_type");
        $decimalformat = $application->getParameter("decimalformat");
        
        $decimalprecision = $application->getParameter("decimalprecision");
        
        if($decimalprecision == null)
        {
            $decimalprecision = 2;
        }
        
        if(empty($decimalformat))
        {
            $decimalformat = ".";
        }

        $parameters = array();
        $parameters_metrics = array("accuracy","time","memory","detectionaccuracy","entropy",
            "mdr", "mtfa", "mtd", "mtr", "dist", "precision", "recall", "mcc", "f1",
            "fn", "fp", "tn", "tp"
            );
        
        foreach($parameters_metrics as $item){
            $parameters[$item] = ($metricstracts==$item?1:0);
        }
        
        $parameters["descriptivestatistics"] = $descriptivestatistics;
        
        $parameters["type_extract"] = ($type_extract==null?0:1);
        $parameters["column"] = ($column==null?0:1);
        $parameters["interval"] = ($interval==null?0:1);
        $parameters["decimalformat"] = ($decimalformat==null?".":$decimalformat);
        $parameters["decimalprecision"] = ($decimalprecision==null?".":$decimalprecision);
        
        
        
//         $parameters = array("accuracy"=>($metricstracts=="accuracy"?0:1),
//             "type_extract"=>($type_extract==null?0:1),
//             "timer"=>($metrics["timer"]==null?0:1),
//             "memory"=>($metrics["memory"]==null?0:1),
//             "dissimilarity"=>($metrics["dissimilarity"]==null?0:1),
//             //"fp"=>($fp==null?0:1),
//             //"fn"=>($fn==null?0:1),
//             "column"=>($column==null?0:1),
//             "interval"=>($interval==null?0:1),
//             "dist"=>($metrics["dist"]==null?0:1),
//             "fn"=>($metrics["fn"]==null?0:1),
//             "fp"=>($metrics["fp"]==null?0:1),
//             "tn"=>($metrics["tn"]==null?0:1),
//             "tp"=>($metrics["tp"]==null?0:1),
//             "precision"=>($metrics["precision"]==null?0:1),
//             "recall"=>($metrics["recall"]==null?0:1),
//             "mcc"=>($metrics["mcc"]==null?0:1),
//             "f1"=>($metrics["f1"]==null?0:1),
//             "resume"=>($metrics["resume"]==null?0:1),
//             "mdr"=>($metrics["mdr"]==null?0:1),
//             "mtfa"=>($metrics["mtfa"]==null?0:1),
//             "mtd"=>($metrics["mtd"]==null?0:1),
//             "mtr"=>($metrics["mtr"]==null?0:1),
// //             "mcclist"=>($metrics["mcclist"]==null?0:1),
//             "decimalformat"=>($decimalformat==null?".":$decimalformat),
//             "decimalprecision"=>($decimalprecision==null?".":$decimalprecision),
//             "detector"=>($detector==null?0:1),
//             //  "mcc"=>($mcc==null?0:1),
//         // "f1"=>($f1==null?0:1),
//         "detectorsum"=>($detectorsum==null?0:1)
//         );
        
        
        $dir = Properties::getBase_directory_destine($application)
        .$application->getUser()
        .DIRECTORY_SEPARATOR
        .$application->getParameter("folder");
                
        $mining = new Mining();
        
        $mining_store = array();
        $mining_store2 = array();
        
        $two_folders = 0;
        
        
        
        
        if($type_extract!=2)
        {
            
            $template_file = $application->getParameter("template_file");
            
            if($template_file != null)
            {
				$filename_template_dataset = $dir . $template_file;
			}
            
            /*foreach($element as $key=>$item)
            {            
				$ext = "";
				
				if(is_file($dir . $item))
				{					
					if(strpos($item,".") !== false)
					{
						$ext = substr($item, strrpos($item, ".")+1);
						
						if(in_array($ext, array("tmpl")))
						{
							$filename_template_dataset = $dir . $item;
						}
					}
				}
				
			}*/

            //$filename_template_dataset = $dir . "template.txt";
            $template_user = FALSE;
            
            if(file_exists($filename_template_dataset))
            {
                $data_tmpl = $utils->getContentFile($filename_template_dataset);
                
                $s = json_decode($data_tmpl);
     
                if($s == null)
                {
                    $template_user = FALSE;
                }
                else 
                {
                    if(isset($s->order))
                    {
                        if(isset($s->order->list))
                        {
                            $data_order_tmpl = $s->order->list;
                        }
                        if(isset($s->order->enable))
                        {
                            $data_order_enable_tmpl = $s->order->enable;
                        }                    
                    }
                    
                    if(isset($s->renames))
                    {
                        if(isset($s->renames->list))
                        {
                            $data_renames_tmpl = $s->renames->list;
                        }
                        if(isset($s->renames->enable))
                        {
                            $data_renames_enable_tmpl = $s->renames->enable;
                        }
                    }
                    
                    if(isset($s->filter))
                    {
                        if(isset($s->filter->columns))
                        {
                            $data_filter_tmpl = $s->filter->columns;
                        }
                        if(isset($s->filter->lines))
                        {
                            $data_filter_lines_tmpl = $s->filter->lines;
                        }
                        if(isset($s->filter->enable))
                        {
                            $data_filter_enable_tmpl = $s->filter->enable;
                        }
                    }
                               
                    if(isset($s->datasets))
                    {
                        if(isset($s->datasets->list))
                        {
                            $data_tmpl = $s->datasets->list;
                        }
                        if(isset($s->datasets->name))
                        {
                            $datasets_name = $s->datasets->name;
                        }
                        if(isset($s->datasets->enable))
                        {
                            $datasets_enable = $s->datasets->enable;
                        }
                                           
                        if(isset($datasets_enable))
                        {
                            if($datasets_enable)
                            {
                                $tmpl_lines = array();
                                
                                foreach($data_tmpl as $key=>$item){
                                    
                                    if(substr(trim($item),0,1)!="#" && trim($item) != ""){
                                        //                     $tmpl_lines[] = trim($item);
                                        array_push($mining_store2, array("dirname"=>$datasets_name, "results"=>array(array("Dataset"=>trim($item)))));
                                    }
                                }
                            }
                        }
                        
                    }
                    
                    //             $data_tmpl = explode("\n", $data_tmpl);
                    
                    
                    
                    $template_user = TRUE;
                    
                }
            }
            else 
            {
                
                
//                 foreach($data_tmpl as $key=>$item){
                    
//                     if(substr(trim($item),0,1)!="#" && trim($item) != ""){
//                         //                     $tmpl_lines[] = trim($item);
//                         array_push($mining_store2, array("dirname"=>$datasets_name, "results"=>array(array("Dataset"=>trim($item)))));
//                     }
//                 }
            }
            
            
            //if(count($mining_store2) > 0)
            //{
//                 $template_user = TRUE;
            //}
            
        }
        
        
        $scripts = "";
        
        
        
        foreach($element as $key=>$item){
            
            if(is_file($dir.$item)){
                
                
                $from_file = $dir.$item;//.DIRECTORY_SEPARATOR;
                
                //echo $from_file."<br>";
                
                
                if($type_extract==2){
                    
                    if($scripts != "")
                    {
                        $scripts .= "\n\n";
                    }
                    
                    $scripts .= $mining->getScriptMOA($from_file);
                    
                    
                }else{
                    
                    
                    if($type_extract==1){
                        
//                         exit("fim");
                        $miningResult = $mining->extract_averages_in_file($from_file, $parameters);
                        
//                         var_dump($miningResult);exit("ok");
                        
                    }else{
                        //all
                        
                        if($detector == 1 || $detectorsum == 1)
                        {
                            
                            $miningResult = $mining->extract_averages_detector_in_file($from_file, $parameters);
                            
                        }
                        else
                        {
                            $miningResult = $mining->miningFile($from_file, $parameters);
                        }
                        
                    }
                    
                    array_push($mining_store, $miningResult);
                    
                    //echo "file - from: ".$from_file."<br>";
                    
                }
                
            }else{
                
                if(is_dir($dir.$item))
                {
                    
                    $two_folders++;
                    
                    //exit("bruno");
                    
                    $from_dir = $dir.$item.DIRECTORY_SEPARATOR;
                    
                    //echo "dir - from: ".$from_dir."<br>";
                    
                    $files = $utils->getListElementsDirectory1($from_dir, array("txt"));
                    
                    //
                    // verifica e mantem na listagem apenas os arquivos com mesmo nome do diretório
                    //
                    if(substr($from_dir,strrpos($from_dir, DIRECTORY_SEPARATOR)) == DIRECTORY_SEPARATOR)
                    {
                        $dirname_project = substr($from_dir,0,strrpos($from_dir, DIRECTORY_SEPARATOR));
                        $dirname_project = substr($dirname_project,strrpos($dirname_project, DIRECTORY_SEPARATOR)+1);
                    }
                    else
                    {
                        $dirname_project = substr($from_dir,strrpos($from_dir, DIRECTORY_SEPARATOR));
                    }
                    

                    $files_names = array();
                    
                    foreach($files as $key2=>$item2)
                    {
                        $seq = substr($item2['name'], strrpos($item2['name'], "-")+1);
                        $seq = substr($seq,0,strrpos($seq, "."));
                        
                        $f_name = substr($item2['name'], 0, strrpos($item2['name'], "-"));
                        $f_name .= "-" . $seq;//$utils->format_number($seq,4);
                        $f_name_ex = substr($item2['name'], strrpos($item2['name'], ".")+1);
                        $f_name .= "." . $f_name_ex;
                        
                        $f = substr($f_name, 0, strrpos($f_name, "-"));
                        
                        if(count($files_names) > 0)
                        {
                            $find_ok = false;
                            
                            foreach($files_names as $key3=>$item3)
                            {
                                if($f == $key3)
                                {
                                    $files_names[$f]++;
                                    $find_ok = true;
                                }
                            }
                            
                            if($find_ok == false)
                            {
                                $files_names[$f] = 1;
                            }
                        }
                        else 
                        {
                            $files_names[$f] = 1;
                        }
                                                
                    }
                    
                    
                    
                    $dirname_project = "";
                    $dirname_project_last_count = 0;
                    
                    foreach($files_names as $key=>$item5)
                    {
                        if($dirname_project == "")
                        {
                            $dirname_project = $key;
                            $dirname_project_last_count = $item5;
                        }
                        else
                        {
                            if($item > $files_names[$dirname_project])
                            {
                                $dirname_project = $key;
                                $dirname_project_last_count = $item5;
                            }
                        }
                    }
                    
                    
                    
                    
                    
                    //
                    // ********************************
                    // 
                    
                    $files_aux = array();
                    
                    foreach($files as $key2=>$item2)
                    {
                        $seq = substr($item2['name'], strrpos($item2['name'], "-")+1);
                        $seq = substr($seq,0,strrpos($seq, "."));
                        
                        $f_name = substr($item2['name'], 0, strrpos($item2['name'], "-"));
                        $f_name .= "-" . $seq;//$utils->format_number($seq,4);
                        $f_name_ex = substr($item2['name'], strrpos($item2['name'], ".")+1);
                        $f_name .= "." . $f_name_ex;
                        
                        $f = substr($f_name, 0, strrpos($f_name, "-"));
                        
                        if($dirname_project == $f)
                        {
                            $files_aux[] = $item2;
                        }
                                
                    }
                    
                    $files = $files_aux;
                    
                    
                    $files2 = array();                    
                    $lastseq = 0;
                    $lastseqname = "";
                                        
                    foreach($files as $key2=>$item2)
                    {
                        $seq = substr($item2['name'], strrpos($item2['name'], "-")+1);
                        $seq = substr($seq,0,strrpos($seq, "."));
                        
//                         while(strpos(substr($seq,0,1), "0") !== false)
//                         {
//                             $seq = substr($seq,1);
//                         }
                        
                        $seq = (int) $seq;
                        
                        if($lastseq == 0)
                        {
                            $lastseq = $seq;
                            $lastseqname = $item2['name'];
                            $files2[] = $item2;
                        }
                        else 
                        {
                            if($seq == $lastseq+1)
                            {
                                $lastseq = $seq;
                                $lastseqname = $item2['name'];
                                $files2[] = $item2;
                            }
                            else 
                            {
                                $f_name = substr($item2['name'], 0, strrpos($item2['name'], "-"));
                                $f_name .= "-" . $utils->format_number($lastseq+1,4);
                                $f_name_ex = substr($item2['name'], strrpos($item2['name'], ".")+1);
                                $f_name .= "." . $f_name_ex;
                                
                                $files2[] = array("name"=>$f_name);
                                $files2[] = $item2;
                                
                                $lastseq = $seq;
                                $lastseqname = $f_name;
                                
                            }
                        }
                                               
                    }
                    
                    //
                    // **********************************************
                    // 
                    
                    $files = $files2;
                                        
                    

                    
                    foreach($files as $keyname=>$file){
                        
                        
                        if($type_extract==2){
                            
                            if($scripts != "")
                            {
                                $scripts .= "\n\n";
                            }
                            
                            $scripts .= $mining->getScriptMOA($from_dir.$file["name"]);
                            
                            
                        }else{
                            if($type_extract==1){
//                                 exit("error");
                                $miningResult = $mining->extract_averages_in_file($from_dir.$file["name"], $parameters);
                               
                            }else{
                                
                                
                                if($detector == 1)
                                {
                                    $miningResult = $mining->extract_averages_detector_in_file($from_dir.$file["name"], $parameters);
                                    
                                }
                                else if($detectorsum == 1)
                                {
                                    $miningResult = $mining->extract_averages_detector_in_file($from_dir.$file["name"], $parameters);
                                    
                                    $aux = array();
                                    
                                    foreach($miningResult as $item)
                                    {
                                        foreach($item as $key=>$value)
                                        {
                                            if(!isset($aux[$key]))
                                                $aux[$key] = 0;
                                                
                                                $aux[$key]=$aux[$key]+$value;
                                        }
                                        
                                    }
                                    
                                    $miningResult = $aux;
                                    
                                }
                                else
                                {
                                    $miningResult = $mining->miningFile($from_dir.$file["name"], $parameters);
  
                                }
                                
                                
                            }
                            
                            
                            
                            //$miningResult = $mining->miningFile($from_dir.$file["name"], $parameters);
                            
                            // echo $file["name"];
                            // var_dump($miningResult);
                            // exit();
                            array_push($mining_store, $miningResult);
                            array_push($mining_store2, array("dirname"=>$item, "results"=>$miningResult));
                        }
                        
                    }
                    
                    
                }
            }
            
        }
        
        
//         var_dump($mining_store);exit();
//        
        
        if($two_folders > 1){
            
//             var_dump($mining_store2);
//             exit();
            
            $lastname = "";
            $columns_labels = array();
            $data_values = array();
            //$decimalformat = $parameters["decimalformat"];
            
            foreach($mining_store2 as $item)
            {
                if($lastname == ""){
                    $lastname = $item['dirname'];
                    $columns_labels[] = $lastname;
                }else{
                    if($item['dirname'] != $lastname){
                        $lastname = $item['dirname'];
                        $columns_labels[] = $lastname;
                    }
                }
                
                foreach($item as $key=>$item2){
                    
                    if(is_array($item2)){
                        
                        foreach($item2 as $item3){
                            
                            if(is_array($item3)){
                                
                                foreach($item3 as $item4){
                                    
                                    if($decimalformat != ".")
                                    {
									    $item4 = str_replace(".", "", $item4);
                                    //    $item4 = str_replace(".", $decimalformat, $item4);
                                    }
                                    $data_values[$lastname][] = $item4;
                                }
                            }
                            
                        }
                    }
                }
            }
            
            

            if($template_user == TRUE)
            {
                
                $data_values_aux = array();
                
                if($datasets_enable)
                {
                    $data_values_aux[$datasets_name] = $data_values[$datasets_name];
                }
                
                
                $data_values_aux2 = array();                
                
                if($data_order_enable_tmpl)
                {                    
                    foreach($data_order_tmpl as $col)
                    {
                        foreach($data_values as $key=>$item)
                        {
                            //                         echo $key . "=" . $col."\n";
                            if($key == $col)
                            {
                                $data_values_aux2[$key]  = $item;
                            }
                        }
                    }
                }
                
                $data_values_aux3 = array();
                
                if($data_filter_enable_tmpl)
                {
					if(count($data_filter_tmpl)>0)
					{
						if($data_order_enable_tmpl)
						{
							$s = $data_values_aux2;
						}
						else
						{
							$s = $data_values;
						}
						
						foreach($data_filter_tmpl as $col)
						{
							foreach($s as $key=>$item)
							{
								//                         echo $key . "=" . $col."\n";
								if($key == $col)
								{
									$data_values_aux3[$key]  = $item;
								}
							}
						}
					}
					
					if(count($data_filter_lines_tmpl)>0)
					{
						if(count($data_filter_tmpl)>0)
						{
							$s = $data_values_aux3;
						}
						else
						{
							if($data_order_enable_tmpl)
							{
								$s = $data_values_aux2;
							}
							else
							{
								$s = $data_values;
							}
						}
						
						$data_values_aux3 = array();
						
						foreach($data_filter_lines_tmpl as $line)
						{
							foreach($s as $key=>$item)
							{
								foreach($item as $index=>$value)
								{
									if($index == $line-1)
									{
										$data_values_aux3[$key][$index]  = $value;
									}
								}
							}
						}

					}
					
					
                }
                
                
                   
                $data_values_aux4 = array();
                
                if($data_renames_enable_tmpl)
                {
                    if($data_filter_enable_tmpl || $data_order_enable_tmpl)
                    {
                        if($data_filter_enable_tmpl && count($data_filter_tmpl)>0)
                        {
							$s = $data_values_aux3;							
                        }
                        else 
                        {
                            $s = $data_values_aux2;
                        }
                    }
                    else
                    {
                        $s = $data_values;
                    }
                    
                    
                    foreach($s as $key=>$item)
                    {
                        foreach($data_renames_tmpl as $key_find=>$rename_newkey)
                        {                            
                            if($key == $key_find)
                            {
                                $key = $rename_newkey;
                                break;
                            }
                        }
                        $data_values_aux4[$key]  = $item;
                    }
                  
                }
                
                $data_values2 = array();
                
                if(count($data_values_aux) > 0)
                {
                    foreach($data_values_aux as $key=>$item)
                    {
                        $data_values2[$key] = $item;
                    }
                }

                
                if(count($data_values_aux4) > 0)
                {
                    foreach($data_values_aux4 as $key=>$item)
                    {
                        $data_values2[$key] = $item;
                    }
                                        
                }
                else 
                {
                    if(count($data_values_aux3) > 0)
                    {
                        foreach($data_values_aux3 as $key=>$item)
                        {
                            $data_values2[$key] = $item;
                        }
                    }
                    else
                    {
                        if(count($data_values_aux2) > 0)
                        {
                            foreach($data_values_aux2 as $key=>$item)
                            {
                                $data_values2[$key] = $item;
                            }
                        }
                        else
                        {
                            
                        }
                    }
                }
                
                
                
                if(count($data_values2) > 0)
                {					
                    $data_values = $data_values2;
                }
       
            }
            
            
            $index = 0;
            $data_matriz = array();
            
           
            
            foreach($data_values as $key=>$item)
            {
                $data_matriz[$index][] = $key;
                //             var_dump($key);exit("=");
                //for($i = 0; $i < count($data_values[$key]);$i++)
                foreach($data_values[$key] as $key2=>$value)
                {
                    $data_matriz[$index][] = $value;//$data_values[$key][$i];
                    //var_dump( $data_values[$key][$i] );//. ", ";
                }
                
                $index++;
            }
            
            
//             var_dump($data_matriz);
//             exit();
            
            $data_csv_aux  = "";
            $index_line = 0;
            $cols_count = count($data_matriz);
            $oef = false;
            $eof_lines = false;
            $eof = false;
            
            while($eof == false)
            {
                $first_col = true;
                $eof_lines = true;
                $data_line_aux = "";
                
                for($i = 0; $i < $cols_count; $i++)
                {                    
                    if($first_col == false){
                        $data_line_aux .= "\t";
                    }
                    
                    if(isset($data_matriz[$i][$index_line]))
                    {
                        $data_line_aux .= $data_matriz[$i][$index_line];
                        $eof_lines = false;
                    }else
                    {
                        $data_line_aux .= "00" . $decimalformat . "00";
                    }
    
                    $first_col = false;
                }
                
                $index_line++;
                
                if($eof_lines)
                {
                    $eof = true;
                }else{
                    $data_csv_aux .= $data_line_aux . "\n";
                }
                
            }
            
            $data_csv_aux = trim($data_csv_aux);
            
//             var_dump($data_csv_aux);
            
            
//             exit("fim");
            
        }
        else 
        {
            unset($mining_store2);
        }
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
//         if($dist == 1
//             || $fn == 1
//             || $fp == 1
//             || $tn == 1
//             || $tp == 1
//             || $precision == 1
//             || $recall == 1
//             || $mcc == 1
//             || $f1 == 1)
//         {
            
//             $mining_store_aux = array();
            
//             //$pos = array(0,1,2,3,4,5,6,7,8);
            
//             if($dist == 1)
//             {
//                 $pos  = 0;
//             }
            
//             if($fn == 1)
//             {
//                 $pos  = 1;
//             }
            
//             if($fp == 1)
//             {
//                 $pos  = 2;
//             }
            
//             if($tn == 1)
//             {
//                 $pos  = 3;
//             }
            
//             if($tp == 1)
//             {
//                 $pos  = 4;
//             }
            
//             if($precision == 1)
//             {
//                 $pos  = 5;
//             }
            
//             if($recall == 1)
//             {
//                 $pos  = 6;
//             }
            
//             if($mcc == 1)
//             {
//                 $pos  = 7;
//             }
            
//             if($f1 == 1)
//             {
//                 $pos  = 8;
//             }
            
//             foreach($mining_store as $item)
//             {
//                 $a = array();
                
//                 foreach($item as $key=>$value)
//                 {
//                     if(strpos($value["resume"], "\t") !== false)
//                     {
//                         $itens = explode("\t", $value["resume"]);
                        
//                         //0 = dist
//                         //7 = mcc
//                         //8 = f1
                        
//                         $a[] = array("resume"=>$itens[$pos]);
//                     }
                    
//                 }
                
//                 array_push($mining_store_aux, $a);
                
//             }
            
//             //
//             $mining_store = $mining_store_aux;
//         }
        
        
        
//         var_dump($mining_store);exit();
        
        
        
        if($type_extract==2){
            
            
        }else{
            
            $resume = $metrics['resume'];//$application->getParameter("resume");
            $detector = $application->getParameter("detector");
            
            if($resume == 1){
                
                $data_csv = $mining->convertCSV($mining_store, $parameters, 1);
                
            }else if($detector == 1 || $detectorsum == 1){
                
                if($detectorsum == 1)
                {
                    $data_csv = $utils->castArrayToCSV($mining_store, "\t", $decimalformat);
                }
                else
                {
                    $data_csv = $mining->convertCSV($mining_store, $parameters, 7);
                }
                
            }else{
                
                if($two_folders > 1){
                    $data_csv = $data_csv_aux;
                }else{
                    
                    $data_csv = $mining->convertCSV($mining_store, $parameters, $breakline);
                    
                }
                // 				    var_dump($mining_store);
                // 				    exit();
                
                
            }
            

            
//             $csv = $application->getParameter("csv");
//             $tex = $application->getParameter("tex");
//             $html = $application->getParameter("html");
            
            
            $save = $application->getParameter("save");
            
            if($save != null)
            {                
                
                //var_dump($_POST);
                
                $element = $application->getParameter("element");
                
                $dir = PATH_USER_WORKSPACE_STORAGE . $folder; //$application->getParameter("folder");
                
                
                /*foreach($element as $key=>$item){
                    
                    if(is_file($dir.$item)){
                        
                        $filename_to_save = $dir.$item;//.DIRECTORY_SEPARATOR;
                        
                    }else{
                        
                        $filename_to_save = $dir.$item;//.DIRECTORY_SEPARATOR;
                        
                    }
                    
                }*/
                
                $filename_to_save = str_replace(DIRECTORY_SEPARATOR , "-", $folder);
                
                if($filename_to_save != null){
					if(substr($filename_to_save, strlen($filename_to_save)-1)=="-"){
						$filename_to_save = substr($filename_to_save, 0, strlen($filename_to_save)-1);
					}
				}                
                
                $overwrite = $application->getParameter("overwrite");
                $extensions = array();
                
                $viewdata = $application->getParameter("viewdata");
                
                
                switch($viewdata){
                    
                    case	"html":{
                        
                        $extensions[] = "html";
                        break;
                    }
                    case	"txt":{
                        
                        $extensions[] = "txt";
                        break;
                    }
                    case	"tex":{
                        
                        $extensions[] = "tex";
                        break;
                    }
                    
                }
                
                
                
                                                            
                foreach($extensions as $extension)
                {
                    
                    
                    $filename = $dir . $metricstract . "-" . $filename_to_save . "." . $extension;
                    					
                    if(file_exists($filename))
                    {
                        if($overwrite != null)
                        {
                            unlink($filename);
						}
						else
                        {
							$filename_to_save__ = $filename_to_save;
							$y=1;
							
							while(is_file($dir . $metricstract . "-" . $filename_to_save__ . "." . $extension))
							{
								$filename_to_save__ = $filename_to_save."-" . $y . "";
								$y++;
							}
							
							$filename = $dir . $metricstract . "-" . $filename_to_save__ . "." . $extension;
							//break;
						}    
					}
					
						
					switch($extension)
					{
						
						case "txt":
							
							$data = $data_csv;
							
							break;
						case "html":
							
							$data = $utils->castToHTML($data_csv);
							
							break;
						case "tex":
							
							$data = $utils->castToTex($data_csv);
							
							$title = substr($filename_to_save,
								strrpos($filename_to_save,
									DIRECTORY_SEPARATOR)+1);
							
							$title = str_replace(" ", "-", $title);
							
							$data = str_replace("%title%", $title, $data);
							
							$data = str_replace("%label%",$title, $data);
							
							
							break;
					}
						
						
						$utils->setContentFile($filename, $data);
                                
                                
                }
                                                            
                                                            
            }
            
            
            
            $resume = $application->getParameter("resume");
            
            if($resume == 1){
                
                $labels = array("Dist.","FN","FP","TN","TP","Precision","Recall","MCC","F1", "MDR", "MTFA", "MTD", "MTR");
                //-815.95 (+-1936.92)		467	13	599417		103	0.887931	0.000171804	0.400363313	0.000343542
                
                $result_view = implode("\t", $labels)."\n";
                
                $result_view .= $data_csv;
                
            }else if($detector == 1 || $detectorsum == 1){
                
                $labels = array("WFalse","WTrue","Drift","WcFalse","WcT","Dc", "Acc.");
                //-815.95 (+-1936.92)		467	13	599417		103	0.887931	0.000171804	0.400363313	0.000343542
                
                $result_view = implode("\t", $labels)."\n";
                
                $result_view .= $data_csv;
                
            }else{
                
                $result_view = $data_csv;
            }
            
            
            $viewdata = $application->getParameter("viewdata");
            
            
            switch($viewdata){
                
                case	"html":{
                    
//                     header( 'Content-Type: text/html' );
                    
                    
                    if($statisticaltest != 'no')
                    {
						if(!empty($folder))
						{
							$folder2 = str_replace("/", "-", $folder);
							
							if(substr($folder2, strlen($folder2)-1) == "-")
							{
								$folder2 = substr($folder2, 0, strlen($folder2)-1);
							}
		
							$filename = $folder2 . ".tmp";
						}
						else
						{
							$filename = "AUTOLOAD" . time() . ".tmp";
						}
                        
                        if(is_file(PATH_USER_WORKSPACE_PROCESSING . $filename))
                        {
							unlink(PATH_USER_WORKSPACE_PROCESSING . $filename);
						}
						
                        $utils->setContentFile(PATH_USER_WORKSPACE_PROCESSING . $filename, $result_view);
                                                                        
                        $redirect = array();
                        
                        $redirect['url'] = '?';
                        $redirect['component'] = "statistical";
                        $redirect['folder'] = $folder;
                        
                        foreach($metrics as $key=>$value)
                        {
							if(!empty($metrics[$key]))
							{
								if($metrics[$key])
								{
									$redirect['source'] = @ucfirst($key);
								}
							}
								
						}
                      
                        if($statisticaltest == 'NemenyiGraph')
                        {
                            $redirect['controller'] = "graphnemenyi";
                        }
                        else if($statisticaltest == 'BonferroriDunnGraph')
                        {
                            $redirect['controller'] = "graphbonferronidunn";
                        }
                        else 
                        {
                            $redirect['controller'] = "texteditor";
                        }                        
                        
                        $redirect['filename'] = rawurlencode($filename);
                        $redirect['task'] = $statisticaltest;
                        
                        $application->redirect($redirect);
                        
                    }
                    else 
                    {
                        $result_view = $utils->createSheetHtml($result_view);
                    }
                    
                    break;
                }
                case	"txt":{
                    
//                     ob_end_clean();
//                     header( 'Content-Type: text/plain' );
                    
                    
                    
                    if($resume == 1){
                        
                        $labels = array("Dist.","FN","FP","TN","TP","Precision","Recall","\tMCC\t","F1", "MDR", "MTFA", "MTD", "MTR");
                        //-815.95 (+-1936.92)		467	13	599417		103	0.887931	0.000171804	0.400363313	0.000343542
                        
                        $result_view = implode("\t", $labels)."\n";
                        
                        $result_view .= $data_csv;
                        
                    }else if($detector == 1 || $detectorsum == 1){
                        
                        $labels = array("WFalse","WTrue","Drift","WcFalse","WcT","Dc", "Acc.");
                        //-815.95 (+-1936.92)		467	13	599417		103	0.887931	0.000171804	0.400363313	0.000343542
                        
                        $result_view = implode("\t", $labels)."\n";
                        
                        $result_view .= $data_csv;
                        
                    }
                    
                    
                    //$a = $utils->castCsvToArray($result_view);
                    
                    //var_dump($a);exit("=====");
                    
                    //$result_view = $utils->createSheetHtml($result_view);
                    //var_dump($statisticaltest);exit("s");
                    
                    if($statisticaltest != 'no')
                    {
                        if(!empty($folder))
						{
							$folder2 = str_replace("/", "-", $folder);
							
							if(substr($folder2, strlen($folder2)-1) == "-")
							{
								$folder2 = substr($folder2, 0, strlen($folder2)-1);
							}
		
							$filename = $folder2 . ".tmp";
						}
						else
						{
							$filename = "AUTOLOAD" . time() . ".tmp";
						}
                        
                    
                        if(is_file(PATH_USER_WORKSPACE_PROCESSING . $filename))
                        {
							unlink(PATH_USER_WORKSPACE_PROCESSING . $filename);
						}
						
                        //$filename = "AUTOLOAD" . time() . ".tmp";
                        $utils->setContentFile(PATH_USER_WORKSPACE_PROCESSING . $filename, $result_view);
                        
                        
                        $redirect = array();
                        
                        $redirect['url'] = '?';
                        $redirect['component'] = "statistical";
                        $redirect['folder'] = $folder;
                        
                        if($statisticaltest == 'NemenyiGraph')
                        {
                            $redirect['controller'] = "graphnemenyi";
                        }
                        else
                        {
                            $redirect['controller'] = "texteditor";
                        } 
                        

                        $redirect['filename'] = rawurlencode($filename);
                        $redirect['task'] = $statisticaltest;
                        
                        foreach($metrics as $key=>$value)
                        {
							if(!empty($metrics[$key]))
							{
								if($metrics[$key])
								{
									$redirect['source'] = @ucfirst($key);
								}
							}
								
						}
                        
                        
                        $application->redirect($redirect);
                         
                    }else{
                        
                       // echo $result_view;
                    }
                    
                    
                    //$contLength = ob_get_length();
                    //header( 'Content-Length: '.$contLength);
                    
//                     exit();
                    
                    break;
                }
                case	"tex":{
                    
//                     header( 'Content-Type: text/plain' );
//                     ob_end_clean();
                    
                    $result_view =  $utils->castToTex($result_view);
                    
                    
                    //$contLength = ob_get_length();
                    //header( 'Content-Length: '.$contLength);
                    
                    //exit();
                    
                    break;
                }
                /*case	"csv":{
                
                header( 'Content-Type: text/csv' );
                ob_end_clean();
                
                $result_view = implode("\t", $labels)."\n";
                $result_view .= $data_csv;
                
                echo $result_view;
                
                $contLength = ob_get_length();
                header( 'Content-Length: '.$contLength);
                
                exit();
                
                break;
                }*/
                
            }
            
            
            
            
        }
    }
    
    
    
}

if($folder == null){
    
    $files_list = $utils->getListElementsDirectory1(
        Properties::getBase_directory_destine($application)
        .$application->getUser()
        .DIRECTORY_SEPARATOR, array("txt","report"));
    
}else{
    
    
    //$to_folder =// Properties::getBase_directory_destine($application)
    //.$application->getUser()
    //.DIRECTORY_SEPARATOR
    //$application->getParameter("folder")
    //.DIRECTORY_SEPARATOR
    //.$application->getParameter("rename");
    
    //exit("ddd - ".$folder);
    $files_list = $utils->getListElementsDirectory1(
        Properties::getBase_directory_destine($application)
        .$application->getUser()
        .DIRECTORY_SEPARATOR
        .$folder
        //.DIRECTORY_SEPARATOR
        , array("txt","report"));
}


foreach($files_list as $key=>$element){
    
    if($element["type"]=="dir"){
        if($element["name"]=="scripts"){
            unset($files_list[$key]);
        }
    }else{
        
        /*echo substr($element["name"],strrpos($element["name"],".")+1);
         if(substr($element["name"],strrpos($element["name"],".")+1)=="log"){exit("bruno");
         unset($files_list[$key]);
         }*/
    }
}


$dir_list = $utils->getListDirectory(
    Properties::getBase_directory_destine($application)
    .$application->getUser()
    .DIRECTORY_SEPARATOR
    .$folder);


?>


<style>

div#table_id table tr td{
	border:1px solid #cccccc;
	border-collapse: collapse;
	padding:1px;
}

</style>	
	

  <div id="containerbody" style="height:100%;margin-left: -15px;
margin-right: -15px;list-style-type: none;
margin: 0;
overflow-y: scroll;max-height: 400px;" >  							
									
	<!-- 	<input type="button" value="Return" name="return" onclick="javascript: returnPage();" /> 	<br><br>	 -->	
<?php 

	if($task == "preview"){
		
		$filename = Properties::getBase_directory_destine($application)
						.$application->getUser()
						.DIRECTORY_SEPARATOR
						.$application->getParameter("folder")
						.$application->getParameter("filename");
		
		$extension_file = $filename;
		$extension_file = substr($extension_file, strrpos($extension_file,".")+1);
		
		if($extension_file == "html"){
			
			echo "<div id='table_id'>".$csv."</div>";
			
		}else if($extension_file == "csv"){
			
			echo $utils->createSheetHtml($csv);//htmlCSV($csv);
		}
		
		
		
	}else{

	
		
		if($type_extract==2){
	?>	
							<div class="page-header">
        						<h1>
        							<a href="<?php echo $_SERVER['REQUEST_URI']?>">Read File</a>
        						</h1>
        					</div>
        					
			File name: <?php echo $application->getParameter("filename");?><br>
			Content:<br>
			<textarea id="data"	style="width:100%;height:400px;" name="data"><?php echo $scripts?></textarea><br>
													
	<?php 														
		}else{
				
			if($statisticaltest != 'no')
			{
				
				header("Location: index?component=statistical&controller=texteditor&data=" . rawurlencode($result_view));
				exit();
				
			}else{		
				?>
				
		
     					
        					<?php 
				//if(strlen($csv)>1){
					
					//exit("ooo");
					//$aa = $utils->castToCSV($csv);
					
					//var_dump($aa);exit();
					//App::setParameter('tmpl',true);
				if($viewdata == "txt" || $viewdata == "tex")
			    {			        
					echo "<pre style='font-size:11px;border: 0px solid #000; text-align:left;font-family: monospace,verdana;'>".$result_view."</pre>";//echo $utils->createSheetHtml($csv);//htmlCSV($csv);
			    }
			    else 
			    {
			        echo $result_view;
			    }
			    
				//}
				
			}
	
		}
		
		
	}
?>	
</div>
		
									<div style="float: right; padding-left: 0px;margin-top:0px;">
										<input type="button" class="btn btn-default" value="Close"
											onclick="javascript: returnPage();">
									</div>
																
<script>


function returnPage(){
	window.close();

	
}


</script>	




<script type="text/javascript">

<?php if($type_extract==2){?>
	// initialisation
	editAreaLoader.init({
		id: "data"	// id of the textarea to transform	
			,start_highlight: true	
			,font_size: "8"
			,is_editable: true
			,word_wrap: true
			,font_family: "verdana, monospace"
			,allow_resize: "y"
			,allow_toggle: true
			,language: "en"
			,syntax: "xml"	
			,toolbar: " undo, redo, |, select_font"
			//,load_callback: "my_load"
			//,save_callback: "my_save"
			,plugins: "charmap"
			,min_height: 300
			,charmap_default: "arrows"
	});


	function toogle_editable(id, id2)
	{
		if(id2.value == "Toggle to edit mode")
		{
			id2.value = "Toggle to read only mode";
		}
		else
		{
			id2.value = "Toggle to edit mode";
		}
		
		editAreaLoader.execCommand(id, 'set_editable', !editAreaLoader.execCommand(id, 'is_editable'));
	}

<?php }?>

	function resizeImage()
	{
		// browser resized, we count new width/height of browser after resizing
		var height = window.innerHeight - 220;// || $(window).height();

		document.getElementById("containerbody").setAttribute(
			   "style", "border:1px solid #ffffff;margin-left: -15px;  margin-right: -15px;list-style-type: none;  margin: 0;  overflow-y: scroll;max-height: "+height+"px");
	}

	window.addEventListener("resize", resizeImage);

	resizeImage();

	
</script>



								
								
