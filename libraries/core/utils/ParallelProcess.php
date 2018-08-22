<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\libraries\core\utils;

defined('_EXEC') or die();

use moam\core\Framework;
use moam\core\Properties;
use moam\libraries\core\db\DBPDO;
use moam\libraries\core\json\JsonFile;
use moam\libraries\core\log\ExecutionHistory;

// Framework::import("Utils", "core/utils");
Framework::import("execution_history", "core/log");
Framework::import("DBPDO", "core/db");

class ParallelProcess extends Utils
{

    public $base_directory_destine;

    public $base_directory_destine_exec;

    private $execution_history;

    public function __construct()
    {
        $DB = new DBPDO(
            Properties::getDatabaseName(), 
            Properties::getDatabaseHost(), 
            Properties::getDatabaseUser(), 
            Properties::getDatabasePass());

        $this->execution_history = new ExecutionHistory($DB);
    }

    
    
    public function pool_execute2($filename, $nb_max_process, $dirProcess, $user_id)
    { // }, $filename_source="") {
        $pross_ids = array();
        
        $pool = array();
        $foo = "";
        // $contadorList=1;
                
        $jsonfile = new JsonFile($filename);
        
        $jsonfile->open();
        
        $jsonfile->load();
        
//         foreach($jsonfile->getData() as $item){
            
//             //var_dump($item);
            
//             $dirStorage = $item["filename"];
//             $dirStorage = substr($dirStorage, 0, strrpos($dirStorage, DIRECTORY_SEPARATOR)+1);
            
            
//             exit($dirStorage);
//         }
        
        $commandes = $jsonfile->getData();
        
//         $dirStorage = "";
        
//         exit("fim");
        
//         $commandes
        
        
        
        
        for ($i = 0; $i < $nb_max_process; $i ++) {
            $pool[$i] = FALSE;
        }
        
        while (count($commandes) > 0) {
            $commande = array_shift($commandes);
            
            $commande_lancee = FALSE;
            while ($commande_lancee == FALSE) {
                
                //usleep(50000);
                sleep(1);
                
                
                for ($i = 0; $i < $nb_max_process and $commande_lancee == FALSE; $i ++) {
                    
                    if ($pool[$i] === FALSE) {
                        
                        
                        
                        // $data = $jsonfile->getDataKeyValue("id", $contadorList);
                        
                        // if($data['process']==false){
                        
                        $pool[$i] = proc_open($commande["command"], array(), $foo);
                        $commande_lancee = TRUE;
                        
                        $statusProc = proc_get_status($pool[$i]);
                        
                        // $contadorList = substr($commande, strrpos($commande, "/")+1);
                        // $contadorList = substr($contadorList, 0, 4);//strrpos($contadorList, "-"));
                        
                        $contadorList = substr($commande["command"], strrpos($commande["command"], "/") + 1);
                        $contadorList = substr($contadorList, strrpos($contadorList, "-") + 1); // strrpos($contadorList, "-"));
                        $contadorList = substr($contadorList, 0, strrpos($contadorList, "."));
   
                        
                        
                        $data = $jsonfile->getDataKeyValue("id", $commande["id"]);
                        
                        $data['pid'] = $statusProc["pid"];
                        $data['running'] = true;
                        $data['process'] = false;
                        $data['starttime'] = time();                        
                        $jsonfile->setDataKeyValue("id", $commande["id"], $data);                        
                        $jsonfile->save();                        
                        $jsonfile->load();
                        
                        
                        
                        
                        
//                         $jsonfile_pool = new JsonFile($commande["filename"]);                        
//                         $jsonfile_pool->open();
                                                
//                         $data = $jsonfile_pool->getDataKeyValue("id", $contadorList);
                        
//                         $data['pid'] = $statusProc["pid"];
//                         $data['running'] = true;
//                         $data['process'] = false;
//                         $data['starttime'] = time();
                        
//                         // $jsonfile->setData($data);
                        
//                         $jsonfile_pool->setDataKeyValue("id", $contadorList, $data);                        
//                         $jsonfile_pool->save();                        
//                         $jsonfile_pool->load();
                        
                        //
                        // ------------START PROCESS ------
                        //
                        $script = $commande["script"];
                        $process_initialized = date_create()->format('Y-m-d H:i:s');
                        $command = $commande["command"];
                        $from = $filename;
                        $pid = $statusProc["pid"];
                        
                        $id = $this->execution_history->process_initialized(
                            $user_id, 2, $script, 
                            $process_initialized, $command, $from, $pid);
                        
                        $pross_ids[] = array(
                            "id" => $id,
                            "pid" => $pid
                        );
                        
                        //
                        // ------------END START PROCESS ------
                        //
                        
                        // }else{
                        
                        // $pool[$i] = TRUE;
                        // $commande_lancee=TRUE;
                        // break 2;
                        // }
                        
                        // $contadorList++;
                    } else {
                        $etat = @proc_get_status($pool[$i]);
                        
                        if ($etat['running'] == FALSE) {
                            
                            $command = $etat["command"];
                            
                            $filename = substr($command, strrpos($command, ">") + 1);
                            $filename = trim($filename);
                            
                            // echo "==".$filename."----\n";
                            // echo $command."\n\n";
                            // echo $filename."<br>";
                            
                            // echo substr(sprintf('%o', fileperms($filename)), -4)."\n\n";
                            
                            if (is_writable($filename)) {
                                
                                $tagSearch = "moa.DoTask \\";
                                $script = substr($command, strrpos($command, $tagSearch) + strlen($tagSearch) + 2);
                                $script = trim($script);
                                
                                $tagSearch = "\" >";
                                $script = substr($script, 0, strrpos($script, $tagSearch));
                                $script = trim($script);
                                
                                // echo $script."\n\n";
                                
                                $hardwareInfo = $this->getHardwareInfo();
                                
                                $fp = fopen($filename, "r+");
                                rewind($fp);
                                $this->finsert($fp, $script . "\n\n" . $hardwareInfo . "\n\n");
                                fclose($fp);
                                
//                                 $filename = substr($filename, strrpos($filename, "/") + 1);
//                                 $filename = trim($filename);
                                
                                // rename($this->path_tmp_result.$filename, $this->path_real_result.$filename);
                                // echo "From: ".$dirProcess.$filename." - To: ". $dirStorage.$filename."<br><br>";
                                
                                $filename_tmp = $filename;
                                $filename_workspace = $commande["filename"];
                                                                
                                if (file_exists($filename_workspace))
                                {
                                    unlink($filename_workspace);
                                }
                                rename($filename_tmp, $filename_workspace);                        
                                
                            } else {
                                exit("Could not save the file in the directory indicated, perhaps the problem be permission. Please contact your system administrator.\nfile: " . $filename);
                            }
                            
                            $statusProc = proc_get_status($pool[$i]);
                            
                            
                            $data = $jsonfile->getDataKeyValue("pid", $statusProc["pid"]);
                            
                            $data['running'] = false;
                            $data['process'] = true;
                            $data['endtime'] = time();
                            $jsonfile->setDataKeyValue("pid", $statusProc["pid"], $data);
                            $jsonfile->save();
                            $jsonfile->load();
                            
                            
                            
                            
                            
                            
                            
                            
//                             $jsonfile_pool = new JsonFile($commande["filename"]);
//                             $jsonfile_pool->open();
                            
//                             $data = $jsonfile_pool->getDataKeyValue("pid", $statusProc["pid"]);
                            
//                             $data['running'] = false;
//                             $data['process'] = true;
//                             $data['endtime'] = time();
                            
//                             // $jsonfile->setData($data);
                            
//                             $jsonfile_pool->setDataKeyValue("pid", $statusProc["pid"], $data);
                            
//                             $jsonfile_pool->save();
                            
//                             $jsonfile_pool->load();
                            
                            proc_close($pool[$i]);
                            
                            //
                            // ------------CLOSED PROCESS ------
                            //
                            
                            $process_closed = date_create()->format('Y-m-d H:i:s');
                            $pid = $statusProc["pid"];
                            $execution_history_id = null;
                            
                            foreach ($pross_ids as $key => $item)
                            // for($q = 0; $q < count($pross_ids); $q++)
                            {
                                if ($pross_ids[$key]["pid"] == $pid) {
                                    $execution_history_id = $pross_ids[$key]["id"];
                                    unset($pross_ids[$key]);
                                    break;
                                }
                            }
                            
                            if ($execution_history_id != null) {
                                $this->execution_history->closed_process($execution_history_id, $process_closed);
                            }
                            
                            //
                            // ------------END CLOSED PROCESS ------
                            //
                            
                            // $data = $jsonfile->getDataKeyValue("id", $contadorList);
                            
                            // if($data['process']==false){
                            
                            $pool[$i] = proc_open($commande["command"], array(), $foo);
                            $commande_lancee = TRUE;
                            
                            $statusProc = proc_get_status($pool[$i]);
                            
                            $contadorList = substr($commande["command"], strrpos($commande["command"], "/") + 1);
                            $contadorList = substr($contadorList, strrpos($contadorList, "-") + 1); // strrpos($contadorList, "-"));
                            $contadorList = substr($contadorList, 0, strrpos($contadorList, "."));
                            
                            
                            
                            $data = $jsonfile->getDataKeyValue("id", $commande["id"]);
                            
                            $data['pid'] = $statusProc["pid"];
                            $data['running'] = true;
                            $data['process'] = false;
                            $data['starttime'] = time();
                            $jsonfile->setDataKeyValue("id", $commande["id"], $data);
                            $jsonfile->save();
                            $jsonfile->load();
                            
                            
                            
//                             $jsonfile_pool = new JsonFile($commande["filename"]);
//                             $jsonfile_pool->open();
                            
//                             $data = $jsonfile_pool->getDataKeyValue("id", $contadorList);
                            
//                             $data['pid'] = $statusProc["pid"];
//                             $data['running'] = true;
//                             $data['process'] = false;
//                             $data['starttime'] = time();
                            
//                             // $jsonfile->setData($data);
                            
//                             $jsonfile_pool->setDataKeyValue("id", $contadorList, $data);
                            
//                             $jsonfile_pool->save();
                            
//                             $jsonfile_pool->load();
                            
                            //
                            // ------------START PROCESS ------
                            //
                            $script = $commande["script"];
                            $process_initialized = date_create()->format('Y-m-d H:i:s');
                            $command = $commande["command"];
                            $from = $filename;
                            $pid = $statusProc["pid"];
                            
                            $id = $this->execution_history->process_initialized(
                                $user_id, 2, $script, 
                                $process_initialized, $command, $from, $pid);
                            
                            $pross_ids[] = array(
                                "id" => $id,
                                "pid" => $pid
                            );
                            
                            //
                            // ------------END START PROCESS ------
                            //
                            
                            // }else{
                            
                            // $pool[$i] = TRUE;
                            // $commande_lancee=TRUE;
                            // break 2;
                            
                            // }
                            
                            // $contadorList++;
                        }
                    }
                }
            }
        }
        

        
        // Attend que toutes les commandes restantes se terminent
        $fim = FALSE;
        
        while ($fim == FALSE) {
            
            usleep(50000);
            
            $exist_process_running = FALSE;
            $killCount = 1;
            
            for ($i = 0; $i < $nb_max_process; $i ++) {
                
                if (is_resource($pool[$i])) {
                    
                    $etat = proc_get_status($pool[$i]);
                    
                    if ($etat['running'] == FALSE) {
                        
                        // exit("bruno");
                        $command = $etat["command"];
                        
                        $filename = substr($command, strrpos($command, ">") + 1);
                        $filename = trim($filename);
                        
                        // echo $filename."----\n";
                        // echo $command."\n\n";
                        
                        if (is_writable($filename)) {
                            
                            $tagSearch = "moa.DoTask \\";
                            $script = substr($command, strrpos($command, $tagSearch) + strlen($tagSearch) + 2);
                            $script = trim($script);
                            
                            $tagSearch = "\" >";
                            $script = substr($script, 0, strrpos($script, $tagSearch));
                            $script = trim($script);
                            
                            // echo $script."\n\n";
                            
                            // $script = "MOA 2014\n".date("d/m/Y H:i:s")."\n\n";
                                                        
                            $hardwareInfo = $this->getHardwareInfo();
                            
                            $fp = fopen($filename, "r+");
                            rewind($fp);
                            $this->finsert($fp, $script . "\n\n" . $hardwareInfo . "\n\n");
                            fclose($fp);
                            
//                             $filename = substr($filename, strrpos($filename, "/") + 1);
//                             $filename = trim($filename);
                            
//                             // rename($this->path_tmp_result.$filename, $this->path_real_result.$filename);
                            
//                             $dirStorage = substr($commande["filename"], 0,
//                                 strrpos($commande["filename"], DIRECTORY_SEPARATOR)+1);
                                                        
//                             if (file_exists($dirStorage . $filename))
//                                 unlink($dirStorage . $filename);
                                
//                                 rename($dirProcess . $filename, $dirStorage . $filename);

                            $filename_tmp = $filename;
                            $filename_workspace = $commande["filename"];
                            
                            if (file_exists($filename_workspace))
                            {
                                unlink($filename_workspace);
                            }
                            rename($filename_tmp, $filename_workspace);


                        }
                        
                        $statusProc = proc_get_status($pool[$i]);
                        
                        
                        
                        $data = $jsonfile->getDataKeyValue("pid", $statusProc["pid"]);
                        
                        $data['running'] = false;
                        $data['process'] = true;
                        $data['endtime'] = time();
                        $jsonfile->setDataKeyValue("pid", $statusProc["pid"], $data);
                        $jsonfile->save();
                        $jsonfile->load();
                        
                        
                        
//                         $jsonfile_pool = new JsonFile($commande["filename"]);
//                         $jsonfile_pool->open();
                        
//                         $data = $jsonfile_pool->getDataKeyValue("pid", $statusProc["pid"]);
                        
//                         $data['running'] = false;
//                         $data['process'] = true;
//                         $data['endtime'] = time();
                        
//                         // $jsonfile->setData($data);
                        
//                         $jsonfile_pool->setDataKeyValue("pid", $statusProc["pid"], $data);
                        
//                         $jsonfile_pool->save();
                        
//                         $jsonfile_pool->load();
                        
                        proc_close($pool[$i]);
                        
                        //
                        // ------------CLOSED PROCESS ------
                        //
                        
                        $process_closed = date_create()->format('Y-m-d H:i:s');
                        $pid = $statusProc["pid"];
                        $execution_history_id = null;
                        
                        foreach ($pross_ids as $key => $item)
                        // for($q = 0; $q < count($pross_ids); $q++)
                        {
                            if ($pross_ids[$key]["pid"] == $pid) {
                                $execution_history_id = $pross_ids[$key]["id"];
                                unset($pross_ids[$key]);
                                break;
                            }
                        }
                        
                        if ($execution_history_id != null) {
                            $this->execution_history->closed_process($execution_history_id, $process_closed);
                        }
                        
                        //
                        // ------------END CLOSED PROCESS ------
                        //
                    } else {
                        
                        if ($etat['running'] == TRUE) {
                            $exist_process_running = TRUE;
                        }
                    }
                } else {
                    // $killCount++;
                }
            }
            
            // if($killCount==$nb_max_process) {
            // $fim = TRUE;
            // }
            
            if ($exist_process_running == FALSE)
                $fim = TRUE;
        }
        
        // exit("fim-bruno");
    }
    
    
    
    
    /*
    public function pool_execute($filename, $commandes, $nb_max_process, $dirProcess, $dirStorage, $user_id)
    { // }, $filename_source="") {
        $pross_ids = array();

        $pool = array();
        $foo = "";
        // $contadorList=1;

        $jsonfile = new JsonFile($filename);

        $jsonfile->open();

        for ($i = 0; $i < $nb_max_process; $i ++) {
            $pool[$i] = FALSE;
        }

        while (count($commandes) > 0) {
            $commande = array_shift($commandes);

            $commande_lancee = FALSE;
            while ($commande_lancee == FALSE) {
                
                usleep(50000);

                for ($i = 0; $i < $nb_max_process and $commande_lancee == FALSE; $i ++) {

                    if ($pool[$i] === FALSE) {

                        // $data = $jsonfile->getDataKeyValue("id", $contadorList);

                        // if($data['process']==false){

                        $pool[$i] = proc_open($commande, array(), $foo);
                        $commande_lancee = TRUE;

                        $statusProc = proc_get_status($pool[$i]);

                        // $contadorList = substr($commande, strrpos($commande, "/")+1);
                        // $contadorList = substr($contadorList, 0, 4);//strrpos($contadorList, "-"));

                        $contadorList = substr($commande, strrpos($commande, "/") + 1);
                        $contadorList = substr($contadorList, strrpos($contadorList, "-") + 1); // strrpos($contadorList, "-"));
                        $contadorList = substr($contadorList, 0, strrpos($contadorList, "."));

                        $data = $jsonfile->getDataKeyValue("id", $contadorList);

                        $data['pid'] = $statusProc["pid"];
                        $data['running'] = true;
                        $data['process'] = false;
                        $data['starttime'] = time();

                        // $jsonfile->setData($data);

                        $jsonfile->setDataKeyValue("id", $contadorList, $data);

                        $jsonfile->save();

                        $jsonfile->load();

                        //
                        // ------------START PROCESS ------
                        //
                        $script = $data["script"];
                        $process_initialized = date_create()->format('Y-m-d H:i:s');
                        $command = $commande;
                        $from = $filename;
                        $pid = $statusProc["pid"];

                        $id = $this->execution_history->process_initialized($user_id, 2, $script, $process_initialized, $command, $from, $pid);

                        $pross_ids[] = array(
                            "id" => $id,
                            "pid" => $pid
                        );

                        //
                        // ------------END START PROCESS ------
                        //

                        // }else{

                        // $pool[$i] = TRUE;
                        // $commande_lancee=TRUE;
                        // break 2;
                        // }

                        // $contadorList++;
                    } else {
                        $etat = @proc_get_status($pool[$i]);

                        if ($etat['running'] == FALSE) {

                            $command = $etat["command"];

                            $filename = substr($command, strrpos($command, ">") + 1);
                            $filename = trim($filename);

                            // echo "==".$filename."----\n";
                            // echo $command."\n\n";
                            // echo $filename."<br>";

                            // echo substr(sprintf('%o', fileperms($filename)), -4)."\n\n";

                            if (is_writable($filename)) {

                                $tagSearch = "moa.DoTask \\";
                                $script = substr($command, strrpos($command, $tagSearch) + strlen($tagSearch) + 2);
                                $script = trim($script);

                                $tagSearch = "\" >";
                                $script = substr($script, 0, strrpos($script, $tagSearch));
                                $script = trim($script);

                                // echo $script."\n\n";

                                $fp = fopen($filename, "r+");
                                rewind($fp);
                                $this->finsert($fp, $script . "\n\n");
                                fclose($fp);

                                $filename = substr($filename, strrpos($filename, "/") + 1);
                                $filename = trim($filename);

                                // rename($this->path_tmp_result.$filename, $this->path_real_result.$filename);
                                // echo "From: ".$dirProcess.$filename." - To: ". $dirStorage.$filename."<br><br>";

                                if (file_exists($dirStorage . $filename))
                                    unlink($dirStorage . $filename);

                                rename($dirProcess . $filename, $dirStorage . $filename);
                            } else {
                                exit("Could not save the file in the directory indicated, perhaps the problem be permission. Please contact your system administrator.\nfile: " . $filename);
                            }

                            $statusProc = proc_get_status($pool[$i]);

                            $data = $jsonfile->getDataKeyValue("pid", $statusProc["pid"]);

                            $data['running'] = false;
                            $data['process'] = true;
                            $data['endtime'] = time();

                            // $jsonfile->setData($data);

                            $jsonfile->setDataKeyValue("pid", $statusProc["pid"], $data);

                            $jsonfile->save();

                            $jsonfile->load();

                            proc_close($pool[$i]);

                            //
                            // ------------CLOSED PROCESS ------
                            //

                            $process_closed = date_create()->format('Y-m-d H:i:s');
                            $pid = $statusProc["pid"];
                            $execution_history_id = null;

                            foreach ($pross_ids as $key => $item) 
                            // for($q = 0; $q < count($pross_ids); $q++)
                            {
                                if ($pross_ids[$key]["pid"] == $pid) {
                                    $execution_history_id = $pross_ids[$key]["id"];
                                    unset($pross_ids[$key]);
                                    break;
                                }
                            }

                            if ($execution_history_id != null) {
                                $this->execution_history->closed_process($execution_history_id, $process_closed);
                            }

                            //
                            // ------------END CLOSED PROCESS ------
                            //

                            // $data = $jsonfile->getDataKeyValue("id", $contadorList);

                            // if($data['process']==false){

                            $pool[$i] = proc_open($commande, array(), $foo);
                            $commande_lancee = TRUE;

                            $statusProc = proc_get_status($pool[$i]);

                            $contadorList = substr($commande, strrpos($commande, "/") + 1);
                            $contadorList = substr($contadorList, strrpos($contadorList, "-") + 1); // strrpos($contadorList, "-"));
                            $contadorList = substr($contadorList, 0, strrpos($contadorList, "."));

                            $data = $jsonfile->getDataKeyValue("id", $contadorList);

                            $data['pid'] = $statusProc["pid"];
                            $data['running'] = true;
                            $data['process'] = false;
                            $data['starttime'] = time();

                            // $jsonfile->setData($data);

                            $jsonfile->setDataKeyValue("id", $contadorList, $data);

                            $jsonfile->save();

                            $jsonfile->load();

                            //
                            // ------------START PROCESS ------
                            //
                            $script = $data["script"];
                            $process_initialized = date_create()->format('Y-m-d H:i:s');
                            $command = $commande;
                            $from = $filename;
                            $pid = $statusProc["pid"];

                            $id = $this->execution_history->process_initialized($user_id, 2, $script, $process_initialized, $command, $from, $pid);

                            $pross_ids[] = array(
                                "id" => $id,
                                "pid" => $pid
                            );

                            //
                            // ------------END START PROCESS ------
                            //

                            // }else{

                            // $pool[$i] = TRUE;
                            // $commande_lancee=TRUE;
                            // break 2;

                            // }

                            // $contadorList++;
                        }
                    }
                }
            }
        }

        // Attend que toutes les commandes restantes se terminent
        $fim = FALSE;

        while ($fim == FALSE) {

            usleep(50000);

            $exist_process_running = FALSE;
            $killCount = 1;

            for ($i = 0; $i < $nb_max_process; $i ++) {

                if (is_resource($pool[$i])) {

                    $etat = proc_get_status($pool[$i]);

                    if ($etat['running'] == FALSE) {

                        // exit("bruno");
                        $command = $etat["command"];

                        $filename = substr($command, strrpos($command, ">") + 1);
                        $filename = trim($filename);

                        // echo $filename."----\n";
                        // echo $command."\n\n";

                        if (is_writable($filename)) {

                            $tagSearch = "moa.DoTask \\";
                            $script = substr($command, strrpos($command, $tagSearch) + strlen($tagSearch) + 2);
                            $script = trim($script);

                            $tagSearch = "\" >";
                            $script = substr($script, 0, strrpos($script, $tagSearch));
                            $script = trim($script);

                            // echo $script."\n\n";

                            // $script = "MOA 2014\n".date("d/m/Y H:i:s")."\n\n";

                            $fp = fopen($filename, "r+");
                            rewind($fp);
                            $this->finsert($fp, $script . "\n\n");
                            fclose($fp);

                            $filename = substr($filename, strrpos($filename, "/") + 1);
                            $filename = trim($filename);

                            // rename($this->path_tmp_result.$filename, $this->path_real_result.$filename);

                            if (file_exists($dirStorage . $filename))
                                unlink($dirStorage . $filename);

                            rename($dirProcess . $filename, $dirStorage . $filename);
                        }

                        $statusProc = proc_get_status($pool[$i]);

                        $data = $jsonfile->getDataKeyValue("pid", $statusProc["pid"]);

                        $data['running'] = false;
                        $data['process'] = true;
                        $data['endtime'] = time();

                        // $jsonfile->setData($data);

                        $jsonfile->setDataKeyValue("pid", $statusProc["pid"], $data);

                        $jsonfile->save();

                        $jsonfile->load();

                        proc_close($pool[$i]);

                        //
                        // ------------CLOSED PROCESS ------
                        //

                        $process_closed = date_create()->format('Y-m-d H:i:s');
                        $pid = $statusProc["pid"];
                        $execution_history_id = null;

                        foreach ($pross_ids as $key => $item) 
                        // for($q = 0; $q < count($pross_ids); $q++)
                        {
                            if ($pross_ids[$key]["pid"] == $pid) {
                                $execution_history_id = $pross_ids[$key]["id"];
                                unset($pross_ids[$key]);
                                break;
                            }
                        }

                        if ($execution_history_id != null) {
                            $this->execution_history->closed_process($execution_history_id, $process_closed);
                        }

                        //
                        // ------------END CLOSED PROCESS ------
                        //
                    } else {

                        if ($etat['running'] == TRUE) {
                            $exist_process_running = TRUE;
                        }
                    }
                } else {
                    // $killCount++;
                }
            }

            // if($killCount==$nb_max_process) {
            // $fim = TRUE;
            // }

            if ($exist_process_running == FALSE)
                $fim = TRUE;
        }

        // exit("fim-bruno");
    }
    
    
    */
            
            
            
}

?>