<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\components\files;

defined('_EXEC') or die();

use moam\core\Framework;
use moam\core\Application;
use moam\core\Properties;
use moam\libraries\core\utils\Utils;
use ZipArchive;
use moam\libraries\core\menu\Menu;
if (! class_exists('Application')) {
    $application = Framework::getApplication();
}

if (! $application->is_authentication()) {
    $application->alert("Error: you do not have credentials.");
}

Framework::import("menu", "core/menu");

if (! class_exists('Menu')) {
    $menu = new Menu();
}

Framework::import("Utils", "core/utils");

$utils = new Utils();

// proc_nice(-20);

$error = array();

$task = $application->getParameter("task");
$folder = $application->getParameter("folder");

if ($folder != null) {
    if (substr($folder, strlen($folder) - 1) != "/") {
        $folder .= DIRECTORY_SEPARATOR;
    }
}

if ($task == "folder") {

    $foldernew = $application->getParameter("foldernew");

    if ($folder == null) {

        $foldernew = Properties::getBase_directory_destine($application) . $application->getUser() . DIRECTORY_SEPARATOR . $foldernew;
    } else {

        $foldernew = Properties::getBase_directory_destine($application) . $application->getUser() . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $foldernew;
    }

    // exit("-".$foldernew);

    if (! is_dir($foldernew)) {
        mkdir($foldernew, 0777);
    }
} else {

    if ($task == "rename") {

        $from_folder = Properties::getBase_directory_destine($application) . $application->getUser() . DIRECTORY_SEPARATOR . $application->getParameter("folder") . 
        // .DIRECTORY_SEPARATOR
        $application->getParameter("foldernow");

        $to_folder = Properties::getBase_directory_destine($application) . $application->getUser() . DIRECTORY_SEPARATOR . $application->getParameter("folder") . 
        // .DIRECTORY_SEPARATOR
        $application->getParameter("rename");

        if ($application->getParameter("foldernow") == null) {

            $from_file = Properties::getBase_directory_destine($application) . $application->getUser() . DIRECTORY_SEPARATOR . $application->getParameter("folder") . 
            // .DIRECTORY_SEPARATOR
            $application->getParameter("filenow");

            $to_file = Properties::getBase_directory_destine($application) . $application->getUser() . DIRECTORY_SEPARATOR . $application->getParameter("folder") . 
            // .DIRECTORY_SEPARATOR
            $application->getParameter("rename");

            // exit("bruno=".$to_file);

            if ($from_file != $to_file) {

                if (file_exists($from_file)) {

                    if (file_exists($to_file)) {} else {

                        rename($from_file, $to_file);

                        $folder = $application->getParameter("folder") . DIRECTORY_SEPARATOR;
                    }
                }
            }
        } else {

            if ($from_folder != $to_folder) {

                if (is_dir($from_folder)) {

                    if (is_dir($to_folder)) {} else {

                        rename($from_folder, $to_folder);

                        $folder = $application->getParameter("folder") . $application->getParameter("rename") . DIRECTORY_SEPARATOR;
                    }
                }
            }
        }
    } else {

        if ($task == "trash") {

            /*
             * $element = $application->getParameter("element");
             *
             * $dir = Properties::getBase_directory_destine($application)
             * .$application->getUser()
             * .DIRECTORY_SEPARATOR
             * .$application->getParameter("folder");
             *
             * foreach($element as $key=>$item){
             *
             * if(is_file($dir.$item)){
             *
             * $from_file = $dir.$item;
             * unlink($from_file);
             * //echo "file - from: ".$from_file."<br>";
             *
             * }else{
             *
             * if(is_dir($dir.$item)){
             *
             * $from_dir = $dir.$item;
             * $utils->delTree($from_dir);
             * //echo "dir - from: ".$from_dir."<br>";
             *
             * }
             * }
             *
             *
             *
             * }
             */

            $element = $application->getParameter("element");
            $movedestine = DIRNAME_TRASH;

            $dir = Properties::getBase_directory_destine($application) . $application->getUser() . DIRECTORY_SEPARATOR . $application->getParameter("folder");

            foreach ($element as $key => $item) {

                if ($movedestine != $item) {

                    $movedestine_ = Properties::getBase_directory_destine($application) . $application->getUser() . DIRECTORY_SEPARATOR . $movedestine . DIRECTORY_SEPARATOR;

                    if (is_file($dir . $item)) {

                        // chmod($dir, 0777);

                        $from_file = $dir . $item;
                        $to_file = $movedestine_ . $item;

                        if (! file_exists($to_file))
                            rename($from_file, $to_file);

                        // echo "file - from: ".$from_file.", to: ".$to_file."<br>";
                    } else {

                        if (is_dir($dir . $item)) {

                            // chmod($dir, 0777);

                            $from_dir = $dir . $item;
                            $to_dir = $movedestine_ . $item;

                            if (! is_dir($to_dir))
                                rename($from_dir, $to_dir);

                            // echo "dir - from: ".$from_dir.", to: ".$to_dir."<br>";
                        }
                    }
                }
                // echo $item."<br>";
            }

            header("Location: " . PATH_WWW . "?component=" . $application->getComponent() . "&controller=" . $application->getController() . "&folder=" . $application->getParameter("folder"));
        } else {

            if ($task == 'move') {

                $element = $application->getParameter("element");
                $movedestine = $application->getParameter("movedestine");

                $dir = Properties::getBase_directory_destine($application) . $application->getUser() . DIRECTORY_SEPARATOR . $application->getParameter("folder");

                foreach ($element as $key => $item) {

                    if ($movedestine != $item) {

                        if ($movedestine == "..") {

                            $movedestine_ = substr($dir, 0, strrpos($dir, "/"));
                            $movedestine_ = substr($movedestine_, 0, strrpos($movedestine_, "/") + 1);
                        } else {

                            $movedestine_ = Properties::getBase_directory_destine($application) . $application->getUser() . DIRECTORY_SEPARATOR . $application->getParameter("folder") . $movedestine . DIRECTORY_SEPARATOR;
                        }

                        if (is_file($dir . $item)) {

                            // chmod($dir, 0777);

                            $from_file = $dir . $item;
                            $to_file = $movedestine_ . $item;

                            if (! file_exists($to_file))
                                rename($from_file, $to_file);

                            // echo "file - from: ".$from_file.", to: ".$to_file."<br>";
                        } else {

                            if (is_dir($dir . $item)) {

                                // chmod($dir, 0777);

                                $from_dir = $dir . $item;
                                $to_dir = $movedestine_ . $item;

                                if (! is_dir($to_dir))
                                    rename($from_dir, $to_dir);

                                // echo "dir - from: ".$from_dir.", to: ".$to_dir."<br>";
                            }
                        }
                    }
                    // echo $item."<br>";
                }

                // exit("<br>bruno - move");
            } else {

                if ($task == 'zip') {

                    $element = $application->getParameter("element");

                    $filename = $application->getParameter("filename");

                    $filename = str_replace(":", "-", $filename);
                    $filename = str_replace("/", "-", $filename);
                    $filename = trim($filename) . ".zip";

                    $dir = Properties::getBase_directory_destine($application) . $application->getUser() . DIRECTORY_SEPARATOR . $application->getParameter("folder");

                    if (file_exists($dir . $filename)) {

                        $overwrite = $application->getParameter("overwrite");

                        if ($overwrite == "1") {
                            unlink($dir . $filename);

                            // create zip
                            create_zipfile($dir, $filename, $element);
                        } else {
                            $error[] = "File name exists in folder.";
                        }
                    } else {

                        // create zip
                        create_zipfile($dir, $filename, $element);
                    }
                } else {

                    if ($task == 'unzip') {

                        $element = $application->getParameter("element");

                        $dir = Properties::getBase_directory_destine($application) . $application->getUser() . DIRECTORY_SEPARATOR . $application->getParameter("folder");

                        foreach ($element as $key => $item) {

                            $zip = new \ZipArchive();

                            $extension = substr($item, strrpos($item, ".") + 1);

                            if ($extension == "zip") {

                                if (is_file($dir . $item)) {

                                    $newfolder = substr($item, 0, strrpos($item, "."));

                                    if (is_dir($dir . $newfolder)) {

                                        $overwrite = $application->getParameter("overwrite");

                                        if ($overwrite == "1") {

                                            $utils->delTree($dir . $newfolder);

                                            if ($zip->open($dir . $item) === TRUE) {
                                                $zip->extractTo($dir . $newfolder);
                                                $zip->close();
                                            } else {
                                                $error[] = 'Error: failed - ' . $item;
                                            }
                                        } else {
                                            $error[] = 'Error: folder exists - ' . $newfolder;
                                        }
                                    } else {
                                        if ($zip->open($dir . $item) === TRUE) {
                                            $zip->extractTo($dir . $newfolder);
                                            $zip->close();
                                        } else {
                                            $error[] = 'Error: failed - ' . $item;
                                        }
                                    }
                                } else {

                                    // if(is_dir($dir.$item)){

                                    //
                                    // }
                                }
                            }
                        }
                    } else {

                        if ($task == 'merge') {

                            $element = $application->getParameter("element");

                            $filename = $application->getParameter("filename");

                            $filename = str_replace(":", "-", $filename);
                            $filename = str_replace("/", "-", $filename);
                            $filename = trim($filename) . ".zip";

                            $dir = Properties::getBase_directory_destine($application) . $application->getUser() . DIRECTORY_SEPARATOR . $application->getParameter("folder");

                            if (file_exists($dir . $filename)) {

                                $overwrite = $application->getParameter("overwrite");

                                if ($overwrite == "1") {
                                    unlink($dir . $filename);

                                    // create zip
                                    create_merge_zipfile($dir, $filename, $element);
                                } else {
                                    $error[] = "File name exists in folder.";
                                }
                            } else {

                                // create zip
                                create_merge_zipfile($dir, $filename, $element);
                            }
                        } else {

                            if ($task == "backup") {

                                $element = $application->getParameter("element");
                                $movedestine = DIRNAME_BACKUP;

                                $dir = Properties::getBase_directory_destine($application) . $application->getUser() . DIRECTORY_SEPARATOR . $application->getParameter("folder");

                                foreach ($element as $key => $item) {

                                    if ($movedestine != $item) {

                                        $movedestine_ = Properties::getBase_directory_destine($application) . $application->getUser() . DIRECTORY_SEPARATOR . $movedestine . DIRECTORY_SEPARATOR;

                                        if (is_file($dir . $item)) {

                                            // chmod($dir, 0777);

                                            $from_file = $dir . $item;
                                            $to_file = $movedestine_ . $item;

                                            if (! file_exists($to_file))
                                                rename($from_file, $to_file);

                                            // echo "file - from: ".$from_file.", to: ".$to_file."<br>";
                                        } else {

                                            if (is_dir($dir . $item)) {

                                                // chmod($dir, 0777);

                                                $from_dir = $dir . $item;
                                                $to_dir = $movedestine_ . $item;

                                                if (! is_dir($to_dir))
                                                    rename($from_dir, $to_dir);

                                                // echo "dir - from: ".$from_dir.", to: ".$to_dir."<br>";
                                            }
                                        }
                                    }
                                    // echo $item."<br>";
                                }

                                header("Location: " . PATH_WWW . "?component=" . $application->getComponent() . "&controller=" . $application->getController() . "&folder=" . $application->getParameter("folder"));
                            } else {

                                if ($task == 'upload') {

                                    $files_extensions = array(
                                        "txt"
                                    );

                                    $uploaddir = Properties::getBase_directory_destine($application) . $application->getUser() . DIRECTORY_SEPARATOR . $folder;

                                    $uploadfile = $uploaddir . basename($_FILES['filedata']['name']);

                                    echo $uploadfile;

                                    exit("bruno");

                                    // verifica se arquivo existe em tmp
                                    if (is_uploaded_file($_FILES['jarfile']['tmp_name'])) {

                                        // verifica o formato da extensão do arquivo
                                        if (in_array(substr($uploadfile, strrpos($uploadfile, ".") + 1), $files_extensions)) {

                                            // se o arquivo já existir, apaga
                                            if (file_exists($uploadfile))
                                                unlink($uploadfile);

                                            // move o arquivo de tmp para destino
                                            if (move_uploaded_file($_FILES['jarfile']['tmp_name'], $uploadfile)) {

                                                // verifica se arquivo existe em destino
                                                if (is_file($uploadfile)) {

                                                    // verifica se diretorio existe
                                                    if (! is_dir(Properties::getBase_directory_destine($application) . Framework::getUser())) {

                                                        // cria um novo diretório
                                                        if (mkdir(Properties::getBase_directory_destine($application) . Framework::getUser(), 0777, true))

                                                            // define permissões ao diretório
                                                            if (! chmod(Properties::getBase_directory_destine($application) . Framework::getUser(), 0777))
                                                                $error_msg = "Error directory permissions.";
                                                            else {

                                                                // define permissões ao arquivo
                                                                if (! chmod($uploadfile, 0777))
                                                                    $error_msg = "Error setting permissions.";
                                                                else
                                                                    $error_msg = "Upload successful";
                                                            }
                                                        else {
                                                            $error_msg = "Error directory not create.";
                                                        }
                                                    } else {

                                                        // define permissoes ao arquivo
                                                        if (! chmod($uploadfile, 0777))
                                                            $error_msg = "Error setting permissions.";
                                                        else
                                                            $error_msg = "Upload successful";
                                                    }
                                                } else
                                                    $error_msg = "Upload successful";
                                            } else {
                                                $error_msg = "lammer\n";
                                            }
                                        } else {
                                            $error_msg = "Extension not supported.";
                                        }
                                    } else {
                                        $error_msg = "file not exist.";
                                    }

                                    var_dump($_FILES);
                                    exit();

                                    $files_extensions = array(
                                        "txt"
                                    );

                                    $uploaddir = Properties::getBase_directory_destine($application) . $application->getUser() . DIRECTORY_SEPARATOR . $folder;

                                    $uploadfile = $uploaddir . basename($_FILES['filedata']['name']);

                                    echo $uploadfile;

                                    exit("bruno");

                                    if (is_uploaded_file($_FILES['filedata']['tmp_name'])) {

                                        if (in_array(substr($uploadfile, strrpos($uploadfile, ".") + 1), $files_extensions)) {

                                            if (file_exists($uploadfile)) {

                                                $i = 1;
                                                do {

                                                    $extens = substr($uploadfile, strrpos($uploadfile, ".") + 1);
                                                    $filename = substr($uploadfile, strrpos($uploadfile, "/"), strrpos($uploadfile, "."));
                                                    $filename = substr($filename, 0, strrpos($filename, "."));
                                                    $dirnamefile = substr($uploadfile, 0, strrpos($uploadfile, "/"));

                                                    if (strrpos($filename, " - copy")) {

                                                        $filename = substr($filename, 0, strrpos($filename, " - copy"));
                                                    }

                                                    $uploadfile = $dirnamefile . $filename . " - copy " . $i . "." . $extens;
                                                    $i ++;
                                                } while (file_exists($uploadfile));

                                                // echo $uploadfile;

                                                if (move_uploaded_file($_FILES['filedata']['tmp_name'], $uploadfile)) {
                                                    // echo "Arquivo válido e enviado com sucesso.\n";
                                                } else {
                                                    echo "Possível ataque de upload de arquivo!\n";
                                                }
                                            } else {

                                                if (move_uploaded_file($_FILES['filedata']['tmp_name'], $uploadfile)) {
                                                    // echo "Arquivo válido e enviado com sucesso.\n";
                                                } else {
                                                    echo "Possível ataque de upload de arquivo!\n";
                                                }
                                            }
                                        } else {
                                            echo "not extension<br>";
                                        }

                                        // header("Location: spreadsheet.php?file=".substr($uploadfile,strrpos($uploadfile, "/")));
                                        // header("Location: index.php".(empty($folder)?"":"?folder=".$folder));
                                    }

                                    echo '<pre>';

                                    print_r(error_get_last());

                                    print "</pre>";
                                }

                                /*
                                 * if($task == 'bruno'){
                                 *
                                 *
                                 * $breakline = 15;
                                 *
                                 * if($folder == null){
                                 *
                                 * }else{
                                 *
                                 * $element = $application->getParameter("element");
                                 *
                                 * foreach($element as $key=>$item){
                                 *
                                 * }
                                 *
                                 * $files_list = $utils->getListElementsDirectory1(
                                 * Properties::getBase_directory_destine($application)
                                 * .$application->getUser()
                                 * .DIRECTORY_SEPARATOR
                                 * .$folder
                                 * .$item
                                 * .DIRECTORY_SEPARATOR
                                 * , array("txt"));
                                 *
                                 *
                                 *
                                 * }
                                 * //var_dump($files_list);exit();
                                 *
                                 * $y=1;
                                 *
                                 * for($i = 0; $i < 30; $i++){
                                 *
                                 * for($z = 1; $z < $breakline+1; $z++){
                                 *
                                 * if($z == 1){
                                 *
                                 * }else{
                                 *
                                 * if($z == 3 || $z == 4 ){
                                 * //remover
                                 * //echo $z."-".$y;
                                 *
                                 * //exit($files_list[$y]["name"]."-----".$y);
                                 * //exit();
                                 * $a = $y+1;
                                 *
                                 * //echo strpos($files_list[$y]["name"], "4")."---";
                                 *
                                 * if(strpos($files_list[$y]["name"], "".$a) === false){
                                 * // echo $files_list[$y]["name"]."--".$a."<br>";
                                 * }else{
                                 *
                                 * $from_name = Properties::getBase_directory_destine($application)
                                 * .$application->getUser()
                                 * .DIRECTORY_SEPARATOR
                                 * .$folder
                                 * .$item
                                 * .DIRECTORY_SEPARATOR
                                 * .$files_list[$y]["name"];
                                 *
                                 * $to_name = Properties::getBase_directory_destine($application)
                                 * .$application->getUser()
                                 * .DIRECTORY_SEPARATOR
                                 * .$folder
                                 * .$item
                                 * .DIRECTORY_SEPARATOR
                                 * .$files_list[$y]["name"];
                                 *
                                 * $to_name = substr($to_name, 0, strrpos($to_name, "."));
                                 * $to_name .= ".bruno";
                                 *
                                 * rename($from_name, $to_name);
                                 *
                                 * echo $to_name."<br>";
                                 *
                                 *
                                 * }
                                 *
                                 * }
                                 *
                                 * }
                                 *
                                 * $y++;
                                 *
                                 * }
                                 *
                                 *
                                 *
                                 * }
                                 *
                                 *
                                 *
                                 *
                                 * }
                                 */
                            }
                        }
                    }
                }
            }
        }
    }
}

