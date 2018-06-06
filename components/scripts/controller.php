<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\components\generator;

defined('_EXEC') or die();

use moam\core\Framework;
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

$task = $application->getParameter("task");
$folder = $application->getParameter("folder");
$error = array();
$files_extensions = array(
    "txt",
    "data",
    "zip"
);

if ($task == "folder") {

    $foldernew = $application->getParameter("foldernew");

    $foldernew = PATH_USER_WORKSPACE_STORAGE . DIRNAME_SCRIPT . DIRECTORY_SEPARATOR . $folder . $foldernew;

    if (! is_dir($foldernew)) {
        mkdir($foldernew, 0777);
    }
} else {

    if ($task == "rename") {

        $from_folder = PATH_USER_WORKSPACE_STORAGE . DIRNAME_SCRIPT . DIRECTORY_SEPARATOR . $application->getParameter("folder") . 
        // .DIRECTORY_SEPARATOR
        $application->getParameter("foldernow");

        $to_folder = PATH_USER_WORKSPACE_STORAGE . DIRNAME_SCRIPT . DIRECTORY_SEPARATOR . $application->getParameter("folder") . 
        // .DIRECTORY_SEPARATOR
        $application->getParameter("rename");

        if ($from_folder != $to_folder) {

            if (is_dir($from_folder)) {

                if (is_dir($to_folder)) {} else {

                    rename($from_folder, $to_folder);

                    $folder = $application->getParameter("folder") . $application->getParameter("rename") . DIRECTORY_SEPARATOR;
                }
            }

        }

        // echo $from_folder."<br>";
        // echo $to_folder."<br>";
        // echo $folder."<br>";
        // exit("fim");
    } else {

        if ($task == "remove") {

            $element = $application->getParameter("element");

            $dir = PATH_USER_WORKSPACE_STORAGE . DIRNAME_SCRIPT . DIRECTORY_SEPARATOR . $application->getParameter("folder");

            foreach ($element as $key => $item) {

                if (is_file($dir . $item)) {
                    
                    // verifica o formato da extensão do arquivo
                    if (in_array(substr($item, strrpos($item, ".") + 1), $files_extensions)) {

                        $from_file = $dir . $item;
                        unlink($from_file);
                    }else{
                        exit("extension not support.");
                    }
                    
                } else {

                    if (is_dir($dir . $item)) {

                        $from_dir = $dir . $item;

                        $utils->delTree($from_dir);
                        // echo "dir - from: ".$from_dir."<br>";
                    }
                }
            }

            header("Location: " . PATH_WWW . "?component=" . $application->getComponent() . "&controller=" . $application->getController() . "&folder=" . $application->getParameter("folder"));
        } else {

            if ($task == 'move') {

                $element = $application->getParameter("element");
                $movedestine = $application->getParameter("movedestine");

                $dir = PATH_USER_WORKSPACE_STORAGE . DIRNAME_SCRIPT . DIRECTORY_SEPARATOR . $application->getParameter("folder");

                foreach ($element as $key => $item) {

                    if ($movedestine != $item) {

                        if ($movedestine == "..") {

                            $movedestine_ = substr($dir, 0, strrpos($dir, "/"));
                            $movedestine_ = substr($movedestine_, 0, strrpos($movedestine_, "/") + 1);
                        } else {

                            $movedestine_ = PATH_USER_WORKSPACE_STORAGE . DIRNAME_SCRIPT . DIRECTORY_SEPARATOR . $application->getParameter("folder") . $movedestine . DIRECTORY_SEPARATOR;
                        }
         
                        if (is_file($dir . $item)) {

                            // chmod($dir, 0777);
                            
                            if (in_array(substr($item, strrpos($item, ".") + 1), $files_extensions)) {

                                $from_file = $dir . $item;
                                $to_file = $movedestine_ . $item;
    
                                rename($from_file, $to_file);
    
//                                 echo "file - from: ".$from_file.", to: ".$to_file."<br>";exit();
                            
                            }
                            
                        } else {

                            if (is_dir($dir . $item)) {

                                // chmod($dir, 0777);

                                $from_dir = $dir . $item;
                                $to_dir = $movedestine_ . $item;

                                rename($from_dir, $to_dir);

                                // echo "dir - from: ".$from_dir.", to: ".$to_dir."<br>";
                            }
                        }

                        // exit("not implement");
                    }
                    // echo $item."<br>";
                }

                // exit("<br>bruno - move");
            } else {

                if ($task == 'zip') {

                    $folder = $application->getParameter("folder");

                    if ($folder != null) {
                        if (substr($folder, strlen($folder) - 1) != "/") {
                            $folder .= DIRECTORY_SEPARATOR;
                        }
                    }

                    $element = $application->getParameter("element");

                    $filename = $application->getParameter("filename");

                    $filename = str_replace(":", "-", $filename);
                    $filename = str_replace("/", "-", $filename);
                    $filename = trim($filename) . ".zip";

                    $dir = PATH_USER_WORKSPACE_STORAGE . DIRNAME_SCRIPT . 
                    // .DIRECTORY_SEPARATOR
                    $folder;

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

                        $dir = PATH_USER_WORKSPACE_STORAGE . DIRNAME_SCRIPT . DIRECTORY_SEPARATOR . $application->getParameter("folder");

                        foreach ($element as $key => $item) {

                            $zip = new ZipArchive();

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
                                                $utils->chmod_r($dir . $newfolder);
                                                
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
                                            $utils->chmod_r($dir . $newfolder);
//                                             exit("=".$dir . $newfolder);
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
                    }
                }
            }
        }
    }
}

