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

class Mining{
        
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
    
    
    
    
    function extract_averages_detector_in_file($file, $parameters){
        
        $output = array();
        
        if(!is_readable($file))
        {
            return $output;
        }
        
        
        $handle = fopen($file, "r");
        
        if ($handle)
        {
            
            $start_measuret = false;
            $measuret = array();
            $start_metrics = false;
            
            while (($buffer = fgets($handle, 512)) !== false) {
                
                if(strpos($buffer, "learning evaluation instances,evaluation time") !== false){
                    break;
                }
                
                
                if(strpos($buffer, "MetricsDetector") === false)
                {
                    
                    
                }else
                {
                    //echo $buffer."=".(strpos($buffer, "MetricsDetector")===false?0:1)."<br>";
                    
                    if($start_measuret == true)
                    {
                        $output[]  = $measuret;
                        
                        $start_measuret = false;
                        //var_dump($output);
                        //exit("-------");
                    }
                    
                    //$start_measuret = false;
                    $start_metrics = true;
                    
                }
                
                if($start_metrics == true)
                {
                    //
                    if(strpos($buffer, "MeasureDetect:")>-1)
                    {
                        
                        $start_measuret = true;
                        
                        //if($parameters["detector"]==1)
                        //{
                        $data = explode("\t", trim($buffer));
                        
                        $measuret = array("sumOfWarningFalse"=>$data[1],
                            "sumOfWarningTrue"=>$data[2],
                            "sumOfDrift"=>$data[3],
                            "warningCountFalse"=>$data[4],
                            "warningCountTrue"=>$data[5],
                            "driftCount"=>$data[6],
                            "accuracy"=>$data[7]
                        );
                        
                        //}
                        
                        
                    }else
                    {
                        if($start_measuret == true)
                        {
                            //$start_metrics = false;
                            $start_measuret = false;
                            //$measuret = array();
                        }
                        
                    }
                    
                }
                
                
            }
            
            if(isset($measuret["accuracy"]))
            {
                $output[]  = $measuret;
            }
            
            
        }
        
        fclose($handle);
        
        
        if(count($output) == 0)
        {
            $output[] = array("sumOfWarningFalse"=>"0",
                "sumOfWarningTrue"=>"0",
                "sumOfDrift"=>"0",
                "warningCountFalse"=>"0",
                "warningCountTrue"=>"0",
                "driftCount"=>"0",
                "accuracy"=>"0"
            );
        }
        
        return $output;
    }
    
    
    
    
    
    
    
    
    
    
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
            if($parameters["timer"]==1)
            {
                array_push($json_return, array("Timer"=>"*"));
            }
            if($parameters["memory"]==1)
            {
                array_push($json_return, array("Memory"=>"*"));
            }
            if($parameters["dissimilarity"]==1)
            {
                array_push($json_return, array("Dissimilarity"=>"*"));
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
            
            while (($buffer = fgets($handle, 512)) !== false) {
                
                
                if($strategy == "EvaluateInterleavedTestThenTrain2" ||
                    $strategy == "EvaluatePrequential2"
                    || $strategy == "EvaluatePrequentialUFPE"){
                        
                        if(strpos($buffer, "Accuracy:")>-1){
                            $startFind = "accuracy";
                            $accuracy_open = true;
                        }
                        else if(strpos($buffer, "Time:")>-1){
                            $startFind = "time";
                        }
                        else if(strpos($buffer, "Memory (B/s):")>-1){
                            $startFind = "memory";
                        }
                        else if(strpos($buffer, "Mean Distance")>-1){
                            $startFind = "resume";
                        }
                        else if(strpos($buffer, "Dissimilarity")>-1){
                            $startFind = "dissimilarity";
                        }
                        else if(strpos($buffer, "MDR:")>-1){
                            $startFind = "mdr";
                        }
                        else if(strpos($buffer, "MTD:")>-1){
                            $startFind = "mtd";
                        }
                        else if(strpos($buffer, "MTFA:")>-1){
                            $startFind = "mtfa";
                        }
                        else if(strpos($buffer, "MTR:")>-1){
                            $startFind = "mtr";
                        }
                        else if(strpos($buffer, "Precision:")>-1){
                            $startFind = "precision";
                        }
                        else if(strpos($buffer, "Recall:")>-1){
                            $startFind = "recall";
                        }
                        else if(strpos($buffer, "MCC:")>-1){
                            $startFind = "mcc";
                        }
                        else if(strpos($buffer, "F1:")>-1){
                            $startFind = "f1";
                        }
                        else{
                            if($strategy == "Error" && $accuracy_open == false)
                            {
                                $startFind = "Error";
                            }
                        }
                        
                        switch($startFind){
                            
                            case 'accuracy':
                                
                                if($parameters["accuracy"]==1){
                                    $strParamName = "Accuracy";
                                    $strValue = $this->getValueFromList($buffer, $strParamName
                                        , ($parameters["interval"] == 1?true:false)
                                        , $decimalprecision, $decimalseparator);
                                    
                                    if($strValue != ""){
                                        array_push($json_return, array($strParamName=>$strValue));
                                    }                                                                     
                                }
                                
                                break;
                            case 'time':
                                
                                if($parameters["timer"]==1){
                                    
                                    $strParamName = "Time";
                                    $strValue = $this->getValueFromList($buffer, $strParamName
                                        , ($parameters["interval"] == 1?true:false)
                                        , $decimalprecision, $decimalseparator);
                                    
                                    if($strValue != ""){
                                        array_push($json_return, array($strParamName=>$strValue));
                                    }
                                }
                                
                                break;
                            case 'memory':
                                
                                if($parameters["memory"]==1){
                                    
                                    $strParamName = "Memory";
                                    $strValue = $this->getValueFromList($buffer, $strParamName
                                        , ($parameters["interval"] == 1?true:false)
                                        , $decimalprecision, $decimalseparator);
                                    
                                    if($strValue != ""){
                                        array_push($json_return, array($strParamName=>$strValue));
                                    }
                                }
                                
                                break;
                                
                            case 'mtr':
                                
                                if($parameters["mtrlist"]==1){
                                    
                                    $strParamName = "MTR";
                                    $strValue = $this->getValueFromList($buffer, $strParamName
                                        , ($parameters["interval"] == 1?true:false)
                                        , $decimalprecision, $decimalseparator);
                                    
                                    if($strValue != ""){
                                        array_push($json_return, array($strParamName=>$strValue));
                                    }
                                }
                                    
                                break;                                    
                    
                            case 'mcc':
                                
                                if($parameters["mcc"]==1)
                                {
                                    $strParamName = "MCC";
                                    $strValue = $this->getValueFromList($buffer, $strParamName
                                        , ($parameters["interval"] == 1?true:false)
                                        , $decimalprecision, $decimalseparator);
                                    
                                    if($strValue != ""){
                                        array_push($json_return, array($strParamName=>$strValue));
                                    }
                                }
                                
                                break;
                                
                            case 'precision':
                                
                                if($parameters["precision"]==1)
                                {
                                    $strParamName = "Precision";
                                    $strValue = $this->getValueFromList($buffer, $strParamName
                                        , ($parameters["interval"] == 1?true:false)
                                        , $decimalprecision, $decimalseparator);
                                    
                                    if($strValue != ""){
                                        array_push($json_return, array($strParamName=>$strValue));
                                    }
                                }
                                
                                break;
                                
                            case 'recall':
                                
                                if($parameters["recall"]==1)
                                {
                                    $strParamName = "Recall";
                                    $strValue = $this->getValueFromList($buffer, $strParamName
                                        , ($parameters["interval"] == 1?true:false)
                                        , $decimalprecision, $decimalseparator);
                                    
                                    if($strValue != ""){
                                        array_push($json_return, array($strParamName=>$strValue));
                                    }
                                }
                                
                                break;
                                
                            case 'f1':
                                if($parameters["f1"]==1)
                                {
                                    $strParamName = "F1";
                                    $strValue = $this->getValueFromList($buffer, $strParamName
                                        , ($parameters["interval"] == 1?true:false)
                                        , $decimalprecision, $decimalseparator);
                                    
                                    if($strValue != ""){
                                        array_push($json_return, array($strParamName=>$strValue));
                                    }
                                }
                                
                                break;
                                
                        }                        
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
        
        if(!is_readable($file))
        {

            if($parameters["accuracy"]==1)
            {
                array_push($json_return, array("Accuracy"=>"*"));
            }            
            if($parameters["timer"]==1)
            {
                array_push($json_return, array("Timer"=>"*"));
            }
            if($parameters["memory"]==1)
            {
                array_push($json_return, array("Memory"=>"*"));
            }
            if($parameters["dissimilarity"]==1)
            {
                array_push($json_return, array("Dissimilarity"=>"*"));
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

            return $json_return;
        }
        
        $handle = fopen($file, "r");
        
        if ($handle) {

            $startFind = "";            
            $accuracy_open = false;            
            $strategy = $this->detectStrategy($file);
            
            try{
                
                
                while (($buffer = fgets($handle, 512)) !== false) 
                {
                    
                    if(strpos($buffer, "learning evaluation instances,evaluation time") !== false)
                    {
                        break;
                    }
                    
                    
                    if($strategy == "EvaluateInterleavedTestThenTrain2" ||
                        $strategy == "EvaluatePrequential2"
                        || $strategy == "EvaluatePrequentialUFPE"
                        || $strategy == "Error"){
                            
                            if(strpos($buffer, "Accuracy:")>-1){
                                $startFind = "accuracy";
                                $accuracy_open = true;
                            }
                            else if(strpos($buffer, "Time:")>-1){
                                $startFind = "time";
                            }
                            else if(strpos($buffer, "Memory (B/s):")>-1){
                                $startFind = "memory";
                            }
                            else if(strpos($buffer, "General Mean = ")>-1){
                                $startFind = "dist";
                            }
                            else if(strpos($buffer, "Mean Distance")>-1){
                                $startFind = "resume";
                            }
                            else if(strpos($buffer, "Dissimilarity")>-1){
                                $startFind = "dissimilarity";
                            }
                            else if(strpos($buffer, "MDR:")>-1){
                                $startFind = "mdr";
                            }
                            else if(strpos($buffer, "MTD:")>-1){
                                $startFind = "mtd";
                            }
                            else if(strpos($buffer, "MTFA:")>-1){
                                $startFind = "mtfa";
                            }
                            else if(strpos($buffer, "MTR:")>-1){
                                $startFind = "mtr";
                            }
                            else if(strpos($buffer, "Precision:")>-1){
                                $startFind = "precision";
                            }
                            else if(strpos($buffer, "Recall:")>-1){
                                $startFind = "recall";
                            }
                            else if(strpos($buffer, "MCC:")>-1){
                                $startFind = "mcc";
                            }
                            else if(strpos($buffer, "F1:")>-1){
                                $startFind = "f1";
                            }
                            else if(strpos($buffer, "Dist:")>-1){
                                $startFind = "dist";
                            }  
                            else if(strpos($buffer, "FN	FP	TN	TP")>-1){
                                $startFind = "matrix";
                            } 
                            else{
                                if($strategy == "Error" && $accuracy_open == false)
                                {
                                    $startFind = "Error";
                                }
                            }
                            
                            
                            
                            switch($startFind){
                                
                                case 'accuracy':
                                    
                                    if($parameters["accuracy"]==1){
                                        
                                        if(strpos($buffer, "Confidence Interval =")>-1 || strpos($buffer, "Mean (CI) =")>-1){
                                            
                                            
                                            
                                            //$accuracy = substr($accuracy,strpos($accuracy, "Confidence Interval =")+22);
                                            
                                            $tmp = $buffer;
                                            
                                            if(strpos($buffer, "Confidence Interval =")!==false){
                                                $tmp = substr($tmp,strpos($tmp, "Confidence Interval =")+strlen("Confidence Interval =")+1);
                                            }elseif(strpos($buffer, "Mean (CI) =")!==false){
                                                $tmp = substr($tmp,strpos($tmp, "Mean (CI) =")+strlen("Mean (CI) =")+1);
                                            }
                                            
                                            $accuracy = $tmp;                                                                                        
                                            $accuracy = substr($accuracy,0,strpos($accuracy, ")")+1);                                            
                                            $accuracy_aux = $accuracy;
                                            
                                            $accuracy = substr($accuracy,0,strpos($accuracy, "(")-1);
                                            $accuracy = trim($accuracy);
                                            
                                            
                                            $accuracy = $this->numeric_format_option($accuracy, $decimalprecision, $decimalseparator);
                                            
                                            if($parameters["interval"] == 1){
                                                $accuracy_aux = substr($accuracy_aux,strpos($accuracy_aux, "(")-1);
                                                $accuracy_aux = trim($accuracy_aux);
                                                $accuracy .= " " . $accuracy_aux;
                                            }
                                                         
                                            array_push($json_return, array("Accuracy"=>$accuracy));
                                            
                                        }
                                        
                                        
                                        
                                    }
                                    
                                    break;
                                case 'time':
                                    
                                    if($parameters["timer"]==1){
                                        
                                        //if(strpos($buffer, "Confidence Interval =")>-1){
                                        if(strpos($buffer, "Confidence Interval =")>-1 || strpos($buffer, "Mean (CI) =")>-1){
                                            //$atime = $buffer;
                                            
                                            //$atime = substr($atime,strpos($atime, "Time:")+5);
                                            //$atime = substr($atime,strpos($atime, "Confidence Interval =")+22);
                                            
                                            $tmp = $buffer;
                                            
                                            if(strpos($buffer, "Confidence Interval =")!==false){
                                                $tmp = substr($tmp,strpos($tmp, "Confidence Interval =")+strlen("Confidence Interval =")+1);
                                            }elseif(strpos($buffer, "Mean (CI) =")!==false){
                                                $tmp = substr($tmp,strpos($tmp, "Mean (CI) =")+strlen("Mean (CI) =")+1);
                                            }
                                            
                                            $atime = $tmp;
                                            $atime = substr($atime,0,strpos($atime, ")")+1);
                                            $atime_aux = $atime;
                                            
                                            $atime = substr($atime,0,strpos($atime, "(")-1);
                                            $atime = trim($atime);
                                            
                                            $atime = $this->numeric_format_option($atime, $decimalprecision, $decimalseparator);
                                            
                                            if($parameters["interval"]==1){
                                                $atime_aux = substr($atime_aux,strpos($atime_aux, "(")-1);
                                                $atime_aux = trim($atime_aux);
                                                $atime .= " " . $atime_aux;
                                            }
                                            
                                            
                                            array_push($json_return, array("Timer"=>$atime));
                                            
                                        }
                                    }
                                    
                                    break;
                                case 'memory':
                                    
                                    if($parameters["memory"]==1){
                                        
                                        if(strpos($buffer, "Confidence Interval =")>-1 || strpos($buffer, "Mean (CI) =")>-1){
                                            
                                            $tmp = $buffer;
                                            
                                            if(strpos($buffer, "Confidence Interval =")!==false){
                                                $tmp = substr($tmp,strpos($tmp, "Confidence Interval =")+strlen("Confidence Interval =")+1);
                                            }elseif(strpos($buffer, "Mean (CI) =")!==false){
                                                $tmp = substr($tmp,strpos($tmp, "Mean (CI) =")+strlen("Mean (CI) =")+1);
                                            }
                                            
                                            $amemory = $tmp;                                            
                                            $amemory = substr($amemory,0,strpos($amemory, ")")+1);
                                            $amemory_aux = $amemory;
                                            
                                            $amemory = substr($amemory,0,strpos($amemory, "(")-1);
                                            $amemory = trim($amemory);
                                                                                        
                                            $amemory = $this->numeric_format_option($amemory, $decimalprecision, $decimalseparator);
                                            
                                            if($parameters["interval"]==1){
                                                $amemory_aux = substr($amemory_aux,strpos($amemory_aux, "(")-1);
                                                $amemory_aux = trim($amemory_aux);
                                                $amemory .= " " . $amemory_aux;
                                            }
                                            
                                            array_push($json_return, array("Memory"=>$amemory));
                                            
                                            //fim
                                            
                                            break 2;
                                        }
                                    }
                                    
                                    break;    
                                case 'mdr':
                                
									if($parameters["mdr"]==1){
                                        
                                        if(strpos($buffer, "Confidence Interval =")>-1 || strpos($buffer, "Mean (CI) =")>-1){
                                            
                                            $tmp = $buffer;
                                            
                                            if(strpos($buffer, "Confidence Interval =")!==false){
                                                $tmp = substr($tmp,strpos($tmp, "Confidence Interval =")+strlen("Confidence Interval =")+1);
                                            }elseif(strpos($buffer, "Mean (CI) =")!==false){
                                                $tmp = substr($tmp,strpos($tmp, "Mean (CI) =")+strlen("Mean (CI) =")+1);
                                            }
                                            
                                            $value = $tmp;                                            
                                            $value = substr($value,0,strpos($value, ")")+1);
                                            $value_aux = $value;
                                            
                                            $value = substr($value,0,strpos($value, "(")-1);
                                            $value = trim($value);
                                                                                        
                                            $value = $this->numeric_format_option($value, $decimalprecision, $decimalseparator);
                                            
                                            if($parameters["interval"]==1){
                                                $value_aux = substr($value_aux,strpos($value_aux, "(")-1);
                                                $value_aux = trim($value_aux);
                                                $value .= " " . $value_aux;
                                            }
                                            
                                            array_push($json_return, array("MDR"=>$value));
       
                                            break 2;
                                        }
                                    }
                                    
                                    break; 
                                    
                                case 'mtd':
                                
									if($parameters["mtd"]==1){
                                        
                                        if(strpos($buffer, "Confidence Interval =")>-1 || strpos($buffer, "Mean (CI) =")>-1){
                                            
                                            $tmp = $buffer;
                                            
                                            if(strpos($buffer, "Confidence Interval =")!==false){
                                                $tmp = substr($tmp,strpos($tmp, "Confidence Interval =")+strlen("Confidence Interval =")+1);
                                            }elseif(strpos($buffer, "Mean (CI) =")!==false){
                                                $tmp = substr($tmp,strpos($tmp, "Mean (CI) =")+strlen("Mean (CI) =")+1);
                                            }
                                            
                                            $value = $tmp;                                            
                                            $value = substr($value,0,strpos($value, ")")+1);
                                            $value_aux = $value;
                                            
                                            $value = substr($value,0,strpos($value, "(")-1);
                                            $value = trim($value);
                                                                                        
                                            $value = $this->numeric_format_option($value, $decimalprecision, $decimalseparator);
                                            
                                            if($parameters["interval"]==1){
                                                $value_aux = substr($value_aux,strpos($value_aux, "(")-1);
                                                $value_aux = trim($value_aux);
                                                $value .= " " . $value_aux;
                                            }
                                            
                                            array_push($json_return, array("MTD"=>$value));
       
                                            break 2;
                                        }
                                    }
                                    
                                    break; 
                                    
                                case 'mtfa':
                                
									if($parameters["mtfa"]==1){
                                        
                                        if(strpos($buffer, "Confidence Interval =")>-1 || strpos($buffer, "Mean (CI) =")>-1){
                                            
                                            $tmp = $buffer;
                                            
                                            if(strpos($buffer, "Confidence Interval =")!==false){
                                                $tmp = substr($tmp,strpos($tmp, "Confidence Interval =")+strlen("Confidence Interval =")+1);
                                            }elseif(strpos($buffer, "Mean (CI) =")!==false){
                                                $tmp = substr($tmp,strpos($tmp, "Mean (CI) =")+strlen("Mean (CI) =")+1);
                                            }
                                            
                                            $value = $tmp;                                            
                                            $value = substr($value,0,strpos($value, ")")+1);
                                            $value_aux = $value;
                                            
                                            $value = substr($value,0,strpos($value, "(")-1);
                                            $value = trim($value);
                                                                                        
                                            $value = $this->numeric_format_option($value, $decimalprecision, $decimalseparator);
                                            
                                            if($parameters["interval"]==1){
                                                $value_aux = substr($value_aux,strpos($value_aux, "(")-1);
                                                $value_aux = trim($value_aux);
                                                $value .= " " . $value_aux;
                                            }
                                            
                                            array_push($json_return, array("MTFA"=>$value));
       
                                            break 2;
                                        }
                                    }
                                    
                                    break; 
                                    
                                case 'mtr':
                                    
                                    if($parameters["mtr"]==1){
                                        
                                        if(strpos($buffer, "Confidence Interval =")>-1 || strpos($buffer, "Mean (CI) =")>-1){
                                            
                                            $tmp = $buffer;
                                            
                                            if(strpos($buffer, "Confidence Interval =")!==false){
                                                $tmp = substr($tmp,strpos($tmp, "Confidence Interval =")+strlen("Confidence Interval =")+1);
                                            }elseif(strpos($buffer, "Mean (CI) =")!==false){
                                                $tmp = substr($tmp,strpos($tmp, "Mean (CI) =")+strlen("Mean (CI) =")+1);
                                            }
                                            
                                            $value = $tmp;                                            
                                            $value = substr($value,0,strpos($value, ")")+1);
                                            $value_aux = $value;
                                            
                                            $value = substr($value,0,strpos($value, "(")-1);
                                            $value = trim($value);
                                                                                        
                                            $value = $this->numeric_format_option($value, $decimalprecision, $decimalseparator);
                                            
                                            if($parameters["interval"]==1){
                                                $value_aux = substr($value_aux,strpos($value_aux, "(")-1);
                                                $value_aux = trim($value_aux);
                                                $value .= " " . $value_aux;
                                            }                                            									
											
                                            array_push($json_return, array("MTR"=>$value));
       
                                            break 2;
                                        }
                                    }
                                    
                                    break;                                                                    
                                case 'dissimilarity':
                                    
                                    if($parameters["dissimilarity"]==1){
                                        
                                        //if(strpos($buffer, "Confidence Interval =")>-1){
                                        if(strpos($buffer, "Confidence Interval =")>-1 || strpos($buffer, "Mean (CI) =")>-1){
                                            //$amemory = $buffer;
                                            
                                            //$amemory = substr($amemory,strpos($amemory, "Memory (B/s):")+12);
                                            //$amemory = substr($amemory,strpos($amemory, "Confidence Interval =")+22);
                                            
                                            $tmp = $buffer;
                                            
                                            if(strpos($buffer, "Confidence Interval =")!==false){
                                                $tmp = substr($tmp,strpos($tmp, "Confidence Interval =")+strlen("Confidence Interval =")+1);
                                            }elseif(strpos($buffer, "Mean (CI) =")!==false){
                                                $tmp = substr($tmp,strpos($tmp, "Mean (CI) =")+strlen("Mean (CI) =")+1);
                                            }
                                            
                                            $amemory = $tmp;                                            
                                            $amemory = substr($amemory,0,strpos($amemory, ")")+1);
                                            $amemory_aux = $amemory;
                                            
                                            $amemory = substr($amemory,0,strpos($amemory, "(")-1);
                                            $amemory = trim($amemory);
                                            
//                                             if($parameters["interval"]!=1){
//                                                 $amemory = substr($amemory,0,strpos($amemory, "(")-1);
//                                                 $amemory = trim($amemory);
//                                             }
                                            
                                            $amemory = $this->numeric_format_option($amemory, $decimalprecision, $decimalseparator);
                                            
                                            if($parameters["interval"]==1){
                                                $amemory_aux = substr($amemory_aux,strpos($amemory_aux, "(")-1);
                                                $amemory_aux = trim($amemory_aux);
                                                $amemory .= " " . $amemory_aux;
                                            }
                                            
                                            array_push($json_return, array("Dissimilarity"=>$amemory));
                                            
                                            //fim
                                            
                                            break 2;
                                        }
                                    }
                                    
                                    break;  
                                
                            }
                            
                            
                            if($strategy == "EvaluatePrequentialUFPE")
                            {
                                switch($startFind)
                                {
                                    case 'matrix':
                                        
                                        if($parameters["fn"]==1 || $parameters["fp"]==1
                                        || $parameters["tn"]==1 || $parameters["tp"]==1){
                                            
                                            if(strpos($buffer, "Mean (CI) =")>-1){
                                                
                                                $tmp = $buffer;
                                                
                                                if(strpos($buffer, "Mean (CI) =")!==false){
                                                    $tmp = substr($tmp,strpos($tmp, "Mean (CI) =")+strlen("Mean (CI) =")+1);
                                                }
                                                
                                                $matrix = explode(")", $tmp);
                                                foreach($matrix as $key=>$item){
                                                    $matrix[$key] = $item . ")";
                                                }
                                                
//                                                 $fn = $matrix[0];
//                                                 $fp = $matrix[1];
//                                                 $tn = $matrix[2];
//                                                 $tp = $matrix[3];                   
                                                
                                                foreach($matrix as $key=>$item)
                                                {
                                                    
                                                    $item = substr($item,0,strpos($item, ")")+1);
                                                    $item_aux = $item;
                                                    
                                                    $item = substr($item,0,strpos($item, "(")-1);
                                                    $item = trim($item);
                                                    
                                                    $item = $this->numeric_format_option($item, $decimalprecision, $decimalseparator);
                                                    
                                                    if($parameters["interval"]==1){
                                                        $item_aux = substr($item_aux,strpos($item_aux, "(")-1);
                                                        $item_aux = trim($item_aux);
                                                        $item .= " " . $item_aux;
                                                    }
                                                                                  
                                                    if($key == 0){
                                                        if($parameters["fn"]==1){
                                                            array_push($json_return, array("fn"=>$item));
                                                        }
                                                    }else if($key == 1){
                                                        if($parameters["fp"]==1){
                                                            array_push($json_return, array("fp"=>$item));
                                                        }
                                                    }else if($key == 2){
                                                        if($parameters["tn"]==1){
                                                            array_push($json_return, array("tn"=>$item));
                                                        }
                                                    }else if($key == 3){
                                                        if($parameters["tp"]==1){
                                                            array_push($json_return, array("tp"=>$item));
                                                        }
                                                    }
                                                    
                                                    
                                                }

                                                
                                                break 2;
                                            }
                                        }
                                        
                                        break;
                                        
                                    case 'precision':
                                        
                                        if($parameters["precision"]==1){
                                            
                                            if(strpos($buffer, "Confidence Interval =")>-1 || strpos($buffer, "Mean (CI) =")>-1){
                                                
                                                $tmp = $buffer;
                                                
                                                if(strpos($buffer, "Confidence Interval =")!==false){
                                                    $tmp = substr($tmp,strpos($tmp, "Confidence Interval =")+strlen("Confidence Interval =")+1);
                                                }elseif(strpos($buffer, "Mean (CI) =")!==false){
                                                    $tmp = substr($tmp,strpos($tmp, "Mean (CI) =")+strlen("Mean (CI) =")+1);
                                                }
                                                
                                                $value = $tmp;
                                                $value = substr($value,0,strpos($value, ")")+1);
                                                $value_aux = $value;
                                                
                                                $value = substr($value,0,strpos($value, "(")-1);
                                                $value = trim($value);
                                                
                                                $value = $this->numeric_format_option($value, $decimalprecision, $decimalseparator);
                                                
                                                if($parameters["interval"]==1){
                                                    $value_aux = substr($value_aux,strpos($value_aux, "(")-1);
                                                    $value_aux = trim($value_aux);
                                                    $value .= " " . $value_aux;
                                                }
                                                
                                                array_push($json_return, array("Precision"=>$value));
                                                
                                                break 2;
                                            }
                                        }
                                        
                                        break;
                                    
                                    case 'recall':
                                        
                                        if($parameters["recall"]==1){
                                            
                                            if(strpos($buffer, "Confidence Interval =")>-1 || strpos($buffer, "Mean (CI) =")>-1){
                                                
                                                $tmp = $buffer;
                                                
                                                if(strpos($buffer, "Confidence Interval =")!==false){
                                                    $tmp = substr($tmp,strpos($tmp, "Confidence Interval =")+strlen("Confidence Interval =")+1);
                                                }elseif(strpos($buffer, "Mean (CI) =")!==false){
                                                    $tmp = substr($tmp,strpos($tmp, "Mean (CI) =")+strlen("Mean (CI) =")+1);
                                                }
                                                
                                                $value = $tmp;
                                                $value = substr($value,0,strpos($value, ")")+1);
                                                $value_aux = $value;
                                                
                                                $value = substr($value,0,strpos($value, "(")-1);
                                                $value = trim($value);
                                                
                                                $value = $this->numeric_format_option($value, $decimalprecision, $decimalseparator);
                                                
                                                if($parameters["interval"]==1){
                                                    $value_aux = substr($value_aux,strpos($value_aux, "(")-1);
                                                    $value_aux = trim($value_aux);
                                                    $value .= " " . $value_aux;
                                                }
                                                
                                                array_push($json_return, array("Recall"=>$value));
                                                
                                                break 2;
                                            }
                                        }
                                        
                                        break;
                                        
                                        
                                    case 'mcc':
                                        
                                        if($parameters["mcc"]==1){
                                            
                                            if(strpos($buffer, "Confidence Interval =")>-1 || strpos($buffer, "Mean (CI) =")>-1){
                                                
                                                $tmp = $buffer;
                                                
                                                if(strpos($buffer, "Confidence Interval =")!==false){
                                                    $tmp = substr($tmp,strpos($tmp, "Confidence Interval =")+strlen("Confidence Interval =")+1);
                                                }elseif(strpos($buffer, "Mean (CI) =")!==false){
                                                    $tmp = substr($tmp,strpos($tmp, "Mean (CI) =")+strlen("Mean (CI) =")+1);
                                                }
                                                
                                                $value = $tmp;
                                                $value = substr($value,0,strpos($value, ")")+1);
                                                $value_aux = $value;
                                                
                                                $value = substr($value,0,strpos($value, "(")-1);
                                                $value = trim($value);
                                                
                                                $value = $this->numeric_format_option($value, $decimalprecision, $decimalseparator);
                                                
                                                if($parameters["interval"]==1){
                                                    $value_aux = substr($value_aux,strpos($value_aux, "(")-1);
                                                    $value_aux = trim($value_aux);
                                                    $value .= " " . $value_aux;
                                                }
                                                
                                                array_push($json_return, array("MCC"=>$value));
                                                
                                                break 2;
                                            }
                                        }
                                        
                                        break;
                                        
                                    case 'f1':
                                        
                                        if($parameters["f1"]==1){
                                            
                                            if(strpos($buffer, "Confidence Interval =")>-1 || strpos($buffer, "Mean (CI) =")>-1){
                                                
                                                $tmp = $buffer;
                                                
                                                if(strpos($buffer, "Confidence Interval =")!==false){
                                                    $tmp = substr($tmp,strpos($tmp, "Confidence Interval =")+strlen("Confidence Interval =")+1);
                                                }elseif(strpos($buffer, "Mean (CI) =")!==false){
                                                    $tmp = substr($tmp,strpos($tmp, "Mean (CI) =")+strlen("Mean (CI) =")+1);
                                                }
                                                
                                                $value = $tmp;
                                                $value = substr($value,0,strpos($value, ")")+1);
                                                $value_aux = $value;
                                                
                                                $value = substr($value,0,strpos($value, "(")-1);
                                                $value = trim($value);
                                                
                                                $value = $this->numeric_format_option($value, $decimalprecision, $decimalseparator);
                                                
                                                if($parameters["interval"]==1){
                                                    $value_aux = substr($value_aux,strpos($value_aux, "(")-1);
                                                    $value_aux = trim($value_aux);
                                                    $value .= " " . $value_aux;
                                                }
                                                
                                                array_push($json_return, array("F1"=>$value));
                                                
                                                break 2;
                                            }
                                        }
                                        
                                        break;
                                        
                                    case 'dist':
                                        
                                        if($parameters["dist"]==1){

                                            $tmp = $buffer;
                                            
                                            if(strpos($buffer, "=")!==false){
                                                $tmp = substr($tmp,strpos($tmp, "=")+2);
                                            }
                                            $value = trim($tmp);
                                            array_push($json_return, array("Dist"=>$value));
                                            
                                            break 2;

                                        }
                                        
                                        break;
                                }
                                // 
                            }elseif($strategy == "EvaluatePrequential2"){
                                
                                switch($startFind)
                                {
                                    case 'resume':
                                        
                                        if($parameters["dist"] == 1
                                        || $parameters["fn"] == 1
                                        || $parameters["fp"] == 1
                                        || $parameters["tn"] == 1
                                        || $parameters["tp"] == 1
                                        || $parameters["precision"] == 1
                                        || $parameters["recall"] == 1
                                        || $parameters["mcc"] == 1
                                        || $parameters["f1"] == 1
                                        || $parameters["resume"] == 1
                                        )
                                        {
                                            
                                            if(isset($start_filter)){
                                                
                                                if($start_filter == true){
                                                    
                                                    if(strpos($buffer, "(")>-1){
                                                        
                                                        if($parameters["interval"]!=1){
                                                            $data1 = substr($buffer, 0, strpos($buffer, "("));
                                                            $data2 = trim(substr($buffer, strpos($buffer, ")")+1));
                                                            
                                                            $buffer = $data1.$data2;
                                                        }
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
                                                    //var_dump($buffer);
                                                    
                                                    //exit("-fim-");
                                                    
                                                    $buffer = str_replace("\n","", $buffer);
                                                    $buffer = str_replace(" ","", $buffer);
                                                    $buffer = str_replace("\r\n","", $buffer);
                                                    
                                                    $itens_list = array();
                                                    
                                                    if($parameters["dist"] == 1
                                                        || $parameters["fn"] == 1
                                                        || $parameters["fp"] == 1
                                                        || $parameters["tn"] == 1
                                                        || $parameters["tp"] == 1
                                                        || $parameters["precision"] == 1
                                                        || $parameters["recall"] == 1
                                                        || $parameters["mcc"] == 1
                                                        || $parameters["f1"] == 1
                                                        //|| $parameters["mdr"] == 1
                                                        //|| $parameters["mtfa"] == 1
                                                        //|| $parameters["mtd"] == 1
                                                        //|| $parameters["mtr"] == 1
                                                        )
                                                    {
                                                        
                                                        if(strpos($buffer, "\t") !== false)
                                                        {
                                                            $itens_list = explode("\t", $buffer);
                                                            
                                                            //0 = dist
                                                            //7 = mcc
                                                            //8 = f1
                                                            
                                                        }
                                                    }
                                                    
                                                    
                                                    if($parameters["dist"] == 1)
                                                    {
                                                        $value = $itens_list[0];
                                                        $value = $this->numeric_format_option($value, $decimalprecision, $decimalseparator);
                                                        array_push($json_return, array("dist"=>$value));
                                                    }
                                                    
                                                    if($parameters["fn"] == 1)
                                                    {
                                                        $value = $itens_list[1];
                                                        $value = $this->numeric_format_option($value, $decimalprecision, $decimalseparator);
                                                        array_push($json_return, array("fn"=>$value));
                                                    }
                                                    
                                                    if($parameters["fp"] == 1)
                                                    {
                                                        $value = $itens_list[2];
                                                        $value = $this->numeric_format_option($value, $decimalprecision, $decimalseparator);
                                                        array_push($json_return, array("tp"=>$value));
                                                    }
                                                    
                                                    if($parameters["tn"] == 1)
                                                    {
                                                        $value = $itens_list[3];
                                                        $value = $this->numeric_format_option($value, $decimalprecision, $decimalseparator);
                                                        array_push($json_return, array("tn"=>$value));
                                                    }
                                                    
                                                    if($parameters["tp"] == 1)
                                                    {
                                                        $value = $itens_list[4];
                                                        $value = $this->numeric_format_option($value, $decimalprecision, $decimalseparator);
                                                        array_push($json_return, array("tp"=>$value));
                                                    }
                                                    
                                                    if($parameters["precision"] == 1)
                                                    {
                                                        $value = $itens_list[5];
                                                        $value = $this->numeric_format_option($value, $decimalprecision, $decimalseparator);
                                                        array_push($json_return, array("precision"=>$value));
                                                    }
                                                    
                                                    if($parameters["recall"] == 1)
                                                    {
                                                        $value = $itens_list[6];
                                                        $value = $this->numeric_format_option($value, $decimalprecision, $decimalseparator);
                                                        array_push($json_return, array("recall"=>$value));
                                                    }
                                                    
                                                    if($parameters["mcc"] == 1)
                                                    {
                                                        $value = $itens_list[7];//var_dump($value);
                                                        $value = $this->numeric_format_option($value, $decimalprecision, $decimalseparator);
                                                        array_push($json_return, array("mcc"=>$value));//exit();
                                                    }
                                                    
                                                    if($parameters["f1"] == 1)
                                                    {
                                                        $value = $itens_list[8];
                                                        $value = $this->numeric_format_option($value, $decimalprecision, $decimalseparator);
                                                        array_push($json_return, array("f1"=>$value));
                                                    }
                                                    
                                                    if($parameters["resume"] == 1)
                                                    {
                                                        array_push($json_return, array("resume"=>$buffer));
                                                    }
                                                    
                                                    $start_filter = false;
                                                }
                                                
                                            }else{
                                                
                                                $start_filter = true;
                                            }
                                            
                                        }
                                        
                                        break;
                                    }
                                
                            }
                            
                            
                    
                    
                }
                else if($strategy == "GeneticAlgorithm"){
                    
                    
                    if($parameters["final_generation"]==1){
                        
                        //var_dump($parameters);exit("dd");
                        try{
                            $fp = $handle;
                            $num=23;
                            //navega no arquivo p/ o final
                            $line_count = 0; $line = ''; $pos = -1; $lines = array(); $c = '';
                            
                            while($line_count < $num) {
                                $line = $c . $line;
                                fseek($fp, $pos--, SEEK_END);
                                $c = fgetc($fp);
                                if($c == "\n") { $line_count++; $lines[] = $line; $line = ''; $c = ''; }
                            }
                            // / return $lines;
                            
                        }catch(Exception $e){
                            print $e->getMessage();
                        }
                        //var_dump($lines);
                        
                        for($i=0; $i<count($lines);$i++){
                            
                            //if(trim($lines[$i])=="")
                            //  break;
                            
                            print $lines[count($lines)-$i-1]."<br>";
                        }
                        
                        exit();
                        
                    }
                    
                    if($parameters["accuracy"]==1){
                        
                        if(strpos($buffer, "[Parameter1:")>-1){
                            
                            try{
                                $fp = $handle;
                                $num=2;
                                //navega no arquivo p/ o final
                                $line_count = 0; $line = ''; $pos = -1; $lines = array(); $c = '';
                                
                                while($line_count < $num) {
                                    $line = $c . $line;
                                    fseek($fp, $pos--, SEEK_END);
                                    $c = fgetc($fp);
                                    if($c == "\n") { $line_count++; $lines[] = $line; $line = ''; $c = ''; }
                                }
                                // / return $lines;
                                
                            }catch(Exception $e){
                                print $e->getMessage();
                            }
                            
                            for($i=0; $i<count($lines);$i++){
                                
                                if(trim($lines[$i])=="")
                                    break;
                                    
                                    $buffer = $lines[$i];
                            }
                            
                            
                            //$buffer = $t;
                            //var_dump($lines);
                            //exit("==");
                            //$buffer = "[Parameter1: 9, Parameter2: 1.1304253664938206, Parameter3: 2.135766846200818, Accuracy: 71.61]";
                            
                            $accuracy = explode(",", $buffer);
                            
                            for($i=0; $i <count($accuracy); $i++){
                                
                                $accuracy[$i] = trim($accuracy[$i]);
                                $accuracy[$i] = substr($accuracy[$i], strpos($accuracy[$i], " ")+1);
                                
                                if(strpos($accuracy[$i], "]")>-1){
                                    $accuracy[$i] = substr($accuracy[$i], 0,strpos($accuracy[$i], "]"));
                                }
                                
                            }
                            
                            array_push($json_return,
                                array("Parameter1"=>$accuracy[0],
                                    "Parameter2"=>$accuracy[1],
                                    "Parameter3"=>$accuracy[2],
                                    "Accuracy"=>$accuracy[3]));
                                
                                break;//sair do arquivo
                        }
                        
                        
                        
                    }
                    
                }else{
                    //error
                    
                    array_push($json_return, array("Accuracy"=>"x"));
                    break;
                }
                
                
                
                
                // $output .= $buffer;
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
                
                if(strpos($buffer, "EvaluateInterleavedTestThenTrain2")>-1){
                    
                    $result = "EvaluateInterleavedTestThenTrain2";
                    break;
                    
                }else if(strpos($buffer, "EvaluateInterleavedTestThenTrain3")>-1){
                    
                    $result = "EvaluateInterleavedTestThenTrain3";
                    break;
                    
                }else if(strpos($buffer, "Generation 1")>-1){
                    
                    $result = "GeneticAlgorithm";
                    break;
                }else if(strpos($buffer, "EvaluatePrequential2")>-1){
                    
                    $result = "EvaluatePrequential2";
                    break;
                }else if(strpos($buffer, "EvaluatePrequential3")>-1){
                    
                    $result = "EvaluatePrequential3";
                    break;
                }else if(strpos($buffer, "EvaluatePrequentialUFPE")>-1){
                    
                    $result = "EvaluatePrequentialUFPE";
                    break;
                }else if(strpos($buffer, "EvaluatePrequential")>-1){
                    
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
