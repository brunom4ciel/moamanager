<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\components\moa;

defined('_EXEC') or die();
 
use moam\core\AppException;
use moam\core\Framework;
use moam\core\Properties;
use moam\core\Template;
use moam\libraries\core\db\DBPDO;
use moam\libraries\core\email\UsageReportMail;
use moam\libraries\core\file\Files;
use moam\libraries\core\json\JsonFile;
use moam\libraries\core\log\ExecutionHistory;
// use moam\libraries\core\menu\Menu;
// use moam\libraries\core\sms\Plivo;
use moam\libraries\core\utils\ParallelProcess;
use moam\libraries\core\utils\Utils;



if (!class_exists('Application'))
{
    $application = Framework::getApplication();
}

if(!$application->is_authentication()) 
{
    $application->alert ( "Error: you do not have credentials." );
}

Template::setDisabledMenu();

// Framework::import("menu", "core/menu");

// if (!class_exists('Menu'))
// {
//     $menu = new Menu();
    
// }


Framework::import("Utils", "core/utils");
// Framework::import("Plivo", "core/sms");
Framework::import("ParallelProcess", "core/utils");
Framework::import("UsageReportMail", "core/email");
Framework::import("Files", "core/file");
Framework::import("JsonFile", "core/json");
Framework::import("execution_history", "core/log");
Framework::import("DBPDO", "core/db");
Framework::import("class_CPULoad", "core/sys");

$DB = new DBPDO(Properties::getDatabaseName(),
    Properties::getDatabaseHost(),
    Properties::getDatabaseUser(),
    Properties::getDatabasePass());

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


$execution_history = new ExecutionHistory($DB);
$extension_scripts = array("data","txt");


$filename = $application->getParameter("filename");
$folder = $application->getParameter("folder");
$task = $application->getParameter("task");

if ($folder != null) {
    if (substr($folder, strlen($folder) - 1) != "/") {
        $folder .= DIRECTORY_SEPARATOR;
    }
}

$user_id    =   $application->getUserId();

$data	="";

if(!defined('MAXIMUM_NUMBER_OF_PROCESSES_IN_PARALLEL'))
{
    define('MAXIMUM_NUMBER_OF_PROCESSES_IN_PARALLEL',16);
}

function getDirContents($dir, &$results = array()){
    
    $files = scandir($dir);
    
    foreach($files as $key => $value){
        $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
        if(is_dir($path) == false) {
            $results[] = $path;
        }
        else if($value != "." && $value != "..") {
            getDirContents($path, $results);
            if(is_dir($path) == false) {
                $results[] = $path;
            }
        }
    }
    return $results;
    
}

$moadefaulttools = array(Properties::getBase_directory_moa_jar_default(), "moa2014optimized.jar");