if ($folder == null) {

    $files_list = $utils->getListElementsDirectory1(Properties::getBase_directory_destine($application) . $application->getUser() . DIRECTORY_SEPARATOR, array(
        "txt",
        "tex",
        "csv",
        "html",
        "report",
        "zip"
    ));
} else {

    if ($task == "rename") {

        $folder = $application->getParameter("folder");

        $files_list = $utils->getListElementsDirectory1(Properties::getBase_directory_destine($application) . $application->getUser() . DIRECTORY_SEPARATOR . $folder, 
            // .DIRECTORY_SEPARATOR
            array(
                "txt",
                "tex",
                "csv",
                "html",
                "report",
                "zip"
            ));
    } else {

        $files_list = $utils->getListElementsDirectory1(Properties::getBase_directory_destine($application) . $application->getUser() . DIRECTORY_SEPARATOR . $folder, 
            // .DIRECTORY_SEPARATOR
            array(
                "txt",
                "tex",
                "csv",
                "html",
                "report",
                "zip"
            ));
    }
}

foreach ($files_list as $key => $element) {

    if ($element["type"] == "dir") {
        if (trim($element["name"]) == DIRNAME_SCRIPT || trim($element["name"]) == DIRNAME_TRASH || trim($element["name"]) == DIRNAME_BACKUP) {
            unset($files_list[$key]);
        }
    } else {

        /*
         * echo substr($element["name"],strrpos($element["name"],".")+1);
         * if(substr($element["name"],strrpos($element["name"],".")+1)=="log"){exit("bruno");
         * unset($files_list[$key]);
         * }
         */
    }
}

