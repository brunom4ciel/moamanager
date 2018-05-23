<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\templates;

defined('_EXEC') or die();

use moam\core\Template;

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
	
	
		<?php require_once( PATH_TEMPLATES . "/default/menu.php")?>
		
		
		
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