if($task == "open"){
    
    if($filename!=null){
        
        $filename = PATH_USER_WORKSPACE_STORAGE
        .DIRNAME_SCRIPT
        .DIRECTORY_SEPARATOR
        .$folder
        //.DIRECTORY_SEPARATOR
        .$filename
        ;//.$extension_scripts;
        
        if (in_array(substr($filename, strrpos($filename, ".") + 1), $extension_scripts)) {
            $data = $utils->getContentFile($filename);
        }else{
            exit("problems file extension");
        }
        
        
    }
    
}else{
    
    if($task == "continue"){
        
        
        
        
        $filename = PATH_USER_WORKSPACE_STORAGE
        .$folder
        //.DIRECTORY_SEPARATOR
        . $application->getParameter("filename")
        ;
                
        $data = "";

        
    }else{
        
        //$aux_dir_workspace = str_replace(PATH_USER_WORKSPACE_STORAGE,"", $aux_dir_workspace);
        if($task == "run"){
            
            $parallel = new ParallelProcess();
            
            
            $dirProcess = PATH_USER_WORKSPACE_PROCESSING;
            
            $application->setParameter("memory_used", base64_decode($application->getParameter("memory_used")));
            $application->setParameter("version_software", base64_decode($application->getParameter("version_software")));
            $application->setParameter("dirstorage", base64_decode($application->getParameter("dirstorage")));
            $application->setParameter("data", base64_decode($application->getParameter("data")));
            $application->setParameter("email", base64_decode($application->getParameter("email")));
//             $application->setParameter("phone", base64_decode($application->getParameter("phone")));
            $application->setParameter("filename", base64_decode($application->getParameter("filename")));
            $application->setParameter("parallel_process", base64_decode($application->getParameter("parallel_process")));
            $application->setParameter("interfacename", base64_decode($application->getParameter("interfacename")));
            $application->setParameter("javaparameters", base64_decode($application->getParameter("javaparameters")));
            $application->setParameter("javaagent", base64_decode($application->getParameter("javaagent")));
            
            
            $javaparameters = $application->getParameter("javaparameters");
            
            $interfacename = $application->getParameter("interfacename");
            
            $javaagent = $application->getParameter("javaagent");
            
            if(empty($interfacename))
            {
                $interfacename = "moa.DoTask";
            }
            
            $version_software = $application->getParameter("version_software");
            $moa_menory_used =  $application->getParameter("memory_used");
            //$moa_memory_unit =  "M";
            
            /*
             * parser unit memory
             * if(empty($moa_menory_used))
            {
                $moa_menory_used = "1000M";
            }
            else 
            {
                $unit = substr($moa_menory_used, strlen($moa_menory_used)-1);
                $value = substr($moa_menory_used, 0, strlen($moa_menory_used)-1);
                
                $moa_menory_used
            }*/
            
            $application->setParameter("java", base64_decode($application->getParameter("java")));
            
            $javap = $application->getParameter("java");
            
            if(empty($javap))
            {
                $javap = "jar";
            }
            
            
            
            
            
            //*************************************************
            //
            //************************************************
            
                        
            
            if(strpos($version_software, $application->getUser())===false
                && !in_array($version_software,$moadefaulttools))//strpos($version_software, 
                    //Properties::getBase_directory_moa_jar_default())===false)
            {                    
                    exit("error not version software permission");
                    
            }else
            {
                
                if(is_file(Properties::getBase_directory_moa()
                    ."bin"
                    .DIRECTORY_SEPARATOR
                    .$version_software))
                {
                        
                    $moafile = $version_software;
                    
                }else
                {
                    exit("error version software not found");
                }
                
            }
            
            
            
            
            
            
            

            
            
            
            //executar todos de um diretorio
            if($application->getParameter("foldername")!=null)
            {
                
                //*************************************************
                //
                //************************************************
                $fname = $application->getParameter("foldername");
                
                if(strpos($fname, "/") === false)
                {
                    
                }else
                {
                    $fname = substr($fname, strrpos($fname, "/")+1);
                }
                
                
                
                //$fname = str_replace("/", "-", $fname);
                
                
                if($application->getParameter("dirstorage") == null)
                {                    
                    $foldernew = $fname;//$application->getParameter("filename");
                    
                }else
                {                    
                    $foldernew = $application->getParameter("dirstorage")
                                    .DIRECTORY_SEPARATOR
                                    .$fname//$application->getParameter("filename")
                                    .DIRECTORY_SEPARATOR;
                }
                                
                
                $foldernew__ = $foldernew;
                $y=0;
                
                while(is_dir(PATH_USER_WORKSPACE_STORAGE
                    .$foldernew__))
                {
                    $foldernew__ = $foldernew."-new-(".$utils->format_number($y,4).")";
                    $y++;
                }
                          
                $foldernew = $foldernew__;
                
                
                
                $dirStorage = $utils->create_dir($foldernew, 
                    PATH_USER_WORKSPACE_STORAGE
                            ,"0777");
                                
                if(!$dirStorage)
                {
                    exit("error: not permission in" .  PATH_USER_WORKSPACE_STORAGE);
                }
                

                //*************************************************
                //
                //************************************************
                
        
                
                $files = new Files();
                
                $from_folder =  PATH_USER_WORKSPACE_STORAGE
                    ."scripts"
                    .DIRECTORY_SEPARATOR;
                
                $from_folder2 = trim($application->getParameter("foldername"));
                    
                if(substr($from_folder2, 0, 1) == DIRECTORY_SEPARATOR)
                {
                    $from_folder .= substr($from_folder2, 1);
                }else
                {
                    $from_folder .= $from_folder2;
                }
                
                if(substr($from_folder, strlen($from_folder)-1) != DIRECTORY_SEPARATOR)
                {
                    $from_folder .= DIRECTORY_SEPARATOR;
                }
                
                $idSeq = 1;
                
                //verifica se o diretorio existe
                if(is_dir($from_folder))
                {                    
                    $files_list = getDirContents($from_folder);
                    $aux = array();
                    
                    foreach($files_list as $file_item)
                    {                        
                        $aux[] = substr($file_item, strlen($from_folder));                      
                    }
                    
                    $files_log_list = array();
                    
                    foreach($aux as $file_item)
                    {
                        
                        $aux_str = explode(DIRECTORY_SEPARATOR, $file_item);
                        $aux_dir = $from_folder;
                        $aux_dir_workspace = $dirStorage;
                        
                        foreach($aux_str as $aux_item){                                                     
                            
                            if(is_dir($aux_dir . $aux_item . DIRECTORY_SEPARATOR))
                            {                                
                                $aux_dir .= $aux_item . DIRECTORY_SEPARATOR;  
                                
                                if(!is_dir($aux_dir_workspace . $aux_item . DIRECTORY_SEPARATOR))
                                {
                                    //echo "create dir " . $aux_dir_workspace . "<br>";
                                    $aux_result = $utils->create_dir($aux_item,
                                        $aux_dir_workspace
                                        ,"0777");
                                    
                                    if(!$aux_result)
                                    {
                                        exit("error: not permission in" 
                                            .  PATH_USER_WORKSPACE_STORAGE);
                                    }
                                    
                                }
                                
                                $aux_dir_workspace .= $aux_item . DIRECTORY_SEPARATOR;
                                
                            }else
                            {
                                $aux_filename = substr($aux_item, 0, strrpos($aux_item, "."));
                                                                
                                if(!is_dir($aux_dir_workspace . $aux_filename . DIRECTORY_SEPARATOR))
                                {
                                    //echo "create dir " . $aux_dir_workspace . "<br>";
                                    $aux_result = $utils->create_dir($aux_filename,
                                        $aux_dir_workspace
                                        ,"0777");
                                    
                                    if(!$aux_result)
                                    {
                                        exit("error: not permission in" .  PATH_USER_WORKSPACE_STORAGE);
                                    }
                                    
//                                     $aux_dir .= $aux_filename . DIRECTORY_SEPARATOR;  
                                    $aux_dir_workspace .= $aux_filename . DIRECTORY_SEPARATOR;
                                }
                                
                                $filename = $aux_item;
                                
                              
                                if(file_exists($aux_dir . $aux_item))
                                {
                                    
                                    $script_list = $files->loadListScripts($aux_dir . $filename);
                                    
                                    $username = $application->getUser();
                                    
                                    $list_scripts = array();
                                    
//                                     $lines_cmd = array();
                                    $w=1;
                                    $dataJson = array();
                                    
                                        
                                    $filename_source = $filename;
                                    $filename_source = str_replace(" ", "", $filename_source);
                                    $filename_source = substr($filename_source, 0, strrpos($filename_source, "."));
                                    
//                                     $filename = $aux_dir_workspace
//                                     .$filename_source.".log";
                                    
//                                     if(file_exists($filename)){                                        
//                                         unlink($filename);
//                                     }
                                    
                                    $javaagent_cmd = "";
                                    if($javaagent != "no"){
                                        $javaagent_cmd = "-javaagent:"
                                            .Properties::getBase_directory_moa()
                                            ."lib"
                                                .DIRECTORY_SEPARATOR
                                                .$javaagent . " ";// "sizeofag-1.0.0.jar ";
                                    }
                                    
                                    
                                    foreach($script_list as $script_item)
                                    {
                                        
                                        $filename_script_w = $filename_source."-".$utils->format_number($w,4).".txt"; //format_number2($i,4)
                                        
                                        $filename_script= $filename_source."-".$utils->format_number($w, 4)
                                        . "-" . $idSeq . ".txt"; //format_number2($i,4)
                                        
                                        
                                        $cmd = "";
                                        
                                        
                                        if($javap == "runnable")
                                        {
                                            
                                            $cmd = Properties::getFileJavaExec()
                                            . " " . $javaparameters
                                            ." -Xmx".$moa_menory_used." -jar \""
                                            .Properties::getBase_directory_moa()
                                            ."bin"
                                            .DIRECTORY_SEPARATOR
                                            .$moafile . "\""
                                                        //." -javaagent:"
                                            //.Properties::getBase_directory_moa()
                                            //."lib"
                                            //.DIRECTORY_SEPARATOR
                                            //."sizeofag-1.0.0.jar "
                                            ." \ \"".$script_item."\" > "
                                            .$dirProcess . $filename_script;
                                                
                                        }
                                        else
                                        {
                                            
                                            $cmd = Properties::getFileJavaExec()
                                            . " " . $javaparameters
                                            ." -Xmx".$moa_menory_used." -cp \""
                                            .Properties::getBase_directory_moa()
                                            ."bin"
                                            .DIRECTORY_SEPARATOR
                                            .$moafile
                                            .":"
                                            .Properties::getBase_directory_moa()
                                            ."lib"
                                            .DIRECTORY_SEPARATOR
                                            ."*\" "
                                            .$javaagent_cmd 
//                                             ." -javaagent:"
//                                             .Properties::getBase_directory_moa()
//                                             ."lib"
//                                             .DIRECTORY_SEPARATOR
//                                             ."sizeofag-1.0.0.jar " 
                                            . $interfacename . " \ \"".$script_item."\" > "
                                            .$dirProcess.$filename_script;
                                                                            
                                        }

                                        $files_log_list[] = array(
                                            //"id"=>$utils->format_number($w,4),
                                            "id"=>$idSeq,
                                            "pid"=>0,
                                            "filename"=>$aux_dir_workspace.$filename_script_w,
                                            "command"=>$cmd,
                                            "running"=>false,
                                            "script"=>$script_item,
                                            "process"=>false,
                                            "starttime"=>"",
                                            "endtime"=>"",
                                            "user"=>$username
                                        );
                                        
                                        
                                        $w++;
                                        $idSeq++;
                                        
                                    }
                                    
                                    
                                }
                            }
                        }                           
                    }
                    
                }
                
                $filename_man_log = $dirStorage . $foldernew__ . ".log";                
                
                $jsonfile = new JsonFile($filename_man_log);
                
                $jsonfile->setData($files_log_list);
                
                $jsonfile->save();
                
                chmod($filename, 0777);
                
                
                   
                //$filename_man_log =  substr($filename_man_log, strrpos($filename_man_log, DIRECTORY_SEPARATOR)+1);
                
                //
                // ------------START PROCESS ------
                //
                $script = "";
                $process_initialized = date_create()->format('Y-m-d H:i:s');
                $command = "";
                $from = $filename_man_log;
                $pid = getmypid();

                $execution_history_id = $execution_history->process_initialized(
                    $user_id,
                    1,
                    $script,
                    $process_initialized,
                    $command,
                    $from,
                    $pid);

                //
                // ------------END START PROCESS ------
                //


                $parallel->pool_execute2($filename_man_log,
                    $application->getParameter("parallel_process"),
//                     $dirProcess,
                    $user_id,
                    $interfacename, USERNAME);


                //
                // ------------CLOSED PROCESS ------
                //

                $process_closed = date_create()->format('Y-m-d H:i:s');

                if($execution_history_id != null)
                {
                    $execution_history->closed_process(
                        $execution_history_id,
                        $process_closed);
                }

                //
                // ------------END CLOSED PROCESS ------
                //


                        
                    
                    
                    
            }else{
                //execucao por arquivo ou caixa de texto da interface gráfica
                
                
                //*************************************************
                //
                //************************************************
                $fname = $folder;//$application->getParameter("folder");
                
                
                
                if(strpos($fname, "/") === FALSE){

                }else{
                                        
                    if(substr($fname,0,1) === "/")
                    {
                        $fname = substr($fname, 1);
                    }
                    
                    if(substr($fname,strlen($fname)-1) === "/")
                    {
                        $fname = substr($fname,0, strlen($fname)-1);
                    }
                    
                    $fname = str_replace("/", "-", $fname);
                    //$fname = substr($fname, strrpos($fname, "/")+1);
                }
                                                
                
                
                
                //$fname = str_replace("/", "-", $fname);
                
                
                if($application->getParameter("filename") == null){
                    
                    $filename_source = "moamanager";//$application->getParameter("filename");
                    
                }else{
                    
                    $filename_source = $application->getParameter("filename");
                    if(strpos($filename_source, ".")=== false)
                    {
                        
                    }
                    else 
                    {
                        $filename_source = substr($filename_source, 0, strrpos($filename_source, "."));
                    }
                    
                    $filename_source = str_replace(" ", "", $filename_source);
                }
                 
                
                
                
//                 $dir = PATH_USER_WORKSPACE_STORAGE;
//                 $dir = str_replace(DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $dir);
                
                
                if($application->getParameter("dirstorage") == null){
                    
                    $foldernew = $fname . $filename_source;//$application->getParameter("filename");
                    
                }else{
                    
                    $foldernew = ""
                    . $application->getParameter("dirstorage")
                    . DIRECTORY_SEPARATOR
                    .$fname
                    .$filename_source;//$application->getParameter("filename");
                    //.DIRECTORY_SEPARATOR;
                }
                
                
                //var_dump($application->getParameter("dirstorage"));
//                 var_dump($foldernew);
//                 exit("---");
                
                
                
                
                
                $foldernew__ = $foldernew;
                $y=0;
                
                while(is_dir(PATH_USER_WORKSPACE_STORAGE . $foldernew__)){
                        $foldernew__ = $foldernew."-new-(".$utils->format_number($y,4).")";
                        $y++;
                }                
                
                $foldernew = $foldernew__;
                                
                
                $dirStorage = $utils->create_dir($foldernew,
                    PATH_USER_WORKSPACE_STORAGE
                    ,"0777");
                
                if(!$dirStorage){
                    exit("error: not permission in" .  PATH_USER_WORKSPACE_STORAGE);
                }
                
                
                $aux_dir_workspace = $dirStorage;
                


                
                //*************************************************
                //
                //************************************************
                
                
                $data = $application->getParameter("data");
                
                
//                 $folder = $application->getParameter("folder");
                
//                 if ($folder != null) {
//                     if (substr($folder, strlen($folder) - 1) != "/") {
//                         $folder .= DIRECTORY_SEPARATOR;
//                     }
//                 }
                
                $dir = PATH_USER_WORKSPACE_STORAGE . DIRNAME_SCRIPT . $folder;
                $dir = str_replace(DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $dir);
                
                
                
                if(empty($application->getParameter("dirstorage")))
                {
                    $filename = $dir
                    . $application->getParameter("filename");
                }
                else
                {
                    $filename = $dir
                    . $application->getParameter("filename");
                }
                                
                                
                
//                 var_dump($filename);
//                 exit("---");
                
                
                
                $data = $utils->getContentFile($filename);
                
                if(!$data)
                {
                    chmod($filename, 0777);    
                    $data = $utils->getContentFile($filename);
                    
                    if(!$data)
                    {
                        echo "problems in file path: "+$filename;
                    }                    
                }
                                
                
                
                $list_scripts = explode("\n", $data);
                $list_scripts2 = array();
                
                if(count($list_scripts) > 0)
                {                
                    for($i=0;$i<count($list_scripts);$i++){
                        if(trim($list_scripts[$i])!="")
                            array_push($list_scripts2, trim($list_scripts[$i]));
                    }
                }
                else 
                {
                    exit("Error: without scripts in the file.");
                }
                
//                 var_dump($_REQUEST);exit();
                              
                $username = $application->getUser();
                $lines_cmd = array();
                $w=1;
                $dataJson = array();
                $idSeq = 1;
                
                $javaagent_cmd = "";
                if($javaagent != "no"){
                    $javaagent_cmd = "-javaagent:"
                        .Properties::getBase_directory_moa()
                        ."lib"
                            .DIRECTORY_SEPARATOR
                            .$javaagent . " ";// "sizeofag-1.0.0.jar ";
                }
                
                foreach($list_scripts2 as $key=>$script_item){
                    
                    //usleep(5000);
                    //sleep(1);
                    
                    $filename_script_w = $filename_source."-".$utils->format_number($idSeq,4).".txt";
                    
                    $filename_script= $filename_source."-".$utils->format_number($idSeq, 4)
                    . "-" . $idSeq . ".txt"; //format_number2($i,4)
                    
                    
                    if($javap == "runnable"){
                        
                        $cmd = Properties::getFileJavaExec()
                        . " " . $javaparameters
                        ." -Xmx".$moa_menory_used." -jar \""
                        .Properties::getBase_directory_moa()
                        ."bin"
                        .DIRECTORY_SEPARATOR
                        .$moafile . "\""
                                   //." -javaagent:"
                        //.Properties::getBase_directory_moa()
                        //."lib"
                        //.DIRECTORY_SEPARATOR
                        //."sizeofag-1.0.0.jar "
                        ." \ \"".$script_item."\" > "
                        .$dirProcess . $filename_script;
                            
                    }else {
                        
                        $cmd = Properties::getFileJavaExec()
                        . " " . $javaparameters
                        ." -Xmx".$moa_menory_used." -cp \""
                        .Properties::getBase_directory_moa()
                        ."bin"
                        .DIRECTORY_SEPARATOR
                        .$moafile
                        .":"
                        .Properties::getBase_directory_moa()
                        ."lib"
                        .DIRECTORY_SEPARATOR
                        ."*\" "
                        .$javaagent_cmd
//                         ." -javaagent:"
//                         .Properties::getBase_directory_moa()
//                         ."lib"
//                         .DIRECTORY_SEPARATOR
//                         ."sizeofag-1.0.0.jar " 
                        .$interfacename . " \ \"".$script_item."\" > "
                        .$dirProcess.$filename_script;

                                                        
                    }
                    
                    
                    $files_log_list[] = array(
                        //"id"=>$utils->format_number($w,4),
                        "id"=>$idSeq,
                        "pid"=>0,
                        "filename"=>$aux_dir_workspace . $filename_script_w,
                        "command"=>$cmd,
                        "running"=>false,
                        "script"=>$script_item,
                        "process"=>false,
                        "starttime"=>"",
                        "endtime"=>"",
                        "user"=>$username
                    );
                    
                    
                    
//                     $lines_cmd[] = $cmd;
                    
//                     $dataJson[] = array("id"=>$utils->format_number($w,4),
//                         "pid"=>0,
//                         "running"=>false,
//                         "script"=>$item,
//                         "process"=>false,
//                         "starttime"=>"",//time(),
//                         "endtime"=>"",
//                         "command"=>$cmd,
//                         "user"=>$application->getUser());
                    
//                     $w++;
                    $idSeq++;
                    
//                     var_dump($files_log_list);exit();
                }
                
                
                $filename_man_log = ""
                    . $dirStorage . $filename_source
                    . substr(microtime(true),0,8).".log";
                
                
                
                //$filename_man_log = $dirStorage . $foldernew__ . ".log";
                
                if(file_exists($filename_man_log))
                {
                    unlink($filename_man_log);   
                }
                
                
//                 exit("==" . $filename_man_log);
                
                $jsonfile = new JsonFile($filename_man_log);                
                $jsonfile->setData($files_log_list);                
                $jsonfile->save();                
                chmod($filename_man_log, 0777);
//                 var_dump($list_scripts2);
//                 exit("fim");
                
                
//                 $parallel->pool_execute2($filename_man_log,
//                     $application->getParameter("parallel_process"),
//                     $dirProcess,
//                     $user_id);
                
                
                
                //
                // ------------START PROCESS ------
                //
                $script = "";
                $process_initialized = date_create()->format('Y-m-d H:i:s');
                $command = "";
                $from = $filename_man_log;
                $pid = getmypid();
                
                $execution_history_id = $execution_history->process_initialized(
                    $user_id,
                    1,
                    $script,
                    $process_initialized,
                    $command,
                    $from,
                    $pid);
                
                //
                // ------------END START PROCESS ------
                //
                
                
                $parallel->pool_execute2($filename_man_log,
                    $application->getParameter("parallel_process"),
                    $user_id,
                    $interfacename, USERNAME);
                
                
                //
                // ------------CLOSED PROCESS ------
                //
                
                $process_closed = date_create()->format('Y-m-d H:i:s');
                
                if($execution_history_id != null)
                {
                    $execution_history->closed_process(
                        $execution_history_id,
                        $process_closed);
                }
                
                //
                // ------------END CLOSED PROCESS ------
                //
                
                
                
                
                
                
                
                
            }
            
            
            
            
            
            $notification = $application->getParameter("notification");
            
            
            if($notification=="1"){
                
                $email = $application->getParameter("email");
                
                if($email!= null){
                    
                    $url_report = "http://".$_SERVER['HTTP_HOST']
                    .PATH_WWW;
                    
                    $body = "Finished: "
                        .$application->getParameter("filename")
                        ."<br><br>"
                            .$url_report;
                            
                            $subject = "Notification - Completed Experiment.";
                            
                            try{
                                
                                $mail = new UsageReportMail();
                                $mail->sendMail($email, $body, $subject);
                                
                            }catch(AppException $e) {
                                
                                throw new AppException( $e->getMessage());
                            }
                            
                }
                
//                 $phone = $application->getParameter("phone");
//                 //var_dump($phone);
//                 if($phone!= null){
                    
//                     $plivo = new Plivo(PLIVO_AUTH_ID, PLIVO_AUTH_TOKEN);
                    
                    
//                     $result = $plivo->sendSMS("Completed Experiment - "
//                         .substr($application->getParameter("filename"),0,30),
//                         array($phone));
                    
//                     //var_dump($result);
//                     exit("---");
                    
//                 }
                
            }
            
            
            exit("Finished");
            
            
            
            
        }else{
            
            if($task == "restart"){
                
                
                //exit("problems");
                

                $application->setParameter("memory_used", base64_decode($application->getParameter("memory_used")));
                $application->setParameter("version_software", base64_decode($application->getParameter("version_software")));
                $application->setParameter("dirstorage", base64_decode($application->getParameter("dirstorage")));
                //App::setParameter("data", base64_decode($application->getParameter("data")));
                $application->setParameter("email", base64_decode($application->getParameter("email")));
//                 $application->setParameter("phone", base64_decode($application->getParameter("phone")));
                $application->setParameter("filename", base64_decode($application->getParameter("filename")));
                $application->setParameter("parallel_process", base64_decode($application->getParameter("parallel_process")));
                
                $application->setParameter("interfacename", base64_decode($application->getParameter("interfacename")));
                $application->setParameter("javaparameters", base64_decode($application->getParameter("javaparameters")));
                $application->setParameter("javaagent", base64_decode($application->getParameter("javaagent")));
                
                $javaparameters = $application->getParameter("javaparameters");
                
                $interfacename = $application->getParameter("interfacename");
                
                $javaagent = $application->getParameter("javaagent");
                
                if(empty($interfacename)){
                    $interfacename = "moa.DoTask";
                }
                
                
                $application->setParameter("java", base64_decode($application->getParameter("java")));
                
                $javap = $application->getParameter("java");
                
                if(empty($javap)){
                    $javap = "jar";
                }
                
                
                
                $filename = $application->getParameter("filename");
                
                $filename = str_replace(" ", "", $filename);
                
                $filename = PATH_USER_WORKSPACE_STORAGE
                .$folder
                //.DIRECTORY_SEPARATOR
                .$filename
                ;
                
                $dirProcess = PATH_USER_WORKSPACE_PROCESSING;
                
                $dirStorage = PATH_USER_WORKSPACE_STORAGE
                .$folder;
                
                
                
                
                $version_software =  $application->getParameter("version_software");
                $moa_menory_used =  $application->getParameter("memory_used");
                //$moa_memory_unit =  "M";
                
                
                //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
                
                
                if(strpos($version_software, $application->getUser())===false
                    && !in_array($version_software,$moadefaulttools))//
                    //&& strpos($version_software, Properties::getBase_directory_moa_jar_default())===false){
                {
                        exit("error not version software permission");
                        
                }else{
                    
                    if(is_file(Properties::getBase_directory_moa()
                        ."bin"
                        .DIRECTORY_SEPARATOR
                        .$version_software)){
                            
                            $moafile = $version_software;
                            
                    }else{
                        exit("error version software not found");
                    }
                    
                }
                
                //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
                
                
                $filename_man_log = $filename;
                
                $jsonfile = new JsonFile();
                
                $jsonfile->open($filename);                
                
                $data2 = $jsonfile->getData();
                                
                

                
                $length_data = count($data);
                $length_process= 0;
                
                $data = "";
                $data_aux = array();
                
                
                if($length_data>0){
                    
                    $lines_cmd = array();
                    $username = $application->getUser();
                    $idSeq = 1;
                    
                    
                    
                    foreach($data2 as $key=>$element){
                        
                            
//                         $filename_ = $element["filename"];
                            
//                         var_dump($element['filename']);  exit();
                        
                        if(file_exists($element["filename"])){
                                
//                             var_dump($element['filename']);   
                            $data_aux[] = $element;
                            
                        }else{
                            
                            //if($element["process"] == false){
                                
                                $script_item = $element["script"];                                
                                    
//                                 $aux_dir_workspace = substr($element["command"], 0, strrpos($filename_, DIRECTORY_SEPARATOR)+1);
                                    
//                                 $filename_script = substr($filename_, strrpos($filename_, DIRECTORY_SEPARATOR)+1);
                                      
                                
                                $filename_script = substr($element["command"], strrpos($element["command"], ">") + 1);
                                $filename_script = trim($filename_script);
                                
                                $aux_dir_workspace = substr($filename_script, 0, strrpos($filename_script, DIRECTORY_SEPARATOR)+1);
                                $filename_script = substr($filename_script, strrpos($filename_script, DIRECTORY_SEPARATOR)+1);
                                
                                  
    //                                 $filename_script= $filename_source."-".$utils->format_number($idSeq,4).".txt"; //format_number2($i,4)
                                    
                                $cmd = "";
                                    
                                    
                                if($javap == "runnable"){
                                    
                                    $cmd = Properties::getFileJavaExec()
                                    . " " . $javaparameters
                                    ." -Xmx".$moa_menory_used." -jar \""
                                    .Properties::getBase_directory_moa()
                                    ."bin"
                                    .DIRECTORY_SEPARATOR
                                    .$moafile . "\""
                                    //." -javaagent:"
                                    //.Properties::getBase_directory_moa()
                                    //."lib"
                                    //.DIRECTORY_SEPARATOR
                                    //."sizeofag-1.0.0.jar "
                                    ." \ \"".$script_item."\" > "
                                        .$aux_dir_workspace . $filename_script;
                                    
                                }else{
                                    
                                    $cmd = Properties::getFileJavaExec()
                                    . " " . $javaparameters
                                    ." -Xmx".$moa_menory_used." -cp \""
                                    .Properties::getBase_directory_moa()
                                    ."bin"
                                    .DIRECTORY_SEPARATOR
                                    .$moafile
                                    .":"
                                    .Properties::getBase_directory_moa()
                                    ."lib"
                                    .DIRECTORY_SEPARATOR
                                    ."*\""
                                    ." -javaagent:"
                                    .Properties::getBase_directory_moa()
                                    ."lib"
                                    .DIRECTORY_SEPARATOR
                                    ."sizeofag-1.0.0.jar " . $interfacename . " \ \"".$script_item."\" > "
                                    . $aux_dir_workspace . $filename_script;
                                    
                                }
                                    
                                    
    //                             $files_log_list[] = array(
    //                                 //"id"=>$utils->format_number($w,4),
    //                                 "id"=>$idSeq,
    //                                 "pid"=>0,
    //                                 "filename"=>$aux_dir_workspace.$filename_script,
    //                                 "command"=>$cmd,
    //                                 "running"=>false,
    //                                 "script"=>$script_item,
    //                                 "process"=>false,
    //                                 "starttime"=>"",
    //                                 "endtime"=>"",
    //                                 "user"=>$username
    //                             );
                                
                                
//                                 $jsonfile->open($filename);
                                
//                                 $data = $jsonfile->getDataKeyValue("id", $element["id"]);
                                
                                $data = $element;
                                $data["process"] = false;
                                $data["starttime"] = "";
                                $data["endtime"] ="";
                                $data["running"] = false;
                                $data["pid"] = "";
                                $data["command"] = $cmd;
                                
                                $data_aux[] = $data;
                                
//                                 $jsonfile->setDataKeyValue("id", $element["id"], $data);                                
//                                 $jsonfile->save();
                                //$jsonfile->load();
                                
    //                             $idSeq++;
                            
                            //}else{

                            //    $data_aux[] = $element;
                            //}

                        }
                    }
                    

                    

                    
                    $jsonfile = new JsonFile($filename);                    
                    $jsonfile->setData($data_aux);                    
                    $jsonfile->save();                    
                    chmod($filename, 0777);
                    
                    
                    $filename_man_log = $filename;
                    
                    //var_dump($lines_cmd);
                    
//                     exit("--ok");
                    //exit("----".$application->getParameter("parallel_process"));
                    
                                      
//                     $parallel = new ParallelProcess();
//                     $parallel->pool_execute2($filename_man_log,
//                         $application->getParameter("parallel_process"),
//                         $dirProcess,
//                         $user_id);
                    
                    
                  
                    //
                    // ------------START PROCESS ------
                    //
                    $script = "";
                    $process_initialized = date_create()->format('Y-m-d H:i:s');
                    $command = "";
                    $from = $filename_man_log;
                    $pid = getmypid();
                    
                    $execution_history_id = $execution_history->process_initialized(
                        $user_id,
                        1,
                        $script,
                        $process_initialized,
                        $command,
                        $from,
                        $pid);
                    
                    //
                    // ------------END START PROCESS ------
                    //
                    
                    $parallel = new ParallelProcess();
                    
                    $parallel->pool_execute2($filename_man_log,
                        $application->getParameter("parallel_process"),
//                         $dirProcess,
                        $user_id,
                        $interfacename, USERNAME);
                    
                    
                    //
                    // ------------CLOSED PROCESS ------
                    //
                    
                    $process_closed = date_create()->format('Y-m-d H:i:s');
                    
                    if($execution_history_id != null)
                    {
                        $execution_history->closed_process(
                            $execution_history_id,
                            $process_closed);
                    }
                    
                    //
                    // ------------END CLOSED PROCESS ------
                    //
                    
                    
                    
                    
                    
                    
                    $email = $application->getParameter("email");
                    
                    if($email!= null){
                        
                        $url_report = "http://".$_SERVER['HTTP_HOST']
                        .PATH_WWW
                        ;
                        
                        $body = "Finished: "
                            .$application->getParameter("filename")
                            ."<br><br>"
                                .$url_report;
                                
                                $subject = "Notification - Completed Experiment.";
                                
                                try{
                                    
                                    $mail = new UsageReportMail();
                                    $mail->sendMail($email, $body, $subject);
                                    
                                }catch(AppException $e) {
                                    
                                    throw new AppException( $e->getMessage());
                                }
                                
                    }
                    
                    
//                     $phone = $application->getParameter("phone");
                    
//                     if($phone!= null){
                        
//                         $plivo = new Plivo(PLIVO_AUTH_ID, PLIVO_AUTH_TOKEN);
                        
//                         $result = $plivo->sendSMS("Completed Experiment - "
//                             .substr($application->getParameter("filename"),0,30),
//                             array($phone));
                        
//                         //var_dump($result);
                        
//                     }
                    
                    
                    
                }
                
                exit("Finished");
            }
            
        }
        
    }
    
}