$dir_list = $utils->getListDirectory(Properties::getBase_directory_destine($application) . $application->getUser() . DIRECTORY_SEPARATOR . $folder);

foreach ($dir_list as $key => $element) {

    if (trim($element) == DIRNAME_SCRIPT || trim($element) == DIRNAME_TRASH || trim($element) == DIRNAME_BACKUP) {

        unset($dir_list[$key]);
    }
}



function create_zipfile($dir, $filename, $element)
{
    $zip = new \ZipArchive();

    if ($zip->open($dir . $filename, ZIPARCHIVE::CREATE) !== TRUE) {
        exit("cannot open <$filename>\n");
    }

    // $dir = $dir = Properties::getBase_directory_destine($application)
    // .$application->getUser()
    // .DIRECTORY_SEPARATOR
    // .$application->getParameter("folder");

    foreach ($element as $key => $item) {

        // if(is_file($dir.$item)){

        // $from_file = $dir.$item;

        // $zip->addFile( $from_file , $item );

        // }else{

        // if(is_dir($dir.$item)){

        /*
         * $zip->addEmptyDir($item);
         * $iter = new RecursiveDirectoryIterator($dir.$item, FilesystemIterator::SKIP_DOTS);
         *
         * foreach ($iter as $fileinfo) {
         * if (! $fileinfo->isFile() && !$fileinfo->isDir()) {
         * continue;
         * }
         *
         * $method = $fileinfo->isFile() ? 'addFile' : 'addDir';
         *
         * $zip->$method($fileinfo->getPathname(), $item . '/' .
         * $fileinfo->getFilename());
         * }
         *
         */

        /*
         * $rootPath = $dir.$item;
         *
         * $files = new RecursiveIteratorIterator(
         * new RecursiveDirectoryIterator($rootPath),
         * RecursiveIteratorIterator::LEAVES_ONLY
         * );
         *
         * foreach ($files as $name => $file){
         *
         * // Skip directories (they would be added automatically)
         * if (!$file->isDir()){
         *
         * // Get real and relative path for current file
         * $filePath = $file->getRealPath();
         * $relativePath = substr($filePath, strlen($rootPath) + 1);
         *
         * // Add current file to archive
         * $zip->addFile($filePath, $relativePath);
         * }else{
         * //$zip->addEmptyDir($item);
         * }
         * }
         */

        if (is_file($dir . $item)) {

            $dir_ = preg_replace('/[\/]{2,}/', DIRECTORY_SEPARATOR, $dir . $item);

            // $folder_last = substr($dir_, strlen($dir));

            // echo $folder_last;

            // exit();

            $zip->addFile($dir . $item, $item);
        } else {

            $dir_ = preg_replace('/[\/]{2,}/', DIRECTORY_SEPARATOR, $dir . $item . DIRECTORY_SEPARATOR);

            $dirs = array(
                $dir_
            );

            while (count($dirs)) {

                $dir_ = current($dirs);

                // echo $dir_;
                //
                // $folder_last = substr($dir_, 0, strrpos($dir_, DIRECTORY_SEPARATOR));
                // $folder_last = substr($folder_last, strrpos($folder_last, DIRECTORY_SEPARATOR));

                $folder_last = substr($dir_, strlen($dir));

                // echo $folder_last."<br>";

                // exit();

                if (is_dir($dir_)) {

                    $zip->addEmptyDir($folder_last);
                } else {}

                $dh = opendir($dir_);
                while ($file = readdir($dh)) {

                    if ($file != '.' && $file != '..') {

                        // echo $folder_last.$file."<br>";

                        if (is_file($dir_ . $file)) {

                            // var_dump($dir_.$file);

                            // echo $item.DIRECTORY_SEPARATOR.$file;

                            // exit("--");

                            $zip->addFile($dir_ . $file, $folder_last . $file);
                        } else { // if (is_dir($file)){

                            $dirs[] = $dir_ . $file . DIRECTORY_SEPARATOR;
                        }
                    }
                }
                closedir($dh);
                array_shift($dirs);
            }
        }

        // $folder = $application->getParameter("folder");

        // }
        // }

        // echo $item."<br>";
    }

    $zip->close();
}

