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
use moam\libraries\core\utils\Utils;
use moam\libraries\core\menu\Menu;

if (!class_exists('Application'))
{
    $application = Framework::getApplication();
}

Framework::import("Utils", "core/utils");
Framework::import("menu", "core/menu");

if (! class_exists('Menu')) {
    $menu = new Menu();
}

$utils = new Utils();

define('IS_AUTHENTICATION', $application->is_authentication());
define('IS_ADMIN', ($application->getUserType() == 1?TRUE:FALSE));


if(IS_AUTHENTICATION)
{
    $isUpdate = FALSE;
    $version = "";
    if(IS_ADMIN)
    {
        $version = $application->getSoftwareRemoteVersion();
        
        $isUpdate = $utils->compareVersion(MOAMANAGER_VERSION, $version);
//         $update = new SoftwareUpdate();
//         $isUpdate = $update->isUpdate(MOAMANAGER_VERSION);
        
    }
}

if(Template::getDisabledMenu())
{
    $menu = "";
}
else 
{
    $menu = $application->showMenu($menu);
}

if(!defined('TITLE_COMPONENT'))
{
    define('TITLE_COMPONENT','');
}

?>
<!DOCTYPE html>
<html>
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
					<a class="navbar-brand" href="<?php echo PATH_WWW;?>"><?php echo (Template::getTitle() == null ? APPLICATION_NAME : Template::getTitle());?></a><?php echo (IS_ADMIN?($isUpdate ? "&nbsp;<a href='?component=settings&controller=softwareupdate' title='Click to software update'>New version ".$version."</a>":""  ):""); ?>
				</div>
				<div class="navbar-collapse collapse" collapse="navCollapsed" style="height: 0px;">
					<ul class="nav navbar-nav navbar-right">
						<li>
							<a href="<?php echo PATH_WWW;?>?component=contact" title="Bug reports" style="padding:0px"><img width="34px" src="<?php echo WWW_IMAGES?>/bug-128.png"></a>
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

$body = false;
		
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

        $body = true;
    }
} else {

    $body = true;
}

?>

<div class="content content-alt"><!-- 1 open -->    
	<div class="container" style="width: 90%"><!-- 2 open -->	    
		<div class="row"><!-- 3 open -->			
			<div class=""><!-- 4 open -->				
				<div class="card" style="width: 100%"><!-- 5 open -->
				
					<?php 
					if($menu == "")
    				{            				    
    				    
    				            				
    				}else{?>
					<div class="page-header">
						<h1>
							<a href="<?php echo $_SERVER['REQUEST_URI']?>"><?php echo TITLE_COMPONENT?></a>
						</h1>
					</div>
					<?php }?>
					
                    <div class="componentcontainer"><!-- component column open -->
                    	
                    	<?php if($menu != ""){?>
                    	<div class="divmenuleft"><!-- left menu column open -->
                    		<?php echo $menu;?>
						</div><!-- left menu column close -->
						<?php }?>


                      	<div class="<?php echo ($menu != ""?"divcontentmenu":"divcontent")?>"><!-- component content column open -->

            				<?php 
            				if($body == true)
            				{            				    
            				    echo Template::getBody();
            				            				
            				 }?>
            				
						</div><!-- component content column open -->
                    </div><!-- component column close -->									
				</div><!-- 5 close -->				
			</div><!-- 4 close -->						
		</div><!-- 3 close -->				
	</div><!-- 2 close -->	
</div><!-- 1 close -->
	
<div style="width:98%;text-aling:center;font-size:10px; display:flex;  align-items: center;  justify-content:center;margin:10px;"><?php echo APPLICATION_NAME . " Version " . MOAMANAGER_VERSION;?></div>

	
		
	</body>
</html>