?>






							<div class="page-header">
        						<h1>
        							<a href="<?php echo $_SERVER['REQUEST_URI']?>">Run Experiment</a>
        						</h1>
        					</div>
        					
        					


									<form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>" name="saveform" class="ng-pristine ng-valid-email ng-invalid ng-invalid-required">
										<input type="hidden" value="<?php echo $application->getComponent()?>" name="component" id="component">
										<input type="hidden" value=<?php echo $application->getController()?> name="controller" id="controller">	
										
										<?php 
										
										if(!empty($application->getParameter("foldername"))){
										?>
										
										<input type="hidden" value="<?php echo (empty($application->getParameter("folder"))? $application->getParameter("foldername"):$application->getParameter("folder").$application->getParameter("foldername"));?>" name="foldername" id="foldername">	
										
										<?php }else{?>
										
										<input type="hidden" value="" name="foldername" id="foldername">	
										
										<?php }?>
										
										<input type="hidden" value="run" name="task" id="task">
												
										<input type="hidden" value="<?php echo $application->getParameter("folder");?>" name="folder" id="folder">
										
										
											<div style="float:left;padding-left:5px;width:100%;margin-top:5px;">
												
											<?php 
											
												if($application->getParameter("foldername")!=null){
													
													echo "Folder name: ".$application->getParameter("folder").$application->getParameter("foldername");
											?>
											<?php ?>
												<input type="hidden" value="<?php echo $application->getParameter("filename");?>" name="filename" id="filename" style="width:100%;">									
												<textarea id="data"	style="visibility:hidden;display:none;width:100%;height:400px;" name="data" <?php echo ($task=="continue"?"readOnly=\"true\"":"")?>><?php echo $data;?></textarea>
												<br>
												
											<?php 		
													
												}else{
											?>
												
												<input type="text" value="<?php echo $application->getParameter("filename");?>" name="filename" id="filename" style="width:100%;">									
												
												<textarea id="data"	style="width:100%;height:400px;" name="data" <?php echo ($task=="continue"?"readOnly=\"true\"":"")?>><?php echo ($task=="continue"?"":$data);?></textarea>
												
												<br>
												
											<?php }?>	
												
												<input type='button' class="btn btn-info" onclick='toogle_editable("data", this);' value='Toggle to edit mode' />
												
												<br>
												
												<fieldset style="border:1px solid #000"><legend>Preferences</legend>
												<table style="padding:5px;border-spacing: 10px;border-collapse: separate;">
													<tr>
														<td style="width:250px;">Number Of Process Parallel</td><td><select name="parallel_process" class="btn btn-default" id="parallel_process" onchange="setCookieElementSelectValue(this);">
												<?php 
												for($i = 1; $i <= MAXIMUM_NUMBER_OF_PROCESSES_IN_PARALLEL; $i++)
												{												
												    echo "<option value=\"".$i."\">".$i."</option>";
												}
												?>
												</select></td>
													</tr>
													<tr>
														<td>Memory Used By Process
														</td>
														<td>														
												<input type="text" id="memory_used" name="memory_used" onchange="setCookieElementValue(this);"/> Example: 1000M, 1G, 3500M
														</td>
													</tr>
													<tr>
														<td><label><input type="checkbox" name="notification" id="notification" value="1" onclick="setCookieCheckbox(this);" > Notification by email</label>
														</td>
														<td><input type="text" id="email" name="email" onchange="setCookieElementValue(this);"/>
														</td>
													</tr>
													<tr>
														<td>Version of the software
														</td>
														<td><select name="version_software" class="btn btn-default" id="version_software" onchange="setCookieElementSelectValue(this);">
												
												<?php 
													
												
												
												$files_list = $utils->getListElementsDirectory1(Properties::getBase_directory_moa()."bin/", array("jar"));
												
												foreach($files_list as $key=>$element){
												
													if(strpos($element["name"], $application->getUser())===false){
														unset($files_list[$key]);
													}else{
															
													}
												
												}
												
												
												foreach($files_list as $key=>$element){
														
													//if($element["name"] == $application->getUser().".jar"){
													//	echo "<option value=\"".$element["name"]."\">".$element["name"]."</option>";
													//}else{
												    echo "<option value=\"".$element["name"]."\">".$element["name"] . " " . date("Y/m/d H:i:s", filemtime(Properties::getBase_directory_moa()."bin/". $element["name"])) ."</option>";
													//}
													//}
																			
												}
												
												foreach($moadefaulttools as $item){
												    echo "<option value=\"".$item."\">".$item. " " . date("Y/m/d H:i:s", filemtime(Properties::getBase_directory_moa()."bin/".$item)) ."</option>";
												    
												}
    
												//echo "<option value=\"".Properties::getBase_directory_moa_jar_default()."\">".Properties::getBase_directory_moa_jar_default(). " " . date("Y/m/d H:i:s", filemtime(Properties::getBase_directory_moa()."bin/".Properties::getBase_directory_moa_jar_default())) ."</option>";
												//echo "<option value=\"".$moaopt."\">".$moaopt. " " . date("Y/m/d H:i:s", filemtime(Properties::getBase_directory_moa()."bin/".$moaopt)) ."</option>";
												
												?>
												</select>
														</td>
													</tr>
													<tr>
														<td>Java File
														</td>
														<td><select name="java" class="btn btn-default" id="java"
											onchange="setCookieElementSelectValue(this);">
											<option value="jar">JAR file</option>
											<option value="runnable">Runnable JAR file</option>
										</select>
														</td>
													</tr>
													<tr>
														<td>Java paramenter in command line
														</td>
														<td><input type="text"  id="javaparameters" name="javaparameters" onchange="setCookieElementValue(this);" value="" style="width:100%"/>
														</td>
													</tr>
													<tr>
														<td>Instance of a class supplied to it as a command line
														</td>
														<td><input type="text"  id="interfacename" name="interfacename" onchange="setCookieElementValue(this);" value="moa.DoTask"/>
														</td>
													</tr>
													
													<tr>
														<td>Java -javaagent
														</td>
														<td><select name="javaagent" class="btn btn-default" id="javaagent"
											onchange="setCookieElementSelectValue(this);">
											<option value="no">Do not use</option>
											<option value="sizeofag-1.0.0.jar">sizeofag-1.0.0.jar</option>
										</select>
														</td>
													</tr>
													<tr>
														<td>Save result in folder 
														</td>
														<td><select name="dirstorage" class="btn btn-default" id="dirstorage" <?php echo ($task=="continue"?"disabled":"")?> >
													<option value=""></option>
													<?php 
													
													$files_list = $utils->getListDirectory(PATH_USER_WORKSPACE_STORAGE);
																	
													foreach($files_list as $key=>$element){
													
														//if($element["type"]=="dir"){
															if($element== DIRNAME_SCRIPT
															|| $element== DIRNAME_TRASH
															|| $element== DIRNAME_BACKUP){
																unset($files_list[$key]);
															}
														//}
													}
													
													
													foreach($files_list as $key=>$element){
													
														//if($element["type"]=="dir"){
													
															echo "<option value=\"".$element."\">".$element."</option>";	
														//}
													
													}
													
													?>
													
												</select>
														</td>
													</tr>
													
												</table>
											</fieldset>
											
											<br>
												
												
											</div>										
											
											
										
										
									<div style="float: right; padding-left: 10px">
										
											

											
												<?php if($application->getParameter("task")=="continue"){ ?>
												
												<input type="button" id="button_send" class="btn btn-warning" name="button_send" value="Continue" onclick="javascript: sendScripts('restart'); this.disabled=true; this.style.background = '#ffffff'; this.value=this.value+' [disabled]'">	
												
												<?php }else{ ?>
												
												<input type="button" class="btn btn-success" id="button_send" name="button_send" value="Execute" onclick="javascript: sendScripts('run'); this.disabled=true; this.style.background = '#ffffff'; this.value=this.value+' [disabled]'">	
												
												<?php }?>
												
												
										<?php 
											
												if($application->getParameter("folder") != "")
												{
											?>	
												
												<input type="button" value="Return" class="btn btn-default" onclick="javascript: window.location.href='?component=scripts&folder=<?php echo $application->getParameter("folder");?>';">	
																							
											<?php 
												}
											?>
											
									</div>
									
									</form>
			