function create_merge_zipfile($dir, $filename, $element)
{
    $zip = new ZipArchive();

    if ($zip->open($dir . $filename, ZIPARCHIVE::CREATE) !== TRUE) {
        exit("cannot open <$filename>\n");
    }

    $index = 0;
    foreach ($element as $key => $item) {

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



<script>











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




	var chk_arr =  document.getElementsByName(element.name);
	var chklength = chk_arr.length;             

	for(k=0;k< chklength;k++)
	{
		var checkedbox = chk_arr[k].checked;
		
		if(checkedbox)
			checkedbox=true;
		else
			checkedbox=false;
		
		setCookie(element.name+"["+k+"]",checkedbox,365);

	} 


/*
	
		
	var values = [];
	  var vehicles = document.form_data.streams[];//document.getElementsByTagName("streams[]");//form.vehicle;

	  alert(vehicles.length);

	  
	  for (var i=0; i<vehicles.length; i++) {
	    if (vehicles[i].checked) {

		    alert("sim");
	    //  values.push(vehicles[i].value);
	    }
	  }*/

	  

}



function historicCookieCheckbox(elementId){


	var chk_arr =  document.getElementsByName(elementId);
	var chklength = chk_arr.length;             

	for(k=0;k< chklength;k++)
	{
		var elementCookieHistoric = getCookie(elementId+"["+k+"]");

		
		if(elementCookieHistoric==""){
			var elementCookieChecked=false;
		}else
			var elementCookieChecked=elementCookieHistoric;
		
		//alert(elementId+"="+elementCookieChecked);
		//alert(elementId+"="+checkedbox+", elementCookieChecked=");//+elementCookieChecked);
		

		if(typeof(elementCookieChecked) === "boolean")
			chk_arr[k].checked = elementCookieChecked;
		else
			if(elementCookieChecked === null)
				alert(elementId);
			else
				chk_arr[k].checked = parseBool2(elementCookieChecked);

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





/*function ConfirmDelete()
{
  var x = confirm("Are you sure you want to delete?");
  if (x)
     return true;
  else
    return false;
}*/

function renameFile(obj){
	
	var newName = prompt("Please enter file name", obj.name);
	
	if (newName != null) {
		window.location.href="?component=<?php echo $application->getComponent();?>&controller=<?php echo $application->getController() ;?>&task=rename&folder=<?php echo $folder;?>&filenow="+obj.name+"&rename="+newName;
    	
	}

}
function renameFolder(obj){
	
	var newName = prompt("Please enter folder name", obj.name);
	
	if (newName != null) {
		window.location.href="?component=<?php echo $application->getComponent();?>&controller=<?php echo $application->getController() ;?>&task=rename&folder=<?php echo $folder;?>&foldernow="+obj.name+"&rename="+newName;
    	
	}

}
function newFolder(){
	
	var folder = prompt("Please enter older name", "New Folder");
	
	
	if (folder != null) {
    	window.location.href="?component=<?php echo $application->getComponent();?>&controller=<?php echo $application->getController() ;?>&folder=<?php echo $folder;?>&task=folder&foldernew="+folder;
    	
	}
	
}

function newFile(){
	
	var filename = prompt("Please enter file name", "New file");	
	
	if (filename != null) {
    	window.location.href="?component=<?php echo $application->getComponent();?>&controller=edit&task=new&filename="+filename+"&folder=<?php echo $folder ;?>";
    	
	}
	
}

function sendAction(task){

	if(task == 'upload'){

	  var x = confirm("sure you want to send the file?");
	  if (!x)
	     return;

	}
	
	if(task == 'trash'){

	  var x = confirm("Are you sure you want to trash?");
	  if (!x)
	     return;

	}

	if(task == 'move'){

	  var x = confirm("Are you sure you want to move?");
	  if (!x)
	     return;

	}

	if(task == 'backup'){

	  var x = confirm("Are you sure you want to backup?");
	  if (!x)
	     return;

	}

	if(task == 'renamelote'){

		  var x = confirm("Are you sure you want to rename?");
		  if (!x)
		     return;

	}


	if(task == 'merge'){


		var x = confirm("File merge - confirm zip?");
		if (!x)
			return;
			
		var filename = prompt("Please enter file name", "New file merge");

		var x = confirm("File merge - overwrite file if it exists?");
		if (x)
			overwrite = "1";
		else
			overwrite = "";
		     
		document.getElementById("filename").value = filename;
		document.getElementById("overwrite").value = overwrite;
	}

	
	if(task == 'zip'){


		var x = confirm("File compress - confirm zip?");
		if (!x)
			return;
			
		var filename = prompt("Please enter file name", "New file compress");

		var x = confirm("File compress - overwrite file if it exists?");
		if (x)
			overwrite = "1";
		else
			overwrite = "";
		     
		document.getElementById("filename").value = filename;
		document.getElementById("overwrite").value = overwrite;
	}


	if(task == 'unzip'){

		var x = confirm("File extract - confirm unzip?");
		if (!x)
			return;
		
		var x = confirm("File extract - overwrite file or folder if it exists?");
		if (x)
			overwrite = "1";
		else
			overwrite = "";
		     
		document.getElementById("overwrite").value = overwrite;
	}
	
	
	document.getElementById('task').value = task;
	document.getElementById('formulario').submit();
	
}




function do_this2(){

    var checkboxes = document.getElementsByName('element[]');
    var button = document.getElementById('checkall');
    
    if(button.checked ==  true){
        for (var i in checkboxes){
            checkboxes[i].checked = 'FALSE';
        }
        //button.value = 'deselect'
    }else{
        for (var i in checkboxes){
            checkboxes[i].checked = '';
        }
       // button.value = 'select';
        button.checked == false;
    }
}

function do_this(){

    var checkboxes = document.getElementsByName('element[]');
    var button = document.getElementById('toggle');

    if(button.value == 'select'){
        for (var i in checkboxes){
            checkboxes[i].checked = 'FALSE';
        }
        button.value = 'deselect'
    }else{
        for (var i in checkboxes){
            checkboxes[i].checked = '';
        }
        button.value = 'select';
    }
}


</script>



<div class="content content-alt">
	<div class="container" style="width: 90%">
		<div class="row">
			<div class="">

				<div class="card" style="width: 100%">
					<div class="page-header">
						<h1>
							<a href="<?php echo $_SERVER['REQUEST_URI']?>"><?php echo TITLE_COMPONENT?></a>
						</h1>
					</div>

					<div style="width: 100%; padding-bottom: 15px; display: table">

						<div
							style="float: left; width: 18%; border: 1px solid #fff; display: table-cell">
																
									<?php echo $application->showMenu($menu);?>								

								</div>

						<div
							style="float: left; width: 80%; border: 1px solid #fff; display: table-cell">



							<form name="formulario" id="formulario" action="" method="POST"
								enctype="multipart/form-data">
								<input type="hidden"
									value="<?php echo $application->getComponent()?>"
									name="component" id="component"> <input type="hidden"
									value=<?php echo $application->getController()?>
									name="controller" id="controller"> <input type="hidden"
									name="folder" value="<?php echo $folder;?>" /> <input
									type="hidden" name="task" id="task" value="" /> <input
									type="hidden" name="filename" id="filename" value="" /> <input
									type="hidden" name="overwrite" id="overwrite" value="" />

								<div id="container">
    
    
    <?php

    if (count($error) > 0) {

        for ($i = 0; $i < count($error); $i ++) {
            echo $error[$i] . "<br>";
        }
    }

    ?>
    
<a
										href="?component=<?php echo $application->getComponent()?>&controller=upload&folder=<?php echo $folder;?>">Upload
										File (*.txt, *.zip)</a> <br>
									<!-- 
<input type="button" value="BRUNO-Kill-files-alert" name="bruno" onclick="javascript: sendAction('bruno');" />
|| -->
									<input type="button" value="New folder" name="folder"
										onclick="javascript: newFolder();" /> || <input type="button"
										value="Trash" name="trash"
										onclick="javascript: sendAction('trash');" /> || <input
										type="button" value="Backup" name="backup"
										onclick="javascript: sendAction('backup');" /> || <input
										type="button" value="Merge" name="compredss"
										onclick="javascript: sendAction('merge');" /> || <input
										type="button" value="zip" name="compress"
										onclick="javascript: sendAction('zip');" /> <input
										type="button" value="unzip" name="decompress"
										onclick="javascript: sendAction('unzip');" /> || Move to: <select
										name="movedestine" id=movedestine>		
		<?php

// $folder = $application->getParameter("folder");

if ($folder != null) {
    echo "<option value=\"..\">..</option>";
}

// foreach($dir_list as $key=>$element){

// //if($element["type"]=="dir"){
// if($element=="scripts"){
// unset($files_list[$key]);
// }
// //}
// }

foreach ($dir_list as $key => $element) {

    // if($element["type"]=="dir"){

    echo "<option value=\"" . $element . "\">" . $element . "</option>";
    // }
}

?>
													
												</select> <input type="button" value="Move" name="move"
										id="move" onclick="javascript: sendAction('move');" /> <br> <a
										href="<?php echo PATH_WWW ?>?component=<?php echo $application->getComponent()?>&controller=<?php echo $application->getController();?>">Root</a>

<?php

$levels = explode("/", $folder);

$fold = "";

foreach ($levels as $key => $item) {

    if (! empty($item)) {

        $fold .= $item . DIRECTORY_SEPARATOR;

        echo " > <a href=\"" . PATH_WWW . "?component=" . $application->getComponent() . "&controller=" . $application->getController() . "&folder=" . $fold . "\">" . $item . "</a>";
    }
}

?>
		
		
	<table border='1' id="temporary_files" style="width: 100%;">
										<tr>
											<th>#</th>
											<th style="width: 60%;"><label><input type="checkbox"
													id="checkall" onClick="do_this2()" value="select" />Name</label></th>
											<th>Size</th>
											<th>DateTime</th>
										</tr>
<?php
$i = 0;
foreach ($files_list as $key => $element) {
    $i ++;

    if ($element["type"] == "dir") {

        echo "<tr><td>" . $i . "</td><td>" . 
        "<a onclick='javascript: renameFolder(this);' name='" . $element["name"] . "' title='Rename' href='#'>" . "<img align='middle' width='24px' src='" . $application->getPathTemplate() . "/images/icon-rename.png' border='0'></a> " . 
        "<a href='?component=" . $application->getComponent() . "&folder=" . (empty($folder) ? "" : $folder) . $element["name"] . "/&task=open'>" . "<img width='24px' align='middle' src='" . $application->getPathTemplate() . "/images/icon-folder.png' title='Open'/></a> " . 

        // ."<a title='Remove' onclick='javascript: return ConfirmDelete();' href='?component=home&controller=files&task=remove&folder=".$folder.$element["name"]."'>"
        // ."<img src='".App::getDirTmpl()."images/icon-remove.gif' border='0'></a>

        "<label><input type='checkbox' name='element[]' value='" . $element["name"] . "' />" . $element["name"] . "</label> " . "</td><td>" . $element["size"] . "</td><td>" . $element["datetime"] . "</td></tr>";
    }
}

$i = 0;
foreach ($files_list as $key => $element) {

    $i ++;
    if ($element["type"] != "dir") {

        echo "<tr><td>" . $i . "</td><td>" . "<a onclick='javascript: renameFile(this);' name='" . $element["name"] . "' title='Rename' href='#'>" . "<img align='middle' width='24px' src='" . $application->getPathTemplate() . "/images/icon-rename.png' border='0'></a> " . "<a href='?component=" . $application->getComponent() . "&controller=openreadonly&filename=" . $element["name"] . "&folder=" . $application->getParameter("folder") . "'>" . "<img width='16px' align='middle' src='" . $application->getPathTemplate() . "/images/icon-view.png' title='View contents'/></a> " . 
        '<a href="' . PATH_WWW . '?component=resource&tmpl=tmpl&task=download&file=' . $application->getParameter("folder") . $element["name"] . '">' . "<img width='16px' align='middle' src='" . $application->getPathTemplate() . "/images/icon_download.png' title='View contents'/></a> " . 
        "<label><input type='checkbox' name='element[]' value='" . $element["name"] . "' />" . $element["name"] . "</label> " . 

        // ."<a title='Move file' href='?component=moa&controller=run&filename=".$element["name"]."&folder=".$application->getParameter("folder")."'>"
        // ."<img align='middle' width='24px' src='".App::getDirTmpl()."/images/icon-play.png' border='0'></a> "
        // ."<a title='Remove' onclick='javascript: return ConfirmDelete();' href='?component=home&controller=files&task=remove&folder=".$folder."&filename=".$folder.$element["name"]."'>"
        // ."<img width='16px' src='".App::getDirTmpl()."images/icon-remove.gif' border='0'></a>"
        "</td><td>" . $element["size"] . "</td><td>" . $element["datetime"] . "</td></tr>";
    }
}

?>		
	</table>
							
							</form>
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	

	
	
	
	
									<?php 
																	
									/*	for($i=0; $i<count($files_list); $i++){
										
											echo "<span style='margin-left:65px;' data-reactid=\".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1.0\">".$files_list[$i]."</span><br>\n";
										
										}*/
										
									?>
								
								</div>

					</div>

				</div>
			</div>
		</div>
	</div>
</div>