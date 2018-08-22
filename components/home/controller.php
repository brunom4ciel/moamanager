<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\components\home;

use moam\core\Framework;
use moam\core\Application;
use moam\libraries\core\menu\Menu;
use moam\core\Template;
use moam\libraries\core\utils\Utils;

if (! class_exists('Application')) {
    $application = Framework::getApplication();
}

if (! $application->is_authentication()) {
    $application->alert("Error: you do not have credentials.");
}

Framework::import("menu", "core/menu");

Framework::import("Utils", "core/utils");
// Framework::import("class_CPULoad", "core/sys");

// Template::addHeader(array("tag"=>"script",
//     "type"=>"text/javascript",
//     "src"=>""
//     . $application->getPathTemplate()
//     . "/javascript/base64.js"));

$utils = new Utils();

if (! class_exists('Menu')) {
    $menu = new Menu();
}

Template::addHeader(array(
    "tag" => "script",
    "type" => "text/javascript",
    "src" => "" . PATH_WWW . "templates/default/javascript/json-to-table.js"
));


// Template::addHeader(array("tag"=>"link",
// "type"=>"text/css",
// "rel"=>"stylesheet",
// "href"=>"" . PATH_WWW . "templates/default/css/style2.css"));

// Template::setTitle("Teste");

// $menu = Framework::getInstance("Menu");

// $application = Framework::getApplication();

$time = $application->getParameter("time");

if (! empty($time))
    sleep($time);

?>
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
							style="float: left; width: 80%; border: 1px solid #fff; display: table-cell;overflow-wrap: break-word;">
			
																
																
						<div id="usage_machine" style="text-align: left;display: block;overflow-wrap: break-word;max-width: 100%;">
								<pre style="font-family: monospace,monospace;font-size: 11px;text-aling:left;max-width: 90%;">

<?php 
						
						$cmd = "w";
						// $cmd = "uptime| sed 's/,//g'| awk '{print $3\" \"$4\" e \"$5\"h\"}'";
						
						// $output = shell_exec($cmd);
						$result = $utils->runExternal($cmd);
						$output = $result["output"];
						$output = explode("\n", $output);
						
						echo trim($output[0])."\n".$output[1]."\n".$output[2];
						

						
						?>	
						
</pre>

<pre style="font-family: monospace,monospace;font-size: 11px;text-aling:left;overflow-wrap: break-word;max-width: 90%;">

<?php 
$sysinfo = $utils->getHardwareInfo();

echo $sysinfo;

$utils->getHardwareKernelVersion();
?>	

</pre>
							</div>
							
						
					

					</div>

				</div>
			</div>
		</div>
	</div>
</div>

