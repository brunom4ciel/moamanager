<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\components\settings;

defined('_EXEC') or die();

use moam\core\Framework;
use moam\core\Template;
use moam\libraries\core\db\DBPDO;
use moam\libraries\core\utils\Utils;
use moam\core\Properties;
use PDO;
use moam\core\AppException;


if (! class_exists('Application')) {
    $application = Framework::getApplication();
}

if (! $application->is_authentication()) {
    $application->alert("Error: you do not have credentials.");
}

Framework::import("Utils", "core/utils");
Framework::import("DBPDO", "core/db");

$utils = new Utils();

$DB = new DBPDO(Properties::getDatabaseName(), Properties::getDatabaseHost(), Properties::getDatabaseUser(), Properties::getDatabasePass());

$files_list = $utils->getListElementsDirectory(Properties::getBase_directory_moa() . "lib/", array(
    "jar"
));

if (is_file(Properties::getBase_directory_moa() . "bin/" . $application->getUserId() . ".jar")) {
    $moafilename = $application->getUserId() . ".jar";
    $moafilename .= " (" . $utils->formatSize(filesize(Properties::getBase_directory_moa() . "bin/" . $moafilename)) . ")";
} else {
    $moafilename = Properties::getBase_directory_moa_jar_default();
}


Template::addHeader(array(
    "tag" => "link",
    "type" => "text/css",
    "rel" => "stylesheet",
    "href" => "" . $application->getPathTemplate() . "/css/style4.css"
));

Template::setDisabledMenu();

$task = $application->getParameter('task');

if($task == 'downloadjar')
{
   
    $filename = Properties::getBase_directory_moa()
            .   "bin"
            .   DIRECTORY_SEPARATOR    
            .   Properties::getBase_directory_moa_jar_default();

            
    if (file_exists($filename)) {
        
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filename));
        
        ob_clean();
        ob_end_flush();
        
        // readfile($file);
        
        $handle = fopen($filename, "rb");
        while (! feof($handle))
        {
            echo fread($handle, 1000);
        }
        
        exit();
    }
    
}

if($task == 'downloadlib')
{
    
    $filename = Properties::getBase_directory_moa()
    .   "lib"
        .   DIRECTORY_SEPARATOR
        .   $application->getParameter('filename');
        
        
        if (file_exists($filename)) {
            
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filename));
            
            ob_clean();
            ob_end_flush();
            
            // readfile($file);
            
            $handle = fopen($filename, "rb");
            while (! feof($handle))
            {
                echo fread($handle, 1000);
            }
            
            exit();
        }
        
}

?>
<script>


function confirmCleanDirectory(){

	return true;
//  	var x = confirm("Are you sure you want to delete your temporary files?");

//  	if (x){
//  	 	var x = confirm("If the temporary files is delete all files in processing will cease to function .\nAre you sure you want to delete your temporary files?");
// 		if(x)
// 			return true;
// 	  	else
// 			return false;
//  	}else
//     	return false;
	
}


