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
$data_rank_hypothesis_html = "";

$statistical_test_array = array(
    "Bonferroni-Dunn",
    "Nemenyi",
    "Wilcoxon",
    "Holm",
    "Shaffer",
    "Bergmann-Hommel",
    "Kullback-Leibler",
    "Minkowski"
);

function rank_avg($value, $array, $order = 0) {
    // sort
    if ($order) sort ($array); else rsort($array);
    // add item for counting from 1 but 0
    array_unshift($array, $value+1);
    // select all indexes vith the value
    $keys = array_keys($array, $value);
    if (count($keys) == 0) return NULL;
    // calculate the rank
    return array_sum($keys) / count($keys);
}


function silas_rank_hypothesis_prepar($result_value){
    
    $hypothesis_h0 = "H0";
    $hypothesis_h1 = "H1";
    $hypothesis_h2 = "H2";
    
    $result = array();
    $result[$hypothesis_h0] = array();
    $result[$hypothesis_h1] = array();
    $result[$hypothesis_h2] = array();
    $result["diff"] = array();
    $result["rank"] = array();
    
    foreach($result_value as $key=>$item){
        if(count($item)>0 && $key != "method"){
            $h0 = $h1 = $h2 = 0;
            foreach($item as $index=>$value){                
                if($value == $hypothesis_h0){
                    $h0++;
                }else if($value == $hypothesis_h1){
                    $h1++;
                }else if($value == $hypothesis_h2){
                    $h2++;
                }                
            }
            $result[$hypothesis_h0][$key] = $h0;
            $result[$hypothesis_h1][$key] = $h1;
            $result[$hypothesis_h2][$key] = $h2;
            
//             var_dump($result);
        }
    }
    
    foreach($result as $key=>$item){
        foreach($item as $index=>$value){
            $result["diff"][$index] = $result[$hypothesis_h1][$index] - $result[$hypothesis_h2][$index];
        }        
    }
    
    $diff = array();
    
    foreach($result as $key=>$item){
        if($key == "diff"){
            foreach($item as $index=>$value){
                $diff[] = $result["diff"][$index];
            }
            break;
        }
    }
    
    foreach($result as $key=>$item){
        if($key == "diff"){
            foreach($item as $index=>$value){
                $r = rank_avg($result["diff"][$index], $diff, false);
                $result["rank"][$index] = $r;
            }
            break;
        }
    }
    
//     var_dump($diff);exit();
    
    return $result;
}


function silas_rank_tabular_prepar($result_value, $rank){

    
    
    $destine2 = "<table border=1 style='float:left;width:auto;'>";
    
    
    foreach($result_value as $key=>$item){
        
        $destine2 .= "<tr><td>". $key . "</td>";
        $destine2 .= "</tr>";
        
    }
    
    foreach($rank as $key=>$item){
        
        if($key == "diff"){
            $key = "&Sigma;H<sub>1</sub> - H<sub>2</sub>";
        }else{
            $key = str_replace("H0", "&Sigma;H<sub>0</sub>", $key);
            $key = str_replace("H1", "&Sigma;H<sub>1</sub>", $key);
            $key = str_replace("H2", "&Sigma;H<sub>2</sub>", $key);
            //$key = "".$key;
        }        
        
        $destine2 .= "<tr><td>". $key . "</td>";
        $destine2 .= "</tr>";
        
    }
    
    $destine2 .= "</table>";
    
    
    $destine2 .= "<table border=1 style='float:left;width:auto;'>";
    
    
    foreach($result_value as $key=>$item){
        
        $destine2 .= "<tr>";//<td>". $key . "</td>";
        
        foreach($item as $value){
            
            if($value == "H0"){
                $color = "#F2F5A9";
                $value = str_replace("H0", "H<sub>0</sub>", $value);
            }else if($value == "H1"){
                $color = "#81F781";
                $value = str_replace("H1", "H<sub>1</sub>", $value);
            }else if($value == "H2"){
                $color = "#F78181";
                $value = str_replace("H2", "H<sub>2</sub>", $value);
            }else if($value == "*"){
                $color = "#6E6E6E";
            }else{
                $color = "";
            }
                       
            
            //foreach($value as $value2){
            $destine2 .= "<td style='background-color:$color;text-align:center;'>".$value."</td>";
            //}
            
        }
        $destine2 .= "</tr>";
        
    }
    
    //$destine2 .= "</table>";
    
    
    //$destine2 .= "<table border=1 style='float:left;width:auto;'>";
    
    
    foreach($rank as $key=>$item){
        
        $destine2 .= "<tr>";//<td>". $key . "</td>";
        
        
        
        //var_dump($rank);exit(); 
        foreach($item as $value){
            
            
            
            //foreach($value as $value2){
            $destine2 .= "<td style='text-align:center;'>".$value."</td>";
            //}
            
        }
        $destine2 .= "</tr>";
        
    }
    
    $destine2 .= "</table>";
    
    
    
    $destine2 = "<div style='float:left;width:auto;'>heatmap<br>". $destine2 . "<br><br>*&Sigma;H<sub>0</sub>: not reject, if reject H<sub>0</sub> then H<sub>1</sub>: <spam style='text-decoration:overline; padding:0px'>X</spam><sub>1</sub> > <spam style='text-decoration:overline; padding:0px'>X</spam><sub>2</sub> or H<sub>2</sub>: <spam style='text-decoration:overline; padding:0px'>X</spam><sub>1</sub> < <spam style='text-decoration:overline; padding:0px'>X</spam><sub>2</sub>... <spam style='text-decoration:overline; padding:0px'>X</spam><sub>1</sub> is equal the first column, <spam style='text-decoration:overline; padding:0px'>X</spam><sub>2</sub> is equal other columns compared.</div>";
    
    return $destine2;
}

