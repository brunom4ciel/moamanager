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
use moam\libraries\core\utils\Utils;
use moam\libraries\core\log\ExecutionHistory;
use moam\libraries\core\menu\Menu;
use moam\core\Template;
use moam\libraries\core\json\JsonFile;
use moam\libraries\core\utils\ParallelProcess;
use moam\libraries\core\file\Files;
use moam\libraries\core\db\DBPDO;
use moam\libraries\core\email\UsageReportMail;
use moam\libraries\core\sms\Plivo;



if (!class_exists('Application'))
{
    $application = Framework::getApplication();
}

if(!$application->is_authentication())
{
    $application->alert ( "Error: you do not have credentials." );
}

Framework::import("menu", "core/menu");

if (!class_exists('Menu'))
{
    $menu = new Menu();
    
}


Framework::import("Utils", "core/utils");
Framework::import("Plivo", "core/sms");
Framework::import("ParallelProcess", "core/utils");
Framework::import("UsageReportMail", "core/email");
Framework::import("Files", "core/file");
Framework::import("JsonFile", "core/json");
Framework::import("execution_history", "core/log");
Framework::import("DBPDO", "core/db");

$DB = new DBPDO(Properties::getDatabaseName(),
    Properties::getDatabaseHost(),
    Properties::getDatabaseUser(),
    Properties::getDatabasePass());

Template::addHeader(array("tag"=>"script",
    "type"=>"text/javascript",
    "src"=>""
    . $application->getPathTemplate()
    . "/javascript/base64.js"));

$utils = new Utils();


$execution_history = new ExecutionHistory($DB);
$extension_scripts = array("data","txt");


$filename = $application->getParameter("filename");
$folder = $application->getParameter("folder");
$task = $application->getParameter("task");

$user_id    =   $application->getUserId();

$data	="";