function confirmRemoveAccount(){

 	var x = confirm("Are you sure you want to delete your account?");

 	if (x){
 	 	var x = confirm("If the account is delete all files related to this account will be deleted as well.\nAre you sure you want to delete your account?");
		if(x)
			return true;
	  	else
			return false;
 	}else
    	return false;
	
}
</script>



					<div data-reactid=".1lisbcwokxs.3" class="bd">

						<div data-reactid=".1lisbcwokxs.3.0"
							class="responsive-account-container">
							<div data-reactid=".1lisbcwokxs.3.0.0">
								<h1 data-reactid=".1lisbcwokxs.3.0.0.0" class="account-header">My
									Account</h1>

								<div data-reactid=".1lisbcwokxs.3.0.0.1"
									class="account-messages-container"></div>
								<div data-reactid=".1lisbcwokxs.3.0.0.2"
									class="responsive-account-content">
									<div data-reactid=".1lisbcwokxs.3.0.0.2.0"
										class="account-section collapsable-panel clearfix membership-section-wrapper">
										<header data-reactid=".1lisbcwokxs.3.0.0.2.0.0"
											class="account-section-header collapsable-section-toggle">
											<h2 data-reactid=".1lisbcwokxs.3.0.0.2.0.0.0"
												class="account-section-heading">
												<span data-reactid=".1lisbcwokxs.3.0.0.2.0.0.0.0">Membership</span>

											</h2>
										</header>
										<section data-reactid=".1lisbcwokxs.3.0.0.2.0.1"
											class="collapsable-section-content account-section-content">
											<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0"
												class="account-subsection clearfix">
												<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0"
													class="clearfix">
													<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0"
														class="account-section-group">

														<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1"
															class="account-section-item account-section-item-disabled">
															<span data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1.0">Type:</span>
															<span data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1.1"> <?php echo ($application->getUserType()==1?"Manager":"Registered");?></span>
														</div>
														<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.2"
															class="account-section-item account-section-item-disabled"></div>
														<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1"
															class="account-section-item account-section-item-disabled">
															<span data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1.0">Password:</span>
															<span data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1.1">
																********</span>
														</div>


													</div>
													<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.1"
														class="account-section-group">

														<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.1.1"
															class="account-section-item">
															<a data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.1.1.0"
																href="<?php echo PATH_WWW?>?component=user&controller=passwordChange"
																class="account-section-link">Change password</a>
														</div>

													</div>
													<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.1"
														class="account-section-group">

														<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.1.1"
															class="account-section-item">
															<a data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.1.1.0"
																href="<?php echo PATH_WWW?>?component=settings&controller=accountremove"
																onclick='javascript: return confirmRemoveAccount();'
																class="account-section-link">Remove Account</a>
														</div>

													</div>
												</div>
											</div>
										</section>
									</div>

									<div data-reactid=".1lisbcwokxs.3.0.0.2.0"
										class="account-section collapsable-panel clearfix membership-section-wrapper">
										<header data-reactid=".1lisbcwokxs.3.0.0.2.0.0"
											class="account-section-header collapsable-section-toggle">
											<h2 data-reactid=".1lisbcwokxs.3.0.0.2.0.0.0"
												class="account-section-heading">
												<span data-reactid=".1lisbcwokxs.3.0.0.2.0.0.0.0">MOA</span>

											</h2>
										</header>
										<section data-reactid=".1lisbcwokxs.3.0.0.2.0.1"
											class="collapsable-section-content account-section-content">
											<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0"
												class="account-subsection clearfix">
												<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0"
													class="clearfix">
													<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0"
														class="account-section-group">

														<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1"
															class="account-section-item account-section-item-disabled">
															<span data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1.0">Directory:</span>
															<span data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1.1"> <?php echo Properties::getBase_directory_moa();?></span>
														</div>
														<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.2"
															class="account-section-item account-section-item-disabled"></div>

													</div>
												</div>



												<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0"
													class="clearfix">
													<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0"
														class="account-section-group">

														<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1"
															class="account-section-item account-section-item-disabled">
															<span data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1.0">Process
																Output:</span> <span
																data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1.1"> <?php echo Properties::getBase_directory_destine_exec()?></span>
														</div>
														<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.2"
															class="account-section-item account-section-item-disabled"></div>

													</div>
												</div>



												<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0"
													class="clearfix">
													<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0"
														class="account-section-group">

														<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1"
															style="width: 500px;"
															class="account-section-item account-section-item-disabled">
															<span data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1.0">Binary
																(/bin/):</span> <span
																data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1.0">
