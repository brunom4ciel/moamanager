<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\templates;

defined('_EXEC') or die();

use moam\core\Framework;
use moam\core\Template;

if (!class_exists('Application'))
{
    $application = Framework::getApplication();
}

define('IS_AUTHENTICATION', $application->is_authentication());


?>
<!DOCTYPE html>
<html itemscope itemtype="http://schema.org/Product">
<head>
<title><?php echo (Template::getTitle() == null ? APPLICATION_NAME : Template::getTitle());?></title>
<link rel="stylesheet"
	href="<?php echo PATH_WWW;?>templates/default/css/style2.css">
<link rel="stylesheet"
	href="<?php echo PATH_WWW;?>templates/default/css/controls.css">
<link rel="stylesheet"
	href="<?php echo PATH_WWW;?>templates/default/css/menu.css">

		<?php echo Template::getHeadersHTML();?>
		
	</head>

<body>
	

<?php 

if(IS_AUTHENTICATION){

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
							<a href="<?php echo PATH_WWW;?>?component=settings"><?php echo USERNAME;?></a>
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
		
		
		
		<?php

if (isset($_GET['alert'])) {

    if ($_GET['alert'] == true) {
        ?>		
				
		<div class="content content-alt">
		<div class="container">
			<div class="row">
				<div class="col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
					<div class="card">
						<div class="page-header">
							<h1>Alert</h1>
							
							<?php

        if (! empty($_GET['msg'])) {

            echo $_GET['msg'];
        }
        ?>
								
							</div>



						<a href="javascript: history.go(-1);" style="float: right"><?php echo LABEL_BACK?></a><br>



					</div>
				</div>
			</div>
		</div>
	</div>
				
		<?php
    } else {

        echo Template::getBody();
    }
} else {

    echo Template::getBody();
}

?>
	

	</body>
</html>