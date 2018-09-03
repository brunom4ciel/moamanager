<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\components\user;

defined('_EXEC') or die();

use moam\core\AppException;
use moam\core\Framework;
use moam\core\Properties;
use moam\libraries\core\db\DBPDO;
use moam\libraries\core\user\User;
use moam\libraries\core\sys\SoftwareUpdate;
use moam\core\Template;


if (! class_exists('Application')) {
    $application = Framework::getApplication();
}

Template::setDisabledMenu();

// Template::addHeader(array("tag"=>"link",
// "type"=>"text/css",
// "rel"=>"stylesheet",
// "href"=>"" . PATH_WWW . "templates/default/css/style2.css"));

// Template::setTitle("Teste");

// $menu = Framework::getInstance("Menu");

// $application = Framework::getApplication();

if (isset($_GET["logout"])) {

    $http_referer = $application->getParameter("http_referer");
    $application->logout($http_referer);
    
} else if ($application->is_authentication())
    header("Location: " . PATH_WWW . "?component=home");

$error_msg = "";

if (isset($_GET["msg"])) {

    $error_msg = $_GET["msg"];
} else {

    if (isset($_POST["email"]) && isset($_POST["password"])) {
        
        $email = $_POST["email"];
        $password = $_POST["password"];

        if (! empty($email) && ! empty($password)) {
            
            try {

                Framework::import("UsageReportMail", "core/email");
                Framework::import("DBPDO", "core/db");
                Framework::import("User", "core/user");
                Framework::import("SoftwareUpdate", "core/sys");                
                
            } catch (AppException $e) {

                throw new AppException('import library ' . ' error - component ' . $application->getComponent());
            }
            
            try {

                $db = new DBPDO(Properties::getDatabaseName(), 
                                    Properties::getDatabaseHost(), 
                                    Properties::getDatabaseUser(), 
                                    Properties::getDatabasePass());
                
                $user = new User($db);

                
                
                $error_msg = "Error: email does not exist";

                // }else{

                if ($user->login($email, $password)) {

                    $error_msg = "Successfully";

                    $credentials = $user->getCredentials($email);

                    $application->authentication($email, $credentials['type'], $credentials['user_id'], $credentials['workspace']);

                    $http_referer = $application->getParameter("http_referer");
                    
                    $update = new SoftwareUpdate();
//                     $isUpdate = $update->isUpdate(MOAMANAGER_VERSION);
//                     $version = $update->getVersion();
                    
                    $application->setSoftwareRemoteVersion($update->getVersion());
                    
                    if (! empty($http_referer)) {
                        $http_referer = base64_decode($http_referer);
                        $application->redirect($http_referer);
                    } else
                        $application->redirect(PATH_WWW . "?component=systemmonitor");
                } else {
                    $error_msg = "Error: mail or password invalid";
                }

                // }
            } catch (AppException $e) {

                exit("Error: " . $e->getMessage());
            }
        }
    } else {}
}

?>


<div class="content">
	<div class="container">
		<div class="row">
			<div class="col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
				<div class="card">
					<div class="page-header">
						<h1>Log In</h1>
							
							<?php

    if (! empty($error_msg)) {

        echo $error_msg;
    }
    ?>
							</div>


					<form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>"
						name="loginForm" async-form="login"
						class="ng-pristine ng-valid-email ng-invalid ng-invalid-required">
						<input type="hidden"
							value="<?php echo $application->getComponent()?>"
							name="component"> <input type="hidden"
							value="<?php echo $application->getController()?>"
							name="controller"> <input type="hidden"
							value="<?php echo $application->getParameter( "http_referer" );?>"
							name="http_referer">
						<form-messages for="loginForm" class="ng-isolate-scope">
						<div ng-show="!!form.response.message"
							ng-class="{
										'alert-danger': form.response.message.type == 'error',
										'alert-success': form.response.message.type != 'error'
									}"
							class="alert ng-binding alert-success ng-hide"></div>
						<div ng-transclude=""></div>
						</form-messages>
						<div class="form-group">
							<input type="email"
								class="form-control ng-pristine ng-isolate-scope ng-valid-email ng-invalid ng-invalid-required ng-touched"
								focus="true" ng-init="email = undefined"
								ng-model-options="{ updateOn: 'blur' }" ng-model="email"
								placeholder="email@example.com" required="" name="email"><span
								class="small text-primary ng-hide"
								ng-show="loginForm.email.$invalid &amp;&amp; loginForm.email.$dirty"><?php echo LABEL_EMAIL_INVALID?></span>
						</div>
						<div class="form-group">
							<input type="password"
								class="form-control ng-pristine ng-untouched ng-invalid ng-invalid-required"
								ng-model="password" placeholder="********" required=""
								name="password"><span class="small text-primary ng-hide"
								ng-show="loginForm.password.$invalid &amp;&amp; loginForm.password.$dirty"><?php echo LABEL_REQUIRED?></span>
						</div>
						<div class="actions">
							<button class="btn-primary btn" ng-disabled="loginForm.inflight"
								type="submit">
								<span ng-show="!loginForm.inflight"><?php echo BUTTON_LOGIN?></span>
								<span ng-show="loginForm.inflight" class="ng-hide">Logging in...</span>
							</button>
							<a class="pull-right"
								href="?component=user&controller=passwordReset"><?php echo LABEL_FORGOT?></a>
						</div>
					</form>
							
							<?php //} ?>
							
						</div>
			</div>
		</div>
	</div>
</div>

