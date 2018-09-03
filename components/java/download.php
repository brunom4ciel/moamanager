<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\components\java;

defined('_EXEC') or die();

use moam\core\Framework;
// use moam\core\Application;
use moam\core\Properties;
use moam\libraries\core\utils\Utils;
use ZipArchive;


if (! class_exists('Application')) {
    $application = Framework::getApplication();
}

if (! $application->is_authentication() || $application->getUserType() != 1) {
    $application->alert("Error: you do not have credentials.");
}


Framework::import("Utils", "core/utils");

$utils = new Utils();



$dir_moa = Properties::getBase_directory_moa() . ""
;

$dir_moa_src = Properties::getBase_directory_moa() . "src"
. DIRECTORY_SEPARATOR;


$filename = $application->getParameter('filename');

if($filename != null)
{
    if(file_exists($dir_moa . $filename))
    {
        $filezip_tmp = $dir_moa . $filename;
    }
}
else 
{
    
    $filezip_tmp = $dir_moa . "moa-src-" .date("Y-m-d-H:i:s") .".zip";
    
    $files_list = $utils->getListElementsDirectory1($dir_moa, array("zip"));
    
    foreach($files_list as $item)
    {
        if(is_file($dir_moa . $item["name"]))
        {
            if(file_exists($dir_moa . $item["name"]))
            {
                unlink($dir_moa . $item["name"]);
            }
        }
    }
    
    $element = array();
    $element[] = "lib";
    $element[] = "src";    
    $element[] = "bin" . DIRECTORY_SEPARATOR . Properties::getBase_directory_moa_jar_default();
    
    // create zip
    create_zipfile($dir_moa, $filezip_tmp, $element);
    
//     $element = array();
//     $element[] = "lib";
        
//     create_merge_zipfile($dir_moa, $filezip_tmp, $element);
    
}



if (file_exists($filezip_tmp)) 
{                                                    
    
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($filezip_tmp) . '"');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filezip_tmp));
             
    
    ob_clean();
    ob_end_flush();
    
    // readfile($file);
    
    $handle = fopen($filezip_tmp, "rb");
    while (! feof($handle)) {
        echo fread($handle, 1000);
    }
    fclose($handle);
    exit();
    
} else {
    // echo "Error: file not found.";
    $application->alert("Error: file not found.");
}
               



function create_zipfile($dir, $filename, $element)
{
    $zip = new ZipArchive();
    
    if ($zip->open($filename, ZIPARCHIVE::CREATE) !== TRUE) {
        exit("cannot open <$filename>\n");
    }

    
    foreach ($element as $key => $item) {
        
//         if (is_dir($item)) {} else {
//             if (is_file($dir . $item . ".java")) {
//                 $item .= ".java";
//             }
//         }
        
        
        if (is_file($dir . $item)) {
            
            $dir_ = preg_replace('/[\/]{2,}/', DIRECTORY_SEPARATOR, $dir . $item);
            $zip->addFile($dir . $item, $item);
        } else {
            
            $dir_ = preg_replace('/[\/]{2,}/', DIRECTORY_SEPARATOR, $dir . $item . DIRECTORY_SEPARATOR);
            
            $dirs = array(
                $dir_
            );
            
            
            while (count($dirs)) {
                
                $dir_ = current($dirs);
                $folder_last = substr($dir_, strlen($dir));
                
                if (is_dir($dir_)) {
                    
                    $zip->addEmptyDir($folder_last);
                } else {}
                
                $dh = opendir($dir_);
                while ($file = readdir($dh)) {
                    
                    if ($file != '.' && $file != '..') {

                        if (is_file($dir_ . $file)) {
                            
                            $zip->addFile($dir_ . $file, $folder_last . $file);
                        } else {
                            
                            $dirs[] = $dir_ . $file . DIRECTORY_SEPARATOR;
                        }
                    }
                }
                closedir($dh);
                array_shift($dirs);
            }
        }
        
    }
    
    $zip->close();
}



function create_merge_zipfile($dir, $filename, $element)
{
    $zip = new ZipArchive();
    
    if ($zip->open($filename, ZIPARCHIVE::CREATE) !== TRUE) {
        exit("cannot open <$filename>\n");
    }
    
    $index = 0;
    foreach ($element as $key => $item) {
        
        if (is_dir($item)) {} else {
            if (is_file($dir . $item . ".jar")) {
                $item .= ".jar";
            }
        }
        
        
        if (is_file($dir . $item)) {
            
            $dir_ = preg_replace('/[\/]{2,}/', DIRECTORY_SEPARATOR, $dir . $item);
            $zip->addFile($dir . $item, $item);
            $zip->setCompressionIndex($index ++, ZIPARCHIVE::CM_STORE);
            
        } else {
            
            $dir_ = preg_replace('/[\/]{2,}/', DIRECTORY_SEPARATOR, $dir . $item . DIRECTORY_SEPARATOR);
            
            $dirs = array(
                $dir_
            );
            
            
            while (count($dirs)) {
                
                $dir_ = current($dirs);
                $folder_last = substr($dir_, strlen($dir));
                
                if (is_dir($dir_)) {
                    
                    $zip->addEmptyDir($folder_last);
                } else {}
                
                $dh = opendir($dir_);
                while ($file = readdir($dh)) {
                    
                    if ($file != '.' && $file != '..') {
                        
                        if (is_file($dir_ . $file)) {
                            
                            $zip->addFile($dir_ . $file, $folder_last . $file);
                            $zip->setCompressionIndex($index ++, ZIPARCHIVE::CM_STORE);
                        } else {
                            
                            $dirs[] = $dir_ . $file . DIRECTORY_SEPARATOR;
                        }
                    }
                }
                closedir($dh);
                array_shift($dirs);
            }           
        }
    }
    
    $zip->close();
}


                    
?>