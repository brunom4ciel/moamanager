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
							<a href="?"><?php echo TITLE_COMPONENT?></a>
						</h1>
					</div>

					<div style="width: 100%; padding-bottom: 15px; display: table">

						<div style="float: left; width: auto; border: 1px solid #fff">
																
									<?php echo $application->showMenu($menu);?>									

						</div>

						<div
							style="float: left; width: 100%; max-width: 80%; border: 1px solid #fff">

							<div style="float: left; padding-left: 20px; width: 100%">
								<div
									style="float: left; padding-left: 5px; width: 100%; margin-top: 5px;">
									<div class="div_table" style="width: 100%">

										<div id="container"></div>
									</div>
								</div>
							</div>

						</div>

					</div>

				</div>
			</div>
		</div>
	</div>
</div>