if($task == "open"){
    
    if($filename!=null){
        
        $filename = Properties::getBase_directory_destine($application)
        .$application->getUser()
        .DIRECTORY_SEPARATOR
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
        
        
        
        
        $filename = Properties::getBase_directory_destine($application)
        .$application->getUser()
        .DIRECTORY_SEPARATOR
        .$folder
        //.DIRECTORY_SEPARATOR
        . $application->getParameter("filename")
        ;
        
        //exit("gg".$filename);
        
        //Framework::includeLib("JsonFile.php");
        
        $jsonfile = new JsonFile();
        
        $jsonfile->open($filename);
        
        $data2 = $jsonfile->getData();
        
        $length_data = count($data);
        $length_process= 0;
        
        $data = "";
        
        if($length_data>0){
            
            foreach($data2 as $key=>$element){
                
                if(is_array($element)){
                    
                    //if($element["process"]==false){
                    
                    $data  .= $element["script"]."\n\n";
                    
                    //}
                    
                }
                
            }
            
        }
        
        /*	$parallel = new ParallelProcess();
        
        $parallel->pool_execute($filename,
        $lines_cmd,
        $application->getParameter("parallel_process"),
        $dirProcess,
        $dirStorage);*/
        
        
        
    }else{
        
        
        if($task == "run"){
            
            $parallel = new ParallelProcess();
            
            
            
            
            
            $dirProcess = Properties::getBase_directory_destine_exec()
            .$application->getUser()
            .DIRECTORY_SEPARATOR;
            
            $application->setParameter("memory_unit", base64_decode($application->getParameter("memory_unit")));
            $application->setParameter("memory_used", base64_decode($application->getParameter("memory_used")));
            $application->setParameter("version_software", base64_decode($application->getParameter("version_software")));
            $application->setParameter("dirstorage", base64_decode($application->getParameter("dirstorage")));
            $application->setParameter("data", base64_decode($application->getParameter("data")));
            $application->setParameter("email", base64_decode($application->getParameter("email")));
            $application->setParameter("phone", base64_decode($application->getParameter("phone")));
            $application->setParameter("filename", base64_decode($application->getParameter("filename")));
            $application->setParameter("parallel_process", base64_decode($application->getParameter("parallel_process")));
            $application->setParameter("interfacename", base64_decode($application->getParameter("interfacename")));
            
            $interfacename = $application->getParameter("interfacename");
            
            if(empty($interfacename)){
                $interfacename = "moa.DoTask";
            }
            
            $version_software = $application->getParameter("version_software");
            $moa_menory_used =  $application->getParameter("memory_used");
            $moa_memory_unit =  $application->getParameter("memory_unit");
            
            
            $application->setParameter("java", base64_decode($application->getParameter("java")));
            
            $javap = $application->getParameter("java");
            
            if(empty($javap)){
                $javap = "jar";
            }
            
            
            
            
            
            //*************************************************
            //
            //************************************************
            
            
            
            // 				if(file_exists(App::getBase_directory_moa()
            // 						."bin"
            // 						.DIRECTORY_SEPARATOR
            // 						.$application->getUser()
            // 						.".jar")){
            
            // 					$moafile = $application->getUser().".jar";
            
            // 				}else{
            
            // 					$moafile = App::getBase_directory_moa_jar_default();
            // 					//$moafile = substr($moafile, strrpos($moafile, DIRECTORY_SEPARATOR));
            
            // 				}
            
            
            if(strpos($version_software, $application->getUser())===false
                && strpos($version_software, Properties::getBase_directory_moa_jar_default())===false){
                    
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
            
            
            
            
            
            
            
            
            
            
            
            //executar todos de um diretorio
            if($application->getParameter("foldername")!=null){
                
                //*************************************************
                //
                //************************************************
                $fname = $application->getParameter("foldername");
                $fname = str_replace("/", "-", $fname);
                
                
                if($application->getParameter("dirstorage") == null){
                    
                    $foldernew = $fname;//$application->getParameter("filename");
                    
                }else{
                    
                    $foldernew = $application->getParameter("dirstorage")
                    .DIRECTORY_SEPARATOR
                    .$fname//$application->getParameter("filename")
                    .DIRECTORY_SEPARATOR;
                    
                    
                }
                
                
                
                $foldernew__ = $foldernew;
                $y=0;
                
                while(is_dir(Properties::getBase_directory_destine($application)
                    .$application->getUser()
                    .DIRECTORY_SEPARATOR
                    .$foldernew__)){
                        
                        $foldernew__ = $foldernew." (".$utils->format_number($y,4).")";
                        $y++;
                }
                
                $foldernew = $foldernew__;
                
                //criar o diretorio
                mkdir(Properties::getBase_directory_destine($application)
                    .$application->getUser()
                    .DIRECTORY_SEPARATOR
                    .$foldernew, 0777, true);
                
                //modifica as permissoes do diretorio
                chmod(Properties::getBase_directory_destine($application)
                    .$application->getUser()
                    .DIRECTORY_SEPARATOR
                    .$foldernew, 0777);
                
                //define o local do diretorio base
                $dirStorage = Properties::getBase_directory_destine($application)
                .$application->getUser()
                .DIRECTORY_SEPARATOR
                .$foldernew
                .DIRECTORY_SEPARATOR;
                
                
                //*************************************************
                //
                //************************************************
                
                
                
                $files = new Files();
                
                $from_folder =  Properties::getBase_directory_destine($application)
                .$application->getUser()
                .DIRECTORY_SEPARATOR
                ."scripts"
                    .DIRECTORY_SEPARATOR
                    .$application->getParameter("foldername")
                    .DIRECTORY_SEPARATOR;
                    
                    
                    
                    
                    //verifica se o diretorio existe
                    if(is_dir($from_folder)){
                        
                        //carrega a lista de arquivos de um diterório
                        $files_list = $utils->getListElementsDirectory($from_folder, array("data"));
                        
                        
                        //carrega a lista de script dentro de um arquivo
                        foreach($files_list as $file_item){
                            
                            $filename = substr($file_item,
                                strrpos($file_item,
                                    DIRECTORY_SEPARATOR),
                                strrpos($file_item,"."));
                            
                            
                            mkdir($dirStorage
                                .$filename, 0777, true);
                            
                            chmod($dirStorage
                                .$filename, 0777);
                            
                            $dirStorage_script = $dirStorage
                            .$filename
                            .DIRECTORY_SEPARATOR;
                            
                            
                            
                            $script_list = $files->loadListScripts($from_folder.$file_item);
                            
                            $list_scripts = array();
                            
                            $lines_cmd = array();
                            $w=1;
                            $dataJson = array();
                            
                            // 						$filename = substr($file_item,
                            // 												strrpos($file_item,
                            // 												DIRECTORY_SEPARATOR),
                                // 												strrpos($file_item,"."));
                                
                            $filename_source = $filename;
                            $filename_source = str_replace(" ", "", $filename_source);
                            
                            
                            
                            
                            
                            foreach($script_list as $script_item){
                                
                                $filename_script= $filename_source."-".$utils->format_number($w,4).".txt"; //format_number2($i,4)
                                
                                
                                
                                
                                
                                if($javap == "runnable"){
                                    
                                    $cmd = Properties::getFileJavaExec()
                                    ." -Xmx".$moa_menory_used.$moa_memory_unit." -jar \""
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
                                        
                                }else{
                                    $cmd = Properties::getFileJavaExec()
                                    ." -Xmx".$moa_menory_used.$moa_memory_unit." -cp \""
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
                                                                    .$dirProcess.$filename_script;
                                                                    
                                                                    
                                }
                                
                                
                                $lines_cmd[] = $cmd;
                                
                                $dataJson[] = array("id"=>$utils->format_number($w,4),
                                    "pid"=>0,
                                    "running"=>false,
                                    "script"=>$script_item,
                                    "process"=>false,
                                    "starttime"=>"",//time(),
                                    "endtime"=>"",
                                    "command"=>$cmd,
                                    "user"=>$application->getUser());
                                
                                $w++;
                                
                                
                                
                            }
                            
                            //var_dump($lines_cmd);exit("fim");
                            
                            $filename = $dirStorage_script
                            .$filename_source.".log";
                            
                            
                            if(file_exists($filename)){
                                
                                unlink($filename);
                                
                            }else{
                                
                                
                            }
                            
                            
                            //Framework::includeLib("JsonFile.php");
                            
                            $jsonfile = new JsonFile($filename);
                            
                            //$jsonfile->load();
                            
                            $jsonfile->setData($dataJson);
                            
                            $jsonfile->save();
                            
                            chmod($filename, 0777);
                            
                            
                            //
                            // ------------START PROCESS ------
                            //
                            $script = "";
                            $process_initialized = date_create()->format('Y-m-d H:i:s');
                            $command = "";
                            $from = $filename;
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
                            
                            
                            
                            $parallel->pool_execute($filename,
                                $lines_cmd,
                                $application->getParameter("parallel_process"),
                                $dirProcess,
                                $dirStorage_script,
                                $user_id);
                            
                            
                            
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
                        
                        
                        
                    }
                    
                    
                    
                    
                    
                    
            }else{
                //execucao por arquivo ou caixa de texto da interface gráfica
                
                
                //*************************************************
                //
                //************************************************
                $fname = $application->getParameter("filename");//$application->getParameter("filename");
                $fname = str_replace("/", "-", $fname);
                
                
                if($application->getParameter("dirstorage") == null){
                    
                    $foldernew = $fname;//$application->getParameter("filename");
                    
                }else{
                    
                    $foldernew = $application->getParameter("dirstorage")
                    .DIRECTORY_SEPARATOR
                    .$fname//$application->getParameter("filename")
                    .DIRECTORY_SEPARATOR;
                    
                    
                }
                
                
                $foldernew__ = $foldernew;
                $y=0;
                
                while(is_dir(Properties::getBase_directory_destine($application)
                    .$application->getUser()
                    .DIRECTORY_SEPARATOR
                    .$foldernew__)){
                        
                        $foldernew__ = $foldernew."".$utils->format_number($y,4)."";
                        $y++;
                }
                
                $foldernew = $foldernew__;
                
                
                //criar o diretorio
                mkdir(Properties::getBase_directory_destine($application)
                    .$application->getUser()
                    .DIRECTORY_SEPARATOR
                    .$foldernew, 0777, true);
                
                //modifica as permissoes do diretorio
                chmod(Properties::getBase_directory_destine($application)
                    .$application->getUser()
                    .DIRECTORY_SEPARATOR
                    .$foldernew, 0777);
                
                //define o local do diretorio base
                $dirStorage = Properties::getBase_directory_destine($application)
                .$application->getUser()
                .DIRECTORY_SEPARATOR
                .$foldernew
                .DIRECTORY_SEPARATOR;
                
                
                //*************************************************
                //
                //************************************************
                
                
                $data = $application->getParameter("data");
                
                
                $list_scripts = explode("\n", $data);
                $list_scripts2 = array();
                
                for($i=0;$i<count($list_scripts);$i++){
                    if(trim($list_scripts[$i])!="")
                        array_push($list_scripts2, trim($list_scripts[$i]));
                }
                
                
                
                
                
                
                
                $lines_cmd = array();
                $w=1;
                $dataJson = array();
                
                foreach($list_scripts2 as $key=>$item){
                    
                    //usleep(5000);
                    //sleep(1);
                    
                    if($application->getParameter("filename")==null){
                        
                        $filename_source = "script";
                        
                    }else{
                        
                        $filename_source = $application->getParameter("filename");
                        
                        $filename_source = str_replace(" ", "", $filename_source);
                        
                        //$filename_source = $application->getParameter("filename");
                    }
                    
                    
                    $filename = $filename_source."-".$utils->format_number($w,4).".txt"; //format_number2($i,4)
                    
                    
                    if($javap == "runnable"){
                        
                        $cmd = Properties::getFileJavaExec()
                        ." -Xmx".$moa_menory_used.$moa_memory_unit." -jar \""
                            .Properties::getBase_directory_moa()
                            ."bin"
                                .DIRECTORY_SEPARATOR
                                .$moafile . "\""
                                    //." -javaagent:"
                        //.Properties::getBase_directory_moa()
                        //."lib"
                        //.DIRECTORY_SEPARATOR
                        //."sizeofag-1.0.0.jar "
                        ." \ \"".$item."\" > "
                            .$dirProcess . $filename;
                            
                    }else {
                        
                        $cmd = Properties::getFileJavaExec()
                        ." -Xmx".$moa_menory_used.$moa_memory_unit." -cp \""
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
                                                    ."sizeofag-1.0.0.jar " . $interfacename . " \ \"".$item."\" > "
                                                        .$dirProcess.$filename;
                                                        
                    }
                    
                    
                    
                    $lines_cmd[] = $cmd;
                    
                    $dataJson[] = array("id"=>$utils->format_number($w,4),
                        "pid"=>0,
                        "running"=>false,
                        "script"=>$item,
                        "process"=>false,
                        "starttime"=>"",//time(),
                        "endtime"=>"",
                        "command"=>$cmd,
                        "user"=>$application->getUser());
                    
                    $w++;
                }
                
                
                
                $filename = $dirStorage
                .$application->getParameter("filename").".log";
                
                
                if(file_exists($filename)){
                    
                    unlink($filename);
                    
                }else{
                    
                    
                }
                
                
                //$utils->setContentFile($dirStorage.$filename, $data);
                
                //Framework::includeLib("JsonFile.php");
                
                $jsonfile = new JsonFile($filename);
                
                //$jsonfile->load();
                
                $jsonfile->setData($dataJson);
                
                $jsonfile->save();
                
                
                chmod($filename, 0777);
                
                
                
                
                
                //
                // ------------START PROCESS ------
                //
                $script = "";
                $process_initialized = date_create()->format('Y-m-d H:i:s');
                $command = "";
                $from = $filename;
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
                
                
                $parallel->pool_execute($filename,
                    $lines_cmd,
                    $application->getParameter("parallel_process"),
                    $dirProcess,
                    $dirStorage,
                    $user_id);
                
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
                
                $phone = $application->getParameter("phone");
                //var_dump($phone);
                if($phone!= null){
                    
                    $plivo = new Plivo(Properties::getPlivoAuthId(),
                        Properties::getPlivoAuthToken());
                    
                    
                    $result = $plivo->sendSMS("Completed Experiment - "
                        .substr($application->getParameter("filename"),0,30),
                        array($phone));
                    
                    var_dump($result);
                    exit("---");
                    
                }
                
            }
            
            
            exit("fim-");
            
            
            
            
        }else{
            
            if($task == "restart"){
                
                $application->setParameter("memory_unit", base64_decode($application->getParameter("memory_unit")));
                $application->setParameter("memory_used", base64_decode($application->getParameter("memory_used")));
                $application->setParameter("version_software", base64_decode($application->getParameter("version_software")));
                $application->setParameter("dirstorage", base64_decode($application->getParameter("dirstorage")));
                //App::setParameter("data", base64_decode($application->getParameter("data")));
                $application->setParameter("email", base64_decode($application->getParameter("email")));
                $application->setParameter("phone", base64_decode($application->getParameter("phone")));
                $application->setParameter("filename", base64_decode($application->getParameter("filename")));
                $application->setParameter("parallel_process", base64_decode($application->getParameter("parallel_process")));
                
                $application->setParameter("interfacename", base64_decode($application->getParameter("interfacename")));
                
                $interfacename = $application->getParameter("interfacename");
                
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
                
                $filename = Properties::getBase_directory_destine($application)
                .$application->getUser()
                .DIRECTORY_SEPARATOR
                .$folder
                //.DIRECTORY_SEPARATOR
                .$filename
                ;
                
                $dirProcess = Properties::getBase_directory_destine_exec()
                .$application->getUser()
                .DIRECTORY_SEPARATOR;
                
                $dirStorage = Properties::getBase_directory_destine($application)
                .$application->getUser()
                .DIRECTORY_SEPARATOR
                .$folder;
                
                
                
                
                
                $version_software =  $application->getParameter("version_software");
                $moa_menory_used =  $application->getParameter("memory_used");
                $moa_memory_unit =  $application->getParameter("memory_unit");
                
                
                //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
                
                
                if(strpos($version_software, $application->getUser())===false
                    && strpos($version_software, Properties::getBase_directory_moa_jar_default())===false){
                        
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
                
                
                // 					Framework::includeLib("JsonFile.php");
                
                $jsonfile = new JsonFile();
                
                $jsonfile->open($filename);
                
                
                $data2 = $jsonfile->getData();
                
                
                
                
                $length_data = count($data);
                $length_process= 0;
                
                $data = "";
                
                if($length_data>0){
                    
                    $lines_cmd = array();
                    
                    foreach($data2 as $key=>$element){
                        
                        if(is_array($element)){
                            
                            //if($element["process"]==false){
                            
                            $command = $element["command"];
                            $filename_ = substr($command,strrpos($command,">")+1);
                            $filename_ = trim($filename_);
                            
                            $filename_ = substr($filename_,strrpos($filename_,"/")+1);
                            $filename_ = trim($filename_);
                            
                            if(file_exists($dirStorage.$filename_)){
                                
                            }else{
                                
                                $cmd = $element["command"];
                                
                                /*$script = substr($cmd,
                                 strrpos($cmd, "moa.DoTask \ \"")
                                 +strlen("moa.DoTask \ \""));
                                
                                 $script_item = substr($script, 0,
                                 strrpos($script, "\" >"));*/
                                
                                
                                $filename_script = substr($cmd,
                                    strrpos($cmd, "\" >")+4);
                                
                                
                                
                                /*$cmd = Properties::getFileJavaExec()
                                 ." -Xmx".$moa_menory_used.$moa_memory_unit." -cp \""
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
                                 ."sizeofag-1.0.0.jar " . $interfacename . " \ \"".$element["script"]."\" > "
                                 .$filename_script;*/
                                
                                
                                if($javap == "runnable"){
                                    
                                    $cmd = Properties::getFileJavaExec()
                                    ." -Xmx".$moa_menory_used.$moa_memory_unit." -jar \""
                                        .Properties::getBase_directory_moa()
                                        ."bin"
                                            .DIRECTORY_SEPARATOR
                                            .$moafile . "\""
                                                //." -javaagent:"
                                    //.Properties::getBase_directory_moa()
                                    //."lib"
                                    //.DIRECTORY_SEPARATOR
                                    //."sizeofag-1.0.0.jar "
                                    ." \ \"".$element["script"]."\" > "
                                        . $filename_script;
                                        
                                        
                                }else {
                                    
                                    $cmd = Properties::getFileJavaExec()
                                    ." -Xmx".$moa_menory_used.$moa_memory_unit." -cp \""
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
                                                                ."sizeofag-1.0.0.jar " . $interfacename . " \ \"".$element["script"]."\" > "
                                                                    .$filename_script;
                                                                    
                                }
                                
                                
                                
                                
                                
                                
                                $data = "";
                                
                                $data = $jsonfile->getDataKeyValue("id", $element["id"]);
                                
                                $data["process"] = false;//(strtolower($application->getParameter("process"))=="true"?true:false);
                                //$data["script"] = $element["script"];
                                $data["starttime"] = "";
                                $data["endtime"] ="";
                                $data["running"] = false;
                                $data["pid"] = "";
                                $data["command"] = $cmd;
                                
                                //var_dump($data);
                                
                                $jsonfile->setDataKeyValue("id", $element["id"], $data);
                                
                                $jsonfile->save();
                                
                                $lines_cmd[] = $cmd;
                            }
                            //}
                        }
                    }
                    
                    
                    
                    
                    //var_dump($lines_cmd);
                    
                    //exit("--ok");
                    //exit("----".$application->getParameter("parallel_process"));
                    
                    $parallel = new ParallelProcess();
                    
                    //var_dump($lines_cmd);exit();
                    
                    //exit("bruno");
                    // 						$parallel->pool_execute($filename,
                    // 												$lines_cmd,
                    // 												$application->getParameter("parallel_process"),
                    // 												$dirProcess,
                    // 						    $dirStorage, $user_id);
                        
                        
                        
                        
                    //
                    // ------------START PROCESS ------
                    //
                    $script = "";
                    $process_initialized = date_create()->format('Y-m-d H:i:s');
                    $command = "";
                    $from = $filename;
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
                    
                    
                    $parallel->pool_execute($filename,
                        $lines_cmd,
                        $application->getParameter("parallel_process"),
                        $dirProcess,
                        $dirStorage, $user_id);
                    
                    
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
                    
                    
                    $phone = $application->getParameter("phone");
                    
                    if($phone!= null){
                        
                        $plivo = new Plivo();
                        
                        $result = $plivo->sendSMS("Completed Experiment - "
                            .substr($application->getParameter("filename"),0,30),
                            array($phone));
                        
                        //var_dump($result);
                        
                    }
                    
                    
                    
                }
                
                exit();
            }
            
        }
        
    }
    
}

?>


		<div class="content content-alt">
			<div class="container" style="width:90%">
				<div class="row">
					<div class="" >
					
						<div class="card" style="width:100%">
							<div class="page-header">
								<h1><a href="<?php echo $_SERVER['REQUEST_URI']?>"><?php echo TITLE_COMPONENT?></a></h1>
							</div>
							
							<div style="width:100%;padding-bottom:15px;display:table">
							
								<div style="float:left;width:18%;border:1px solid #fff;display:table-cell">
																
									<?php echo $application->showMenu($menu);?>								

								</div>
								
								<div style="float:left;width:80%;border:1px solid #fff;display:table-cell">
								
									
									<form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>" name="saveform" async-form="login" class="ng-pristine ng-valid-email ng-invalid ng-invalid-required">
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
										
										<div style="float:left;padding-left:20px;width:100%">
											
											<div style="float:left;padding-left:5px;width:100%;margin-top:5px;">
												
											<?php 
											
												if($application->getParameter("foldername")!=null){
													
													echo "Folder name: ".$application->getParameter("folder").$application->getParameter("foldername");
											?>
											<?php ?>
												<input type="hidden" value="<?php echo $application->getParameter("filename");?>" name="filename" id="filename" style="width:100%;">									
												<textarea id="data"	style="visibility:hidden;display:none;width:100%;height:400px;" name="data" <?php echo ($task=="continue"?"readOnly=\"true\"":"")?>><?php echo $data?></textarea>
												<br>
												
											<?php 		
													
												}else{
											?>
												
												<input type="text" value="<?php echo $application->getParameter("filename");?>" name="filename" id="filename" style="width:100%;">									
												<textarea id="data"	style="width:100%;height:400px;" name="data" <?php echo ($task=="continue"?"readOnly=\"true\"":"")?>><?php echo $data?></textarea>
												<br>
												
											<?php }?>	
												
												
												
												<br>
												
												Number Of Process Parallel <select name="parallel_process" id="parallel_process" onchange="setCookieElementSelectValue(this);">
													<option value="1">1</option>
													<option value="2">2</option>
													<option value="3">3</option>
													<option value="4">4</option>
													<option value="5">5</option>
													<option value="6">6</option>
													<option value="7">7</option>
													<option value="8">8</option>
													<option value="9">9</option>
													<option value="10">10</option>
													<option value="11">11</option>
													<option value="12">12</option>
													<option value="13">13</option>
													<option value="14">14</option>
													<option value="15">15</option>
													<option value="16">16</option>
												</select>
												<br>
												Memory Used By Process <select name="memory_used" id="memory_used" onchange="setCookieElementSelectValue(this);">
													<option value="256">256</option>
													<option value="512">512</option>
													<option value="768">768</option>
													<option value="1000">1000</option>
													<option value="1500">1500</option>
													<option value="2000">2000</option>
													<option value="2500">2500</option>
													<option value="3000">3000</option>
													<option value="4000">4000</option>
													<option value="5000">5000</option>
<!-- 													<option value="6000">6</option> -->
<!-- 													<option value="7000">7</option> -->
<!-- 													<option value="8000">8</option> -->
<!-- 													<option value="9000">9</option> -->
<!-- 													<option value="10000">10000</option> -->
												</select> 
												
												<select name="memory_unit" id="memory_unit" onchange="setCookieElementSelectValue(this);">
													<option value="M">Megabyte</option>
<!-- 													<option value="G">Gigabyte</option> -->
												</select> unit of measurement
												
												<br>
												
												<div style="border: 1px solid #cfcfcf;box-sizing: border-box;padding:2px;">
													
													
													<label><input type="checkbox" name="notification" id="notification" value="1" onclick="setCookieCheckbox(this);" >Notification</label>
													<br>
													Notification by email <input type="text" id="email" name="email" onchange="setCookieElementValue(this);"/>
												
													<br>
													Notification by phone <input type="text" id="phone" name="phone"  placeholder="+5581998070481" onchange="setCookieElementValue(this);"/>
													<br>
												
												</div>
												
												<br>
												
												Version of the software<select name="version_software" id="version_software" onchange="setCookieElementSelectValue(this);">
												
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
														echo "<option value=\"".$element["name"]."\">".$element["name"]."</option>";
													//}
													//}
																			
												}
												
												echo "<option value=\"".Properties::getBase_directory_moa_jar_default()."\">".Properties::getBase_directory_moa_jar_default()."</option>";
												
												?>
												</select><br>
												
												
												Java <select name="java" id="java"
											onchange="setCookieElementSelectValue(this);">
											<option value="jar">JAR file</option>
											<option value="runnable">Runnable JAR file</option>
										</select>  <br>
										
												
												Instance of a class supplied to it as a command line<input type="text" id="interfacename" name="interfacename" onchange="setCookieElementValue(this);" value="moa.DoTask"/>
												<br>
												
												Save result in folder <select name="dirstorage" id="dirstorage" <?php echo ($task=="continue"?"disabled":"")?> >
													<option value=""></option>
													<?php 
													
													$files_list = $utils->getListDirectory(Properties::getBase_directory_destine($application)
																					.$application->getUser()
																					.DIRECTORY_SEPARATOR);
																	
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
												
											</div>										
											
											<div style="float:left;padding-left:0px">
											
											<br>
											<?php 
											
												if($application->getParameter("folder") != "")
												{
											?>	
												
												<input type="button" value="Return" onclick="javascript: window.location.href='?component=scripts&folder=<?php echo $application->getParameter("folder");?>';">	
																							
											<?php 
												}
											?>
												
												<?php if($application->getParameter("task")=="continue"){ ?>
												
												<input type="button" value="Continue" onclick="javascript: sendScripts('restart');">	
												
												<?php }else{ ?>
												
												<input type="button" id="button_send" name="button_send" value="Execute" onclick="javascript: sendScripts('run'); this.disabled=true; this.style.background = '#ffffff'; this.value=this.value+' [disabled]'">	
												
												<?php }?>
											</div>
										
										</div>
										
									</form>
			
			
								</div>
							
							</div>
							
						</div>
					</div>
				</div>
			</div>
		</div>
			
	

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
	
	if(elementCookieHistoric=="")//{
		elementCookieValue=Base64.encode(defaultValue);//=defaultValue;"";
	//}else
	//	elementCookieValue=elementCookieHistoric;
	
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
historicCookieElementSelectValue("memory_used");

historicCookieElementSelectValue("memory_unit");


historicCookieElementValue("email");
historicCookieElementValue("phone");
historicCookieCheckbox("notification");
historicCookieElementValue("interfacename", "moa.DoTask");


historicCookieElementSelectValue("version_software");//, "<?php echo $application->getUser().".jar";?>");


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
        		
        			var button_send = document.getElementById('button_send');
        			
        			button_send.disabled=false; 
        			button_send.style.background = ''; 

        			button_send.value= "Execute";

            		alert('Finished');
            			
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
	var parallel_process = document.getElementById('parallel_process');
		parallel_process = parallel_process.options[parallel_process.selectedIndex].value;

	var memory_used = document.getElementById('memory_used');
		memory_used = memory_used.options[memory_used.selectedIndex].value;
		
	var memory_unit = document.getElementById('memory_unit');
		memory_unit = memory_unit.options[memory_unit.selectedIndex].value;
		
		
	var dirstorage = document.getElementById('dirstorage');
		dirstorage = dirstorage.options[dirstorage.selectedIndex].value;

	var email = document.getElementById('email').value;
	var phone = document.getElementById('phone').value;

	if(document.getElementById('notification').checked == true )	
		var notification = 1;//document.getElementById('notification').value;
	else
		var notification = 0;//
			

	var version_software = document.getElementById('version_software');
	version_software = version_software.options[version_software.selectedIndex].value;
		
	var javap = document.getElementById('java');
	javap = javap.options[javap.selectedIndex].value;	
	
	//if( document.getElementById('message_check').checked ==true )
	HttpReq.send("data="+encodeURIComponent(Base64.encode(data))
				+'&parallel_process='+encodeURIComponent(Base64.encode(parallel_process))
				+'&memory_used='+encodeURIComponent(Base64.encode(memory_used))
				+'&memory_unit='+encodeURIComponent(Base64.encode(memory_unit))
				+'&email='+encodeURIComponent(Base64.encode(email))
				+'&phone='+encodeURIComponent(Base64.encode(phone))
				+'&dirstorage='+encodeURIComponent(Base64.encode(dirstorage))
				+'&component='+encodeURIComponent(component)
				+'&controller='+encodeURIComponent(controller)
				+'&task='+encodeURIComponent(task)
				+'&folder='+encodeURIComponent(folder)
				+'&interfacename='+encodeURIComponent(Base64.encode(interfacename))
				+'&foldername='+encodeURIComponent(foldername)
				+'&notification='+encodeURIComponent(notification)
				+'&filename='+encodeURIComponent(Base64.encode(filename))
				+'&version_software='+encodeURIComponent(Base64.encode(version_software))
				+'&java='+encodeURIComponent(Base64.encode(javap))
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

			