<script type='text/javascript'>

function SetSelectIndex(idElement, elementText)
{
    var elementObj = document.getElementById(idElement);
//alert("id"+elementObj.id);

    for(i = 0; i < elementObj.length; i++)
    {
      // check the current option's text if it's the same with the input box
      if (elementObj.options[i].innerHTML == elementText)
      {
         elementObj.selectedIndex = i;
         break;
      }     
    }
}



function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+d.toGMTString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i].trim();
        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
    }
    return "";
}






function setCookieElementSelectValue(element){
	
//	alert(" "+element.name);
	//alert("==="+element.options[element.selectedIndex].innerHTML);
	setCookie(element.id,element.options[element.selectedIndex].innerHTML,365);
}


function setCookieElementValue(element){
	
	setCookie(element.id,Base64.encode(element.value),365);
}




function historicCookieElementSelectValue(elementId, defaultValue){

	var elementCookieHistoric = getCookie(elementId);
	
	if(elementCookieHistoric==""){
		elementCookieValue=defaultValue;//=defaultValue;"";
	}else
		elementCookieValue=elementCookieHistoric;
	
	//document.getElementById(elementId).value = elementCookieValue;
	SetSelectIndex(elementId, elementCookieValue);
	
	
}


function historicCookieElementValue(elementId, defaultValue){

	var elementCookieHistoric = getCookie(elementId);


	if(elementCookieHistoric=="")
	{	
		elementCookieHistoric=Base64.encode(defaultValue);//=defaultValue;"";
	}

	document.getElementById(elementId).value = Base64.decode(elementCookieHistoric);
}
















