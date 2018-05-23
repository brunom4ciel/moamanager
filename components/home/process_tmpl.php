<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\components\home;

defined('_EXEC') or die();

use moam\core\Framework;
use moam\core\Application;
use moam\libraries\core\utils\Utils;
use moam\core\Properties;

if (!class_exists('Application'))
{
    $application = Framework::getApplication();
}

if(!$application->is_authentication())
{
    $application->alert ( "Error: you do not have credentials." );
}


Framework::import("Utils", "core/utils");


$utils = new Utils();


$task = $application->getParameter("task");

if($task == "kill"){
    
    $pip = $application->getParameter("pip");
    
    echo "disabled to user.";//.$pip;
    exit();
    
}



//$json_return = array();

//$cmd = "TERM=xterm /usr/bin/top n 1 b i";//"ps -aux";
//$cmd = "ps aux";//"ps -aux";

$col_names = array("pid"=>"PID",
    "pri"=>"PRI",
    "uname"=>"NAME",
    "pmem"=>"%MEN",
    "pcpu"=>"%CPU",
    "start_time"=>"START_TIME",
    "cputime"=>"CPU_TIME",
    "cmd"=>"COMMAND"
    ,"args"=>"ARGS");
$cols_string = "";

foreach($col_names as $key=>$item){
    if($cols_string == ""){
        $cols_string = $key;
    }else{
        $cols_string .= "," .$key;
    }
}



//$cols_string = "pid,pri,uname,pmem,pcpu,start_time,cputime,cmd";
//$col_names = explode(",",$cols_string);

$max_number_on_list_of_process = Properties::getMax_number_on_list_of_process()+1;

$cmd = "ps -e -o " . $cols_string . " --sort=-pcpu,+pmem,+start_time | head -" . $max_number_on_list_of_process;

//$output = shell_exec($cmd);
$result= $utils->runExternal($cmd);
$output = $result["output"];

//var_dump($output);exit();

//$output = substr($output,strpos($output, "COMMAND")+9);
$linhas = explode("\n", $output);
$json=array();

//echo strlen("/opt/google/chrome/chrome - ");
//print str_replace("\n", "<br>", $output);
//print $output;
//exit();


for($i=1; $i<count($linhas); $i++){
    
    if(trim($linhas[$i])=="")
        continue;
        
        //$colunas = explode(" ", trim($linhas[$i]));//str_replace(" ", "", $linhas[$i]));
        
        $cols=array();
        /*
         $y=0;
         foreach($colunas as $key=>$item){
         if($item != ""){
         $cols[$y] = $item;
         $y++;
         }
         }*/
        
        $linha = trim($linhas[$i]);
        $indexCol=0;
        
        $cols["#"] = $i;
        
        foreach($col_names as $key=>$item){
            
            if($key == "args"){
                $cols[$item] = substr($linha, 0);
            }else{
                
                $cols[$item	] = substr($linha, 0, strpos($linha, " "));
                $linha = trim($linha);
                $linha = substr($linha, strpos($linha, " ")+1);
                $linha = trim($linha);
            }
            
        }
        
        
        $json_values = array();
        $indexCol = 0;
        
        foreach($cols as $key=>$item){
            
            //if($key == "PID"){
            //	$json_values[$key] = "<a href='#' onclick=\"javascript:killprocess('" . $item . "')\" title='Click to terminate process'>" . $item . "</a>";
            //}else{
            $json_values[$key] = $item;
            //}
            
            
        }
        
        $json[] = $json_values;
        
        
        
        
        //}
        
}


print json_encode($json)


?>	
	
								