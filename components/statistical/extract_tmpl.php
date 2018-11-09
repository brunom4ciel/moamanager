<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\components\extract;

defined('_EXEC') or die();

use moam\core\Framework;
use moam\core\Application;
use moam\core\Properties;
use moam\core\Template;
use moam\libraries\core\utils\Utils;
use moam\libraries\core\menu\Menu;
use moam\libraries\core\mining\Mining;
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
Framework::import("Mining", "core/mining");

Template::addHeader(array(
    "tag" => "link",
    "type" => "text/css",
    "rel" => "stylesheet",
    "href" => "" . $application->getPathTemplate() . "/css/table-excel.css"
));

Template::addHeader(array(
    "tag" => "script",
    "type" => "text/javascript",
    "src" => "" . $application->getPathTemplate() . "/javascript/base64.js"
));

$utils = new Utils();

$task = $application->getParameter("task");

$task = $application->getParameter("task");
$folder = $application->getParameter("folder");

if ($folder != null) {
    if (substr($folder, strlen($folder) - 1) != "/") {
        $folder .= DIRECTORY_SEPARATOR;
    }
}

$scripts = "";

if ($task == "view") {

    $filename = PATH_USER_WORKSPACE_STORAGE . $application->getParameter("folder") . $application->getParameter("filename");

    // Header('Content-Description: File Transfer');
    // Header('Content-Type: application/force-download');
    // Header('Content-Disposition: attachment; filename=pedidos.csv');

    // ob_end_clean();

    // echo "<pre>";
    $scripts = $utils->getContentFile($filename);
} else if ($task == "download") {

    $filename = PATH_USER_WORKSPACE_STORAGE . $application->getParameter("folder") . $application->getParameter("filename");

    ob_end_clean();

    Header('Content-Description: File Transfer');

    $extension = $application->getParameter("filename");
    $extension = substr($extension, strrpos($extension, ".") + 1);

    switch ($extension) {

        case 'tex':

            header('Content-Type: application/x-tex');
            break;
        case 'csv':

            header('Content-Type: text/csv');

            break;
        case 'html':

            header('Content-Type: text/html');
            break;
    }

    // Header('Content-Type: application/force-download');
    header('Content-Disposition: attachment;filename=' . $application->getParameter("filename"));

    echo $utils->getContentFile($filename);

    $contLength = ob_get_length();
    header('Content-Length: ' . $contLength);

    exit();
} else {

    $friedman_bin = "/opt/statistical/friedman-test";

    $element = $application->getParameter("element");

    $dir = PATH_USER_WORKSPACE_STORAGE . $application->getParameter("folder");

    $count = 0;

    foreach ($element as $key => $item) {

        if (is_file($dir . $item)) {

            $command = $friedman_bin . " < " . $dir . $item . " > " . $dir . $item . "-output.csv";

            // echo $command;
            exec($command);
        }

        $count ++;
    }

    if ($count < 2)
        $scripts = $utils->getContentFile($dir . $item . "-output.csv");
}

?>


<style>
div#table_id table tr td {
	border: 1px solid #cccccc;
	border-collapse: collapse;
	padding: 1px;
}
</style>

<div class="content content-alt">
	<div class="container" style="width: 90%">
		<div class="row">
			<div class="">

				<div class="card" style="width: 100%">
					<div class="page-header">
						<h1>
							<a href="<?php echo $_SERVER['REQUEST_URI']?>">Data</a>
						</h1>
					</div>

					<div style="width: 100%; padding-bottom: 15px; display: table">


						<div
							style="float: left; width: 100%; border: 1px solid #fff; display: table;">


							<!-- 	<input type="button" value="Return" name="return" onclick="javascript: returnPage();" /> 	<br><br>	 -->	
<?php

if ($task == "preview") {} else {

    if (! empty($scripts)) {
        ?>	
			Content:<br>
							<textarea id="data" style="width: 100%; height: 400px;"
								name="data"><?php echo $scripts?></textarea>
							<br>
													
	<?php
    }
}
?>	
									
<script>


function returnPage(){
	//window.history.go(-1);

	//http://localhost/iea/?component=moa&controller=reportview&filename=maciel.log&folder=New%20Folder/

		window.location.href='?component=<?php echo $application->getParameter("component");?>'
			+'&controller=extract-values'
			+'&folder=<?php echo $application->getParameter("folder");?>'
			+'&task=open';
		
}


</script>

						</div>

					</div>

				</div>
			</div>
		</div>
	</div>
</div>