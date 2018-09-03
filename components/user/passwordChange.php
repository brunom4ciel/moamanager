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
// use moam\core\Application;
use moam\core\Properties;
use moam\core\Template;
use moam\libraries\core\db\DBPDO;
use moam\libraries\core\user\User;
use moam\libraries\core\email\UsageReportMail;

if (! class_exists('Application')) {
    $application = Framework::getApplication();
}

Template::setDisabledMenu();


$error_msg = "";

if (isset($_GET["msg"])) {

    $error_msg = $_GET["msg"];
} else {

    if (isset($_POST["oldpwd"]) && isset($_POST["newpwd"])) {

        $oldpwd = $_POST["oldpwd"];
        $newpwd = $_POST["newpwd"];

        if (! empty($oldpwd) && ! empty($newpwd)) {

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

                if ($user->user_exists($application->getUser())) {

                    $error_msg = "Successfully";

                    $credentials = $user->getCredentials($application->getUser());

                    if ($oldpwd == $credentials["password"]) {

                        $user->changePassword($application->getUser(), $newpwd);

                        $mail = new UsageReportMail();

                        $body = "<html><head></head><body><h1>Change password</h1>
								<h3>email: {$credentials["email"]}<br>
								<h3>password: {$newpwd}<br>
								<hr><br>
								<center>From {Framework::getApp_title()}</center>
								</body></html>"; // email: ".$email."password: ".$password;

                        $subject = "Change password";

                        if ($mail->sendMail($application->getUser(), $body, $subject)) {

                            $error_msg = "Change password. Send your mail.";
                        } else {
                            $error_msg = "Error: send mail";
                        }
                    } else {

                        $error_msg = "Error: old and new passwords are different.";
                    }
                }

                // }else{
                // $error_msg = "Error: old and new passwords are different.";

                // }

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
						<h1>Password Change</h1>
								
							<?php

    if (! empty($error_msg)) {

        echo $error_msg;
    }
    ?>
							</div>

					<div class="messageArea"></div>
					<form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>"
						name="passwordResetForm" async-form="password-reset-request"
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
								ng-show="passwordResetForm.response.success">You have been sent
								an email to complete your password reset.</div>
						</div>
						</form-messages>
						<div class="form-group">
							<label for="email">Please enter your old password</label> <input
								type="password"
								class="form-control ng-pristine ng-untouched ng-valid-email ng-invalid ng-invalid-required"
								autofocus="" ng-model="oldpwd" required="" name="oldpwd"> <span
								class="small text-primary ng-hide"
								ng-show="passwordResetForm.email.$invalid &amp;&amp; passwordResetForm.email.$dirty">Must
								be an email address</span>
						</div>
						<div class="form-group">
							<label for="email">Please enter your new password</label> <input
								type="password"
								class="form-control ng-pristine ng-untouched ng-valid-email ng-invalid ng-invalid-required"
								autofocus="" ng-model="newpwd" required="" " name="newpwd"> <span
								class="small text-primary ng-hide"
								ng-show="passwordResetForm.email.$invalid &amp;&amp; passwordResetForm.email.$dirty">Must
								be an email address</span>
						</div>
						<div class="actions">
							<button class="btn btn-primary"
								ng-disabled="passwordResetForm.$invalid" type="submit">Change</button>
						</div>
					</form>
				</div>
						
							
							<?php //} ?>
					
					</div>
		</div>
	</div>
</div>


</body>
</html>

