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
use moam\libraries\core\utils\Utils;
if (! class_exists('Application')) {
    $application = Framework::getApplication();
}

Framework::import("Utils", "core/utils");

$utils = new Utils();

$error = array();

$task = $application->getParameter("task");
$filename = $application->getParameter("filename");

if ($task == "remove") {

    $path_jar = PATH_MOA_BIN;

    if (file_exists($path_jar . $filename)) {

        if (strpos($filename, $application->getUser()) === false) {

            $error[] = "I'm sorry, but the file does not belong to your user.";
        } else {

            if (unlink($path_jar . $filename)) {

                $error[] = "File successfully deleted.";
            }
        }
    }
} else {

    if ($task == "download") {

        $file = PATH_MOA_BIN . $filename;

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

        if ($task == "default") {

            if ($filename != USERNAME . ".jar") {

                $from_file = PATH_MOA_BIN . $filename;

                $last_file_default = DEFAULT_MOA_BIN_USER;

                $to_file = DEFAULT_MOA_BIN_USER;

                $y = 1;

                while (is_file($last_file_default)) {

                    $last_file_default = PATH_MOA_BIN . USERNAME . "-v" . $utils->format_number($y ++, 4) . ".jar";
                }

                // default to other
                rename($to_file, $last_file_default);

                rename($from_file, $to_file);
            }
        }
    }
}

if (isset($_POST['default'])) {

    if (strtolower($_POST['default']) == "update") {

        if (isset($_FILES['jarfile'])) {

            $files_extensions = array(
                "jar"
            );

            // $uploaddir = $base_directory_destine = dirname(__FILE__)."/../tests/".$folder;

            $uploaddir = PATH_MOA_BIN;
            $uploadfile = $uploaddir . USERNAME . ".jar"; // basename($_FILES['spreadsheet']['name']);

            // verifica se arquivo existe em tmp
            if (is_uploaded_file($_FILES['jarfile']['tmp_name'])) {

                // verifica o formato da extensão do arquivo
                if (in_array(substr($uploadfile, strrpos($uploadfile, ".") + 1), $files_extensions)) {

                    // se o arquivo já existir, apaga
                    // if(file_exists($uploadfile)){

                    // }
                    // unlink($uploadfile);

                    $y = 1;

                    while (is_file($uploadfile)) {
                        $uploadfile = $uploaddir . USERNAME . "-v" . $utils->format_number($y ++, 4) . ".jar";
                    }

                    // move o arquivo de tmp para destino
                    if (move_uploaded_file($_FILES['jarfile']['tmp_name'], $uploadfile)) {

                        // verifica se arquivo existe em destino
                        if (is_file($uploadfile)) {

                            // //verifica se diretorio existe
                            // if(!is_dir(Properties::getBase_directory_destine().$application->getUser()))
                            // {

                            // //cria um novo diretório
                            // if(mkdir(Properties::getBase_directory_destine().$application->getUser(), 0777, true))
                            // {
                            // //define permissões ao diretório
                            // if(!chmod(Properties::getBase_directory_destine().$application->getUser(), 0777))
                            // $error[] = "Error directory permissions.";
                            // else{
                            // //define permissões ao arquivo
                            // if(!chmod($uploadfile, 0777))
                            // $error[] = "Error setting permissions.";
                            // else
                            // $error[] = "Upload successful";
                            // }

                            // }
                            // else {
                            // $error[] = "Error directory not create.";
                            // }

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

        if (file_exists(DEFAULT_MOA_BIN_USER))
            unlink(DEFAULT_MOA_BIN_USER);

        $error[] = "MOA default successful.";
    }
} else {
    // $error_msg = "Not defined fields";
}

$files_list = $utils->getListElementsDirectory1(PATH_MOA_BIN, array(
    "jar"
));

foreach ($files_list as $key => $element) {

    if (strpos($element["name"], $application->getUser()) === false) {
        unset($files_list[$key]);
    } else {}
}

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

<div class="content content-alt">
	<div class="container" style="width: 70%">
		<div class="row">
			<div class="">
				<div class="card" style="width: 100%">



					<div class="page-header">
						<h1>MOA Binary</h1>
					</div>
							
							<?php

    if (count($error) > 0) {

        for ($i = 0; $i < count($error); $i ++) {
            echo $error[$i] . "<br>";
        }
    }

    ?>
							
							<p>Versions user</p>

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

        if ($element["name"] == USERNAME . ".jar") {

            echo "" . "<img width='16px' align='middle' src='" . $application->getPathTemplate() . "/images/icon-star.png' title='default'/> ";
        } else {

            echo "<a href='" . PATH_WWW . "?component=" . $application->getComponent() . "&controller=" . $application->getController() . "&task=default&filename=" . $element["name"] . "'/>" . "<img width='16px' align='middle' src='" . $application->getPathTemplate() . "/images/icon-star2.png' title='Set default'/></a> ";
        }

        echo $element["name"] . "<a href='#' onclick=\"javascript: remove('" . $element["name"] . "');\">" . "<img width='16px' align='middle' src='" . $application->getPathTemplate() . "/images/icon-remove.gif' title='Remove'/></a> " . "<a href='" . PATH_WWW . "?component=" . $application->getComponent() . "&controller=" . $application->getController() . "&task=download&filename=" . $element["name"] . "'/>" . "<img width='16px' align='middle' src='" . $application->getPathTemplate() . "/images/icon_download.png' title='Download'/></a> " . 
        "</td>" . "<td>" . $element["size"] . "</td>" . "<td>" . $element["datetime"] . "</td></tr>";
    }

    ?>
							
							</table>

					<form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>"
						name="loginForm" enctype="multipart/form-data">
						<input type="hidden"
							value="<?php echo $application->getComponent()?>"
							name="component"> <input type="hidden"
							value="<?php echo $application->getController()?>"
							name="controller">

						<h2>Upload File</h2>
						<table>
							<tr>
								<td>MOA Binary Upload (*.jar):</td>
								<td><input type="file" name="jarfile" /></td>
								<td><input type="submit" name="default" value="update" /></td>
							</tr>
						</table>
						<!-- <input type="submit" name="default" value="default system" />-->

					</form>


				</div>

			</div>
		</div>
	</div>
</div>
</div>

