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
use moam\libraries\core\date\DateTimeFormats;

// Framework::import("Utils", "core/utils");
Framework::import("execution_history", "core/log");
Framework::import("DBPDO", "core/db");
Framework::import("DateTimeFormats", "core/date");

class ParallelProcess extends Utils
{

    public $base_directory_destine;

    public $base_directory_destine_exec;

    private $execution_history;
    
    private $app_name = "";
    private $app_version = "";
    private $app_release = "";
    private $datetimeformat = null;
    
    public function __construct()
    {
        $DB = new DBPDO(
            Properties::getDatabaseName(), 
            Properties::getDatabaseHost(), 
            Properties::getDatabaseUser(), 
            Properties::getDatabasePass());

        $this->execution_history = new ExecutionHistory($DB);
        
        if(defined('APPLICATION_NAME'))
        {
            $this->app_name = APPLICATION_NAME;
        }
        
        if(defined('MOAMANAGER_VERSION'))
        {
            $this->app_version = MOAMANAGER_VERSION;
        }
        
        if(defined('MOAMANAGER_RELEASES'))
        {
            $this->app_release = MOAMANAGER_RELEASES;
        }
     
        $this->datetimeformat = new DateTimeFormats();
        
    }

    private function tagMetadata($metadata = array(), $attribute = "moamanager")
    {
        $result = array();
        
        foreach($metadata as $item)
        {
            foreach($item as $key=>$value)
            {
                if(strpos($value, '"') === FALSE)
                {
                    
                }
                else
                {
                    $value = str_replace('"', '""', $value);
                }
                
                $result[] = "<meta-data " . $attribute . ":name=\"" . $key . "\" "
                    . $attribute . ":value=\"" . $value . "\"/>";
            }
        }            
        
        return $result;
    }
    
    private function metadata($opts=array())//$filename = "", $script = "", ="", $output, $cpustarttime=0, $username = "")
    {
        $filename = $opts["filename"];
        $script = $opts["script"];
        $input = $opts["command"];
        $output = $opts["output"];
        $username = $opts["username"];
        $cpustarttime = $opts["timestart"];
        $ramusage = $opts["ramusagem"];
        $cpuusage = $opts["cpuusagem"];     
        
        
        if(file_exists($filename))
        {
            $hash_file = hash_hmac_file('md5', $filename, $script);
        }
        else
        {
            $hash_file = "file not found";
        }
        
                
        $filename_ = substr($input, strrpos($input, ">") + 1);
        $filename_ = substr($filename_, strrpos($filename_, DIRECTORY_SEPARATOR)+1);        
        $filename_ex = substr($filename_, strrpos($filename_, "."));
        $filename_ = substr($filename_, 0, strrpos($filename_, "-"));
        $filename_ = $filename_ . $filename_ex;        
        $filename_ = trim($filename_);
                
        
        $startime = date("Y-m-d H:i:s", $cpustarttime);
        $endtime = date("Y-m-d H:i:s", time());
        
        
        $result = $this->datetimeformat->date_diff($startime, $endtime, array(
            "d",
            "h",
            "i",
            "s"
        )); // i-minutes
        
        $secs = 0;
        
        if(!empty($result['d']))
        {
            $secs = ((int) $result['d']) * 86400;
        }
        
        if(!empty($result['h']))
        {
            $secs += ((int) $result['h']) * 3600;
        }
        
        if(!empty($result['i']))
        {
            $secs += ((int) $result['i']) * 60;
        }
        
        if(!empty($result['s']))
        {
            $secs += ((int) $result['s']);
        }
        
//         $secs = $result['s'];
        $diff_dates = $this->formatDatetime($secs);
        
        
        $metadata = array();
        $metadata[] = array("software-name"=>$this->app_name);
        $metadata[] = array("software-version"=>$this->app_version);
        $metadata[] = array("software-release"=>$this->app_release);
        $metadata[] = array("software-copyright"=>"(C) 2015-2018 CIn (Informatic Center) of UFPE (Federal University of Pernambuco), Pernambuco, Brazil");
        $metadata[] = array("software-web"=>"https://github.com/brunom4ciel/moamanager");
        
        $metadata[] = array("user-owner"=>$username);
        
        $metadata[] = array("script-data"=>trim($script));
        $metadata[] = array("script-original-filename"=>trim($filename_));
        
        $metadata[] = array("script-cpu-datetime-start"=>$startime);
        $metadata[] = array("script-cpu-datetime-end"=>$endtime);
        $metadata[] = array("script-cpu-time"=>$diff_dates);        
        $metadata[] = array("script-cpu-usage-start"=>$cpuusage . "%");
        $metadata[] = array("script-cpu-usage-end"=>$this->getHardwareCpuUsage() . "%");
        
        $metadata[] = array("script-ram-usage-start"=>$ramusage . "%");        
        $metadata[] = array("script-ram-usage-end"=>$this->getHardwareMemoryRamUsage() . "%");
        
        $metadata[] = array("hardware-cpu"=>$this->getHardwareCpuName());
        $metadata[] = array("hardware-ram"=>$this->getHardwareMemory());

        $metadata[] = array("hardware-disk"=>$this->getHardwareDisk());
        $metadata[] = array("hardware-disk-usage"=>$this->getHardwareDiskUsage());
        $metadata[] = array("hardware-disk-free"=>$this->getHardwareDiskFree());
        
        $metadata[] = array("os-system"=>$this->getHardwareKernelVersion());
        $metadata[] = array("os-uptime"=>$this->getHardwareUptime());
        
        $metadata[] = array("command-input"=>trim($input));
        $metadata[] = array("command-output"=>trim($output));
        
        $metadata[] = array("security-hash-hmac-file"=>$hash_file);
        
        
        $result = implode("\n",$this->tagMetadata($metadata));
                                
        return $result."\n\n";
    }
        