<a href="<?php echo PATH_WWW . '?component=settings&tmpl=tmpl&task=downloadjar';?>">
<img width='16px' align='middle' src='<?php echo $application->getPathTemplate()?>/images/icon_download.png' title='Download'/></a>

																<?php echo $moafilename;?> 
																<?php echo date("Y/m/d H:i:s", filemtime(Properties::getBase_directory_moa()."bin/".Properties::getBase_directory_moa_jar_default()));?></span>

														</div>
														<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.2"
															class="account-section-item account-section-item-disabled"></div>
													</div>
													<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.1"
														class="account-section-group">

														<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.1.1"
															class="account-section-item">
															<a data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.1.1.0"
																href="<?php echo PATH_WWW?>?component=settings&controller=changeMOA"
																class="account-section-link">Binary MOA Manager</a>
														</div>

													</div>
												</div>

												<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0"
													class="clearfix">
													<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0"
														class="account-section-group">


														<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1"
															class="account-section-item account-section-item-disabled">
															<span data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1.0">Library
																(/lib/):</span> <span
																data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1.1"> </span>
														</div>
														<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0"
															class="account-section-group">

															<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1"
																style="width: 400px;"
																class="account-section-item account-section-item-disabled">
																	
																	<?php

                for ($i = 0; $i < count($files_list); $i ++) {
                
                    echo    "<span style='margin-left:65px;' data-reactid=\".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1.0\">";
                    
                    echo    '<a href="'. PATH_WWW . '?component=settings&tmpl=tmpl&task=downloadlib&filename=' . $files_list[$i] . '">';
                    echo    "<img width='16px' align='middle' src='". $application->getPathTemplate() . "/images/icon_download.png' title='Download'/></a>&nbsp; "; 
                    
                    echo $files_list[$i] . " (" . $utils->formatSize(filesize(Properties::getBase_directory_moa() . "lib/" . $files_list[$i])) . ")" . "</span><br>\n";
                }

                ?>
																	
																</div>
															<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.2"
																class="account-section-item account-section-item-disabled"></div>
														</div>
														<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.2"
															class="account-section-item account-section-item-disabled"></div>




													</div>
												</div>

											</div>
										</section>
									</div>






									<div data-reactid=".1lisbcwokxs.3.0.0.2.0"
										class="account-section collapsable-panel clearfix membership-section-wrapper">
										<header data-reactid=".1lisbcwokxs.3.0.0.2.0.0"
											class="account-section-header collapsable-section-toggle">
											<h2 data-reactid=".1lisbcwokxs.3.0.0.2.0.0.0"
												class="account-section-heading">
												<span data-reactid=".1lisbcwokxs.3.0.0.2.0.0.0.0">Temporary Files</span>

											</h2>
										</header>
										<section data-reactid=".1lisbcwokxs.3.0.0.2.0.1"
											class="collapsable-section-content account-section-content">
											<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0"
												class="account-subsection clearfix">
												<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0"
													class="clearfix">


													<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0"
														class="account-section-group">

														<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1"
															class="account-section-item account-section-item-disabled">
															<span data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1.0">Directory:</span> <span
																data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1.1"> <?php echo Properties::getBase_directory_destine_exec($application);?><?php echo $application->getUser()?></span>
														</div>
														<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.2"
															class="account-section-item account-section-item-disabled"></div>

													</div>

													<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.1"
														class="account-section-group">

														<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.1.1"
															class="account-section-item">
															<a data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.1.1.0"
																href="<?php echo PATH_WWW?>?component=settings&controller=cleantmp&task=confirm"
																onclick='javascript: return confirmCleanDirectory();'
																class="account-section-link">Clean Directory</a>
														</div>

													</div>
												</div>

											</div>
										</section>
									</div>
									
									
									
									
									
									
									
									<div data-reactid=".1lisbcwokxs.3.0.0.2.0"
										class="account-section collapsable-panel clearfix membership-section-wrapper">
										<header data-reactid=".1lisbcwokxs.3.0.0.2.0.0"
											class="account-section-header collapsable-section-toggle">
											<h2 data-reactid=".1lisbcwokxs.3.0.0.2.0.0.0"
												class="account-section-heading">
												<span data-reactid=".1lisbcwokxs.3.0.0.2.0.0.0.0">Web</span>

											</h2>
										</header>
										<section data-reactid=".1lisbcwokxs.3.0.0.2.0.1"
											class="collapsable-section-content account-section-content">
											<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0"
												class="account-subsection clearfix">
												<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0"
													class="clearfix">


													<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0"
														class="account-section-group">

														<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1"
															class="account-section-item account-section-item-disabled">
															<span data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1.0">Web
																Output:</span> <span
																data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1.1"> <?php echo Properties::getBase_directory_destine($application);?></span>
														</div>
														<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.2"
															class="account-section-item account-section-item-disabled"></div>

													</div>


												</div>

												<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0"
													class="clearfix">
													<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0"
														class="account-section-group" style="width: 600px;">

														<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1"
															class="account-section-item account-section-item-disabled">
															<span data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1.0">Workspace:</span>
															<span data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1.1"> <?php echo Properties::getBase_directory_destine($application);?><?php echo $application->getUser()?>/</span>
														</div>
														<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.2"
															class="account-section-item account-section-item-disabled"></div>

													</div>
												</div>


											</div>
										</section>
									</div>


									<div data-reactid=".1lisbcwokxs.3.0.0.2.0"
										class="account-section collapsable-panel clearfix membership-section-wrapper">
										<header data-reactid=".1lisbcwokxs.3.0.0.2.0.0"
											class="account-section-header collapsable-section-toggle">
											<h2 data-reactid=".1lisbcwokxs.3.0.0.2.0.0.0"
												class="account-section-heading">
												<span data-reactid=".1lisbcwokxs.3.0.0.2.0.0.0.0">Real
													Datasets</span>

											</h2>
										</header>
										<section data-reactid=".1lisbcwokxs.3.0.0.2.0.1"
											class="collapsable-section-content account-section-content">
											<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0"
												class="account-subsection clearfix">
												<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0"
													class="clearfix">


													<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0"
														class="account-section-group">

														<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1"
															class="account-section-item account-section-item-disabled">
															<span data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1.0">Folder:
															</span> <span
																data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1.0"><?php echo Properties::getBase_directory_moa()."datasets";?></span>

														</div>
														<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.2"
															class="account-section-item account-section-item-disabled"></div>
													</div>

													<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.1"
														class="account-section-group">

														<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.1.1"
															class="account-section-item">
															<a data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.1.1.0"
																href="<?php echo PATH_WWW?>?component=settings&controller=changeDatasets"
																class="account-section-link">View</a>
														</div>

													</div>




												</div>
											</div>
										</section>
									</div>
										
									
									
									
									<div data-reactid=".1lisbcwokxs.3.0.0.2.0"
										class="account-section collapsable-panel clearfix membership-section-wrapper">
										<header data-reactid=".1lisbcwokxs.3.0.0.2.0.0"
											class="account-section-header collapsable-section-toggle">
											<h2 data-reactid=".1lisbcwokxs.3.0.0.2.0.0.0"
												class="account-section-heading">
												<span data-reactid=".1lisbcwokxs.3.0.0.2.0.0.0.0">MOA src/</span>

											</h2>
										</header>
										<section data-reactid=".1lisbcwokxs.3.0.0.2.0.1"
											class="collapsable-section-content account-section-content">
											<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0"
												class="account-subsection clearfix">
												<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0"
													class="clearfix">

													<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0"
														class="account-section-group">

														<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1"
															class="account-section-item account-section-item-disabled">
															
														</div>
														<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.2"
															class="account-section-item account-section-item-disabled"></div>
													</div>

													<div style="float:right;">

														<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.1.1"
															class="account-section-item">
															<a data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.1.1.0"
																href="<?php echo PATH_WWW?>?component=java&controller=download"
																class="account-section-link" onclick="document.getElementById('latestversionmoa').href='#';">Download Java src files</a>
															<?php 
															
															
															$dir_moa = Properties::getBase_directory_moa() . ""
															. DIRECTORY_SEPARATOR;															
														
															$files_list = $utils->getListElementsDirectory1($dir_moa, array("zip"));
															
                                                            $ok=FALSE;
															foreach($files_list as $item)
															{
															    if(file_exists($dir_moa . $item["name"]) && $item['type'] == "file")
															    {$ok = TRUE;
															?>
																<br><a id="latestversionmoa" data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.1.1.0"
																href="<?php echo PATH_WWW?>?component=java&controller=download&filename=<?php echo $item["name"]?>"
																class="account-section-link">Download last version <?php echo $item["name"]?></a>
															
															<?php         
															    }
															}
															
															if($ok == FALSE)
															{
															    echo "<a id=\"latestversionmoa\" href='#'></a>";
															}
															?>	
																
																
															<?php if($application->getUserType() == 1){ ?>	
																<br>
															<a data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.1.1.0"
																href="<?php echo PATH_WWW?>?component=java"
																class="account-section-link">Java Manager 'src files'</a>
															<?php }?>
														</div>

													</div>


												</div>
											</div>
										</section>
									</div>
									
									
									
										
										<?php if($application->getUserType() == 1){ ?>
										
										
										
										
										
										<h1 data-reactid=".1lisbcwokxs.3.0.0.0" class="account-header">Build and Deploy</h1>
										<div data-reactid=".1lisbcwokxs.3.0.0.2.0"
										class="account-section collapsable-panel clearfix membership-section-wrapper">
										<header data-reactid=".1lisbcwokxs.3.0.0.2.0.0"
											class="account-section-header collapsable-section-toggle">
											<h2 data-reactid=".1lisbcwokxs.3.0.0.2.0.0.0"
												class="account-section-heading">
												<span data-reactid=".1lisbcwokxs.3.0.0.2.0.0.0.0">Build and Deploy</span>

											</h2>
										</header>
										<section data-reactid=".1lisbcwokxs.3.0.0.2.0.1"
											class="collapsable-section-content account-section-content">
											<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0"
												class="account-subsection clearfix">
												<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0"
													class="clearfix">

													<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0"
														class="account-section-group">

														<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1"
															class="account-section-item account-section-item-disabled">
															
														</div>
														<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.2"
															class="account-section-item account-section-item-disabled"></div>
													</div>

													<div style="float:right;">

														<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.1.1"
															class="account-section-item">
															<a data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.1.1.0"
																href="<?php echo PATH_WWW?>?component=settings&controller=buildmoa"
																class="account-section-link">Automatically Build MOA and Deploy bin/<?php echo Properties::getBase_directory_moa_jar_default();?></a>
														</div>

													</div>


												</div>
											</div>
										</section>
									</div>
									
									
									
									
									
									
									
									
									
									
									
									
										<h1 data-reactid=".1lisbcwokxs.3.0.0.0" class="account-header">Update</h1>
									
										<div data-reactid=".1lisbcwokxs.3.0.0.2.0"
										class="account-section collapsable-panel clearfix membership-section-wrapper">
										<header data-reactid=".1lisbcwokxs.3.0.0.2.0.0"
											class="account-section-header collapsable-section-toggle">
											<h2 data-reactid=".1lisbcwokxs.3.0.0.2.0.0.0"
												class="account-section-heading">
												<span data-reactid=".1lisbcwokxs.3.0.0.2.0.0.0.0">Software Update</span>

											</h2>
										</header>
										<section data-reactid=".1lisbcwokxs.3.0.0.2.0.1"
											class="collapsable-section-content account-section-content">
											<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0"
												class="account-subsection clearfix">
												<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0"
													class="clearfix">

													<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0"
														class="account-section-group">

														<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1"
															class="account-section-item account-section-item-disabled">
															
														</div>
														<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.2"
															class="account-section-item account-section-item-disabled"></div>
													</div>

													<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.1"
														class="account-section-group">

														<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.1.1"
															class="account-section-item">
															<a data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.1.1.0"
																href="<?php echo PATH_WWW?>?component=settings&controller=softwareupdate"
																class="account-section-link">Check for Update</a>
														</div>

													</div>


												</div>
											</div>
										</section>
									</div>
										
										<h1 data-reactid=".1lisbcwokxs.3.0.0.0" class="account-header">Users</h1>
										
										
										<div data-reactid=".1lisbcwokxs.3.0.0.2.0"
										class="account-section collapsable-panel clearfix membership-section-wrapper">
										<header data-reactid=".1lisbcwokxs.3.0.0.2.0.0"
											class="account-section-header collapsable-section-toggle">
											<h2 data-reactid=".1lisbcwokxs.3.0.0.2.0.0.0"
												class="account-section-heading">
												<span data-reactid=".1lisbcwokxs.3.0.0.2.0.0.0.0">Users</span>

											</h2>
										</header>
										<section data-reactid=".1lisbcwokxs.3.0.0.2.0.1"
											class="collapsable-section-content account-section-content">
											<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0"
												class="account-subsection clearfix">
												<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0"
													class="clearfix">

													<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0"
														class="account-section-group">

														<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1"
															class="account-section-item account-section-item-disabled">
																

																<?php

            $data_db = $DB->prep_query("SELECT
	
	email, workspace
	
	FROM  user 

	ORDER by email asc");

            $data_db->bindParam(1, $user_id, PDO::PARAM_INT);

            $error = "";

            try {

                // open transaction
                $DB->beginTransaction();

                // execute query
                $data_db->execute();

                // confirm transaction
                $DB->commit();

                $db_result_error = $data_db->errorInfo();

                if ($db_result_error[2] != "")
                    $error = $db_result_error[2];
            } catch (AppException $e) {

                // back transaction
                $DB->rollback();

                throw new AppException($e->getMessage());
            }

            foreach ($data_db as $element) {

                $path = $element["workspace"] . DIRECTORY_SEPARATOR . $element["email"];

                echo "<span data-reactid=\".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1.0\">" . $element["email"] . ",</span> <span>" . $utils->getDirSize($path) . "</span><br>\n";
            }

            ?><?php echo $error;?>

																
																
															</div>
														<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.2"
															class="account-section-item account-section-item-disabled"></div>
													</div>

													<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.1"
														class="account-section-group">

														<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.1.1"
															class="account-section-item">
															<a data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.1.1.0"
																href="<?php echo PATH_WWW?>?component=settings&controller=managerUsers"
																class="account-section-link">Manager</a>
														</div>

													</div>


												</div>
											</div>
										</section>
									</div>
										
										
										
										
										
										
										<h1 data-reactid=".1lisbcwokxs.3.0.0.0" class="account-header">Preferences</h1>
										
										
										
										
										<div data-reactid=".1lisbcwokxs.3.0.0.2.0"
										class="account-section collapsable-panel clearfix membership-section-wrapper">
										<header data-reactid=".1lisbcwokxs.3.0.0.2.0.0"
											class="account-section-header collapsable-section-toggle">
											<h2 data-reactid=".1lisbcwokxs.3.0.0.2.0.0.0"
												class="account-section-heading">
												<span data-reactid=".1lisbcwokxs.3.0.0.2.0.0.0.0">Properties</span>

											</h2>
										</header>
										<section data-reactid=".1lisbcwokxs.3.0.0.2.0.1"
											class="collapsable-section-content account-section-content">
											<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0"
												class="account-subsection clearfix">
												<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0"
													class="clearfix">

													<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0"
														class="account-section-group">

														<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1"
															class="account-section-item account-section-item-disabled">
															<span data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1.0">File:
															</span> <span
																data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1.0"><?php echo PATH_CORE.DIRECTORY_SEPARATOR;?>properties.php</span>

														</div>
														<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.2"
															class="account-section-item account-section-item-disabled"></div>
													</div>

													<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.1"
														class="account-section-group">

														<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.1.1"
															class="account-section-item">
															<a data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.1.1.0"
																href="<?php echo PATH_WWW?>?component=settings&controller=editProperties"
																class="account-section-link">Edit</a>
														</div>

													</div>


												</div>
											</div>
										</section>
									</div>





									<div data-reactid=".1lisbcwokxs.3.0.0.2.0"
										class="account-section collapsable-panel clearfix membership-section-wrapper">
										<header data-reactid=".1lisbcwokxs.3.0.0.2.0.0"
											class="account-section-header collapsable-section-toggle">
											<h2 data-reactid=".1lisbcwokxs.3.0.0.2.0.0.0"
												class="account-section-heading">
												<span data-reactid=".1lisbcwokxs.3.0.0.2.0.0.0.0">Defines</span>

											</h2>
										</header>
										<section data-reactid=".1lisbcwokxs.3.0.0.2.0.1"
											class="collapsable-section-content account-section-content">
											<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0"
												class="account-subsection clearfix">
												<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0"
													class="clearfix">

													<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0"
														class="account-section-group">

														<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1"
															class="account-section-item account-section-item-disabled">
															<span data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1.0">File:
															</span> <span
																data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1.0"><?php echo PATH_BASE.DIRECTORY_SEPARATOR . "includes" . DIRECTORY_SEPARATOR;?>defines.php</span>

														</div>
														<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.2"
															class="account-section-item account-section-item-disabled"></div>
													</div>

													<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.1"
														class="account-section-group">

														<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.1.1"
															class="account-section-item">
															<a data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.1.1.0"
																href="<?php echo PATH_WWW?>?component=settings&controller=editDefines"
																class="account-section-link">Edit</a>
														</div>

													</div>


												</div>
											</div>
										</section>
									</div>
									
									
									
									
									


									







									<div data-reactid=".1lisbcwokxs.3.0.0.2.0"
										class="account-section collapsable-panel clearfix membership-section-wrapper">
										<header data-reactid=".1lisbcwokxs.3.0.0.2.0.0"
											class="account-section-header collapsable-section-toggle">
											<h2 data-reactid=".1lisbcwokxs.3.0.0.2.0.0.0"
												class="account-section-heading">
												<span data-reactid=".1lisbcwokxs.3.0.0.2.0.0.0.0">Database</span>

											</h2>
										</header>
										<section data-reactid=".1lisbcwokxs.3.0.0.2.0.1"
											class="collapsable-section-content account-section-content">
											<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0"
												class="account-subsection clearfix">
												<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0"
													class="clearfix">

													<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0"
														class="account-section-group">

														<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1"
															class="account-section-item account-section-item-disabled">
															<span data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1.0">Address:
															</span> <span
																data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1.0"><?php echo Properties::getDatabaseHost()?></span><br>

															<span data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1.0">Name:
															</span> <span
																data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1.0"><?php echo Properties::getDatabaseName()?></span><br>

															<span data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1.0">User:
															</span> <span
																data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1.0"><?php echo Properties::getDatabaseUser()?></span><br>

															<span data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1.0">Pass:
															</span> <span
																data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1.0">*******</span><br>

														</div>
														<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.2"
															class="account-section-item account-section-item-disabled"></div>
													</div>

													<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.1"
														class="account-section-group">

														<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.1.1"
															class="account-section-item">
															<a data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.1.1.0"
																href="<?php echo PATH_WWW?>?component=settings&controller=managerDatabase"
																class="account-section-link">Manager</a>
														</div>

													</div>


												</div>
											</div>
										</section>
									</div>



									<div data-reactid=".1lisbcwokxs.3.0.0.2.0"
										class="account-section collapsable-panel clearfix membership-section-wrapper">
										<header data-reactid=".1lisbcwokxs.3.0.0.2.0.0"
											class="account-section-header collapsable-section-toggle">
											<h2 data-reactid=".1lisbcwokxs.3.0.0.2.0.0.0"
												class="account-section-heading">
												<span data-reactid=".1lisbcwokxs.3.0.0.2.0.0.0.0">Command
													line</span>

											</h2>
										</header>
										<section data-reactid=".1lisbcwokxs.3.0.0.2.0.1"
											class="collapsable-section-content account-section-content">
											<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0"
												class="account-subsection clearfix">
												<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0"
													class="clearfix">

													<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0"
														class="account-section-group">

														<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.1"
															class="account-section-item account-section-item-disabled">

														</div>
														<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.2"
															class="account-section-item account-section-item-disabled"></div>
													</div>

													<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.1"
														class="account-section-group">

														<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.1.1"
															class="account-section-item">
															<a data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.1.1.0"
																href="<?php echo PATH_WWW?>?component=settings&controller=terminal"
																class="account-section-link">Terminal</a>
														</div>

													</div>


												</div>
											</div>
										</section>
									</div>
										
										<?php }?>
										
										<!-- 
										<div data-reactid=".1lisbcwokxs.3.0.0.2.0" class="account-section collapsable-panel clearfix membership-section-wrapper">
											<header data-reactid=".1lisbcwokxs.3.0.0.2.0.0" class="account-section-header collapsable-section-toggle">
												<h2 data-reactid=".1lisbcwokxs.3.0.0.2.0.0.0" class="account-section-heading">
													<span data-reactid=".1lisbcwokxs.3.0.0.2.0.0.0.0">MOA Directory</span>
													
												</h2>
											</header>
											<section data-reactid=".1lisbcwokxs.3.0.0.2.0.1" class="collapsable-section-content account-section-content">
												<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0" class="account-subsection clearfix">
													<div data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0" class="clearfix">
													
														<div class="account-section-group" data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0" style="width: 450px;">
															
															
															<div class="account-section-item account-section-item account-section-gifts" data-reactid=".1d9i1r55m2o.3.0.0.2.4.1.0.0.0" style="700px;">
															
																<label class="ui-label ui-input-label" data-reactid=".1d9i1r55m2o.3.0.0.2.4.1.0.0.0.0" style="width:310px;float:left;">
																	<span data-reactid=".1d9i1r55m2o.3.0.0.2.4.1.0.0.0.0.0" class="ui-label-text" style="float:left;margin-right:10px;margin-top:10px;">Directory: </span>
																	<input value="/opt/moa/" class="ui-text-input medium" tabindex="0" data-reactid=".1d9i1r55m2o.3.0.0.2.4.1.0.0.0.0.2" style="width:200px;float:left;">
																</label>
																<button class="btn gift-redeem-btn btn-plain btn-small" type="button" autocomplete="off" tabindex="0" data-reactid=".1d9i1r55m2o.3.0.0.2.4.1.0.0.0.1" style="width:70px;float:left;margin-top:-5px;">
																	<span data-reactid=".1d9i1r55m2o.3.0.0.2.4.1.0.0.0.1.0">Save</span>
																</button>
																
															</div>
															

															<div class="account-section-item account-section-item-disabled" data-reactid=".1lisbcwokxs.3.0.0.2.0.1.0.0.0.2"></div>
														</div>
														
													</div>
												</div>												
											</section>
										</div>
										 -->



								</div>
							</div>
						</div>
					</div>







