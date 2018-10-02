<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\components\settings;

defined('_EXEC') or die();

use moam\core\Framework;
use moam\core\Application;
use moam\core\Properties;
use moam\libraries\core\utils\Utils;


if (! class_exists('Application')) {
    $application = Framework::getApplication();
}

if (! $application->is_authentication() || $application->getUserType() != 1) {
    $application->alert("Error: you do not have credentials.");
}

Framework::import("Utils", "core/utils");

$utils = new Utils();




//https://github.com/brunom4ciel/moamanager/blob/master/index.php




$dirProcess = Properties::getBase_directory_destine_exec()
// .$application->getUser()
// .DIRECTORY_SEPARATOR
;

$chmod = "0777";

$tmp_update = $dirProcess . "repository/";

if(is_dir($tmp_update))
{
    $utils->delTree($tmp_update);  
    
}else{
    if(mkdir($tmp_update, octdec($chmod), true))
    {
        chmod($tmp_update, octdec($chmod));
    }
}


                                
$commandes = array();
// $commandes[] = array("command"=>"rm -fr " . $dirProcess . "update". " 2> " . $dirProcess . "rRm.txt");
// $commandes[] = array("command"=>"mkdir " . $dirProcess . "update". " 2> " . $dirProcess . "rMkdir.txt");
//$commandes[] = array("command"=>"git clone https://github.com/brunom4ciel/moamanager/ " . $dirProcess . "repository/moamanager/" . " 2> " . $dirProcess . "rGitClone.txt");
$commandes[] = array("command"=>"sh " . $dirProcess . "repository/moamanager/setup/update-latest.sh" . " 2> " . $dirProcess . "rMOAManagerUpdate.txt");
$commandes[] = array("command"=>"rm -fr " . $dirProcess . "repository");
$commandes[] = array("command"=>"rm -fr " . $dirProcess . "rGitClone.txt");
$commandes[] = array("command"=>"rm -fr " . $dirProcess . "rMOAManagerUpdate.txt");

$commandes_aux = $commandes;

$nb_max_process = 1;
$filename = "";
$content = array();

for ($i = 0; $i < $nb_max_process; $i ++) {
    $pool[$i] = FALSE;
}

while (count($commandes) > 0)
{
    $commande = array_shift($commandes);       
    
    $commande_lancee = FALSE;
    while ($commande_lancee == FALSE) {
        
        sleep(1);        
        
        for ($i = 0; $i < $nb_max_process and $commande_lancee == FALSE; $i ++) {
            
            if ($pool[$i] === FALSE) {
                
                $pool[$i] = proc_open($commande["command"], array(), $foo);
                $commande_lancee = TRUE;
                
                if(strrpos($commande["command"], ">") === FALSE)
                {
                    $filename = null;
                }
                else 
                {
                    $filename = substr($commande["command"], strrpos($commande["command"], ">") + 1);
                    $filename = trim($filename);
                }
                
                                               
        } else {
            $etat = @proc_get_status($pool[$i]);
            
            if ($etat['running'] == FALSE) {
                                
                proc_close($pool[$i]);
                
                if($filename != null)
                {
                    $content[] = $filename . "\n" . file_get_contents($filename);
                }else
                {
                    $content[] = "";
                }
                
                $pool[$i] = proc_open($commande["command"], array(), $foo);
                $commande_lancee = TRUE;
                
                $filename = substr($commande["command"], strrpos($commande["command"], ">") + 1);
                $filename = trim($filename);
                
            }
        }
        }
    }
}



// Attend que toutes les commandes restantes se terminent
$fim = FALSE;

while ($fim == FALSE) {
    
    sleep(1);
    
    $exist_process_running = FALSE;
    $killCount = 1;
    
    for ($i = 0; $i < $nb_max_process; $i ++) {
        
        if (is_resource($pool[$i])) {
            
            $etat = proc_get_status($pool[$i]);
            
            if ($etat['running'] == FALSE) {
                
                proc_close($pool[$i]);                
                //$content[] .= $filename . "\n" . file_get_contents($filename);                
                
                if($filename != null)
                {
                    $content[] = $filename . "\n" . file_get_contents($filename);
                }else
                {
                    $content[] = "";
                }
                
            } else {
                
                if ($etat['running'] == TRUE) {
                    $exist_process_running = TRUE;
                }
            }
        }
    }
    
    
    if ($exist_process_running == FALSE)
    {
        $fim = TRUE;
    }
}




echo "Finished Update\n";
//echo "tmp path: " . $dirProcess . "\n";
for($i = 0; $i < count($commandes_aux); $i++)
{
    echo "command :" . $commandes_aux[$i]["command"] . "\n";
    echo $content[$i] . "\n";
}

?>