function parseBool2( str ){

    var boolmap = { 
        'no'    : false ,
        'NO'    : false ,
        'FALSE' : false ,
        'false' : false,
        'yes'   : true ,
        'YES'   : true ,
        'TRUE'  : true ,
        'true'  : true 
    };

    return ( str in boolmap && boolmap.hasOwnProperty(str)) ? 
      boolmap[ str ] :  !!str ;
};


function setCookieCheckbox(element){

	var checkedbox = element.checked;
	
	if(checkedbox)
		checkedbox=true;
	else
		checkedbox=false;
	
	setCookie(element.id,checkedbox,365);
}



function historicCookieCheckbox(elementId){

	var elementCookieHistoric = getCookie(elementId);
	
	if(elementCookieHistoric==""){
		var elementCookieChecked=false;
	}else
		var elementCookieChecked=elementCookieHistoric;
	
	//alert(elementId+"="+elementCookieChecked);
	//alert(elementId+"="+checkedbox+", elementCookieChecked=");//+elementCookieChecked);
	
	
	document.getElementById(elementId).checked = parseBool2(elementCookieChecked);
	

}








function setCookieRadioBox(element){

	var value = getRadioValue(element.id);

	setCookie(element.id,value,365);
}

function getRadioValue(groupName) {
    var _result;
    try {
        var o_radio_group = document.getElementsByName(groupName);
        for (var a = 0; a < o_radio_group.length; a++) {
            if (o_radio_group[a].checked) {
                _result = o_radio_group[a].value;
                break;
            }
        }
    } catch (e) { }
    return _result;
}


