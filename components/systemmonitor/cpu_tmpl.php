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
// use moam\core\Application;
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

$df = $utils->getFreeSpace(Properties::getBase_directory_destine($application));
$dt = disk_total_space(Properties::getBase_directory_destine($application));
$du = $dt - $df;

$dp = sprintf('%.2f', ($du / $dt) * 100);

$df = $utils->formatSize($df);
$du = $utils->formatSize($du);
$dt = $utils->formatSize($dt);

$cpuload = new CPULoad();
$cpuload->get_load();
// $cpuload->print_load();
$cpu_du = 0;
$cpu_df = 0;
$cpu_dt = 0;

// echo "CPU load is: ".$cpuload->load["cpu"]."%";

$cpu_dp = round($cpuload->load["cpu"], 2);

function get_memory()
{
    foreach (file('/proc/meminfo') as $ri)
        $m[strtok($ri, ':')] = strtok('');
    return 100 - @round(($m['MemFree'] + $m['Buffers'] + $m['Cached']) / $m['MemTotal'] * 100);
}

$memory_dp = get_memory();

$json = array();

array_push($json, array(
    "%CPU" => $cpu_dp,
    "%RAM" => $memory_dp,
    "%DISC" => $dp
));

// }

print json_encode($json);

?>