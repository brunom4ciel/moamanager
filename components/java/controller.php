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
use moam\core\Template;
use moam\core\Properties;
use moam\libraries\core\utils\Utils;
// use ZipArchive;
// use moam\libraries\core\menu\Menu;

if (! class_exists('Application')) {
    $application = Framework::getApplication();
}

if (! $application->is_authentication() || $application->getUserType() != 1) {
    $application->alert("Error: you do not have credentials.");
}

// Framework::import("menu", "core/menu");

// if (! class_exists('Menu')) {
//     $menu = new Menu();
// }

Template::setDisabledMenu();

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

$files_extensions = array("java","zip");

if ($task == "folder") {

    $foldernew = $application->getParameter("foldernew");

    if ($folder == null) {

        $foldernew = Properties::getBase_directory_moa() . "src"  . DIRECTORY_SEPARATOR . $foldernew;
    } else {

        $foldernew = Properties::getBase_directory_moa() . "src"  . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $foldernew;
    }

    // exit("-".$foldernew);

    if (! is_dir($foldernew)) {
        mkdir($foldernew, 0777);
    }
} else {

    if ($task == "rename") {

        $from_folder = Properties::getBase_directory_moa() . "src"  . DIRECTORY_SEPARATOR . $application->getParameter("folder") . 
        // .DIRECTORY_SEPARATOR
        $application->getParameter("foldernow");

        $to_folder = Properties::getBase_directory_moa() . "src"  . DIRECTORY_SEPARATOR . $application->getParameter("folder") . 
        // .DIRECTORY_SEPARATOR
        $application->getParameter("rename");

        if ($application->getParameter("foldernow") == null) {

            $from_file = Properties::getBase_directory_moa() . "src"  . DIRECTORY_SEPARATOR . $application->getParameter("folder") . 
            // .DIRECTORY_SEPARATOR
            $application->getParameter("filenow");

            $to_file = Properties::getBase_directory_moa() . "src"  . DIRECTORY_SEPARATOR . $application->getParameter("folder") . 
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

        if ($task == "remove") {

    
            $element = $application->getParameter("element");
            
            $dir = Properties::getBase_directory_moa() . "src"  . DIRECTORY_SEPARATOR . $application->getParameter("folder");
            
            foreach ($element as $key => $item) {
                
                if (is_file($dir . $item)) 
                {
                    
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

                $dir = Properties::getBase_directory_moa() . "src"  . DIRECTORY_SEPARATOR . $application->getParameter("folder");

                foreach ($element as $key => $item) {

                    if ($movedestine != $item) {

                        if ($movedestine == "..") {

                            $movedestine_ = substr($dir, 0, strrpos($dir, "/"));
                            $movedestine_ = substr($movedestine_, 0, strrpos($movedestine_, "/") + 1);
                        } else {

                            $movedestine_ = Properties::getBase_directory_moa() . "src"  . DIRECTORY_SEPARATOR . $application->getParameter("folder") . $movedestine . DIRECTORY_SEPARATOR;
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
                
            } 
            else
            {
                if($task == "download")
                {
                    
                    $file = Properties::getBase_directory_moa() . "src"  
                        . DIRECTORY_SEPARATOR . $application->getParameter("folder") 
                    . $application->getParameter("file");
                    
                    
                    if (file_exists($file)) 
                    {                                                    
                        
                        header('Content-Description: File Transfer');
                        header('Content-Type: application/octet-stream');
                        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
                        header('Content-Transfer-Encoding: binary');
                        header('Expires: 0');
                        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                        header('Cache-Control: must-revalidate');
                        header('Pragma: public');
                        header('Content-Length: ' . filesize($file));
                                 
                        
                        ob_clean();
                        ob_end_flush();
                        
                        // readfile($file);
                        
                        $handle = fopen($file, "rb");
                        while (! feof($handle)) {
                            echo fread($handle, 1000);
                        }
                    } else {
                        // echo "Error: file not found.";
                        $application->alert("Error: file not found.");
                    }
                }
                
            }
            
        }
    }
}

if ($folder == null) {

    $files_list = $utils->getListElementsDirectory1(Properties::getBase_directory_moa() . "src" . DIRECTORY_SEPARATOR, array(
        "java","zip"
    ));
} else {

    if ($task == "rename") {

        $folder = $application->getParameter("folder");

        $files_list = $utils->getListElementsDirectory1(Properties::getBase_directory_moa() . "src" . DIRECTORY_SEPARATOR . $folder, 
            // .DIRECTORY_SEPARATOR
            array(
                "java","zip"
            ));
    } else {

        $files_list = $utils->getListElementsDirectory1(Properties::getBase_directory_moa() . "src" . DIRECTORY_SEPARATOR . $folder, 
            // .DIRECTORY_SEPARATOR
            array(
                "java","zip"
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

$dir_list = $utils->getListDirectory(Properties::getBase_directory_moa() . "src" . DIRECTORY_SEPARATOR . $folder);

foreach ($dir_list as $key => $element) {

    if (trim($element) == DIRNAME_SCRIPT || trim($element) == DIRNAME_TRASH || trim($element) == DIRNAME_BACKUP) {

        unset($dir_list[$key]);
    }
}




?>



<script>


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



function sendAction(task){

	if(task == 'upload'){

	  var x = confirm("sure you want to send the file?");
	  if (!x)
	     return;

	}
	
	if(task == 'remove'){

	  var x = confirm("Are you sure you want to remove?");
	  if (!x)
	     return;

	}

	if(task == 'move'){

	  var x = confirm("Are you sure you want to move?");
	  if (!x)
	     return;

	}

	
	document.getElementById('task').value = task;
	document.getElementById('formulario').submit();
	
}




</script>



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

								
    
    
    <?php

    if (count($error) > 0) {

        for ($i = 0; $i < count($error); $i ++) {
            echo $error[$i] . "<br>";
        }
    }

    ?>
    
<a
										href="?component=<?php echo $application->getComponent()?>&controller=upload&folder=<?php echo $folder;?>">File Upload
										(*.java)</a> <br>
									<!-- 
<input type="button" value="BRUNO-Kill-files-alert" name="bruno" onclick="javascript: sendAction('bruno');" />
|| -->
									<input type="button" class="btn btn-default" value="New folder" name="folder"
										onclick="javascript: newFolder();" /> || <input type="button" class="btn btn-danger"
										value="Delete" name="trash"
										onclick="javascript: sendAction('remove');" /> || Move to: <select
										name="movedestine" class="btn btn-default" id=movedestine>		
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
													
												</select> <input type="button" class="btn btn-default" value="Move" name="move"
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

        echo "<tr><td>" . $i . "</td><td>" 
            . "<a onclick='javascript: renameFile(this);' name='" . $element["name"] . "' title='Rename' href='#'>" 
            . "<img align='middle' width='24px' src='" . $application->getPathTemplate() . "/images/icon-rename.png' border='0'></a> " 
        . "<a href='?component=" . $application->getComponent() . "&controller=edit&filename=" . $element["name"] . "&folder=" . $application->getParameter("folder") . "'>" 
            . "<img width='16px' align='middle' src='" . $application->getPathTemplate() . "/images/icon-view.png' title='View contents'/></a> " . 
        '<a href="' . PATH_WWW . '?component=java&tmpl=tmpl&task=download&file=' . $application->getParameter("folder") . $element["name"] . '">' . "<img width='16px' align='middle' src='" . $application->getPathTemplate() . "/images/icon_download.png' title='View contents'/></a> " . 
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
	
	
	<br>
	
									<div style="float: right; padding-left: 10px">
									
											<input type="button" class="btn btn-default"
                							onclick="javascript: window.location.href='?component=settings';"
                							name="cancel" value="Return" />
									</div>
									
									
									
	