if ($folder == null) {

    $files_list = $utils->getListElementsDirectory1(PATH_USER_WORKSPACE_STORAGE . DIRNAME_SCRIPT . DIRECTORY_SEPARATOR, array(
        "data",
        "zip"
    ));
} else {

    if ($task == "rename") {

        $folder = $application->getParameter("folder");

        $files_list = $utils->getListElementsDirectory1(PATH_USER_WORKSPACE_STORAGE . DIRNAME_SCRIPT . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR, array(
            "data",
            "zip"
        ));
    } else {

        $files_list = $utils->getListElementsDirectory1(PATH_USER_WORKSPACE_STORAGE . DIRNAME_SCRIPT . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR, array(
            "data",
            "zip"
        ));
    }
}

$dir_list = $utils->getListDirectory(PATH_USER_WORKSPACE_STORAGE . DIRNAME_SCRIPT . DIRECTORY_SEPARATOR . $folder);

function create_zipfile($dir, $filename, $element)
{
    $zip = new ZipArchive();

    if ($zip->open($dir . $filename, ZIPARCHIVE::CREATE) !== TRUE) {
        exit("cannot open <$filename>\n");
    }

    // $dir = $dir = Properties::getBase_directory_destine($application)
    // .$application->getUser()
    // .DIRECTORY_SEPARATOR
    // .$application->getParameter("folder");

    foreach ($element as $key => $item) {

        if (is_dir($item)) {} else {
            if (is_file($dir . $item . ".data")) {
                $item .= ".data";
            }
        }

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
    $zip = new \ZipArchive();

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

function ConfirmDelete()
{
  var x = confirm("Are you sure you want to delete?");
  if (x)
     return true;
  else
    return false;
}

function renameFolder(obj){
	
	var newName = prompt("Please enter folder name", obj.name);
	
	if (newName != null) {
		//window.location.href="?component=<?php echo $application->getComponent();?>&controller=<?php echo $application->getController() ;?>&task=rename&folder="+obj.name+"&rename="+newName;

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

	if(task == 'remove'){

	  var x = confirm("Are you sure you want to delete?");
	  if (!x)
	     return;

	}

	if(task == 'move'){

	  var x = confirm("Are you sure you want to move?");
	  if (!x)
	     return;

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
										File (*.txt or *.zip)</a><br> <input type="button"
										value="New folder" name="folder"
										onclick="javascript: newFolder();" /> || <input type="button"
										value="New file" name="file" onclick="javascript: newFile();" />

									|| <input type="button" value="Remove" name="remove"
										onclick="javascript: sendAction('remove');" /> || <input
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

foreach ($dir_list as $key => $element) {

    // if($element["type"]=="dir"){
    if ($element == "scripts") {
        unset($files_list[$key]);
    }
    // }
}

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
											<th>Scripts</th>
											<th>Size</th>
											<th>DateTime</th>
										</tr>
<?php

$i = 0;
foreach ($files_list as $key => $element) {
    $i ++;

    $folder = $application->getParameter("folder");

    if ($folder == "") {
        $folder = "/";
    }

    if ($element["type"] == "dir") {

        echo "<tr><td>" . $i . "</td><td colspan='2'>" . 
        "<a title='Execute script' href='?component=moa&controller=run&foldername=" . $element["name"] . "&task=open&folder=" . $folder . "'>" . "<img align='middle' width='24px' src='" . $application->getPathTemplate() . "/images/icon-play.png' border='0'></a> " . 
        "<a onclick='javascript: renameFolder(this);' name='" . $element["name"] . "' title='Rename' href='#'>" . "<img align='middle' width='24px' src='" . $application->getPathTemplate() . "/images/icon-rename.png' border='0'></a> " . 
        "<a href='?component=" . $application->getComponent() . "&controller=" . $application->getController() . "&folder=" . (empty($folder) ? "" : $folder) . $element["name"] . "/&task=open'>" . "<img width='24px' align='middle' src='" . $application->getPathTemplate() . "/images/icon-folder.png' title='Open'/></a> " . 

        // ."<a title='Remove' onclick='javascript: return ConfirmDelete();' href='?component=home&controller=files&task=remove&folder=".$folder.$element["name"]."'>"
        // ."<img src='".$application->getPathTemplate()."images/icon-remove.gif' border='0'></a>

        "<label><input type='checkbox' name='element[]' value='" . $element["name"] . "' />" . $element["name"] . "</label> " . "</td><td>" . $element["size"] . "</td><td>" . $element["datetime"] . "</td></tr>";

        /*
         * echo "<tr><td><img align='middle' src='../../images/directory.png'/> "
         * ."<a onclick='javascript: renameFolder(this);' name='".$element["name"]."' title='rename' href='#'>"
         * ."<img align='middle' src='../../images/rename.png' border='0'></a> "
         * ."<a title='remover' onclick='javascript: return ConfirmDelete();' href='?component=".$application->getComponent()."&controller=".$application->getController()."&task=remove&folder=".$folder.$element["name"]."'><img src='../../images/remove.gif' border='0'></a></td><td>".$element["size"]."</td><td>".$element["datetime"]."</td></tr>";
         */
    }
}

$i = 0;
foreach ($files_list as $key => $element) {
    $i ++;

    // $element["name"] = substr($element["name"],0,strrpos($element["name"], "."));

    if ($element["type"] != "dir") {

        // exit(substr($element["name"],strrpos($element["name"], ".")+1));

        if (substr($element["name"], strrpos($element["name"], ".") + 1) == "zip") {

            $numberLines = "";

            echo "<tr><td>" . $i . "</td><td>" . 
            // ."<a title='Execute script' href='?component=moa&controller=run&filename=".$element["name"]."&task=open&folder=".$application->getParameter("folder")."'>"
            // ."<img align='middle' width='24px' src='".$application->getPathTemplate()."/images/icon-play.png' border='0'></a> "
            "<a href='?component=" . $application->getComponent() . "&controller=openreadonly&filename=" . $element["name"] . "&folder=" . $folder . "'>" . "<img width='16px' align='middle' src='" . $application->getPathTemplate() . "/images/icon-view.png' title='View contents'/></a> " . "<label><input type='checkbox' name='element[]' value='" . $element["name"] . "' />" . $element["name"] . "</label> </td>" . "<td align='center'>" . $numberLines . "</td>" . "<td>" . $element["size"] . "</td>" . "<td>" . $element["datetime"] . "</td></tr>";
        } else {

            $numberLines = $utils->getScriptsNumber(Properties::getBase_directory_destine($application) . $application->getUser() . DIRECTORY_SEPARATOR . DIRNAME_SCRIPT . DIRECTORY_SEPARATOR . $folder . $element["name"]);

            //$element["name"] = substr($element["name"], 0, strrpos($element["name"], "."));

            echo "<tr><td>" . $i . "</td><td>" . "<a title='Execute script' href='?component=moa&controller=run&filename=" . $element["name"] . "&task=open&folder=" . $folder . "'>" . "<img align='middle' width='24px' src='" . $application->getPathTemplate() . "/images/icon-play.png' border='0'></a> " . "<a href='?component=" . $application->getComponent() . "&controller=edit&filename=" . $element["name"] . "&folder=" . $application->getParameter("folder") . "'>" . "<img width='16px' align='middle' src='" . $application->getPathTemplate() . "/images/icon-view.png' title='View contents'/></a> " . "<label><input type='checkbox' name='element[]' value='" . $element["name"] . "' />" . $element["name"] . "</label> </td>" . "<td align='center'>" . $numberLines . "</td>" . "<td>" . $element["size"] . "</td>" . "<td>" . $element["datetime"] . "</td></tr>";
        }

        /*
         * echo "<tr><td> "
         * ."<a href='?component=home&controller=edit&filename=".$element["name"]."&folder=".$application->getParameter("folder")."'>"
         * .$element["name"]."</a> "
         * ."<a title='Execute script' href='?component=moa&controller=run&filename=".$element["name"]."&task=open&folder=".$application->getParameter("folder")."'>"
         * ."<img align='middle' width='24px' src='".$application->getPathTemplate()."/images/icon-play.png' border='0'></a> "
         * ."</td><td>".$element["size"]."</td><td>".$element["datetime"]."</td></tr>";
         */
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