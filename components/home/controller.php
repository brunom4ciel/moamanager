<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\components\home;

use moam\core\Framework;
// use moam\core\Application;
// use moam\libraries\core\menu\Menu;
// use moam\core\Template;
use moam\libraries\core\utils\Utils;
use moam\core\Properties;
use moam\libraries\core\db\DBPDO;
// use PDOException;
use PDO;

exit("home");

if (! class_exists('Application')) {
    $application = Framework::getApplication();
}

if (!$application->is_authentication()) {
//     $application->alert("Error: you do not have credentials.");
//     ?component=user&controller=login
//     $application->redirect("?component=user&controller=login");
}
else{

    $application->redirect("?component=systemmonitor");
}

// Framework::import("menu", "core/menu");

Framework::import("Utils", "core/utils");
Framework::import("DBPDO", "core/db");
// Framework::import("class_CPULoad", "core/sys");

// Template::addHeader(array("tag"=>"script",
//     "type"=>"text/javascript",
//     "src"=>""
//     . $application->getPathTemplate()
//     . "/javascript/base64.js"));

// Template::setDisabledMenu(true);

$db = new DBPDO(Properties::getDatabaseName(),
    Properties::getDatabaseHost(),
    Properties::getDatabaseUser(),
    Properties::getDatabasePass());

$utils = new Utils();

// if (! class_exists('Menu')) {
//     $menu = new Menu();
// }

// Template::addHeader(array(
//     "tag" => "script",
//     "type" => "text/javascript",
//     "src" => "" . PATH_WWW . "templates/default/javascript/json-to-table.js"
// ));


// Template::addHeader(array("tag"=>"link",
// "type"=>"text/css",
// "rel"=>"stylesheet",
// "href"=>"" . PATH_WWW . "templates/default/css/style2.css"));

// Template::setTitle("Teste");

// $menu = Framework::getInstance("Menu");

// $application = Framework::getApplication();

// $time = $application->getParameter("time");

// if (! empty($time))
// {
//     sleep($time);
// }

    
// $cmd = 'java -Xmx768M -cp "/opt/moamanager/moa/bin/moa2014.jar:/opt/moamanager/moa/lib/*" -javaagent:/opt/moamanager/moa/lib/sizeofag-1.0.0.jar moa.DoTask \ "EvaluatePrequential2 -l (drift.SingleClassifierDrift -l bayes.NaiveBayes -d DDM) -s (ConceptDriftStream -s (ConceptDriftStream -s (ConceptDriftStream -s (ConceptDriftStream -s (generators.AgrawalGenerator -f 1 -p 1.0) -d (generators.AgrawalGenerator -f 2) -p 2000 -w 1000) -d (generators.AgrawalGenerator -f 3) -p 4000 -w 1000) -d (generators.AgrawalGenerator -f 4) -p 6000 -w 1000) -d (generators.AgrawalGenerator -f 5) -p 8000 -w 1000) -r 2 -c -i 10000 -f 10 -q 10" > /var/www/moamanagerdata/exec/brunom4ciel@gmail.com/testerro-0001-1.txt';
// $s = $utils->runExternal($cmd);    

// var_dump($s);

?>


<pre style="width:350px;float:left;font-family: monospace,monospace;font-size: 11px;text-aling:left;padding:0px;vertical-align: top;">
<?php 



    
    $data_db = $db->prep_query("SELECT 

DATE_FORMAT(date(execution_history.process_closed), '%d/%m/%Y') dates
, count(execution_history.execution_history_id) qtd 

FROM moamanager.execution_history
inner join user on execution_history.user_id = user.user_id

where execution_history.process_closed is not null

and DATEDIFF(NOW(), execution_history.process_closed) < 45

group by dates

order by dates asc;");
        
    if ($data_db->execute()) {
        $data_db = $data_db->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($data_db)> 0) {
            
            echo "Top last 45 days\n";
            echo "Date\t\tNumber of scripts processed\n";
            
            foreach($data_db as $row)
            {
                echo $row['dates'] . "\t" . $row['qtd'] . "\n";
            }
        }
    } 

    ?>
</pre>


<pre style="width:60%;float:left;font-family: monospace,monospace;font-size: 11px;text-aling:left;padding:0px;vertical-align: top;">
<?php 
    $data_db = $db->prep_query("SELECT

TIMESTAMPDIFF(SECOND, execution_history.process_initialized, execution_history.process_closed) toplast, execution_history.script
, execution_history.process_initialized, execution_history.process_closed
FROM moamanager.execution_history

order by toplast desc limit 0,5");
    
    if ($data_db->execute()) {
        $data_db = $data_db->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($data_db)> 0) {
            
            echo "Most time-consuming script\n";
           // echo "Time\t\tScript\n";
            
            foreach($data_db as $row)
            {
                $secs = $row['toplast'];
                
                $bit = array(
                    'y' => $secs / 31556926 % 12,
                    'w' => $secs / 604800 % 52,
                    'd' => $secs / 86400 % 7,
                    'h' => $secs / 3600 % 24,
                    'm' => $secs / 60 % 60,
                    's' => $secs % 60
                );
                
                $ret = array();
                
                foreach($bit as $k => $v)
                {
                    if($v > 0)
                    {
                        $ret[] = $v . $k;
                    }
                }
                
                $s = join(' ', $ret);
                
//                 $s = $row['toplast'];
//                 $l = "minutes";
                
//                 if($s > 59)
//                 {
//                     $s = $s / 60;
//                     $l = "hours";
                    
//                     if($s > 23)
//                     {
//                         $s = floor($s / 60);
//                         $l = "days";
//                     }
//                 }
                
                echo "<b>cpu time ".$s . " " . $row['process_initialized'] 
                . " - " . $row['process_closed'] . "</b> \n" 
			    . wordwrap($row['script'], 80, "\n") . "\n\n";
            }
        }
    }
    
    
    
?>

</pre>  