function historicCookieRadiobox(elementId){

	var elementCookieHistoric = getCookie(elementId);
	
	if(elementCookieHistoric==""){
		var elementCookieChecked=0;
	}else
		var elementCookieChecked=elementCookieHistoric;
	
	//alert(elementId+"="+elementCookieChecked);
	//alert(elementId+"="+checkedbox+", elementCookieChecked=");//+elementCookieChecked);
	

	var o_radio_group = document.getElementsByName(elementId);//document.getElementById(elementId);//document.getElementsByName(groupName);
	
	for (var a = 0; a < o_radio_group.length; a++) {

		//alert(o_radio_group[a].value +'=='+ elementCookieChecked);
		
            if (o_radio_group[a].value == elementCookieChecked) {
               // _result = o_radio_group[a].value;
                o_radio_group[a].checked = true;
                break;
            }
	}
        
//	alert('historico='+elementCookieChecked);
	
	//document.getElementById(elementId).checked = elementCookieChecked;
	

}




historicCookieElementSelectValue("parallel_process");
// historicCookieElementSelectValue("memory_used");
historicCookieElementValue("memory_used", "1000M");
historicCookieElementValue("email", "<?php echo $application->getUser()?>");
historicCookieCheckbox("notification");
historicCookieElementValue("javaparameters", "");
historicCookieElementValue("interfacename", "moa.DoTask");