    public function pool_execute2($filename, $nb_max_process, $user_id, $interfacename="moa.DoTask", $username = "")
    { // }, $filename_source="") {
        $pross_ids = array();
        $descriptorspec = array(
            0 => array(
                "pipe",
                "r"
            ), // stdin is a pipe that the child will read from
            1 => array(
                "pipe",
                "w"
            ), // stdout is a pipe that the child will write to
            2 => array(
                "pipe",
                "w"
            ) // stderr is a file to write to
        );
        $pool = array();
        $pipes = array();
        $output = array();
        $tmpfile = array();
        $tmpfilehandle = array();
        $cpu = array();
        $ram = array();
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
        
        while (count($commandes) > 0) 
        {
            $commande = array_shift($commandes);            
                        
                                  
            
            
            $commande_lancee = FALSE;
            while ($commande_lancee == FALSE) {
                
                //usleep(50000);
                //sleep(1);
                
                while(file_exists($commande["filename"]))
                {
                    //not reprocess
                    $commande = array_shift($commandes);
                } 
                
                
                for ($i = 0; $i < $nb_max_process and $commande_lancee == FALSE; $i ++) 
                {
                    
//                     if(!isset($pool[$i]))
//                     {
//                         $pool[$i] = FALSE;
//                     }
                    
                    if ($pool[$i] === FALSE) {
                        
                        // $data = $jsonfile->getDataKeyValue("id", $contadorList);
                        
                        // if($data['process']==false){
                        
                        $pool[$i] = proc_open($commande["command"], $descriptorspec, $pipes[$i]);
                        $commande_lancee = TRUE;
                        
                        $tmpfilehandle[$i] = tmpfile();//tempnam(sys_get_temp_dir(), "file-chainer");
                        
                        
                        $ram[$i] = $this->getHardwareMemoryRamUsage();
                        $cpu[$i] = $this->getHardwareCpuUsage();                        
                        $output[$i] = "";
                        
                        // close child's input imidiately
                        fclose($pipes[$i][0]);
                        
                        stream_set_blocking($pipes[$i][1], false);
                        stream_set_blocking($pipes[$i][2], false);
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        $statusProc = proc_get_status($pool[$i]);

                        
                        $data = $jsonfile->getDataKeyValue("id", $commande["id"]);
                        
                        $data['pid'] = $statusProc["pid"];
                        $data['running'] = true;
                        $data['process'] = false;
                        $data['starttime'] = time();                        
                        $jsonfile->setDataKeyValue("id", $commande["id"], $data);                        
                        $jsonfile->save();                        
                        $jsonfile->load();
                        
                        
                        $command = $commande["command"];
                        $filename = substr($command, strrpos($command, ">") + 1);
                        $filename = trim($filename);
                        
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

                        
                        // $contadorList++;
                    } 
                    else 
                    {
                        $etat = @proc_get_status($pool[$i]);
                        
                        if ($etat['running'] == FALSE) 
                        {
                            
                            fclose($pipes[$i][1]);
                            fclose($pipes[$i][2]);
                            
                            
                            
                            $command = $etat["command"];
                            
                            $filename = substr($command, strrpos($command, ">") + 1);
                            $filename = trim($filename);

                            if(strpos($filename, "-") !== false)
                            {
                                $idSeq = substr($filename, strrpos($filename, "-")+1);
                                $idSeq = substr($idSeq,0, strrpos($idSeq, "."));
                                $idSeq = trim($idSeq);
                                $idSeq = (int) $idSeq;
                            }
                            
                            $statusProc = proc_get_status($pool[$i]);     
                            
                            $data = $jsonfile->getDataKeyValue("id", $idSeq);
//                             $data = $jsonfile->getDataKeyValue("pid", $statusProc["pid"]);  
                            
                            $data['running'] = false;
                            $data['process'] = true;
                            $data['endtime'] = time();
                            
                            $jsonfile->setDataKeyValue("id", $idSeq, $data);  
//                             $jsonfile->setDataKeyValue("pid", $statusProc["pid"], $data);
                            $jsonfile->save();
                            $jsonfile->load();
                            
                            
                            if (is_writable($filename))
                            {
                                $tagSearch = $interfacename . " \\";
                                $script = substr($command, strrpos($command, $tagSearch) + strlen($tagSearch) + 2);
                                $script = trim($script);
                                
                                $tagSearch = "\" >";
                                $script = substr($script, 0, strrpos($script, $tagSearch));
                                $script = trim($script);
                                
                                // echo $script."\n\n";
                                
                                $opts = array();
                                $opts["filename"] = $filename;
                                $opts["script"] = $script;
                                $opts["command"] = $command;                                
                                
                                
                                if(is_resource($tmpfilehandle[$i]))
                                {
                                    rewind($tmpfilehandle[$i]);
                                    $s = "";
                                    
                                    while (($buffer = fgets($tmpfilehandle[$i], 1024)) !== false) 
                                    {
                                        $s .= $buffer;
                                    }
                                    
                                    $opts["output"] = base64_encode(gzcompress($s, 9));//$output[$i];
                                    
                                    fclose($tmpfilehandle[$i]);
                                    
                                }
                                else 
                                {
                                    $opts["output"] = "";//$output[$i];
                                }
                                
                                $opts["username"] =  $username;
                                $opts["timestart"] =  $data['starttime'];
                                $opts["ramusagem"] =  $ram[$i];
                                $opts["cpuusagem"] =  $ram[$i];                                
                                
                                $metadata = $this->metadata($opts);
                                        
                                $fp = fopen($filename, "r+");
                                rewind($fp);
                                $this->finsert($fp, $metadata);
                                fclose($fp);
                                
                                
//                                 $idSeq = substr($filename, strrpos($filename, "-")+1);
//                                 $idSeq = substr($idSeq,0, strrpos($idSeq, "."));
//                                 $idSeq = trim($idSeq);
                                                              
                                $data = $jsonfile->getDataKeyValue("id", $idSeq);
                                
                                $filename_tmp = $filename;
                                $filename_workspace = $data['filename'];
                                                                
                                if (file_exists($filename_workspace))
                                {
                                    unlink($filename_workspace);
                                }
                                
                                rename($filename_tmp, $filename_workspace);        
                                
                                
                                
                            } else {
                                exit("Could not save the file in the directory indicated, perhaps the problem be permission. Please contact your system administrator.\nfile: " . $filename);
                            }
                            
                            
                            
                            
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
                                if ($pross_ids[$key]["pid"] == $pid) 
                                {
                                    $execution_history_id = $pross_ids[$key]["id"];
                                    unset($pross_ids[$key]);
                                    break;
                                }
                            }
                            
                            if ($execution_history_id != null) 
                            {
                                $this->execution_history->closed_process($execution_history_id, $process_closed);
                            }
                            
                            //
                            // ------------END CLOSED PROCESS ------
                            //
                            
                            // $data = $jsonfile->getDataKeyValue("id", $contadorList);
                            
                            // if($data['process']==false){
                            
                            $pool[$i] = proc_open($commande["command"], $descriptorspec, $pipes[$i]);
                            $commande_lancee = TRUE;
                            
                            $tmpfilehandle[$i] = tmpfile();
                            
//                             $tmpfile[$i] = tempnam(sys_get_temp_dir(), "file-chainer");
                            
//                             if(is_writable($tmpfile[$i]))
//                             {
//                                 $tmpfilehandle[$i] = fopen($tmpfile[$i], "a");
//                             }
                            
                            
                            
                            $ram[$i] = $this->getHardwareMemoryRamUsage();
                            $cpu[$i] = $this->getHardwareCpuUsage();   
                            $output[$i] = "";
                            
                            // close child's input imidiately
                            fclose($pipes[$i][0]);
                            
                            stream_set_blocking($pipes[$i][1], false);
                            stream_set_blocking($pipes[$i][2], false);
                            
                            
                            
                            $statusProc = proc_get_status($pool[$i]);
                            
                            
                            $data = $jsonfile->getDataKeyValue("id", $commande["id"]);
                            
                            $data['pid'] = $statusProc["pid"];
                            $data['running'] = true;
                            $data['process'] = false;
                            $data['starttime'] = time();
                            $jsonfile->setDataKeyValue("id", $commande["id"], $data);
                            $jsonfile->save();
                            $jsonfile->load();
                            
                            
                            
                            $command = $commande["command"];
                            $filename = substr($command, strrpos($command, ">") + 1);
                            $filename = trim($filename);
                            
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
                            
                        }
                        
                        
                        
                        
                        
                        
                        
                        $read = array();
                        
                        if (! feof($pipes[$i][1]))
                        {
                            $read[] = $pipes[$i][1];
                        }
                        if (! feof($pipes[$i][2]))
                        {       
                            $read[] = $pipes[$i][2];
                        }
                        
                        if ($read)
                        {            
                            $ready = @stream_select($read, $write = NULL, $ex = NULL, 2);
                            
                            if ($ready === false)
                            {
                                // should never happen - something died
                                
                            }else 
                            {
                                foreach ($read as $r)
                                {
                                    $s = fread($r, 1024);
//                                     $output[$i] .= $s;
                                    
                                    if(is_resource($tmpfilehandle[$i]))
                                    {
                                        fwrite($tmpfilehandle[$i], $s);
                                    }
                                }
                            }
                            
                            
                        }
                        
                        
                                    
                                    
                    }
                }
            }
        }
        

        
        // Attend que toutes les commandes restantes se terminent
        $fim = FALSE;
        
