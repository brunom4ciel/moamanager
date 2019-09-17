<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\libraries\core\mining;

use Exception;

defined('_EXEC') or die;


class EvaluateExtract{
    
    private $strategy = "";
    public const EVALUATE_PREQUENTIAL_UFPE = 'EvaluatePrequentialUFPE';
    public const EVALUATE_PREQUENTIAL_UFPE_FOR_DETECTORS = 'EvaluatePrequentialUFPEforDetectors';
    public const EVALUATE_PREQUENTIAL2 = 'EvaluatePrequential2';
    
    public const ACCURACY = "accuracy";
    public const TIME = "time";
    public const MEMORY = "memory";
    
    public const DETECTION_ACCURACY = "detectionaccuracy";
    
    public const ENTROPY = "entropy";
    
    public const MDR = "mdr";
    public const MTFA = "mtfa";
    public const MTD = "mtd";
    public const MTR = "mtr";
    
    public const DIST = "dist";
    
    public const PRECISION = "precision";
    public const RECALL = "recall";
    public const MCC = "mcc";
    public const F1 = "f1";
    
    public const FN = "fn";
    public const FP = "fp";
    public const TN = "tn";
    public const TP = "tp";
        
    private $evaluate_metrics = array();  
    private $descriptive_statistics = array();
    
    public function getEvaluateMetrics(){
        return $this->evaluate_metrics;
    }
    
    public function setEvaluateMetrics($key, $value){
        if(isset($this->evaluate_metrics[$key])){
            $this->evaluate_metrics[$key] = $value;
        }        
    }
    
    public function getDescritiveStatistics(){
        return $this->descriptive_statistics;
    }
    
    public function setDescritiveStatistics($key, $value){
        if(isset($this->descriptive_statistics[$key])){
            $this->descriptive_statistics[$key] = $value;
        }        
    }
    
        
//     public function getText($key=""){
//         $result = "";
        
//         $labels = array(self::ACCURACY=>"Accuracy:",
//                         self::TIMER=>"Timer:",
//                         self::MEMORY=>"Memory (B/s):",
//                         self::ENTROPY=>"Entropy:",
//                         self::MDR=>"MDR:",
//                         self::MTFA=>"MTFA:",
//                         self::MTD=>"MTD:",
//                         self::MTR=>"MTR:",
//                         self::DIST=>"General Mean =",
//                         self::PRECISION=>"Precision:",
//                         self::RECALL=>"Recall:",
//                         self::MCC=>"MCC:",
//                         self::F1=>"F1:",
//                         self::FN=>"FN:",
//                         self::FP=>"FP:",
//                         self::TN=>"TN:",
//                         self::TP=>"TP:");
        
//         if(isset($labels[$key])){
//             $result = $labels[$key];
//         }
//         return $result;
//     }
    
    public function mappingMetrics($strategy = "EvaluatePrequentialUFPE", $version = 1){
        
        $evaluate_metrics = array();
        $descriptive_statistics = array();
        
        if($strategy == self::EVALUATE_PREQUENTIAL_UFPE
            || $strategy == self::EVALUATE_PREQUENTIAL_UFPE_FOR_DETECTORS)
        {

            if($version == 1){
                               
                $evaluate_metrics[self::ACCURACY] = "Accuracy:";                
                $evaluate_metrics[self::TIME] = "Time:";
                $evaluate_metrics[self::MEMORY] = "Memory (B/s):";
            
                $evaluate_metrics[self::DETECTION_ACCURACY] = "Detection Accuracy:";
                
                $evaluate_metrics[self::ENTROPY] = "Entropy:";
                
                $evaluate_metrics[self::MDR] = "MDR:";
                $evaluate_metrics[self::MTFA] = "MTFA:";
                $evaluate_metrics[self::MTD] = "MTD:";
                $evaluate_metrics[self::MTR] = "MTR:";
                
                $evaluate_metrics[self::DIST] = "General Mean =";
                
                $evaluate_metrics[self::PRECISION] = "Precision:";
                $evaluate_metrics[self::RECALL] = "Recall:";
                $evaluate_metrics[self::MCC] = "MCC:";
                $evaluate_metrics[self::F1] = "F1:";
                
                
                $evaluate_metrics[self::FN] = "FN:";
                $evaluate_metrics[self::FP] = "FP:";
                $evaluate_metrics[self::TN] = "TN:";
                $evaluate_metrics[self::TP] = "TP:";
                
                
                $descriptive_statistics["meanci"]="Mean (CI)";
                $descriptive_statistics["mean"]="Mean";
                $descriptive_statistics["gmean"]="Geometric Mean";
                $descriptive_statistics["median"] = "Median";
                $descriptive_statistics["mode"] = "Mode";
                $descriptive_statistics["sum"] = "Sum";
                $descriptive_statistics["variance"] = "Variance";
                $descriptive_statistics["sd"] = "Standard Deviation";
                $descriptive_statistics["md"] = "Mean Deviation";
                $descriptive_statistics["minimum"] = "Minimum";
                $descriptive_statistics["maximum"] = "Maximum";
                $descriptive_statistics["amplitude"] = "Amplitude";
                $descriptive_statistics["q1"] = "Lower Quartile 25% (Q1)";
                $descriptive_statistics["q3"] = "Upper Quartile 75% (Q3)";
                $descriptive_statistics["iqr"] = "Interquartile Range (IQR)";
                $descriptive_statistics["p10"] = "Percentile 10%";
                $descriptive_statistics["p90"] = "Percentile 90%";
                $descriptive_statistics["pckurtosis"] = "Percentage Coefficiente Kurtosis";
                $descriptive_statistics["ek"] = "Excess kurtosis";
                $descriptive_statistics["skewness"] = "Skewness";
                $descriptive_statistics["cskewness"] = "Coefficiente Skewness";
                $descriptive_statistics["sdistribution"] = "Skewed Distribution";
                $descriptive_statistics["ci"] = "Confidence Interval";            
            }        
            
            $this->evaluate_metrics = $evaluate_metrics;
            $this->descriptive_statistics = $descriptive_statistics;
            
        }else if($strategy == self::EVALUATE_PREQUENTIAL2){
            
            $evaluate_metrics[self::ACCURACY] = "Accuracy:";
            $evaluate_metrics[self::TIME] = "Time:";
            $evaluate_metrics[self::MEMORY] = "Memory (B/s):";
                        
            $evaluate_metrics[self::DIST] = "General Mean =";
            
            $evaluate_metrics[self::PRECISION] = "Mean Distance		FN	FP	TN		TP	Precision	Recall		MCC		F1";
            $evaluate_metrics[self::RECALL] = $evaluate_metrics[self::PRECISION];
            $evaluate_metrics[self::MCC] = $evaluate_metrics[self::PRECISION];
            $evaluate_metrics[self::F1] = $evaluate_metrics[self::PRECISION];
            
            $evaluate_metrics[self::FN] = $evaluate_metrics[self::PRECISION];
            $evaluate_metrics[self::FP] = $evaluate_metrics[self::PRECISION];
            $evaluate_metrics[self::TN] = $evaluate_metrics[self::PRECISION];
            $evaluate_metrics[self::TP] = $evaluate_metrics[self::PRECISION];            
            
            $descriptive_statistics["meanci"]="Mean (CI)";
            
            $this->evaluate_metrics = $evaluate_metrics;
            $this->descriptive_statistics = $descriptive_statistics;
        }
       
    }

}

