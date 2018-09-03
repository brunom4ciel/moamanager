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
// use moam\core\Application;
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
.$application->getUser()
.DIRECTORY_SEPARATOR;

$chmod = "0777";

$tmp_update = Properties::getBase_directory_moa()."bin2/";

if(is_dir($tmp_update))
{
    $utils->delTree($tmp_update);  
    
}

if(mkdir($tmp_update, octdec($chmod), true))
{
    chmod($tmp_update, octdec($chmod));
}

if(file_exists(Properties::getBase_directory_moa() . "sources.txt"))
{
    unlink(Properties::getBase_directory_moa() . "sources.txt");
}

$cmd = "find " . Properties::getBase_directory_moa() . "src/ -name \"*.java\" > " . Properties::getBase_directory_moa() . "sources.txt";

$s = $utils->runExternal($cmd);
echo $s['output'];



$filename = Properties::getBase_directory_moa() . "sources.txt";
$s = $utils->getContentFile($filename);
$sourcefiles =  str_replace("\n"," ", $s);



if(file_exists(Properties::getBase_directory_moa() . "classpath.txt"))
{
    unlink(Properties::getBase_directory_moa() . "classpath.txt");
}

$cmd = "find " . Properties::getBase_directory_moa() . "lib/ -name \"*.jar\" > " . Properties::getBase_directory_moa() . "classpath.txt";

$s = $utils->runExternal($cmd);
echo $s['output'];


$filename = Properties::getBase_directory_moa() . "classpath.txt";
$s = $utils->getContentFile($filename);
$classpathfiles =  str_replace("\n",":", $s);


// echo "find " . Properties::getBase_directory_moa() . "src/ -name \"*.java\" > sources.txt";
// // echo "javac -classpath \"" . Properties::getBase_directory_moa() . "lib/sizeofag-1.0.0.jar:" . Properties::getBase_directory_moa() . "lib/commons-math-2.1.jar:" . Properties::getBase_directory_moa() . "lib/Jama.jar:" . Properties::getBase_directory_moa() . "lib/weka-3-7-12-monolithic.jar:" . Properties::getBase_directory_moa() . "lib/commons-math3-3.6.1.jar:" . Properties::getBase_directory_moa() . "lib/guava-18.0.jar\" @sources -d bin2 -Xlint:deprecation -Xlint:unchecked -O -nowarn";
// exit();
                                
// $commandes = array();
// $commandes[] = array("command"=>"rm -fr " . $dirProcess . "update". " 2> " . $dirProcess . "rRm.txt");
// $commandes[] = array("command"=>"mkdir " . $dirProcess . "update". " 2> " . $dirProcess . "rMkdir.txt");
//$commandes[] = array("command"=>"git clone https://github.com/brunom4ciel/moamanager/ " . $dirProcess . "update/moamanager/" . " 2> " . $dirProcess . "rGitClone.txt");
//#$commandes[] = array("command"=>"sh " . $dirProcess . "update/moamanager/setup/update-latest.sh" . " 2> " . $dirProcess . "rMOAManagerUpdate.txt");
//$commandes[] = array("command"=>"rm -fr " . $dirProcess . "update");
//$commandes[] = array("command"=>"rm -fr " . $dirProcess . "rGitClone.txt");
// $commandes[] = array("command"=>"find " . Properties::getBase_directory_moa() . "src/ -name \"*.java\" > " . Properties::getBase_directory_moa() . "sources");
// $commandes[] = array("command"=>"javac -classpath \"" . Properties::getBase_directory_moa() . "lib/sizeofag-1.0.0.jar:" . Properties::getBase_directory_moa() . "lib/commons-math-2.1.jar:" . Properties::getBase_directory_moa() . "lib/Jama.jar:" . Properties::getBase_directory_moa() . "lib/weka-3-7-12-monolithic.jar:" . Properties::getBase_directory_moa() . "lib/commons-math3-3.6.1.jar:" . Properties::getBase_directory_moa() . "lib/guava-18.0.jar\" " . Properties::getBase_directory_moa() . "sources -d bin2 -Xlint:deprecation -Xlint:unchecked -O -nowarn");
// $commandes[] = array("command"=>"sh " . Properties::getBase_directory_moa() . "compile_run.sh");
// $commandes[] = array("command"=>"sh " . Properties::getBase_directory_moa() . "compile_run.sh");
// $commandes[] = array("command"=>"sh " . Properties::getBase_directory_moa() . "compile_run.sh");
// $commandes[] = array("command"=>"sh " . Properties::getBase_directory_moa() . "compile_run.sh");
// $commandes[] = array("command"=>"sh " . Properties::getBase_directory_moa() . "compile_run.sh");


$cmd = "javac -classpath \"" . $classpathfiles . "\" " . $sourcefiles
. " -d " . Properties::getBase_directory_moa() . "bin2 -Xlint:deprecation -Xlint:unchecked -O -nowarn -encoding utf8";


// $cmd = "javac -classpath \"" . Properties::getBase_directory_moa() . "lib/sizeofag-1.0.0.jar:" 
//                                 . Properties::getBase_directory_moa() . "lib/commons-math-2.1.jar:" 
//                                 . Properties::getBase_directory_moa() . "lib/Jama.jar:" 
//                                 . Properties::getBase_directory_moa() . "lib/weka-3-7-12-monolithic.jar:" 
//                                 . Properties::getBase_directory_moa() . "lib/commons-math3-3.6.1.jar:" 
//                                     . Properties::getBase_directory_moa() . "lib/guava-18.0.jar\" " . $sourcefiles
//                     . " -d " . Properties::getBase_directory_moa() . "bin2 -Xlint:deprecation -Xlint:unchecked -O -nowarn -encoding utf8";


$s = $utils->runExternal($cmd);

echo $s['output'];

if(file_exists(Properties::getBase_directory_moa() . "moa2014.jar"))
{
    unlink(Properties::getBase_directory_moa() . "moa2014.jar");
}

$cmd = "jar cfm " . Properties::getBase_directory_moa() . "moa2014.jar " . Properties::getBase_directory_moa() . "MANIFEST.MF -C " . Properties::getBase_directory_moa() . "bin2/ .";

$s = $utils->runExternal($cmd);

echo $s['output'];

if(file_exists(Properties::getBase_directory_moa() . "bin/moa2014.jar"))
{
    unlink(Properties::getBase_directory_moa() . "bin/moa2014.jar");
}

$oldname = Properties::getBase_directory_moa() . "moa2014.jar";
$newname = Properties::getBase_directory_moa() . "bin/moa2014.jar";

if(rename($oldname, $newname))
{
    
}

if(is_dir($tmp_update))
{
    $utils->delTree($tmp_update);
    
}

if(file_exists(Properties::getBase_directory_moa() . "sources.txt"))
{
    unlink(Properties::getBase_directory_moa() . "sources.txt");
}

if(file_exists(Properties::getBase_directory_moa() . "classpath.txt"))
{
    unlink(Properties::getBase_directory_moa() . "classpath.txt");
}

echo "Finished Build\n";



?>