historicCookieElementSelectValue("version_software");
historicCookieElementSelectValue("javaagent");
historicCookieElementSelectValue("java");




function sendScripts(task){

	//if(task=="continue"){
	document.getElementById("task").value = task;
	
	sendMOAREST('<?php echo PATH_WWW?>index.php','','POST');
	
}

   
function sendMOAREST(strURL, content, method){
	
	var parameters ="";
	    


	//var method= elementObj.value.toUpperCase();

	
	var HttpReq;
	
	if (window.XMLHttpRequest)
	{// code for IE7+, Firefox, Chrome, Opera, Safari
		HttpReq=new XMLHttpRequest();
	}
	else
	{// code for IE6, IE5
		HttpReq=new ActiveXObject("Microsoft.XMLHTTP");
	}
	
	HttpReq.withCredentials = false;
	
	var strParameters = content;	 
	 
	if ( method == 'POST'){//create data
		
		HttpReq.open(method, strURL, true);
		
		
		//strParameters = "parameters="+strParameters;
		
	}else if( method == 'PUT'){//update data
		
		HttpReq.open(method, strURL, true);
		//strParameters = "parameters="+strParameters;
		
	}else if( method == 'DELETE'){//delete data
		
		HttpReq.open(method, strURL, true);
		//strParameters = "parameters="+strParameters;
				
	}else if( method == 'GET'){//delete data
		
		strParameters = "script="+Base64.encode(strParameters);
		HttpReq.open(method, strURL +'?'+ strParameters, true);
		//HttpReq.open(method, strURL, true);
				
	}else if( method == 'HEAD'){//delete data
		
		HttpReq.open(method, strURL, true);
		//strParameters = "parameters="+strParameters;
				
	}else if( method == 'OPTIONS'){//delete data
		
		HttpReq.open(method, strURL, true);
		//strParameters = "parameters="+strParameters;
				
	}else{
		//default
		
		//strParameters = "parameters="+strParameters;
		HttpReq.open(method, strURL +'?'+ strParameters, true);
	}
    

	HttpReq.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	
	
	//alert("Content-Type: "+contentType);
	var content_type="text/html";
	var content_type="text/html";
	
	//HttpReq.setRequestHeader('Content-Type', content_type+";charset=UTF-8");
	//HttpReq.setRequestHeader("Accept",accept+";charset=UTF-8");
	    
    HttpReq.onreadystatechange = function() {
        if (HttpReq.readyState == 4) {
        	
        	switch(HttpReq.status){
        		
        		case	200:

        			var result = HttpReq.responseText;
        			var button_send = document.getElementById('button_send');
        			
        			button_send.disabled=false; 
        			button_send.style.background = ''; 

        			button_send.value= "Execute";

            		alert('Result: '+result);
            			
        			break;
        		case	401:
        		
        			alert("401 Unauthorized");
        			
        			break;
        		default:
        			//alert(""+HttpReq.status);
        	}
        }
        
        
	
	}

	
	var component = document.getElementById('component').value;
	var controller = document.getElementById('controller').value;
	var task = document.getElementById('task').value;
	var filename = document.getElementById('filename').value;
	var data = document.getElementById('data').value;
	var folder = document.getElementById('folder').value;
	var foldername = document.getElementById('foldername').value;
	var interfacename = document.getElementById('interfacename').value;	
	var javaparameters = document.getElementById('javaparameters').value;	
	
	var parallel_process = document.getElementById('parallel_process');
		parallel_process = parallel_process.options[parallel_process.selectedIndex].value;
		

	var memory_used = document.getElementById('memory_used').value;
	//var memory_used = document.getElementById('memory_used');
	//	memory_used = memory_used.options[memory_used.selectedIndex].value;
		
		
	var dirstorage = document.getElementById('dirstorage');
		dirstorage = dirstorage.options[dirstorage.selectedIndex].value;

	var email = document.getElementById('email').value;
// 	var phone = document.getElementById('phone').value;

	if(document.getElementById('notification').checked == true )	
		var notification = 1;//document.getElementById('notification').value;
	else
		var notification = 0;//
		
	var version_software = document.getElementById('version_software');
	version_software = version_software.options[version_software.selectedIndex].value;
		
	var javap = document.getElementById('java');
	javap = javap.options[javap.selectedIndex].value;	

	var javaagent = document.getElementById('javaagent');
	javaagent = javaagent.options[javaagent.selectedIndex].value;	
	
	
	//if( document.getElementById('message_check').checked ==true )
	HttpReq.send("data="+encodeURIComponent(Base64.encode(data))
				+'&parallel_process='+encodeURIComponent(Base64.encode(parallel_process))
				+'&memory_used='+encodeURIComponent(Base64.encode(memory_used))
				+'&email='+encodeURIComponent(Base64.encode(email))
// 				+'&phone='+encodeURIComponent(Base64.encode(phone))
				+'&dirstorage='+encodeURIComponent(Base64.encode(dirstorage))
				+'&component='+encodeURIComponent(component)
				+'&controller='+encodeURIComponent(controller)
				+'&task='+encodeURIComponent(task)
				+'&folder='+encodeURIComponent(folder)				
				+'&javaparameters='+encodeURIComponent(Base64.encode(javaparameters))
				+'&interfacename='+encodeURIComponent(Base64.encode(interfacename))
				+'&foldername='+encodeURIComponent(foldername)
				+'&notification='+encodeURIComponent(notification)
				+'&filename='+encodeURIComponent(Base64.encode(filename))
				+'&version_software='+encodeURIComponent(Base64.encode(version_software))
				+'&java='+encodeURIComponent(Base64.encode(javap))
				+'&javaagent='+encodeURIComponent(Base64.encode(javaagent))
				+'&tmpl=tmpl');
	//else
	//	HttpReq.send("");
	
	//var aa = HttpReq.getAllResponseHeaders();
		//table_html		

	//var selectize = $('#select-url').selectize();//[].selectize;
	//var aa = selectize.getOption(2)[0];
	
	//alert("ddd="+aa);



				//
}


</script>

			




<script type="text/javascript">
	// initialisation
	editAreaLoader.init({
		id: "data"	// id of the textarea to transform	
			,start_highlight: true	
			,font_size: "8"
			,is_editable: false
			,word_wrap: true
			,font_family: "verdana, monospace"
			,allow_resize: "y"
			,allow_toggle: true
			,language: "en"
			,syntax: "java"	
			,toolbar: "go_to_line, |, undo, redo, |, select_font"
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

</script>