class Mining extends EvaluateExtract{
        
    
    function convertCSV($arrayElements, $parameters, $breakline=1)
    {
        
        $iteration=0;
        $output="";
        $z=1;
        $columnCount = 0;
        
        $decimalseparator = $parameters["decimalformat"];
//         $decimalprecision = $parameters["decimalprecision"];
        
        foreach($arrayElements as $key=>$element)
        {
            
            
//             if($iteration==0){
                
//                 if($parameters["column"]==1)
//                 {
//                     foreach($element as $key2=>$item)
//                     {                                          
//                         foreach($item as $key3=>$item3)
//                         {
//                             $output .= (empty($output)? $key3:"\t".$key3);
//                         }
//                         $columnCount++;
//                     }
                    
                    
//                     $aux = $output;
                    
//                     $zz = count($element) / $breakline;
//                     $zz = ceil($zz);
                    
//                     for($y = 0; $y < $zz; $y++)
//                     {
//                         $output .= "\t" . $aux;
//                     }
                
                    
//                     $output .= "\n";
//                 }
                
                
//             }
            
            $output2="";
            
            foreach($element as $key2=>$item)
            {
                
                foreach($item as $key3=>$item3)
                {   
                    
                    if($decimalseparator != ".")
                    {
                        $item3 = str_replace(".", $decimalseparator, $item3);
                    }
                        
                    if($z == 1)
                    {
                        $output2 .= $item3;
                    }
                    else
                    {
                        $output2 .= "\t".$item3;
                    }
                    
                    if($z==$breakline)
                    {                                    
                        $z=1;
                        $output2 .= "\n";
                        
                    }else
                    {                                    
                        $z++;
                    }
                    
                }
                
            }
            $output .= $output2;
            
            
            $iteration++;
        }
        
        return $output;
    }
    
    
    
    
//     function extract_averages_detector_in_file($file, $parameters){
        
//         $output = array();
        
//         if(!is_readable($file))
//         {
//             return $output;
//         }
        
        
//         $handle = fopen($file, "r");
        
//         if ($handle)
//         {
            
//             $start_measuret = false;
//             $measuret = array();
//             $start_metrics = false;
            
//             while (($buffer = fgets($handle, 512)) !== false) {
                
//                 if(strpos($buffer, "learning evaluation instances,evaluation time") !== false){
//                     break;
//                 }
                
                
//                 if(strpos($buffer, "MetricsDetector") === false)
//                 {
                    
                    
//                 }else
//                 {
//                     //echo $buffer."=".(strpos($buffer, "MetricsDetector")===false?0:1)."<br>";
                    
//                     if($start_measuret == true)
//                     {
//                         $output[]  = $measuret;
                        
//                         $start_measuret = false;
//                         //var_dump($output);
//                         //exit("-------");
//                     }
                    
//                     //$start_measuret = false;
//                     $start_metrics = true;
                    
//                 }
                
//                 if($start_metrics == true)
//                 {
//                     //
//                     if(strpos($buffer, "MeasureDetect:")>-1)
//                     {
                        
//                         $start_measuret = true;
                        
//                         //if($parameters["detector"]==1)
//                         //{
//                         $data = explode("\t", trim($buffer));
                        
//                         $measuret = array("sumOfWarningFalse"=>$data[1],
//                             "sumOfWarningTrue"=>$data[2],
//                             "sumOfDrift"=>$data[3],
//                             "warningCountFalse"=>$data[4],
//                             "warningCountTrue"=>$data[5],
//                             "driftCount"=>$data[6],
//                             "accuracy"=>$data[7]
//                         );
                        
//                         //}
                        
                        
//                     }else
//                     {
//                         if($start_measuret == true)
//                         {
//                             //$start_metrics = false;
//                             $start_measuret = false;
//                             //$measuret = array();
//                         }
                        
//                     }
                    
//                 }
                
                
//             }
            
//             if(isset($measuret["accuracy"]))
//             {
//                 $output[]  = $measuret;
//             }
            
            
//         }
        
//         fclose($handle);
        
        
//         if(count($output) == 0)
//         {
//             $output[] = array("sumOfWarningFalse"=>"0",
//                 "sumOfWarningTrue"=>"0",
//                 "sumOfDrift"=>"0",
//                 "warningCountFalse"=>"0",
//                 "warningCountTrue"=>"0",
//                 "driftCount"=>"0",
//                 "accuracy"=>"0"
//             );
//         }
        
//         return $output;
//     }
    
    
    
    
    
    
    
    
    
    
    function getICValue($buffer){
        $result = "";
        if(strpos($buffer, "=") !== false){            
            if(strrpos($buffer, "(") !== false){
                $result = substr($buffer, strrpos($buffer, "("));
                $result = trim($result);
                $result = substr($result, 0, strrpos($result, ")")+1);
            }
        }
        return $result;
    }
    
    function isICValue($buffer){
        $result = false;
        if(strpos($buffer, "=") !== false){
            $result = true;
        }
        return $result;
    }
    