function getIndexOfName($value, $list){
    $result = -1;
    $index = 0;
    foreach($list as $key=>$item){
        if($value == $key){
            $result = 1;
            break;
        }
        $index++;
    }
    if($result > 0){
        $result = $index;
    }
    return $result;
}

function silas_rank_prepar($values, $delimiter_decimal_from=".", $delimiter_decimal_to=","){
    
    $values = str_replace($delimiter_decimal_from, $delimiter_decimal_to, $values);
    
    $lines_values = explode("\n", $values);
    
   
    $methods = array();
    $methods_order = array();
    $index = 0;
    
    for($i=0; $i< count($lines_values); $i++){
        
        if(trim($lines_values[$i])!= ""){
            
            $lines_values[$i] = trim($lines_values[$i]);
            
            $text = substr($lines_values[$i], strlen("Method")+1);
            $method = trim(substr($text, 0, strpos($text, " ")));
            
            $methods[trim($method)] = array();
            $methods_order[trim($method)] = $index++;
            
//             var_dump($method);exit();
            
            $text = substr($lines_values[$i], strpos($lines_values[$i], ":")+1);
            $text = trim($text);
            
            //var_dump($text);
            
            $methods_inf = array();
            
            if(strpos($text, "\t")!==false){
                $methods_inf = explode("\t", $text);
            }else{
                if(strpos($text, " ")!==false){
//                     $text = str_replace("  ", " ", $text);
                    $methods_inf = explode(" ", $text);
                }else{
                    if($text != ""){
                        $methods_inf[] = $text;
                    }                    
                }                
            }
            
            $methods[$method] = $methods_inf;
            
            //var_dump($methods[$method]);
            
        }
    }
    
//     var_dump($methods);exit();
    
    
    $result_value = array();
    
    foreach($methods as $key=>$value){
        $result_value["method"][] = $key;
    }
    
    
    foreach($methods as $key=>$value){
        foreach($methods as $item){
            $result_value[$key][] = "";
        }
    }
    
//     var_dump($methods);exit();
    
    
    // lógica para H1
    foreach($methods as $key=>$item){
        if(count($item)>0){
            foreach($item as $value){
                $indexOfName = getIndexOfName($value, $methods_order);
                if($indexOfName > -1){
                    $result_value[$key][$indexOfName] = "H1";
                }
            }
        }
    }
    
    // lógica para X
    foreach($methods as $key=>$item){
        if(count($item)>0){
            foreach($methods_order as $key2=>$value){
                $indexOfName1 = getIndexOfName($key, $methods_order);
                $indexOfName = getIndexOfName($key2, $methods_order);
                
                if($indexOfName == $indexOfName1){
                    $result_value[$key][$indexOfName] = "*";
                }
            }
        }
    }
    
    // lógica para H2
    foreach($methods as $key=>$item){
        if(count($item)>0){
            foreach($methods_order as $key2=>$value){
                
                $indexOfName = getIndexOfName($key2, $methods_order);
                
                if($result_value[$key][$indexOfName] != "*"
                    && $result_value[$key][$indexOfName] != "H1"){
                        
//                         $mu1 = $key;
//                         $mu2 = "" ;
                        //var_dump($mu1);
                        //var_dump($key2);
                        
                        $ok = true;
                        
                        foreach($methods[$key2] as $key3=>$value3){
                            if($key == $value3){
                                $result_value[$key][$indexOfName] = "H2";
                                $ok = false;
                            }
                        }
                        
                        if($ok){
                            $result_value[$key][$indexOfName] = "H0";
                        }
                }
            }
        }else{
            $indexOfName1 = getIndexOfName($key, $methods_order);
            foreach($methods_order as $key2=>$value){
                $indexOfName = getIndexOfName($key2, $methods_order);
                if($indexOfName != $indexOfName1){
                    $result_value[$key][$indexOfName] = "H2";
                }else{
                    $result_value[$key][$indexOfName] = "*";
                }
                
            }
        }
    }
    
    
    return $result_value;
    
}

