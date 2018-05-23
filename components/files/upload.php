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

$error_msg = "";

$folder = $application->getParameter("folder");

if ($folder != null) {
    if (substr($folder, strlen($folder) - 1) != "/") {
        $folder .= DIRECTORY_SEPARATOR;
    }
}

$folder = $application->getParameter("folder");

if (isset($_FILES['uploadfile'])) {

    $files_extensions = array(
        "txt",
        "zip"
    );

    $uploaddir = Properties::getBase_directory_destine($application) . $application->getUser() . DIRECTORY_SEPARATOR . $folder;

    $uploadfile = $uploaddir . basename($_FILES['uploadfile']['name']);

    // verifica se arquivo existe em tmp
    if (is_uploaded_file($_FILES['uploadfile']['tmp_name'])) {

        // verifica o formato da extensão do arquivo
        if (in_array(substr($uploadfile, strrpos($uploadfile, ".") + 1), $files_extensions)) {

            // se o arquivo já existir, apaga
            if (file_exists($uploadfile))
                unlink($uploadfile);

            // move o arquivo de tmp para destino
            if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $uploadfile)) {

                // verifica se arquivo existe em destino
                if (is_file($uploadfile)) {

                    // verifica se diretorio existe
                    if (! is_dir(Properties::getBase_directory_destine($application) . $application->getUser())) {

                        // cria um novo diretório
                        if (mkdir(Properties::getBase_directory_destine($application) . $application->getUser(), 0777, true))

                            // define permissões ao diretório
                            if (! chmod(Properties::getBase_directory_destine($application) . $application->getUser(), 0777))
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
} else {
    $error_msg = "";
}

?>



<div class="content content-alt">
	<div class="container" style="width: 70%">
		<div class="row">
			<div class="">
				<div class="card" style="width: 100%">



					<div class="page-header">
						<h1>
							<a href="<?php echo $_SERVER['REQUEST_URI']?>">Upload files</a>
						</h1>
					</div>
							
							<?php

    if (! empty($error_msg)) {

        echo $error_msg;
    }
    ?>
							
							
							
            				<form method="POST"
						action="<?php echo $_SERVER['PHP_SELF'];?>" name="loginForm"
						enctype="multipart/form-data">
						<input type="hidden"
							value="<?php echo $application->getComponent()?>"
							name="component"> <input type="hidden"
							value="<?php echo $application->getController()?>"
							name="controller"> <input type="hidden" name="folder"
							value="<?php echo $folder;?>" />

						<table>
							<tr>
								<td>Upload files (*.txt, *.zip):</td>
								<td><input type="file" name="uploadfile" /></td>
								<td><input type="submit" name="default" value="Send" /></td>
							</tr>
						</table>

					</form>

					<input type="button"
						onclick="javascript: window.location.href='?component=files&folder=<?php echo $folder;?>';"
						name="cancel" value="Back files" />
				</div>

			</div>
		</div>
	</div>
</div>
</div>