    function getValueFromList($buffer, $strParamName, $ic=false, $decimalprecision=2, $decimalseparator="."){
        $result = "";
        if(!$this->isICValue($buffer)){
            if(strpos($buffer, $strParamName) === false){
                $result = trim($buffer);
                $result = $this->numeric_format_option($result, $decimalprecision, $decimalseparator);
            }
        }else{
            if($ic == true){
                $result = $this->getICValue($buffer);
            }
        }
        return $result;
    }
    
    
    
    function extract_averages_in_file($file, $parameters)
    {        
        
        $json_return = array();
        
        
        
        if(empty($parameters["decimalformat"]))
        {
            $decimalseparator = ".";
        }
        else
        {
            $decimalseparator = $parameters["decimalformat"];
        }
        
        if(!isset($parameters["decimalprecision"]))
        {
            $decimalprecision = 2;
        }
        else
        {
            if($parameters["decimalprecision"] == null)
            {
                $decimalprecision = 2;
            }
            else
            {
                $decimalprecision = $parameters["decimalprecision"];
            }
        }
        
        if(!is_readable($file))
        {
            
            if($parameters["accuracy"]==1)
            {
                array_push($json_return, array("Accuracy"=>"*"));
            }
            if($parameters["time"]==1)
            {
                array_push($json_return, array("Time"=>"*"));
            }
            if($parameters["memory"]==1)
            {
                array_push($json_return, array("Memory"=>"*"));
            }
            if($parameters["entropy"]==1)
            {
                array_push($json_return, array("Entropy"=>"*"));
            }
            if($parameters["resume"]==1)
            {
                $str = implode("\t", array('*','*','*','*','*','*','*','*','*','*','*'));
                array_push($json_return, array("resume"=>$str));
            }
            if($parameters["dist"]==1)
            {
                array_push($json_return, array("dist"=>"*"));
            }
            if($parameters["fn"]==1)
            {
                array_push($json_return, array("fn"=>"*"));
            }
            if($parameters["fp"]==1)
            {
                array_push($json_return, array("fp"=>"*"));
            }
            if($parameters["tn"]==1)
            {
                array_push($json_return, array("tn"=>"*"));
            }
            if($parameters["tp"]==1)
            {
                array_push($json_return, array("tp"=>"*"));
            }
            if($parameters["precision"]==1)
            {
                array_push($json_return, array("precision"=>"*"));
            }
            if($parameters["recall"]==1)
            {
                array_push($json_return, array("recall"=>"*"));
            }
            if($parameters["mcc"]==1)
            {
                array_push($json_return, array("mcc"=>"*"));
            }
            if($parameters["f1"]==1)
            {
                array_push($json_return, array("f1"=>"*"));
            }
            if($parameters["mdr"]==1)
            {
                array_push($json_return, array("mdr"=>"*"));
            }
            if($parameters["mtfa"]==1)
            {
                array_push($json_return, array("mtfa"=>"*"));
            }
            if($parameters["mtd"]==1)
            {
                array_push($json_return, array("mtd"=>"*"));
            }
            if($parameters["mtr"]==1)
            {
                array_push($json_return, array("mtr"=>"*"));
            }
//             if($parameters["mcclist"]==1)
//             {
//                 array_push($json_return, array("mcclist"=>"*"));
//             }
            return $json_return;
        }
        
        
        $handle = fopen($file, "r");
        
        if ($handle) {            

            $startFind = "";        
            $strategy = $this->detectStrategy($file);            
            $this->mappingMetrics($strategy);
            
            $evaluate_metrics = $this->getEvaluateMetrics();
            $descriptive_statistics = $this->getDescritiveStatistics();
            
            
            while (($buffer = fgets($handle, 512)) !== false) {
                
                if(strpos($buffer, "learning evaluation instances,evaluation time") !== false)
                {
                    break;
                }
                
                
                if(//$strategy == "EvaluateInterleavedTestThenTrain2"
                    $strategy == self::EVALUATE_PREQUENTIAL2
                    || $strategy == self::EVALUATE_PREQUENTIAL_UFPE
                    || $strategy == self::EVALUATE_PREQUENTIAL_UFPE_FOR_DETECTORS
                    || $strategy == "Error")
                {
                    
                    
                    //                         var_dump($parameters["descriptivestatistics"]);exit("===");
                    
                    if($strategy == "Error")
                    {
                        $startFind = "Error";
                        
                    }else{
//                         var_dump($buffer);
// //                         var_dump($parameters);
// //                         var_dump($evaluate_metrics);
                        
                        foreach($evaluate_metrics as $key=>$item){
                            if(strpos($buffer, $item) !== FALSE){
                                if(strpos($buffer, $item)==0){
                                    if($parameters[$key] == 1){
                                        $startFind = $key;
                                        break;
                                    }
                                }
                            }
                        }
//                         exit("fim");
                    }
                    
                    foreach($evaluate_metrics as $key=>$item)
                    {
                        
                        if($startFind == $key){   
                            
                            $statistical = $descriptive_statistics[$parameters["descriptivestatistics"]];/// . " =";
                            $str_label = substr($buffer, 0, strlen($statistical));
                            
                            
                            if($statistical == $str_label){
                                $startFind = "";
//                                 var_dump($statistical);
//                                 var_dump($str_label);
//                                 exit("---");
                                break;
                            }else{
                                
//                                 var_dump($buffer);
                            }
//                             var_dump($statistical);
//                             var_dump($str_label);
//                             exit();
                            
//                             $statistical = $descriptive_statistics[$parameters["descriptivestatistics"]] . " =";
//                             $str_label = substr($buffer, 0, strlen($statistical));
                            
//                             var_dump($evaluate_metrics[$startFind]);exit();
                            
                            $strParamName = $evaluate_metrics[$startFind];//"Accuracy";
                            $strValue = $this->getValueFromList($buffer, $strParamName, false
                                , $decimalprecision, $decimalseparator);
                            
                            if($strValue != ""){               
                                
                                array_push($json_return, array(
                                    $descriptive_statistics[$parameters["descriptivestatistics"]]=>$strValue));
                                
//                                 array_push($json_return, array($strParamName=>$strValue));
//                                 var_dump($json_return);exit();
                            }   else{
                                
//                                 var_dump($json_return);
//                                 exit("fim");
                            }
                            
//                             $startFind = "";
                            
//                             var_dump($json_return);exit();
                        }
                        
                    }
//                 if(//$strategy == "EvaluateInterleavedTestThenTrain2"
//                     $strategy == self::EVALUATE_PREQUENTIAL2
//                     || $strategy == self::EVALUATE_PREQUENTIAL_UFPE
//                     || $strategy == self::EVALUATE_PREQUENTIAL_UFPE_FOR_DETECTORS
//                     || $strategy == "Error")
//                 {
                    
//                     foreach($evaluate_metrics as $key=>$item){
//                         if(strpos($buffer, $item) !== FALSE){
//                             if(strpos($buffer, $item)==0){
//                                 if($parameters[$key] == 1){
//                                     $startFind = $key;
//                                     break;
//                                 }
//                             }
//                         }
//                     }
                    
//                     var_dump($buffer);
//                     var_dump($parameters);
//                     var_dump($evaluate_metrics);
//                     exit();
                    
//                     if($startFind !=""){
// //                     $statistical = $descriptive_statistics[$parameters["descriptivestatistics"]] . " =";
// //                     $str_label = substr($buffer, 0, strlen($statistical));
                    
                    
//                     var_dump($startFind);
//                     exit("--");
//                     }
                    
                    
                    
//                         if(strpos($buffer, "Accuracy:")>-1){
//                             $startFind = "accuracy";
//                             $accuracy_open = true;
//                         }
//                         else if(strpos($buffer, "Time:")>-1){
//                             $startFind = "time";
//                         }
//                         else if(strpos($buffer, "Memory (B/s):")>-1){
//                             $startFind = "memory";
//                         }
//                         else if(strpos($buffer, "Mean Distance")>-1){
//                             $startFind = "resume";
//                         }
//                         else if(strpos($buffer, "Entropy")>-1){
//                             $startFind = "entropy";
//                         }
//                         else if(strpos($buffer, "MDR:")>-1){
//                             $startFind = "mdr";
//                         }
//                         else if(strpos($buffer, "MTD:")>-1){
//                             $startFind = "mtd";
//                         }
//                         else if(strpos($buffer, "MTFA:")>-1){
//                             $startFind = "mtfa";
//                         }
//                         else if(strpos($buffer, "MTR:")>-1){
//                             $startFind = "mtr";
//                         }
//                         else if(strpos($buffer, "Precision:")>-1){
//                             $startFind = "precision";
//                         }
//                         else if(strpos($buffer, "Recall:")>-1){
//                             $startFind = "recall";
//                         }
//                         else if(strpos($buffer, "MCC:")>-1){
//                             $startFind = "mcc";
//                         }
//                         else if(strpos($buffer, "F1:")>-1){
//                             $startFind = "f1";
//                         }
//                         else{
//                             if($strategy == "Error" && $accuracy_open == false)
//                             {
//                                 $startFind = "Error";
//                             }
//                         }
                        
//                         switch($startFind){
                            
//                             case 'accuracy':
                                
//                                 if($parameters["accuracy"]==1){
//                                     $strParamName = "Accuracy";
//                                     $strValue = $this->getValueFromList($buffer, $strParamName
//                                         , ($parameters["interval"] == 1?true:false)
//                                         , $decimalprecision, $decimalseparator);
                                    
//                                     if($strValue != ""){
//                                         array_push($json_return, array($strParamName=>$strValue));
//                                     }                                                                     
//                                 }
                                
//                                 break;
//                             case 'time':
                                
//                                 if($parameters["time"]==1){
                                    
//                                     $strParamName = "Time";
//                                     $strValue = $this->getValueFromList($buffer, $strParamName
//                                         , ($parameters["interval"] == 1?true:false)
//                                         , $decimalprecision, $decimalseparator);
                                    
//                                     if($strValue != ""){
//                                         array_push($json_return, array($strParamName=>$strValue));
//                                     }
//                                 }
                                
//                                 break;
//                             case 'memory':
                                
//                                 if($parameters["memory"]==1){
                                    
//                                     $strParamName = "Memory";
//                                     $strValue = $this->getValueFromList($buffer, $strParamName
//                                         , ($parameters["interval"] == 1?true:false)
//                                         , $decimalprecision, $decimalseparator);
                                    
//                                     if($strValue != ""){
//                                         array_push($json_return, array($strParamName=>$strValue));
//                                     }
//                                 }
                                
//                                 break;
//                             case 'mtd':
                                                               
//                                 if($parameters["mtd"]==1){
//                                     $strParamName = "MTD";
//                                     $strValue = $this->getValueFromList($buffer, $strParamName
//                                         , ($parameters["interval"] == 1?true:false)
//                                         , $decimalprecision, $decimalseparator);
                                    
//                                     if($strValue != ""){
//                                         array_push($json_return, array($strParamName=>$strValue));
//                                     }
//                                 }
                                
//                                 break;  
                                
//                             case 'mtr':
                                
//                                 if($parameters["mtr"]==1){
                                    
//                                     $strParamName = "MTR";
//                                     $strValue = $this->getValueFromList($buffer, $strParamName
//                                         , ($parameters["interval"] == 1?true:false)
//                                         , $decimalprecision, $decimalseparator);
                                    
//                                     if($strValue != ""){
//                                         array_push($json_return, array($strParamName=>$strValue));
//                                     }
//                                 }
                                    
//                                 break;                                    
                    
//                             case 'mcc':
                                
//                                 if($parameters["mcc"]==1)
//                                 {
//                                     $strParamName = "MCC";
//                                     $strValue = $this->getValueFromList($buffer, $strParamName
//                                         , ($parameters["interval"] == 1?true:false)
//                                         , $decimalprecision, $decimalseparator);
                                    
//                                     if($strValue != ""){
//                                         array_push($json_return, array($strParamName=>$strValue));
//                                     }
//                                 }
                                
//                                 break;
                                
//                             case 'precision':
                                
//                                 if($parameters["precision"]==1)
//                                 {
//                                     $strParamName = "Precision";
//                                     $strValue = $this->getValueFromList($buffer, $strParamName
//                                         , ($parameters["interval"] == 1?true:false)
//                                         , $decimalprecision, $decimalseparator);
                                    
//                                     if($strValue != ""){
//                                         array_push($json_return, array($strParamName=>$strValue));
//                                     }
//                                 }
                                
//                                 break;
                                
//                             case 'recall':
                                
//                                 if($parameters["recall"]==1)
//                                 {
//                                     $strParamName = "Recall";
//                                     $strValue = $this->getValueFromList($buffer, $strParamName
//                                         , ($parameters["interval"] == 1?true:false)
//                                         , $decimalprecision, $decimalseparator);
                                    
//                                     if($strValue != ""){
//                                         array_push($json_return, array($strParamName=>$strValue));
//                                     }
//                                 }
                                
//                                 break;
                                
//                             case 'f1':
//                                 if($parameters["f1"]==1)
//                                 {
//                                     $strParamName = "F1";
//                                     $strValue = $this->getValueFromList($buffer, $strParamName
//                                         , ($parameters["interval"] == 1?true:false)
//                                         , $decimalprecision, $decimalseparator);
                                    
//                                     if($strValue != ""){
//                                         array_push($json_return, array($strParamName=>$strValue));
//                                     }
//                                 }
                                
//                                 break;
                                
//                         }                        
                }
                
            }
            
            
        }
        
        fclose($handle);
        
        
        
        return $json_return;
    }
    
    
    /*
     * for zero-padding with a length of n
     *
     * @param	mixed	$number
     * @param	string	$format
     *
     * @return	void
     */
    function format_number($number, $format)
    {
        $result = "";
        
        $n = floor(strlen($number) / 10);
        $s = $format - strlen($number);
        
        while ($n < $s) {
            
            $result .= "0";
            $n ++;
        }
        
        $result .= $number;
        
        return $result;
    }
    
