<?php


namespace moam\components\extract;

defined('_EXEC') or die;

use moam\core\Framework;
use moam\core\Application;

if (!class_exists('Application'))
{
    $application = Framework::getApplication();
}

if(!$application->is_authentication())
{
    $application->alert ( "Error: you do not have credentials." );
}

$msg = $application->getParameter("msg");
$http_referer = urldecode(base64_decode($application->getParameter("http_referer")));

?>


<div class="content content-alt">
	<div class="container">
		<div class="row">
			<div class="col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
				<div class="card">
					<div class="page-header">
						<h1>Alert</h1>
							
							<?php

    if (! empty($msg)) {

        echo $msg;
    }
    ?>
								<a href="<?php echo $http_referer;?>"><?php echo $http_referer;?></a>
					</div>



					<a href="?" style="float: right">Home</a><br>



				</div>
			</div>
		</div>
	</div>
</div>

