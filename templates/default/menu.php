<?php
/**
 * @package    MOAM.Application
*
* @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

namespace moam\templates;

defined('_EXEC') or die;

use moam\core\Framework;
use moam\core\Template;

// get Instantiate the application.
$application = Framework::getApplication();


if($application->is_authentication()){

	$vars = "&http_referer="
			.urlencode(base64_encode($_SERVER["REQUEST_URI"]));

			?>
		<nav class="navbar navbar-default">
			<div class="container-fluid">
				<div class="navbar-header">
					<a class="navbar-brand" href="<?php echo PATH_WWW;?>"><?php echo (Template::getTitle() == null ? APPLICATION_NAME : Template::getTitle());?></a>
				</div>
				<div class="navbar-collapse collapse" collapse="navCollapsed" style="height: 0px;">
					<ul class="nav navbar-nav navbar-right">
						<li>
							<a href="#" style="padding:0px"><img width="34px" src="<?php echo WWW_IMAGES?>/bug-128.png"></a>
						</li>
						<li>
							<a href="<?php echo PATH_WWW;?>?component=settings"><?php echo $application->getUser();?></a>
						</li>
						<!-- <li>
							<a href="<?php echo PATH_WWW;?>?component=user&controller=passwordChange">Change Password</a>
						</li> -->
						<li>
							<a href="<?php echo PATH_WWW;?>?component=user&controller=login&logout<?php echo $vars;?>"><?php echo MENU_LOGOUT?></a>
						</li>
					</ul>
				</div>
			</div>
		</nav>
		
<?php }else{ ?>
		<nav class="navbar navbar-default">
			<div class="container-fluid">
				<div class="navbar-header">
					<a class="navbar-brand" href="<?php echo PATH_WWW;?>"><?php echo (Template::getTitle() == null ? APPLICATION_NAME : Template::getTitle());?></a>
				</div>
				<div class="navbar-collapse collapse" collapse="navCollapsed" style="height: 0px;">
					<ul class="nav navbar-nav navbar-right">
						<li class="subdued">
							<a class="subdued" href="<?php echo PATH_WWW;?>?component=about"><?php echo MENU_ABOUT?></a>
						</li>
		
						<li>
							<a href="<?php echo PATH_WWW;?>?component=user&controller=register"><?php echo MENU_REGISTER?></a>
						</li>
						<li>
							<a href="<?php echo PATH_WWW;?>?component=user&controller=login"><?php echo MENU_LOGIN?></a>
						</li>
					</ul>
				</div>
			</div>
		</nav>
<?php }?>