if(!empty($filename_autoload))
{
	$data_source = $utils->getContentFile(PATH_USER_WORKSPACE_PROCESSING . $filename_autoload);
	unlink(PATH_USER_WORKSPACE_PROCESSING . $filename_autoload);
	//$task = "Shaffer";
}

if (in_array($task, $statistical_test_array)) {
    
    if($task == "Kullback-Leibler" || $task == "Minkowski")
    {
        
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
        
        if($task == "Kullback-Leibler")
        {            
        
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
        
        }
        else 
        {
            
            if($task == "Minkowski")
            {
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
                        
                        $mk = minkowski($d1, $d2);
                        
                        
                        
                        /*if($kl >= -0.5 && $kl <= 0.5)
                        {
                            $str_equals .= "KL Divergence between " . $d1_colname . " and " . $d2_colname . "  = " . $kl . "<br>";
                        }
                        else
                        {
                            $str_not_equals .= "KL Divergence between " . $d1_colname . " and " . $d2_colname . "  = " . $kl . "<br>";
                        }*/
                        
                        $kl_data[$d1_colname][$d2_colname] = $mk;
                        $kl_rank[] = $mk;
                        
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
                
            }
            
        }
        
        
    }
    else 
    {
        
        if($task == "Wilcoxon")
        { 
            $friedman_bin = Properties::getbase_directory_statistical() . "wilcoxon_run";
            
        }else{
            $friedman_bin = Properties::getbase_directory_statistical() . "friedman-test/bin/friedman_run";
        }
        
        
//         $friedman_bin = Properties::getbase_directory_statistical() . "friedman-test/bin/friedman_run";
        
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
        
        if($task == "Wilcoxon")
        { 
            $data_destine = $countRows . "\t" . $countCols . "\n" . $cols_names . "\n" .  "\n" . // .ucfirst($task)."\n"
                 $data_source2;
        }else{
            
            $data_destine = $countRows . "\t" . $countCols . "\n" . $cols_names . "\n" . $task . "\n" . // .ucfirst($task)."\n"
                "0.95" . "\n" . $data_source2;
        }
        

        
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
            
            if($task == "Wilcoxon")
            { 
                
                $text_hypothesis = "Ways to reject the hypothesis:";
                
                if(strpos($data_result, $text_hypothesis) !== false){
                    
                    $data_result2 = $data_result;
                    
                    $data_result2 = substr($data_result, strpos($data_result, $text_hypothesis)+strlen($text_hypothesis)+1);
                    $data_result2 = trim($data_result2);
                    
                    $data_postos = $text_hypothesis."\n".$data_result2;
                    
                    $r = silas_rank_prepar($data_result2);
                    
                    $s = silas_rank_hypothesis_prepar($r);     
                    
                    $r = silas_rank_tabular_prepar($r, $s);
                                        
                    
                    $data_rank_hypothesis_html = $r;
                }
                
                $data_rank = "";//$data_result;
                
            }else{
                $s = explode("\n\n", $data_result);
                
                $data_postos = trim($s[4]);
                
                $data_postos = str_replace(".", ",", $data_postos);
                
                $data_diff_statistical  = trim($s[6])."\n\n";
                $data_diff_statistical  .= trim($s[7])."\n\n";
                $data_diff_statistical  .= trim($s[8]);
                
                $data_rank = trim($s[5]);
                
                $data_rank = explode("\n", $data_rank);
                
                $data_rank_colnames = $data_rank[0];
                $data_rank_colnames = explode("\t", $data_rank_colnames);
                foreach($data_rank_colnames as $key=>$item){
					$data_rank_colnames[$key] = substr($item,0,5);				
				}
				
				$data_rank_colnames = implode("\t", $data_rank_colnames);
                
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
                                "value" => number_format($item2, 4, ".", ".")
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
                        $data_rank2 .= "\t\t" . $row["order"];
                    }
                }
                
                $data_rank = $data_rank_colnames . "\n". $data_rank . "\n" . $data_rank2;
                
                
                
                
                
                
                
                
                
                
                $text_hypothesis = "Ways to reject the hypothesis:";
                
                if(strpos($data_result, $text_hypothesis) !== false){
                    
                    $data_result2 = $data_result;
                    
                    
                    $data_result2 = substr($data_result, strpos($data_result, $text_hypothesis)+strlen($text_hypothesis)+1);
                    $data_result2 = trim($data_result2);
                    $data_result2 = substr($data_result2, strpos($data_result2, "Method"));
                    
                    $r = explode("\n", $data_result2);
                    $r2 = array();
                    
                    for($i = 0; $i < count($r); $i++){
                        
                        if(strpos($r[$i], "Method") === false){
                            break;
                        }
                        $r2[] = $r[$i];
                    }
                    $r2 = implode("\n", $r2);
                    $data_result2 = trim($r2);
                    
                    
                    
                    
                    
                    $r = silas_rank_prepar($data_result2);
                    
//                     var_dump($r);
// //                     exit();
                    
                    $s = silas_rank_hypothesis_prepar($r);
                    
                    $r = silas_rank_tabular_prepar($r, $s);
                    
                    
                    $data_rank_hypothesis_html = $r;
                }
                
                
                
                
                
            }
            
            
            
            
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



