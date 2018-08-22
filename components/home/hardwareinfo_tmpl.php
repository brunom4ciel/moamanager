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
use moam\core\Properties;
use moam\libraries\core\utils\Utils;
use moam\libraries\core\sys\CPULoad;
if (! class_exists('Application')) {
    $application = Framework::getApplication();
}

if (! $application->is_authentication()) {
    $application->alert("Error: you do not have credentials.");
}

Framework::import("Utils", "core/utils");
Framework::import("class_CPULoad", "core/sys");

$utils = new Utils();


/* Gets individual core information */
function GetCoreInformation() {
    $data = file('/proc/stat');
    $cores = array();
    foreach( $data as $line ) {
        if( preg_match('/^cpu[0-9]/', $line) )
        {
            $info = explode(' ', $line );
            $cores[] = array(
                'user' => $info[1],
                'nice' => $info[2],
                'sys' => $info[3],
                'idle' => $info[4]
            );
        }
    }
    return $cores;
}
/* compares two information snapshots and returns the cpu percentage */
function GetCpuPercentages($stat1, $stat2) {
    if( count($stat1) !== count($stat2) ) {
        return;
    }
    $cpus = array();
    for( $i = 0, $l = count($stat1); $i < $l; $i++) {
        $dif = array();
        $dif['user'] = $stat2[$i]['user'] - $stat1[$i]['user'];
        $dif['nice'] = $stat2[$i]['nice'] - $stat1[$i]['nice'];
        $dif['sys'] = $stat2[$i]['sys'] - $stat1[$i]['sys'];
        $dif['idle'] = $stat2[$i]['idle'] - $stat1[$i]['idle'];
        $total = array_sum($dif);
        $cpu = array();
        foreach($dif as $x=>$y) $cpu[$x] = round($y / $total * 100, 1);
        $cpus['cpu' . $i] = $cpu;
    }
    return $cpus;
}


/* get core information (snapshot) */
$stat1 = GetCoreInformation();
/* sleep on server for one second */
sleep(1);
/* take second snapshot */
$stat2 = GetCoreInformation();
/* get the cpu percentage based off two snapshots */
$data = GetCpuPercentages($stat1, $stat2);



/* makes a google image chart url */
function makeImageUrl($title, $data) {
    $url = 'http://chart.apis.google.com/chart?chs=220x190&cht=pc&chco=0062FF|498049|F2CAEC|D7D784&chd=t:';
    $url .= $data['user'] . ',';
    $url .= $data['nice'] . ',';
    $url .= $data['sys'] . ',';
    $url .= $data['idle'];
    $url .= '&chdl=User|Nice|Sys|Idle&chdlp=b&chl=';
    $url .= $data['user'] . '%25|';
    $url .= $data['nice'] . '%25|';
    $url .= $data['sys'] . '%25|';
    $url .= $data['idle'] . '%25';
    $url .= '&chtt=Core+' . $title;
    return $url;
}


/* makes a google image chart url */
function makeImageUrl2($title, $data) {
    $url = 'http://chart.apis.google.com/chart?chs=200x160&cht=pc&chco=FF0000|00FF00&chd=t:';
    $url .= $data['usage'] . ',';
    $url .= $data['free'] . '';
    $url .= '&chdl=Usage|Free&chdlp=b&chl=';
    $url .= $data['usage'] . '%25|';
    $url .= $data['free'] . '%25';
    $url .= '&chtt=' . $title;
    return $url;
}


$json_result = array();

function get_memory()
{
    foreach (file('/proc/meminfo') as $ri)
        $m[strtok($ri, ':')] = strtok('');
        return 100 - round(($m['MemFree'] + $m['Buffers'] + $m['Cached']) / $m['MemTotal'] * 100);
}

$stat1 = array();
$stat2 = array();

$stat1[0] = get_memory();
$stat1[1] = 100 - get_memory();

// sleep(1);

// $stat2[0] = get_memory();
// $stat2[1] = 100 - get_memory();

$ram_usage = $stat1[0];// - $stat1[0];
$ram_free = $stat1[1];// - $stat1[1];

// var_dump($ram_usage);exit();

$v = array("usage"=>"".$ram_usage."",
    "free"=>"".$ram_free . "");

$json_result["ram"][] = base64_encode(makeImageUrl2( "RAM", $v ));


// $v = array("usage"=>$utils->getha,
//     "free"=>"".$ram_free . "");

// $json_result["ram"][] = base64_encode(makeImageUrl2( "Disk", $v ));


/* ouput pretty images */
foreach( $data as $k => $v ) {
    $json_result["cpu"][] = base64_encode(makeImageUrl( $k, $v ));
}


echo json_encode($json_result);

?>