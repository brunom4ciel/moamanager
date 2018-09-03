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
use moam\core\Template;
use moam\libraries\core\db\DBPDO;
use moam\libraries\core\user\User;
use moam\libraries\core\email\UsageReportMail;
use moam\libraries\core\utils\Utils;

if (! class_exists('Application')) {
    $application = Framework::getApplication();
}

if ($application->is_authentication()) {
    $application->redirect(PATH_WWW . "?");
}

Template::setDisabledMenu();


$error_msg = "";

if (isset($_POST["email"]) && isset($_POST["password"])) {

    $email = $_POST["email"];
    $password = $_POST["password"];

    if (! empty($email) && ! empty($password)) {

        try {

            Framework::import("UsageReportMail", "core/email");
            Framework::import("DBPDO", "core/db");
            Framework::import("User", "core/user");
            Framework::import("User", "core/user");
            Framework::import("Utils", "core/utils");
        } catch (AppException $e) {

            throw new AppException($e->getMessage());
        }

        try {

            $db = new DBPDO(Properties::getDatabaseName(), Properties::getDatabaseHost(), Properties::getDatabaseUser(), Properties::getDatabasePass());

            $user = new User($db);

            if ($user->user_exists($email)) {

                $error_msg = "Error: email already exists";
            } else {

                $mail = new UsageReportMail();
                $utils = new Utils();

                $body = "<html><head></head><body><h1>Register new user</h1>
					<h3>email: {$email}<br>
					<h3>password: {$password}<br>
					<hr><br>
					<center>From {APPLICATION_NAME}</center>
					</body></html>"; // email: ".$email."password: ".$password;

                $subject = "Register new user";

                $userpath = Properties::getBase_directory_destine($application) . $email . DIRECTORY_SEPARATOR;

                $userpath_tmp = Properties::$base_directory_destine_exec . $email . DIRECTORY_SEPARATOR;

                if (is_dir($userpath)) {
                    $utils->delTree($userpath);
                    // throw new AppException( 'Já existe diretório com o
                    // nome de usuário. Entre em contato com o administrador.' );
                }

                if (is_dir($userpath_tmp)) {
                    $utils->delTree($userpath_tmp);
                }

                /*
                 * if(is_dir($userpath.DIRECTORY_SEPARATOR
                 * .DIRNAME_SCRIPT))
                 * {
                 * $utils->delTree($userpath.DIRECTORY_SEPARATOR
                 * .DIRNAME_SCRIPT);
                 * }
                 */

                // exit(" -- " . $userpath);

                // echo $userpath."<br>";
                if (mkdir($userpath, 0777)) {

                    // echo App::$base_directory_destine_exec
                    // .$email."<br>";

                    if (mkdir($userpath_tmp, 0777)) {

                        // echo $userpath
                        // .App::getDirectorySeparator()
                        // ."scripts"."<br>";

                        if (mkdir($userpath . DIRECTORY_SEPARATOR . DIRNAME_SCRIPT, 0777)) {

                            mkdir($userpath . DIRECTORY_SEPARATOR . DIRNAME_TRASH, 0777);

                            mkdir($userpath . DIRECTORY_SEPARATOR . DIRNAME_BACKUP, 0777);

                            if ($user->create($email, $password, Properties::getser_type_default(), Properties::getBase_directory_destine($application))) {

                                $error_msg = "Successfully created.";

                                if ($mail->sendMail($email, $body, $subject)) {} else {
                                    $error_msg = "Error: send mail.";
                                }
                            } else {
                                $error_msg = "Error: save user.";
                            }
                        }
                    }
                } else {
                    $error_msg = "Error: not permission storage. " . $userpath;
                }

                // cria o diretório do usuário
                // mkdir($userpath, 0777);

                // cria o diretório de processamento do usuário
                // mkdir(App::$base_directory_destine_exec
                // .$email, 0777);

                // cria o diretório com os scripts
                // mkdir($userpath
                // .App::getDirectorySeparator()
                // ."scripts", 0777);
            }
        } catch (AppException $e) {

            // throw new AppException( 'import library '
            // . ' error - component '
            // . $application->getComponent()
            // . ' - '
            // . $e->getMessage());

            throw new AppException($e->getMessage());
        }
    }
}

?>


<div class="content content-alt">
	<div class="container">
		<div class="row">
			<div class="col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
				<div class="card">
					<div class="page-header">
						<h1>Register</h1>
							<?php

    if (! empty($error_msg)) {

        echo $error_msg;
    }
    ?>
							</div>
					<form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>"
						name="registerForm" async-form="register"
						class="ng-pristine ng-valid-email ng-invalid ng-invalid-required">
						<input type="hidden"
							value="<?php echo $application->getComponent()?>"
							name="component"> <input type="hidden"
							value="<?php echo $application->getController()?>"
							name="controller">
						<form-messages for="registerForm" class="ng-isolate-scope">
						<div ng-show="!!form.response.message"
							ng-class="{
										'alert-danger': form.response.message.type == 'error',
										'alert-success': form.response.message.type != 'error'
									}"
							class="alert ng-binding alert-success ng-hide"></div>
						<div ng-transclude=""></div>
						</form-messages>
						<div class="form-group">
							<label for="email">Email</label> <input type="email"
								class="form-control ng-pristine ng-isolate-scope ng-valid-email ng-invalid ng-invalid-required ng-touched"
								focus="true" ng-model-options="{ updateOn: 'blur' }"
								ng-init="email = &quot;&quot;" ng-model="email" required=""
								placeholder="email@example.com" name="email"> <span
								class="small text-primary ng-hide"
								ng-show="registerForm.email.$invalid &amp;&amp; registerForm.email.$dirty">Must
								be an email address</span>
						</div>
						<div class="form-group a_pf-wrap" style="position: relative;">
							<label for="password">Password</label> <input type="password"
								class="form-control ng-pristine ng-untouched a_pf-txt-pass ng-invalid ng-invalid-required"
								complex-password="" ng-model="password" required=""
								placeholder="********" name="password" id="passwordField"
								maxlength="50">
							<div id="a_pf-len-passwordField"
								style="position: absolute; height: 37px; top: -10000px; left: -10000px; display: block; color: transparent; border: medium none; margin-left: 0px; font-family: &amp; quot; Open Sans&amp;quot; , sans-serif; font-size: 16px; font-weight: 400; font-style: normal; font-variant: normal;">
							</div>
							<span class="small text-primary ng-binding ng-hide"
								ng-bind-html="complexPasswordErrorMessage"
								ng-show="registerForm.password.$error.complexPassword"></span>
						</div>
						<div class="actions">
							<button class="btn-primary btn" type="submit">
								<span ng-show="!registerForm.inflight">Register</span> <span
									ng-show="registerForm.inflight" class="ng-hide">Registering...</span>
							</button>
						</div>
					</form>


				</div>


			</div>
		</div>
	</div>
</div>