/**
 * Minkowski distance between the two arrays of type double.
 * NaN will be treated as missing values and will be excluded from the
 * calculation. Let m be the number non-missing values, and n be the
 * number of all values. The returned distance is pow(n * d / m, 1/p),
 * where d is the p-pow of distance between non-missing values.
 */
function minkowski($x, $y) 
{
    if (count($x) != count($y))
    {
        exit("Arrays have different length: x[%d], y[%d]");
    }
    
    $p = 1;
    $n = count($x);
    $m = 0;
    $dist = 0.0;
    
    for ($i = 0; $i < $n; $i++) 
    {
        $m++;
        $d = abs($x[$i] - $y[$i]);
        $dist += pow($d, $p);
    }
    
    $dist = $n * $dist / $m;
    
    return pow($dist, 1.0/$p);
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
									name="controller"> <input type="hidden" value="Shaffer"
									name="task" id="task">

								

									<div
										style="float: left; padding-left: 5px; width: 100%; margin-top: 5px;">


										<textarea  class="dataview" id="data_source" style="width: 100%; height: 400px;"
											name="data_source"><?php echo $data_source?></textarea>



									</div>


									<div style="float: left; padding-left: 10px">
										View decimal separator <input type="text" name="decimalformat"
											id="decimalformat" value="," style="width: 40px;" /> <input
											type="submit" class="btn btn-default" value="Wilcoxon"
											onclick="document.forms[0].task.value=this.value"> <input
											type="submit" class="btn btn-default" value="Shaffer"
											onclick="document.forms[0].task.value=this.value"> <input
											type="submit" class="btn btn-default" value="Nemenyi"
											onclick="document.forms[0].task.value=this.value"> <input
											type="submit" class="btn btn-default" value="Holm"
											onclick="document.forms[0].task.value=this.value"> <input
											type="submit" class="btn btn-default" value="Bonferroni-Dunn"
											onclick="document.forms[0].task.value=this.value"> <input
											type="submit" class="btn btn-default" value="Bergmann-Hommel"
											onclick="document.forms[0].task.value=this.value"> <input
											type="submit" class="btn btn-default" value="Kullback-Leibler"
											onclick="document.forms[0].task.value=this.value"> <input
											type="submit" class="btn btn-default" value="Minkowski"
											onclick="document.forms[0].task.value=this.value">
									</div>
											
											<?php

        if (! empty($data_result)) {
            ?>
											<div
										style="float: left; padding-left: 5px; width: 100%; margin-top: 5px;">

										<textarea class="dataview" id="data_rank" style="width: 100%; height: 70px;"
											name="data_rank"><?php echo $data_rank?></textarea>
											
										<?php echo $data_rank_hypothesis_html?>	

										<textarea class="dataview" id="data_diff_statistical" style="width: 100%; height: 400px;"
											name="data_diff_statistical"><?php echo $data_diff_statistical?></textarea>
											
											
										<textarea class="dataview" id="data_postos" style="width: 100%; height: 400px;"
											name="data_postos"><?php echo $data_postos?></textarea>
										
										<textarea class="dataview" id="data_result" style="width: 100%; height: 400px;"
											name="data_result"><?php echo $data_result?></textarea>
											
									</div>
											
											<?php }?>
					

							</form>

