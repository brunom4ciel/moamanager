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
use moam\core\Application;
use moam\core\Properties;
use moam\libraries\core\db\DBPDO;
use moam\libraries\core\user\User;
use moam\libraries\core\email\UsageReportMail;
if (! class_exists('Application')) {
    $application = Framework::getApplication();
}

if ($application->is_authentication()) {
    $application->redirect(PATH_WWW . "?");
}

$error_msg = "";

if (isset($_GET["msg"])) {

    $error_msg = $_GET["msg"];
} else {

    if (isset($_POST["email"])) {

        $email = $_POST["email"];

        if (! empty($email)) {

            try {

                Framework::import("UsageReportMail", "core/email");
                Framework::import("DBPDO", "core/db");
                Framework::import("User", "core/user");
            } catch (AppException $e) {

                throw new AppException($e->getMessage());
            }

            try {

                $db = new DBPDO(Properties::getDatabaseName(), Properties::getDatabaseHost(), Properties::getDatabaseUser(), Properties::getDatabasePass());

                $user = new User($db);

                $error_msg = "Error: email does not exist";

                // }else{

                if ($user->user_exists($email)) {

                    $error_msg = "Successfully";

                    $credentials = $user->getCredentials($email);

                    $mail = new UsageReportMail();

                    $body = "<html><head></head><body><h1>Password recovery</h1>
						<h3>email: {$credentials["email"]}<br>
						<h3>password: {$credentials["password"]}<br>
						<hr><br>
						<center>From {APPLICATION_NAME}</center>
						</body></html>"; // email: ".$email."password: ".$password;

                    $subject = "Password recovery";

                    if ($mail->sendMail($email, $body, $subject)) {

                        $error_msg = "Password recovery Successfully. Verify your mail.";
                    } else {
                        $error_msg = "Error: send mail";
                    }
                } else {
                    $error_msg = "Error: mail invalid - " . $email;
                }

                // }
            } catch (AppException $e) {

                throw new AppException($e->getMessage());
            }
        }
    } else {}
}

?>


<div class="content content-alt">
	<div class="container">
		<div class="row">
			<div class="col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">


				<div class="card">
					<div class="page-header">
						<h1>Password Reset</h1>
								
							<?php

    if (! empty($error_msg)) {

        echo $error_msg;
    }
    ?>
							</div>

					<div class="messageArea"></div>
					<form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>"
						name="loginForm" async-form="login"
						class="ng-pristine ng-valid-email ng-invalid ng-invalid-required">
						<input type="hidden"
							value="<?php echo $application->getComponent()?>"
							name="component"> <input type="hidden"
							value="<?php echo $application->getController()?>"
							name="controller">
						<form-messages for="passwordResetForm" class="ng-isolate-scope">
						<div ng-show="!!form.response.message"
							ng-class="{
									'alert-danger': form.response.message.type == 'error',
									'alert-success': form.response.message.type != 'error'
								}"
							class="alert ng-binding alert-success ng-hide"></div>
						<div ng-transclude="">
							<div class="alert alert-success ng-scope ng-hide"
								ng-show="passwordResetForm.response.success"></div>
						</div>
						</form-messages>
						<div class="form-group">
							<label for="email"><?php echo LABEL_PLEASE_EMAIL?></label> <input
								type="email"
								class="form-control ng-pristine ng-untouched ng-valid-email ng-invalid ng-invalid-required"
								autofocus="" ng-model="email" required=""
								placeholder="email@example.com" name="email"> <span
								class="small text-primary ng-hide"
								ng-show="passwordResetForm.email.$invalid &amp;&amp; passwordResetForm.email.$dirty"><?php echo LABEL_EMAIL_INVALID?></span>
						</div>
						<div class="actions">
							<button class="btn btn-primary"
								ng-disabled="passwordResetForm.$invalid" type="submit"><?php echo LABEL_REQUEST_PASSWORD_RESET?></button>
						</div>
					</form>
				</div>
						
							
							<?php //} ?>
					
					</div>
		</div>
	</div>
</div>
