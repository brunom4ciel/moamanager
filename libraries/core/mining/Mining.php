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
        
    function convertCSV($arrayElements, $parameters, $breakline=1){
        
        $iteration=0;
        $output="";
        $z=1;
        
        $decimalformat = $parameters["decimalformat"];
        
        
        foreach($arrayElements as $key=>$element){
            
            
            if($iteration==0){
                
                /*if($parameters["column"]==1){
                 foreach($element as $key2=>$item){
                 
                 foreach($item as $key3=>$item3){
                 $output .= (empty($output)? $key3:"\t".$key3);
                 }
                 }
                 
                 $output .= "\n";
                 }*/
            }
            
            $output2="";
            
            foreach($element as $key2=>$item){
                
                foreach($item as $key3=>$item3){
                    
                    if($decimalformat != ".")
                        $item3 = str_replace(".", $decimalformat, $item3);
                        
                        if($z == 1)
                            $output2 .= $item3;
                            else
                                $output2 .= "\t".$item3;
                                
                                if($z==$breakline){
                                    
                                    $z=1;
                                    $output2 .= "\n";
                                    
                                }else{
                                    
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
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    function extract_averages_in_file($file, $parameters){
        
        
        $json_return = array();
        
        $handle = fopen($file, "r");
        
        if ($handle) {
            
            $output="";
            
            $startFind = "";
            
            
            $strategy = $this->detectStrategy($file);
            
            
            while (($buffer = fgets($handle, 512)) !== false) {
                
                //	if(strpos($buffer, "learning evaluation instances")){
                ///		break;
                //}
                
                
                if($strategy == "EvaluateInterleavedTestThenTrain2" ||
                    $strategy == "EvaluatePrequential2"){
                        
                        if(strpos($buffer, "Accuracy:")>-1){
                            $startFind = "accuracy";
                        }else if(strpos($buffer, "Time:")>-1){
                            $startFind = "time";
                        }else if(strpos($buffer, "Memory (B/s):")>-1){
                            $startFind = "memory";
                        }else{
                            
                        }
                        
                        switch($startFind){
                            
                            case 'accuracy':
                                
                                if($parameters["accuracy"]==1){
                                    
                                    if(strpos($buffer, "Confidence Interval =")>-1 || strpos($buffer, "Mean (CI) =")>-1){
                                        //exit("iran");
                                        
                                        
                                        break;
                                    }else{
                                        
                                        if(strpos($buffer, "Accuracy")>-1)
                                            break;
                                            
                                            $accuracy = $buffer;
                                            $accuracy = trim($accuracy);
                                            
                                            if($accuracy != "")
                                                array_push($json_return, array("Accuracy"=>$accuracy));
                                                
                                    }
                                    
                                    
                                    
                                }
                                
                                break;
                            case 'time':
                                
                                if($parameters["timer"]==1){
                                    
                                    //if(strpos($buffer, "Confidence Interval =")>-1){
                                    if(strpos($buffer, "Confidence Interval =")>-1 || strpos($buffer, "Mean (CI) =")>-1){
                                        
                                        $atime = $buffer;
                                        
                                        //$atime = substr($atime,strpos($atime, "Time:")+5);
                                        
                                        if(strpos($buffer, "Confidence Interval =")===true){
                                            $atime = substr($atime,strpos($atime, "Confidence Interval =")+strlen("Confidence Interval =")+1);
                                        }elseif(strpos($buffer, "Mean (CI) =")===true){
                                            $atime = substr($atime,strpos($atime, "Mean (CI) =")+strlen("Mean (CI) =")+1);
                                        }
                                        
                                        //$atime = substr($atime,strpos($atime, "Confidence Interval =")+22);
                                        $atime = substr($atime,0,strpos($atime, ")")+1);
                                        
                                        if($parameters["interval"]!=1){
                                            $atime = substr($atime,0,strpos($atime, "(")-1);
                                            $atime = trim($atime);
                                        }
                                        
                                        array_push($json_return, array("Timer"=>$atime));
                                        
                                    }
                                }
                                
                                break;
                            case 'memory':
                                
                                if($parameters["memory"]==1){
                                    
                                    if(strpos($buffer, "Confidence Interval =")>-1){
                                        
                                        $amemory = $buffer;
                                        
                                        //$amemory = substr($amemory,strpos($amemory, "Memory (B/s):")+12);
                                        //$amemory = substr($amemory,strpos($amemory, "Confidence Interval =")+22);
                                        
                                        if(strpos($buffer, "Confidence Interval =")===true){
                                            $amemory = substr($amemory,strpos($amemory, "Confidence Interval =")+strlen("Confidence Interval =")+1);
                                        }elseif(strpos($buffer, "Mean (CI) =")===true){
                                            $amemory = substr($amemory,strpos($amemory, "Mean (CI) =")+strlen("Mean (CI) =")+1);
                                        }
                                        
                                        
                                        $amemory = substr($amemory,0,strpos($amemory, ")")+1);
                                        
                                        if($parameters["interval"]!=1){
                                            $amemory = substr($amemory,0,strpos($amemory, "(")-1);
                                            $amemory = trim($amemory);
                                        }
                                        
                                        array_push($json_return, array("Memory"=>$amemory));
                                        
                                        //fim
                                        
                                        break 2;
                                    }
                                }
                                
                                break;
                        }
                        
                }else if($strategy == "EvaluateInterleavedTestThenTrain3"){
                    
                    if(strpos($buffer, "Accuracy:")>-1){
                        $startFind = "accuracy";
                    }else if(strpos($buffer, "Time:")>-1){
                        $startFind = "time";
                    }else if(strpos($buffer, "Memory (B/s):")>-1){
                        $startFind = "memory";
                    }else{
                        
                    }
                    
                    
                    switch($startFind){
                        
                        case 'accuracy':
                            
                            if($parameters["accuracy"]==1){
                                
                                if( !is_numeric(trim($buffer)) ){
                                    //if(strpos($buffer, "Confidence Interval =")>-1){
                                    //exit("iran");
                                    //exit("bruno");
                                    
                                    break;
                                }else{
                                    
                                    if(strpos($buffer, "Accuracy")>-1)
                                        break;
                                        
                                        $accuracy = $buffer;
                                        $accuracy = trim($accuracy);
                                        
                                        if($accuracy != "")
                                            array_push($json_return, array("Accuracy"=>$accuracy));
                                            
                                }
                                
                                
                                
                            }
                            
                            break;
                        case 'time':
                            
                            if($parameters["timer"]==1){
                                
                                
                                if( !is_numeric(trim($buffer)) ){
                                    //if(strpos($buffer, "Confidence Interval =")>-1){
                                    //exit("iran");
                                    //exit("bruno");
                                    
                                    break;
                                }else{
                                    
                                    if(strpos($buffer, "Timer")>-1)
                                        break;
                                        
                                        $accuracy = $buffer;
                                        $accuracy = trim($accuracy);
                                        
                                        if($accuracy != "")
                                            array_push($json_return, array("Timer"=>$accuracy));
                                            
                                }
                                
                                
                            }
                            
                            break;
                        case 'memory':
                            
                            if($parameters["memory"]==1){
                                
                                if( !is_numeric(trim($buffer)) ){
                                    //if(strpos($buffer, "Confidence Interval =")>-1){
                                    //exit("iran");
                                    //exit("bruno");
                                    
                                    break;
                                }else{
                                    
                                    if(strpos($buffer, "Memory")>-1)
                                        break;
                                        
                                        $accuracy = $buffer;
                                        $accuracy = trim($accuracy);
                                        
                                        if($accuracy != "")
                                            array_push($json_return, array("Memory"=>$accuracy));
                                            
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
    
    
    //Extrai a media da precisão e variação da "confiança"
    
    function miningFile($file, $parameters){
        
        
        $json_return = array();
        
        $handle = fopen($file, "r");
        
        if ($handle) {
            
            $output="";
            
            $startFind = "";
            
            
            $strategy = $this->detectStrategy($file);
            
            try{
                
                
                while (($buffer = fgets($handle, 512)) !== false) {
                    
                    if(strpos($buffer, "learning evaluation instances,evaluation time") !== false){
                        break;
                    }
                    
                    
                    
                    
                    if($strategy == "EvaluateInterleavedTestThenTrain2" ||
                        $strategy == "EvaluatePrequential2"
                        || $strategy == "EvaluatePrequential3"
                        || $strategy == "EvaluatePrequential4"){
                            
                            if(strpos($buffer, "Accuracy:")>-1){
                                $startFind = "accuracy";
                            }else if(strpos($buffer, "Time:")>-1){
                                $startFind = "time";
                            }else if(strpos($buffer, "Memory (B/s):")>-1){
                                $startFind = "memory";
                            }else if(strpos($buffer, "Mean Distance")>-1){
                                $startFind = "resume";
                            }else{
                                
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
                                            
                                            if($parameters["interval"]!=1){
                                                $accuracy = substr($accuracy,0,strpos($accuracy, "(")-1);
                                                $accuracy = trim($accuracy);
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
                                            
                                            if($parameters["interval"]!=1){
                                                $atime = substr($atime,0,strpos($atime, "(")-1);
                                                $atime = trim($atime);
                                            }
                                            
                                            array_push($json_return, array("Timer"=>$atime));
                                            
                                        }
                                    }
                                    
                                    break;
                                case 'memory':
                                    
                                    if($parameters["memory"]==1){
                                        
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
                                            
                                            if($parameters["interval"]!=1){
                                                $amemory = substr($amemory,0,strpos($amemory, "(")-1);
                                                $amemory = trim($amemory);
                                            }
                                            
                                            array_push($json_return, array("Memory"=>$amemory));
                                            
                                            //fim
                                            
                                            break 2;
                                        }
                                    }
                                    
                                    break;
                                case 'fp':
                                    
                                    if($parameters["fp"]==1){
                                        
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
                                            
                                            if($parameters["interval"]!=1){
                                                $amemory = substr($amemory,0,strpos($amemory, "(")-1);
                                                $amemory = trim($amemory);
                                            }
                                            
                                            array_push($json_return, array("fp"=>$amemory));
                                            
                                            //fim
                                            
                                            break 2;
                                        }
                                    }
                                    
                                    break;
                                    
                                case 'fn':
                                    
                                    if($parameters["fn"]==1){
                                        
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
                                            
                                            if($parameters["interval"]!=1){
                                                $amemory = substr($amemory,0,strpos($amemory, "(")-1);
                                                $amemory = trim($amemory);
                                            }
                                            
                                            array_push($json_return, array("fn"=>$amemory));
                                            
                                            //fim
                                            
                                            break 2;
                                        }
                                    }
                                    
                                    break;
                                    
                                case 'resume':
                                    
                                    if($parameters["resume"]==1){
                                        
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
                                                
                                                array_push($json_return, array("resume"=>$buffer));
                                                
                                                $start_filter = false;
                                            }
                                            
                                        }else{
                                            
                                            $start_filter = true;
                                        }
                                        
                                    }
                                    
                                    
                                    //if(strpos($buffer, "Mean Distance")>-1){
                                    
                                    /*	$amemory = $buffer;
                                    
                                    echo $buffer;
                                    
                                    exit("fim--");
                                    
                                    //$amemory = substr($amemory,strpos($amemory, "Memory (B/s):")+12);
                                    $amemory = substr($amemory,strpos($amemory, "Confidence Interval =")+22);
                                    $amemory = substr($amemory,0,strpos($amemory, ")")+1);
                                    
                                    if($parameters["interval"]!=1){
                                    $amemory = substr($amemory,0,strpos($amemory, "(")-1);
                                    $amemory = trim($amemory);
                                    }
                                    
                                    array_push($json_return, array("fn"=>$amemory));
                                    */
                                    //fim
                                    
                                    //break 2;
                                    //}
                                    //}
                                    
                                    break;
                    }
                    
                    
                }else if($strategy == "EvaluateInterleavedTestThenTrain3"){
                    
                    
                    
                    if(strpos($buffer, "Accuracy:")>-1){
                        $startFind = "accuracy";
                    }else if(strpos($buffer, "Time:")>-1){
                        $startFind = "time";
                    }else if(strpos($buffer, "Memory (B/s):")>-1){
                        $startFind = "memory";
                    }else{
                        
                    }
                    
                    switch($startFind){
                        
                        case 'accuracy':
                            
                            if($parameters["accuracy"]==1){
                                
                                if(strpos($buffer, "IC(")>-1){
                                    
                                    
                                    
                                    $accuracy = $buffer;
                                    
                                    $accuracy = substr($accuracy,strpos($accuracy, "IC(")+3);
                                    $accuracy = substr($accuracy,0,strpos($accuracy, ", "));
                                    
                                    if($parameters["interval"]==1){
                                        
                                        $accuracy2 = $accuracy;
                                        
                                        $accuracy = substr($buffer,strpos($buffer, "+-"));
                                        //$accuracy = trim($accuracy);
                                        
                                        $accuracy = $accuracy2."(".trim($accuracy).")";
                                    }
                                    
                                    array_push($json_return, array("Accuracy"=>$accuracy));
                                    
                                }
                                
                                
                                
                            }
                            
                            break;
                        case 'time':
                            
                            if($parameters["timer"]==1){
                                
                                
                                if(strpos($buffer, "IC(")>-1){
                                    
                                    $accuracy = $buffer;
                                    
                                    $accuracy = substr($accuracy,strpos($accuracy, "IC(")+3);
                                    $accuracy = substr($accuracy,0,strpos($accuracy, ", "));
                                    
                                    if($parameters["interval"]==1){
                                        
                                        $accuracy2 = $accuracy;
                                        
                                        $accuracy = substr($buffer,strpos($buffer, "+-"));
                                        //$accuracy = trim($accuracy);
                                        
                                        $accuracy = $accuracy2."(".trim($accuracy).")";
                                    }
                                    
                                    array_push($json_return, array("Timer"=>$accuracy));
                                    
                                }
                                
                                
                            }
                            
                            break;
                        case 'memory':
                            
                            if($parameters["memory"]==1){
                                
                                if(strpos($buffer, "IC(")>-1){
                                    
                                    $accuracy = $buffer;
                                    
                                    $accuracy = substr($accuracy,strpos($accuracy, "IC(")+3);
                                    $accuracy = substr($accuracy,0,strpos($accuracy, ", "));
                                    
                                    if($parameters["interval"]==1){
                                        
                                        $accuracy2 = $accuracy;
                                        
                                        $accuracy = substr($buffer,strpos($buffer, "+-"));
                                        //$accuracy = trim($accuracy);
                                        
                                        $accuracy = $accuracy2."(".trim($accuracy).")";
                                    }
                                    
                                    array_push($json_return, array("Memory"=>$accuracy));
                                    
                                    break 2;
                                    
                                }
                                
                            }
                            
                            break;
                    }
                    
                    
                }else if($strategy == "GeneticAlgorithm"){
                    
                    
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
        
        
        /*
         $accuracy="";
         $atime="";
         $amemory="";
         //learning evaluation instances,evaluation time
         
         //$output = substr($output,0,strpos($output, "learning evaluation instances"));
         
         if($parameters["accuracy"]==1){
         
         $accuracy = $output;
         
         $accuracy = substr($accuracy,strpos($accuracy, "Confidence Interval =")+22);
         $accuracy = substr($accuracy,0,strpos($accuracy, ")")+1);
                                    
         if($parameters["interval"]!=1){
         $accuracy = substr($accuracy,0,strpos($accuracy, "(")-1);
         $accuracy = trim($accuracy);
         }
         
         array_push($json_return, array("Accuracy"=>$accuracy));
         }
         
         if($parameters["timer"]==1){
         
         $atime = $output;
         
         $atime = substr($atime,strpos($atime, "Time:")+5);
         $atime = substr($atime,strpos($atime, "Confidence Interval =")+22);
         $atime = substr($atime,0,strpos($atime, ")")+1);
         
         if($parameters["interval"]!=1){
         $atime = substr($atime,0,strpos($atime, "(")-1);
         $atime = trim($atime);
         }
         
         array_push($json_return, array("Timer"=>$atime));
         }
         
         if($parameters["memory"]==1){
         
         $amemory = $output;
         
         $amemory = substr($amemory,strpos($amemory, "Memory (B/s):")+12);
         $amemory = substr($amemory,strpos($amemory, "Confidence Interval =")+22);
         $amemory = substr($amemory,0,strpos($amemory, ")")+1);
         
         if($parameters["interval"]!=1){
         $amemory = substr($amemory,0,strpos($amemory, "(")-1);
         $amemory = trim($amemory);
         }
         
         array_push($json_return, array("Memory"=>$amemory));
         }
         */
        
        
        /*
         array_push($json_return, array("Accuracy"=>$accuracy,
         "Timer"=>$atime,
         "Memory"=>$amemory));*/
        
        
        
    }
    
    //array_push($json_return, array("Accuracy"=>"x"));
    
    if(count($json_return)<1){
        array_push($json_return, array("Accuracy"=>"x"));
    }
    
    
    return $json_return;
}

























function extract_averages_in_file2($file){
    
    exit("f2");
    $json_return = array();
    
    $handle = fopen($file, "r");
    
    if ($handle) {
        
        $output="";
        
        $startFind = "";
        
        
        $strategy = $this->detectStrategy($file);
        
        
        while (($buffer = fgets($handle, 512)) !== false) {
            
            if(strpos($buffer, "learning evaluation instances,evaluation time") !== false){
                break;
            }
            
            
            if($strategy == "EvaluateInterleavedTestThenTrain2"){
                
                if(strpos($buffer, "Accuracy:")>-1){
                    $startFind = "accuracy";
                }else if(strpos($buffer, "Time:")>-1){
                    $startFind = "time";
                }else if(strpos($buffer, "Memory (B/s):")>-1){
                    $startFind = "memory";
                }else{
                    
                }
                
                switch($startFind){
                    
                    case 'accuracy':
                        
                        if($parameters["accuracy"]==1){
                            
                            if(strpos($buffer, "Confidence Interval =")>-1){
                                //exit("iran");
                                
                                
                                break;
                            }else{
                                
                                if(strpos($buffer, "Accuracy")>-1)
                                    break;
                                    
                                    $accuracy = $buffer;
                                    $accuracy = trim($accuracy);
                                    
                                    if($accuracy != "")
                                        $json_return["Accuracy"][] = $accuracy;
                                        
                            }
                            
                            
                            
                        }
                        
                        break;
                    case 'time':
                        
                        if($parameters["timer"]==1){
                            
                            if(strpos($buffer, "Confidence Interval =")>-1){
                                
                                $atime = $buffer;
                                
                                //$atime = substr($atime,strpos($atime, "Time:")+5);
                                $atime = substr($atime,strpos($atime, "Confidence Interval =")+22);
                                $atime = substr($atime,0,strpos($atime, ")")+1);
                                
                                if($parameters["interval"]!=1){
                                    $atime = substr($atime,0,strpos($atime, "(")-1);
                                    $atime = trim($atime);
                                }
                                
                                array_push($json_return, array("Timer"=>$atime));
                                
                            }
                        }
                        
                        break;
                    case 'memory':
                        
                        if($parameters["memory"]==1){
                            
                            if(strpos($buffer, "Confidence Interval =")>-1){
                                
                                $amemory = $buffer;
                                
                                //$amemory = substr($amemory,strpos($amemory, "Memory (B/s):")+12);
                                $amemory = substr($amemory,strpos($amemory, "Confidence Interval =")+22);
                                $amemory = substr($amemory,0,strpos($amemory, ")")+1);
                                
                                if($parameters["interval"]!=1){
                                    $amemory = substr($amemory,0,strpos($amemory, "(")-1);
                                    $amemory = trim($amemory);
                                }
                                
                                array_push($json_return, array("Memory"=>$amemory));
                                
                                //fim
                                
                                break 2;
                            }
                        }
                        
                        break;
                }
                
            }else if($strategy == "EvaluateInterleavedTestThenTrain3"){
                
                if(strpos($buffer, "Accuracy:")>-1){
                    $startFind = "accuracy";
                }else if(strpos($buffer, "Time:")>-1){
                    $startFind = "time";
                }else if(strpos($buffer, "Memory (B/s):")>-1){
                    $startFind = "memory";
                }else{
                    
                }
                
                
                switch($startFind){
                    
                    case 'accuracy':
                        
                        //if($parameters["accuracy"]==1){
                        
                        if( !is_numeric(trim($buffer)) ){
                            //if(strpos($buffer, "Confidence Interval =")>-1){
                            //exit("iran");
                            //exit("bruno");
                            
                            break;
                        }else{
                            
                            if(strpos($buffer, "Accuracy")>-1)
                                break;
                                
                                $accuracy = $buffer;
                                $accuracy = trim($accuracy);
                                
                                if($accuracy != "")
                                    $json_return["accuracy"][] = $accuracy;
                                    
                        }
                        
                        
                        
                        //}
                        
                        break;
                    case 'time':
                        
                        //if($parameters["timer"]==1){
                        
                        
                        if( !is_numeric(trim($buffer)) ){
                            //if(strpos($buffer, "Confidence Interval =")>-1){
                            //exit("iran");
                            //exit("bruno");
                            
                            break;
                        }else{
                            
                            if(strpos($buffer, "Timer")>-1)
                                break;
                                
                                $accuracy = $buffer;
                                $accuracy = trim($accuracy);
                                
                                if($accuracy != "")
                                    $json_return["timer"][] = $accuracy;
                                    
                        }
                        
                        
                        //}
                        
                        break;
                    case 'memory':
                        
                        //if($parameters["memory"]==1){
                        
                        if( !is_numeric(trim($buffer)) ){
                            //if(strpos($buffer, "Confidence Interval =")>-1){
                            //exit("iran");
                            //exit("bruno");
                            
                            break;
                        }else{
                            
                            if(strpos($buffer, "Memory")>-1)
                                break;
                                
                                $accuracy = $buffer;
                                $accuracy = trim($accuracy);
                                
                                if($accuracy != "")
                                    $json_return["memory"][] = $accuracy;
                                    
                        }
                        
                        
                        //}
                        
                        break;
                }
                
            }else if($strategy == "EvaluatePrequential2"
                || $strategy == "EvaluatePrequential3" || $strategy == "EvaluatePrequential4"){
                    
                    //     					if(strpos($buffer, "Accuracy:")>-1){
                    //     						$startFind = "accuracy";
                    //     					}else if(strpos($buffer, "Time:")>-1){
                    //     						$startFind = "time";
                    //     					}else if(strpos($buffer, "Memory (B/s):")>-1){
                    //     						$startFind = "memory";
                    //     					}else{
                    
                    //     					}
                    
                    
                    if(strpos($buffer, "Accuracy:") === false){
                        
                    }else
                    {
                        $startFind = "accuracy";
                    }
                    
                    
                    if(strpos($buffer, "Time:") === false)
                    {
                        
                    }else
                    {
                        $startFind = "time";
                    }
                    
                    
                    if(strpos($buffer, "Memory (B/s):") ===  false){
                        
                    }
                    else
                    {
                        $startFind = "memory";
                    }
                    
                    
                    switch($startFind){
                        
                        case 'accuracy':
                            
                            //if($parameters["accuracy"]==1){
                            
                            if( !is_numeric(trim($buffer)) ){
                                //if(strpos($buffer, "Confidence Interval =")>-1){
                                //exit("iran");
                                //exit("bruno");
                                //
                                
                                if(strpos($buffer, "Confidence Interval =") === false){
                                    
                                }else{
                                    $startFind = "";
                                }
                                
                                break;
                            }else{
                                
                                if(strpos($buffer, "Accuracy")>-1)
                                {
                                    break;
                                }
                                
                                $accuracy = $buffer;
                                $accuracy = trim($accuracy);
                                
                                if($accuracy != "")
                                    $json_return["accuracy"][] = $accuracy;
                                    
                            }
                            
                            
                            
                            //}
                            
                            break;
                        case 'time':
                            
                            //if($parameters["timer"]==1){
                            
                            
                            if( !is_numeric(trim($buffer)) ){
                                //if(strpos($buffer, "Confidence Interval =")>-1){
                                //exit("iran");
                                //exit("bruno");
                                
                                break;
                            }else{
                                
                                if(strpos($buffer, "Timer")>-1)
                                    break;
                                    
                                    $accuracy = $buffer;
                                    $accuracy = trim($accuracy);
                                    
                                    if($accuracy != "")
                                        $json_return["timer"][] = $accuracy;
                                        
                            }
                            
                            
                            //}
                            
                            break;
                        case 'memory':
                            
                            //if($parameters["memory"]==1){
                            
                            if( !is_numeric(trim($buffer)) ){
                                //if(strpos($buffer, "Confidence Interval =")>-1){
                                //exit("iran");
                                //exit("bruno");
                                
                                break;
                            }else{
                                
                                if(strpos($buffer, "Memory")>-1)
                                    break;
                                    
                                    $accuracy = $buffer;
                                    $accuracy = trim($accuracy);
                                    
                                    if($accuracy != "")
                                        $json_return["memory"][] = $accuracy;
                                        
                            }
                            
                            
                            //}
                            
                            break;
                    }
                    
            }
            
            
        }
        
        
    }
    
    fclose($handle);
    
    
    
    return $json_return;
}





function extractAllAverages($file){
    
    exit("sim");
    $json_return = array();
    
    $handle = fopen($file, "r");
    
    if ($handle) {
        
        $output="";
        
        $startFind = "";
        
        
        $strategy = $this->detectStrategy($file);
        
        try{
            
            
            while (($buffer = fgets($handle, 512)) !== false) {
                
                if(strpos($buffer, "learning evaluation instances,evaluation time") !== false){
                    break;
                }
                
                
                
                
                if($strategy == "EvaluateInterleavedTestThenTrain2" ||
                    $strategy == "EvaluatePrequential2"
                    || $strategy == "EvaluatePrequential3" || $strategy == "EvaluatePrequential4"){
                        
                        if(strpos($buffer, "Accuracy:") === false){
                            
                        }else
                        {
                            $startFind = "accuracy";
                        }
                        
                        
                        if(strpos($buffer, "Time:") === false)
                        {
                            
                        }else
                        {
                            $startFind = "time";
                        }
                        
                        
                        if(strpos($buffer, "Memory (B/s):") ===  false){
                            
                        }
                        else
                        {
                            $startFind = "memory";
                        }
                        
                        switch($startFind){
                            
                            case 'accuracy':
                                
                                //if($parameters["accuracy"]==1){
                                
                                //if(strpos($buffer, "Confidence Interval =")>-1){
                                if(strpos($buffer, "Confidence Interval =")>-1 || strpos($buffer, "Mean (CI) =")>-1){
                                    
                                    $accuracy = $buffer;
                                    
                                    //$accuracy = substr($accuracy,strpos($accuracy, "Confidence Interval =")+22);
                                    
                                    if(strpos($buffer, "Confidence Interval =")===true){
                                        $accuracy = substr($accuracy,strpos($accuracy, "Confidence Interval =")+strlen("Confidence Interval =")+1);
                                    }elseif(strpos($buffer, "Mean (CI) =")===true){
                                        $accuracy = substr($accuracy,strpos($accuracy, "Mean (CI) =")+strlen("Mean (CI) =")+1);
                                    }
                                    
                                    
                                    $accuracy = substr($accuracy,0,strpos($accuracy, "(")-1);
                                    $accuracy = trim($accuracy);
                                    
                                    //if($parameters["interval"]!=1){
                                    $ic = substr($buffer,0,strpos($buffer, "+-"+2));
                                    $ic = substr($ic,0,strpos($ic, ")"));
                                    $ic =trim($ic);
                                    
                                    array_push($json_return, array("Accuracy"=>$accuracy, "ic"=>$ic));
                                    
                                    $startFind = "";
                                }
                                
                                
                                
                                //}
                                
                                break;
                            case 'time':
                                
                                //if($parameters["timer"]==1){
                                
                                if(strpos($buffer, "Confidence Interval =")>-1){
                                    
                                    $atime = $buffer;
                                    
                                    $atime = substr($atime,strpos($atime, "Confidence Interval =")+22);
                                    $atime = substr($atime,0,strpos($atime, "(")-1);
                                    $atime = trim($atime);
                                    
                                    $ic = substr($atime,0,strpos($atime, "+-"+2));
                                    $ic = substr($ic,0,strpos($ic, ")"));
                                    $ic =trim($ic);
                                    
                                    array_push($json_return, array("Timer"=>$atime, "ic"=>$ic));
                                    
                                }
                                //}
                                
                                break;
                            case 'memory':
                                
                                //if($parameters["memory"]==1){
                                
                                if(strpos($buffer, "Confidence Interval =")>-1){
                                    
                                    $amemory = $buffer;
                                    
                                    $amemory = substr($amemory,strpos($amemory, "Confidence Interval =")+22);
                                    $amemory = substr($amemory,0,strpos($amemory, "(")-1);
                                    $amemory = trim($amemory);
                                    
                                    
                                    $ic = substr($amemory,0,strpos($amemory, "+-"+2));
                                    $ic = substr($ic,0,strpos($ic, ")"));
                                    $ic =trim($ic);
                                    
                                    array_push($json_return, array("Memory"=>$amemory, "ic"=>$ic));
                                    
                                    
                                    //fim
                                    
                                    break 2;
                                }
                                //}
                                
                                break;
                        }
                        
                        
                }else if($strategy == "EvaluateInterleavedTestThenTrain3"){
                    
                    
                    
                    if(strpos($buffer, "Accuracy:")>-1){
                        $startFind = "accuracy";
                    }else if(strpos($buffer, "Time:")>-1){
                        $startFind = "time";
                    }else if(strpos($buffer, "Memory (B/s):")>-1){
                        $startFind = "memory";
                    }else{
                        
                    }
                    
                    switch($startFind){
                        
                        case 'accuracy':
                            
                            //if($parameters["accuracy"]==1){
                            
                            if(strpos($buffer, "IC(")>-1){
                                
                                
                                
                                $accuracy = $buffer;
                                
                                $accuracy = substr($accuracy,strpos($accuracy, "IC(")+3);
                                $accuracy = substr($accuracy,0,strpos($accuracy, ", "));
                                
                                //if($parameters["interval"]==1){
                                
                                //$accuracy2 = $accuracy;
                                
                                $ic = substr($buffer,strpos($buffer, "+-")+2);
                                //$accuracy = trim($accuracy);
                                
                                $ic =trim($ic);
                                //}
                                
                                array_push($json_return, array("Accuracy"=>$accuracy, "ic"=>$ic));
                                
                            }
                            
                            
                            
                            //}
                            
                            break;
                        case 'time':
                            
                            //if($parameters["timer"]==1){
                            
                            
                            if(strpos($buffer, "IC(")>-1){
                                
                                $accuracy = $buffer;
                                
                                $accuracy = substr($accuracy,strpos($accuracy, "IC(")+3);
                                $accuracy = substr($accuracy,0,strpos($accuracy, ", "));
                                
                                //if($parameters["interval"]==1){
                                
                                //$accuracy2 = $accuracy;
                                
                                $ic = substr($buffer,strpos($buffer, "+-")+2);
                                //$accuracy = trim($accuracy);
                                
                                $ic = trim($ic);
                                //}
                                
                                array_push($json_return, array("Timer"=>$accuracy,"ic"=>$ic));
                                
                            }
                            
                            
                            //}
                            
                            break;
                        case 'memory':
                            
                            //if($parameters["memory"]==1){
                            
                            if(strpos($buffer, "IC(")>-1){
                                
                                $accuracy = $buffer;
                                
                                $accuracy = substr($accuracy,strpos($accuracy, "IC(")+3);
                                $accuracy = substr($accuracy,0,strpos($accuracy, ", "));
                                
                                //if($parameters["interval"]==1){
                                
                                //$accuracy2 = $accuracy;
                                
                                $ic = substr($buffer,strpos($buffer, "+-")+2);
                                //$accuracy = trim($accuracy);
                                
                                $ic = trim($ic);
                                //}
                                
                                array_push($json_return, array("Memory"=>$accuracy, "ic"=>$ic));
                                
                                break 2;
                                
                            }
                            
                            //}
                            
                            break;
                    }
                    
                    
                }else if($strategy == "GeneticAlgorithm"){
                    
                    
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
        
        $handle = fopen($file, "r");
        
        if ($handle) {
            
            $output="";
            
            $startFind = "";
            
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
                }else if(strpos($buffer, "EvaluatePrequential4")>-1){
                    
                    $result = "EvaluatePrequential4";
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
    
    try{
        
        $handle = fopen($file, "r");
        
        if ($handle) {
            
            $output="";
            
            $startFind = "";
            
            while (($buffer = fgets($handle, 4096)) !== false) {
                
                $result = $buffer;
                break;
            }
            
            fclose($handle);
            
        }
        
    }catch(Exception $e){
        exit("error: ".$e->getMessage());
    }
    
    
    return $result;
}


}

?>