    function decimalen($value, $decimalseparador=".")
    {
        $result = 0;
        
        if(strpos($value, $decimalseparador) !== false)
        {
            $dPrecision = substr($value, strrpos($value, $decimalseparador)+1);
            $result = strlen($dPrecision);
        }
        
        return $result;
    }
    
    function numeric_format_option($value, $decimalprecision, $decimalseparator)
    {
        //$result = floatval($value);
        $result = $value;
        
        if($decimalprecision > 0)
        {
            $dPrecision = $this->decimalen($result);    
        }
        else
        {
            $dPrecision = 0;
            $value = substr($value, 0, strpos($value,"."));
        }
        
        
        if($dPrecision > 0)//is_numeric($result))
        {            
            
            if($dPrecision > $decimalprecision)
            {
                $result = number_format($result, $decimalprecision, $decimalseparator, ".");
            }
            else 
            {
                $result = str_replace(".", $decimalseparator, $result);
            }

            if(strpos($result, $decimalseparator) === false)
            {

                $result .= $decimalseparator;                
                
                $n = 0;
                while ($n < $decimalprecision) {
                    
                    $result .= "0";
                    $n ++;
                }
            }
            else 
            {
                $d = substr($result, strrpos($result, $decimalseparator)+1);
                $d = strlen($d);
                
                $n = $d;
                while ($n < $decimalprecision) {
                    
                    $result .= "0";
                    $n ++;
                }
                 
//                 var_dump($result);exit();
            }
            
//             $result = $this->format_number($result, $decimalprecision);
        }
        else 
        {
            $result = $value;
        }
        
        return $result;
    }
    