        while ($fim == FALSE) 
        {
            
            //usleep(50000);
            
            $exist_process_running = FALSE;
            $killCount = 1;
            
            for ($i = 0; $i < $nb_max_process; $i ++) 
            {
                
                if (is_resource($pool[$i])) 
                {
                    
                    
                    
                    
                    $etat = proc_get_status($pool[$i]);
                    
                    if ($etat['running'] == FALSE) 
                    {
                        
                        
                        fclose($pipes[$i][1]);
                        fclose($pipes[$i][2]);
                        
//                         fclose($tmpfilehandle[$i]);
                        
                        // exit("bruno");
                        $command = $etat["command"];
                        
                        $filename = substr($command, strrpos($command, ">") + 1);
                        $filename = trim($filename);
                        
                        // echo $filename."----\n";
                        // echo $command."\n\n";
                        
                        $statusProc = proc_get_status($pool[$i]);
                        $data = $jsonfile->getDataKeyValue("pid", $statusProc["pid"]);
                        $data['running'] = false;
                        $data['process'] = true;
                        $data['endtime'] = time();
                        $jsonfile->setDataKeyValue("pid", $statusProc["pid"], $data);
                        $jsonfile->save();
                        $jsonfile->load();
                        
                        
                        if (is_writable($filename))
                        {
                            $tagSearch = $interfacename . " \\";
                            $script = substr($command, strrpos($command, $tagSearch) + strlen($tagSearch) + 2);
                            $script = trim($script);
                            
                            $tagSearch = "\" >";
                            $script = substr($script, 0, strrpos($script, $tagSearch));
                            $script = trim($script);
                            
                            
                            $opts = array();
                            $opts["filename"] = $filename;
                            $opts["script"] = $script;
                            $opts["command"] = $command;
                            
                            
                            
                            if(is_resource($tmpfilehandle[$i]))
                            {
                                //exit("bruno2");
                                rewind($tmpfilehandle[$i]);
                                $s = "";
                                
                                while (($buffer = fgets($tmpfilehandle[$i], 1024)) !== false) 
                                {
                                    $s .= $buffer;
                                }
                                
                                $opts["output"] = base64_encode(gzcompress($s, 9));//$output[$i];
                                
                                fclose($tmpfilehandle[$i]);
                                
                            }else
                            {
                                $opts["output"] = "";//$output[$i];
                            }
                            
                            
                            $opts["username"] =  $username;
                            $opts["timestart"] =  $data['starttime'];
                            $opts["ramusagem"] =  $ram[$i];
                            $opts["cpuusagem"] =  $ram[$i];
                            
                            $metadata = $this->metadata($opts);
                            
                            $fp = fopen($filename, "r+");
                            rewind($fp);
                            $this->finsert($fp, $metadata);
                            fclose($fp);
                            
                            
                            $idSeq = substr($filename, strrpos($filename, "-")+1);
                            $idSeq = substr($idSeq,0, strrpos($idSeq, "."));
                            $idSeq = trim($idSeq);

                            $data = $jsonfile->getDataKeyValue("id", $idSeq);
                            
                            $filename_tmp = $filename;
                            $filename_workspace = $data['filename'];
                            
                            if (file_exists($filename_workspace))
                            {
                                unlink($filename_workspace);
                            }
                            
                            rename($filename_tmp, $filename_workspace);


                        }
                        
                        
                        
                        
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
                            if ($pross_ids[$key]["pid"] == $pid) 
                            {
                                $execution_history_id = $pross_ids[$key]["id"];
                                unset($pross_ids[$key]);
                                break;
                            }
                        }
                        
                        if ($execution_history_id != null) 
                        {
                            $this->execution_history->closed_process($execution_history_id, $process_closed);
                        }
                        
                        //
                        // ------------END CLOSED PROCESS ------
                        //
                    } 
                    else 
                    {
                        
                        $read = array();
                    
						if (! feof($pipes[$i][1]))
						{
							$read[] = $pipes[$i][1];
						}
						if (! feof($pipes[$i][2]))
						{
							$read[] = $pipes[$i][2];
						}
						
						if ($read)
						{
							$ready = @stream_select($read, $write = NULL, $ex = NULL, 2);
							
							if ($ready === false)
							{
								// should never happen - something died
								
							}
							else
							{
								foreach ($read as $r)
								{
									$s = fread($r, 1024);
	//                                 $output[$i] .= $s;
									
									if(is_resource($tmpfilehandle[$i]))
									{
										fwrite($tmpfilehandle[$i], $s);
									}
									
								}
							}                        
							
						}
						
						
                    
                        if ($etat['running'] == TRUE) 
                        {
                            $exist_process_running = TRUE;
                        }
                    }
                } 
                else 
                {
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
    
            
            
}

?>
