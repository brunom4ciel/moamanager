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
use moam\core\Properties;
use moam\libraries\core\utils\Utils;
use moam\core\Template;

if (! class_exists('Application')) {
    $application = Framework::getApplication();
}

Template::setDisabledMenu();

Framework::import("Utils", "core/utils");

$utils = new Utils();

$error = array();

$task = $application->getParameter("task");
$filename = $application->getParameter("filename");

if ($task == "remove") {

    $path_jar = Properties::getBase_directory_moa() . 
    // .App::getDirectorySeparator()
    "datasets" . DIRECTORY_SEPARATOR;

    if (file_exists($path_jar . $filename)) {

        // if(strpos($filename, App::getUser())===false){

        // $error[] = "I'm sorry, but the file does not belong to your user.";

        // }else{
        if ($application->getUserType() == 1) {
            if (unlink($path_jar . $filename)) {

                $error[] = "File successfully deleted.";
            }

            // }
        }
    }
} else {

    if ($task == "download") {

        $file = Properties::getBase_directory_moa() . 
        // .App::getDirectorySeparator()
        "datasets" . DIRECTORY_SEPARATOR . $filename;

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
    } else {}
}

if (isset($_POST['default'])) {

    if (strtolower($_POST['default']) == "update") {

        if (isset($_FILES['jarfile'])) {

            $files_extensions = array(
                "arff"
            );

            // $uploaddir = $base_directory_destine = dirname(__FILE__)."/../tests/".$folder;

            $uploaddir = Properties::getBase_directory_moa() . "datasets/";
            $uploadfile = $_FILES['jarfile']['name'] . ""; // $uploaddir.Framework::getUser().".jar"; //basename($_FILES['spreadsheet']['name']);

            // verifica se arquivo existe em tmp
            if (is_uploaded_file($_FILES['jarfile']['tmp_name'])) {

                // verifica o formato da extensão do arquivo
                if (in_array(substr($uploadfile, strrpos($uploadfile, ".") + 1), $files_extensions)) {

                    // se o arquivo já existir, apaga
                    // if(file_exists($uploadfile)){

                    // }
                    // unlink($uploadfile);

                    $y = 1;
                    $filename = substr($uploadfile, 0, strrpos($uploadfile, "."));

                    $uploadfile_new = $uploaddir . $uploadfile;

                    while (is_file($uploadfile_new)) {

                        $uploadfile_new = $uploaddir . $filename . 
                        // .App::getUser()
                        "-v" . $utils->format_number($y ++, 2) . ".arff";
                    }

                    $uploadfile = $uploadfile_new;

                    // move o arquivo de tmp para destino
                    if (move_uploaded_file($_FILES['jarfile']['tmp_name'], $uploadfile)) {

                        // verifica se arquivo existe em destino
                        if (is_file($uploadfile)) {

                            // verifica se diretorio existe
                            // if(!is_dir(Framework::getBase_directory_destine().Framework::getUser())){

                            // cria um novo diretório
                            // if(mkdir(Framework::getBase_directory_destine().Framework::getUser(), 0777, true))
                            // define permissões ao diretório
                            // if(!chmod(Framework::getBase_directory_moa(), 0777))
                            $error[] = "Error directory permissions.";
                            // else{
                            // define permissões ao arquivo
                            if (! chmod($uploadfile, 0777))
                                $error[] = "Error setting permissions.";
                            else
                                $error[] = "Upload successful";
                            // }
                            // else {
                            // $error[] = "Error directory not create.";
                            // /}

                            // }else{

                            // //define permissoes ao arquivo
                            // if(!chmod($uploadfile, 0777))
                            // $error[] = "Error setting permissions.";
                            // else
                            // $error[] = "Upload successful";

                            // }
                        } else
                            $error[] = "Upload successful";
                    } else {
                        $error[] = "lammer\n";
                    }
                } else {
                    $error[] = "Extension not supported.";
                }
            } else {
                $error[] = "file not exist.";
            }
        } else {
            $error[] = "file not found.";
        }
    } else {

        // if(file_exists(framework::getBase_directory_moa()."datasets/".Framework::getUser().".jar"))
        // unlink(framework::getBase_directory_moa()."bin/".Framework::getUser().".jar");

        // $error[] = "MOA default successful.";
    }
} else {
    // $error_msg = "Not defined fields";
}

$files_list = $utils->getListElementsDirectory1(Properties::getBase_directory_moa() . "datasets/", array(
    "arff"
));

// foreach($files_list as $key=>$element){

// if(strpos($element["name"], App::getUser())===false){
// unset($files_list[$key]);
// }else{

// }

// }

?>

<script>

function remove(filename){

	var x = confirm("Confirm remove?");
	if (!x){
	    return;
	}else{

		window.location.href="?component=<?php echo $application->getComponent();?>&controller=<?php echo $application->getController();?>&task=remove&filename="+filename+"";
	}
	
}

</script>

							<div class="page-header">
        						<h1>
        							<a href="<?php echo $_SERVER['REQUEST_URI']?>">Datasets</a>
        						</h1>
        					</div>

							
							<?php

    if (count($error) > 0) {

        for ($i = 0; $i < count($error); $i ++) {
            echo $error[$i] . "<br>";
        }
    }

    ?>
							
								
							<table border='1' id="temporary_files" style="width: 100%;">
						<tr>
							<th>#</th>
							<th style="width: 60%;">File</th>
							<th>Size</th>
							<th>DateTime</th>
						</tr>
							
							<?php

    $i = 1;
    foreach ($files_list as $key => $element) {

        echo "<tr><td>" . ($i ++) . "</td>" . "<td>";

        echo $element["name"];

        if ($application->getUserType() == 1) {

            echo "<a href='#' onclick=\"javascript: remove('" . $element["name"] . "');\">" . "<img width='16px' align='middle' src='" . $application->getPathTemplate() . "/images/icon-remove.gif' title='Remove'/></a> ";
        }

        echo "<a href='" . PATH_WWW . "?component=" . $application->getComponent() . "&controller=" . $application->getController() . "&task=download&filename=" . $element["name"] . "'/>" . "<img width='16px' align='middle' src='" . $application->getPathTemplate() . "/images/icon_download.png' title='Download'/></a> " . 
        "</td>" . "<td>" . $element["size"] . "</td>" . "<td>" . $element["datetime"] . "</td></tr>";
    }

    ?>
							
							</table>
							
							<?php if($application->getUserType() == 1){ ?>
							
            				<form method="POST"
						action="<?php echo $_SERVER['PHP_SELF'];?>" name="loginForm"
						enctype="multipart/form-data">
						<input type="hidden"
							value="<?php echo $application->getComponent()?>"
							name="component"> <input type="hidden"
							value="<?php echo $application->getController()?>"
							name="controller">

						<h2>Upload File</h2>
						<table>
							<tr>
								<td>Dataset Upload (*.arff):</td>
								<td><input type="file" class="btn btn-default" name="jarfile" /></td>
								<td>&nbsp; <input type="submit" class="btn btn-success" name="default" value="Send" /></td>
							</tr>
						</table>
						<!-- <input type="submit" name="default" value="default system" />-->

					</form>
							<?php }?>
							

									<div style="float: right; padding-left: 10px">
									
											<input type="button" class="btn btn-default"
                							onclick="javascript: window.location.href='?component=settings';"
                							name="cancel" value="Return" />
									</div>
									
									
									
									