    //Extrai a media da precisão e variação da "confiança"
    
    function miningFile($file, $parameters)
    {
       
        $json_return = array();
        
        if(empty($parameters["decimalformat"]))
        {
            $decimalseparator = ".";
        }
        else
        {
            $decimalseparator = $parameters["decimalformat"];
        }
        
        if(!isset($parameters["decimalprecision"]))
        {
            $decimalprecision = 2;
        }
        else
        {
            if($parameters["decimalprecision"] == null)
            {
                $decimalprecision = 2;
            }
            else
            {
                $decimalprecision = $parameters["decimalprecision"];
            }
        }
        
//         if(!is_readable($file))
//         {

//             foreach($this->evaluate_metrics as $key=>$item){
                
//                 if(strpos($buffer, $item) !== FALSE){
//                     if($parameters[$key] == 1){
//                         array_push($json_return, array($item=>"*"));
//                     }
//                 }
//             }
            
//             return $json_return;
//         }
        
        $handle = fopen($file, "r");
        
        if ($handle) {

            $startFind = "";            
            $accuracy_open = false;            
            $strategy = $this->detectStrategy($file);
            
            $this->mappingMetrics($strategy);
                        
            
            
            $fn_fp_tn_tp = "";
            
            if($parameters[self::FN]==1
                || $parameters[self::FP]==1
                || $parameters[self::TN]==1
                ||$parameters[self::TP]==1)
            {                
                
                $metrics = $this->detect_FN_FP_TN_TP($file);
                
                if($metrics == "FN:"){
                    
                }else{
                    
                    if($parameters["descriptivestatistics"]== 'mean'
                        || $parameters["descriptivestatistics"]== 'meanci')
                    {
                        
                    }else if($parameters["descriptivestatistics"]== 'sum')
                    {
                        $metrics = "FN	FP	TN		TP";
                    } 
                    
                    if($parameters[self::FN]==1)
                    {
                        $fn_fp_tn_tp = self::FN;
                        $this->setEvaluateMetrics(self::FN, $metrics);
                    }
                    
                    if($parameters[self::FP]==1)
                    {
                        $fn_fp_tn_tp = self::FP;
                        $this->setEvaluateMetrics(self::FP, $metrics);
                    }
                    
                    if($parameters[self::TN]==1)
                    {
                        $fn_fp_tn_tp = self::TN;
                        $this->setEvaluateMetrics(self::TN, $metrics);
                    }
                    
                    if($parameters[self::TP]==1)
                    {
                        $fn_fp_tn_tp = self::TP;
                        $this->setEvaluateMetrics(self::TP, $metrics);
                    }
                                         
                }
                                  
            }
            
            $evaluate_metrics = $this->getEvaluateMetrics();
            $descriptive_statistics = $this->getDescritiveStatistics();
            
//             var_dump($evaluate_metrics);
//             var_dump($descriptive_statistics);
//             exit("===");
            
//             if($parameters['descriptivestatistics'])
            
            
            
            try{
                
                
                while (($buffer = fgets($handle, 512)) !== false) 
                {
                    
                    if(strpos($buffer, "learning evaluation instances,evaluation time") !== false)
                    {
                        break;
                    }
                    
                    
                    if(//$strategy == "EvaluateInterleavedTestThenTrain2" 
                        $strategy == self::EVALUATE_PREQUENTIAL2
                        || $strategy == self::EVALUATE_PREQUENTIAL_UFPE
                        || $strategy == self::EVALUATE_PREQUENTIAL_UFPE_FOR_DETECTORS
                        || $strategy == "Error")
                    {
                            
                        
//                         var_dump($parameters["descriptivestatistics"]);exit("===");
                            
                        if($strategy == "Error")
                        {
                            $startFind = "Error";
                            
                        }else{
                            foreach($evaluate_metrics as $key=>$item){
                                if(strpos($buffer, $item) !== FALSE){
                                    if(strpos($buffer, $item)==0){
                                        if($parameters[$key] == 1){
                                            $startFind = $key;
                                            break;
                                        } 
                                    }                                                                           
                                }
                            }
                        }
                            
                        foreach($evaluate_metrics as $key=>$item)
                        {                          
                            
                            if($startFind == $key){                                
                                
                                
                                if($key ==  self::DIST){
                                    
                                    
                                    if($parameters[$key] == 1){
                                        
//                                         $statistical = $descriptive_statistics[$parameters["descriptivestatistics"]] . " =";
                                        
//                                         $str_label = substr($buffer, 0, strlen($statistical));
                                        
                                        
//                                         if($str_label == $statistical){//strpos($buffer, $statistical) !== FALSE){
                                            
                                            $tmp = $buffer;
                                            $tmp = substr($tmp,strpos($tmp, "=")+1);
                                            $value_ = trim($tmp);
                                            
                                            if(is_numeric($value_)){
                                                $value_ = $this->numeric_format_option($value_, $decimalprecision, $decimalseparator);
                                            }
                                            
                                            array_push($json_return, array(
                                                $descriptive_statistics[$parameters["descriptivestatistics"]]=>$value_));
                                            
                                            $startFind = "";
//                                         }
                                        
                                    }
                                    
                                }else{
                                    
                                    if($parameters[$key] == 1){
                                        
                                        
                                        
                                        if($strategy == self::EVALUATE_PREQUENTIAL2){
                                            
                                            $buffer = trim($buffer);
                                            
                                            if($parameters["dist"]==1 
                                                || $parameters["fn"]==1 
                                                || $parameters["fp"]==1
                                                || $parameters["tn"]==1
                                                ||$parameters["tp"]==1
                                                || $parameters["precision"]==1
                                                || $parameters["recall"]==1
                                                || $parameters["mcc"]==1
                                                || $parameters["f1"]==1){
                                                
                                                if(trim($buffer) == $evaluate_metrics[self::FN]){
                                                    break;
                                                }
                                                
                                                $detector_space = false;
                                                $newValues = "";
                                                
                                                for($w = 0; $w < strlen($buffer); $w++){
                                                    
                                                    if($buffer[$w] == " " || $buffer[$w] == "\t"){
                                                        
                                                        if($detector_space == false){
                                                            
                                                            $newValues .= "\t";
                                                            
                                                        }else{
                                                            
                                                        }
                                                        
                                                        $detector_space = true;
                                                        
                                                    }else{
                                                        
                                                        $newValues .= $buffer[$w];
                                                        $detector_space = false;
                                                    }
                                                }
                                                
                                                
                                                $buffer = $newValues;
                                                
                                                $buffer = str_replace("\n","", $buffer);
                                                $buffer = str_replace(" ","", $buffer);
                                                $buffer = str_replace("\r\n","", $buffer);
                                                
                                                $itens_list = array();
                                                if(strpos($buffer, "\t") !== false)
                                                {
                                                    $itens_list = explode("\t", $buffer);
                                                }
                                                
                                                
                                                $tmp = "";
                                                
                                                if($parameters["dist"]==1){
                                                    $tmp = $itens_list[0];
                                                }else if($parameters["fn"]==1){
                                                    $tmp = $itens_list[1];
                                                }else if($parameters["fp"]==1){
                                                    $tmp = $itens_list[2];
                                                }else if($parameters["tn"]==1){
                                                    $tmp = $itens_list[3];
                                                }else if($parameters["tp"]==1){
                                                    $tmp = $itens_list[4];
                                                }else if($parameters["precision"]==1){
                                                    $tmp = $itens_list[5];
                                                }else if($parameters["recall"]==1){
                                                    $tmp = $itens_list[6];
                                                }else if($parameters["mcc"]==1){
                                                    $tmp = $itens_list[7];
                                                }else if($parameters["f1"]==1){
                                                    $tmp = $itens_list[8];
                                                }
                                                
                                                
                                                $value_ = trim($tmp);
                                                
                                                if(is_numeric($value_)){
                                                    $value_ = $this->numeric_format_option($value_, $decimalprecision, $decimalseparator);
                                                }
                                                
                                                array_push($json_return, array(
                                                    $descriptive_statistics[$parameters["descriptivestatistics"]]=>$value_));
                                                
                                                $startFind = "";
                                                
                                            }else{
                                                
                                                $statistical = $descriptive_statistics["meanci"] . " =";
                                                $str_label = substr($buffer, 0, strlen($statistical));
                                                
                                               
                                                
                                                if($str_label == $statistical){//strpos($buffer, $statistical) !== FALSE){
                                                    
                                                    $tmp = $buffer;
                                                    $tmp = substr($tmp,strpos($tmp, $statistical)+strlen($statistical)+1);
                                                    $value_ = trim($tmp);
                                                    
                                                    if($parameters["descriptivestatistics"] == "mean"){
                                                        $value_ = substr($value_, 0, strpos($value_, "("));
                                                        $value_ = trim($value_);
                                                    }
                                                    
                                                    if(is_numeric($value_)){
                                                        $value_ = $this->numeric_format_option($value_, $decimalprecision, $decimalseparator);
                                                    }
                                                    
                                                    array_push($json_return, array(
                                                        $descriptive_statistics[$parameters["descriptivestatistics"]]=>$value_));
                                                    
                                                    $startFind = "";
                                                }
                                                
                                            }
                                                                                        
                                            
                                            
//                                             if($parameters["descriptivestatistics"] == self::F1){
                                                
//                                             }
//                                             Mean Distance		FN	FP	TN		TP	Precision	Recall		MCC		F1
                                            
                                        }else if($strategy == self::EVALUATE_PREQUENTIAL_UFPE
                                            || $strategy == self::EVALUATE_PREQUENTIAL_UFPE_FOR_DETECTORS)
                                        {
                                            
                                            if(!empty($fn_fp_tn_tp)){
                                                                                                
                                                if($parameters["descriptivestatistics"]== 'mean'
                                                    || $parameters["descriptivestatistics"]== 'meanci')
                                                {
                                                    $statistical = $descriptive_statistics["meanci"] . " =";
                                                    
                                                    $str_label = substr($buffer, 0, strlen($statistical));
                                                    
                                                    $signal_ci = "+-";
                                                    
                                                    if($str_label == $statistical){
                                                        
                                                        $buffer = substr($buffer, strlen($statistical));
                                                        $buffer = trim($buffer);
                                                        
                                                        $str_list = explode(")", $buffer);
                                                        
                                                        $fn = $str_list[0] . ")";
                                                        $fp = $str_list[1] . ")";
                                                        $tn = $str_list[2] . ")";
                                                        $tp = $str_list[3] . ")";
                                                        
                                                        
                                                        
                                                        $str_list = explode(" ", $fn);
                                                        $fn = trim($str_list[0]);
                                                        $fn_ci = substr($str_list[1],3,strlen($str_list[1])-4);
                                                        
                                                        $str_list = explode(" ", $fp);
                                                        $fp = trim($str_list[0]);
                                                        $fp_ci = substr($str_list[1],3,strlen($str_list[1])-4);
                                                        
                                                        $str_list = explode(" ", $tn);
                                                        $tn = trim($str_list[0]);
                                                        $tn_ci = substr($str_list[1],3,strlen($str_list[1])-4);
                                                        
                                                        $str_list = explode(" ", $tp);
                                                        $tp = trim($str_list[0]);
                                                        $tp_ci = substr($str_list[1],3,strlen($str_list[1])-4);
                                                        
                                                        if(is_numeric($fn)){
                                                            $value_ = $this->numeric_format_option($fn, $decimalprecision, $decimalseparator);
                                                        }
                                                        if(is_numeric($fn_ci)){
                                                            $value_ci = $this->numeric_format_option($fn_ci, $decimalprecision, $decimalseparator);
                                                        }
                                                        
                                                        if(is_numeric($fp)){
                                                            $value_ = $this->numeric_format_option($fp, $decimalprecision, $decimalseparator);
                                                        }
                                                        if(is_numeric($fp_ci)){
                                                            $value_2 = $this->numeric_format_option($fp_ci, $decimalprecision, $decimalseparator);
                                                        }
                                                        
                                                        if(is_numeric($tn)){
                                                            $value_ = $this->numeric_format_option($tn, $decimalprecision, $decimalseparator);
                                                        }
                                                        if(is_numeric($tn_ci)){
                                                            $value_2 = $this->numeric_format_option($tn_ci, $decimalprecision, $decimalseparator);
                                                        }
                                                        
                                                        if(is_numeric($tp)){
                                                            $value_ = $this->numeric_format_option($tp, $decimalprecision, $decimalseparator);
                                                        }
                                                        if(is_numeric($tp_ci)){
                                                            $value_2 = $this->numeric_format_option($tp_ci, $decimalprecision, $decimalseparator);
                                                        }
                                                        
                                                        if($parameters["descriptivestatistics"] == "mean"){
                                                            
                                                        }else if($parameters["descriptivestatistics"] == "meanci"){
                                                            
                                                            if($fn_fp_tn_tp == "fn"){
                                                                $value_ = $value_ . " (" . $signal_ci . $value_2 . ")";
                                                                
                                                            }else if($fn_fp_tn_tp == "fp"){
                                                                $value_ = $value_ . " (" . $signal_ci . $value_2 . ")";
                                                                
                                                            }else if($fn_fp_tn_tp == "tn"){
                                                                $value_ = $value_ . " (" . $signal_ci . $value_2 . ")";
                                                                
                                                            }else if($fn_fp_tn_tp == "tp"){
                                                                $value_ = $value_ . " (" . $signal_ci . $value_2 . ")";
                                                            }
                                                            
                                                        }
                                                        
                                                        array_push($json_return, array(
                                                            $descriptive_statistics[$parameters["descriptivestatistics"]]=>$value_));
                                                        
                                                        $startFind = "";
                                                        
                                                    }
                                                    
                                                    
                                                }else//($parameters["descriptivestatistics"]== 'sum')
                                                {
                                                    if(strpos($buffer, $metrics) !== FALSE){
                                                        
                                                        break;
                                                    }
                                                    
                                                    $buffer = trim($buffer);
                                                    $buffer = str_replace("\t\t", "\t", $buffer);
                                                    $str_list = explode("\t", $buffer);
                                                    
                                                    if($fn_fp_tn_tp == "fn"){
                                                        $value_ = $str_list[0];
                                                        
                                                    }else if($fn_fp_tn_tp == "fp"){
                                                        $value_ = $str_list[1];
                                                        
                                                    }else if($fn_fp_tn_tp == "tn"){
                                                        $value_ = $str_list[2];
                                                        
                                                    }else if($fn_fp_tn_tp == "tp"){
                                                        $value_ = $str_list[3];
                                                    }
                                                    
                                                    array_push($json_return, array(
                                                        $descriptive_statistics[$parameters["descriptivestatistics"]]=>$value_));
                                                    
                                                    $startFind = "";
                                                    break;
                                                    
                                                } 
                                                
                                                
//                                                 $statistical = $descriptive_statistics[$parameters["descriptivestatistics"]] . " =";
                                                
                                                
                                            }else{
                                                
                                                $statistical = $descriptive_statistics[$parameters["descriptivestatistics"]] . " =";
                                                $str_label = substr($buffer, 0, strlen($statistical));
                                                
                                                if($str_label == $statistical){//strpos($buffer, $statistical) !== FALSE){
                                                    
                                                    $tmp = $buffer;
                                                    $tmp = substr($tmp,strpos($tmp, $statistical)+strlen($statistical)+1);
                                                    $value_ = trim($tmp);
                                                    
                                                    if(is_numeric($value_)){
                                                        $value_ = $this->numeric_format_option($value_, $decimalprecision, $decimalseparator);
                                                    }
                                                    
                                                    array_push($json_return, array(
                                                        $descriptive_statistics[$parameters["descriptivestatistics"]]=>$value_));
                                                    
                                                    $startFind = "";
                                                }
                                                
                                            }
                                            
                                            
                                        }
                                        
                                    }
                                }                                
                                
                            }
                        }
                                                        
//                     }else if($strategy == "EvaluatePrequential2"){
                                        
                    
                    
                    }else{
                        //error
                        
                        array_push($json_return, array("Accuracy"=>"x"));
                        break;
                    }
   
            }
            
            fclose($handle);
            
        } catch(Exception $e){
            exit("Error: ".$e->getMessage());
        }
        
        
        
        
        
        
    }
    
    //array_push($json_return, array("Accuracy"=>"x"));
    
    if(count($json_return)<1){
        array_push($json_return, array("Accuracy"=>"x"));
    }
    
    
    return $json_return;
}






function detectStrategy($file){
    
    $result = "";
    
    try{
        
        if(!is_readable($file))
        {
            return $result;
        }
        
        $handle = fopen($file, "r");
        
        if ($handle) {
                        
            while (($buffer = fgets($handle, 4096)) !== false) {
                
                if(strpos($buffer, "EvaluateInterleavedTestThenTrain2 ")>-1){
                    
                    $result = "EvaluateInterleavedTestThenTrain2";
                    break;
                    
//                 }else if(strpos($buffer, "EvaluateInterleavedTestThenTrain3 ")>-1){
                    
//                     $result = "EvaluateInterleavedTestThenTrain3";
//                     break;
                    
//                 }else if(strpos($buffer, "Generation 1")>-1){
                    
//                     $result = "GeneticAlgorithm";
//                     break;
                }else if(strpos($buffer, "EvaluatePrequential2 ")>-1){
                    
                    $result = self::EVALUATE_PREQUENTIAL2; // "EvaluatePrequential2";
                    break;
//                 }else if(strpos($buffer, "EvaluatePrequential3 ")>-1){
                    
//                     $result = "EvaluatePrequential3";
//                     break;
                }else if(strpos($buffer, "EvaluatePrequentialUFPE ")>-1){
                    
                    $result = self::EVALUATE_PREQUENTIAL_UFPE;//"EvaluatePrequentialUFPE";
                    break;
                }else if(strpos($buffer, "EvaluatePrequentialUFPEforDetectors ")>-1){
                    
                    $result = self::EVALUATE_PREQUENTIAL_UFPE_FOR_DETECTORS;//"EvaluatePrequentialUFPEforDetectors";
                    break;
                }else if(strpos($buffer, "EvaluatePrequential ")>-1){
                    
                    $result = "EvaluatePrequential";
                    break;
                }else{
                    $result = "Error";
                }
                
            }
            
            fclose($handle);
            
        }
        
    }catch(Exception $e){
        exit("error: ".$e->getMessage());
    }
    
    
    return $result;
}



function detect_FN_FP_TN_TP($file){
    
    $result = "";    
    try{
        
        if(!is_readable($file))
        {
            return $result;
        }
        
        $handle = fopen($file, "r");
        
        if ($handle) {
            
            while (($buffer = fgets($handle, 4096)) !== false) {                
                if(strpos($buffer, "learning evaluation instances,evaluation time") !== false)
                {
                    break;
                }                
                if(strpos($buffer, "FN	FP	TN	TP") !== FALSE){
                    $result = "FN	FP	TN	TP";
                    break;
                }else if(strpos($buffer, "FN:") !== FALSE){
                    $result = "FN:";
                    break;
                }else{
                    $result = "Error";
                }                
            }            
            fclose($handle);            
        }
        
    }catch(Exception $e){
        exit("error: ".$e->getMessage());
    }    
    
    return $result;
}






function getScriptMOA($file){
    
    $result = "";
    
    try
    {
                
        if(!is_readable($file))
        {
            return $result;
        }
        
        $handle = fopen($file, "r");
        
        if ($handle) 
        {          
            $output = "";
            
            $metadata = FALSE;
            
            while (($buffer = fgets($handle, 4096)) !== false) 
            {            
                  
                if($output != "")
                {
                    $output .= "\n";
                }
                                
                
                if(strpos($buffer, "<meta-data") === FALSE)
                {
                    
                }
                else 
                {
                    $metadata = TRUE;
                }
                
                
                if($metadata == TRUE)
                {
                    if(strpos($buffer, "Accuracy:") === FALSE)
                    {
                        $output .= trim($buffer);
                    }
                    else 
                    {
                        break;
                    }
                }
                else
                {
                    if(trim($buffer) == "")
                    {
                        break;
                    }
                    else
                    {
                        $output .= trim($buffer);
                    }
                }

            }            
                        
            
            if(strpos($output, "<meta-data") === FALSE)
            {
                $result = $output;
            }
            else
            {
                $aux = substr($output, strpos($output, "script-data") + strlen("script-data"));
                $aux = substr($aux, strpos($aux, "moamanager:value=\"") + strlen("moamanager:value=\""));
                $aux = substr($aux, 0, strpos($aux, '"'));
                $result = $aux;
            } 
            
            if(strpos($result, "\n") === FALSE)
            {
                
            }
            else 
            {
                $result = str_replace("\n", "", $result);
            }
            
            fclose($handle);            
        }
        
    }
    catch(Exception $e)
    {
        exit("error: ".$e->getMessage());
    }
    
    
    return $result;
}


}

?>
