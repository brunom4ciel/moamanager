<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\components\files;

defined('_EXEC') or die();

use moam\core\Framework;
use moam\core\Template;
use moam\core\Properties;
use moam\libraries\core\utils\Utils;
// use moam\libraries\core\menu\Menu;
use ZipArchive;
if (! class_exists('Application')) {
    $application = Framework::getApplication();
}

if (! $application->is_authentication()) {
    $application->alert("Error: you do not have credentials.");
}

// Framework::import("menu", "core/menu");

// if (! class_exists('Menu')) {
//     $menu = new Menu();
// }

// Template::setDisabledMenu();

Template::addHeader(array("tag"=>"script",
    "type"=>"text/javascript",
    "src"=>""
    . $application->getPathTemplate()
    . "/javascript/edit_area/edit_area_full.js"));


Framework::import("Utils", "core/utils");

$extension_scripts = ".data";

$filename = $application->getParameter("filename");
$folder = $application->getParameter("folder");
// $dirScriptsName = "scripts";

$data = "";

if ($filename != null) {

    $utils = new Utils();

    $filename = Properties::getBase_directory_destine($application) . $application->getUser() . 
    // .DIRECTORY_SEPARATOR
    // .$dirScriptsName
    DIRECTORY_SEPARATOR . $folder . 
    // .DIRECTORY_SEPARATOR
    $filename;
    // .$extension_scripts
    

    $task = $application->getParameter("task");

    if ($task == "save") {

        $data = $application->getParameter("data");
        $utils->setContentFile($filename, $data);

        $filenamenew = Properties::getBase_directory_destine($application) . $application->getUser() . 
        // .DIRECTORY_SEPARATOR
        // .$dirScriptsName
        DIRECTORY_SEPARATOR . $folder . 
        // .DIRECTORY_SEPARATOR
        $application->getParameter("filenamenew");

        if ($application->getParameter("filenamenew") != $application->getParameter("filename")) {

            if (file_exists($filename)) {

                if (file_exists($filenamenew . $extension_scripts)) {

                    while (file_exists($filenamenew . $extension_scripts)) {
                        $filenamenew = "copy-" . $filenamenew;
                    }
                }

                rename($filename, $filenamenew . $extension_scripts);

                $application->getParameter("filename", substr($filenamenew, strrpos($filenamenew, "/") + 1, strrpos($filenamenew, ".")));
            }
        }
    } else {

        if ($task == "remove") {

//             if (file_exists($filename)) {

//                 unlink($filename);
//                 header("Location: " . PATH_WWW . "?component=" . $application->getComponent() . "&controller=files");
//             }

        } else {

            if ($task == "new") {

                // exit($filename);

//                 $data = "";
//                 $utils->setContentFile($filename, $data);

            } else {

                $extension = substr($filename, strrpos($filename, ".") + 1);

                if ($extension == "zip") {
                    $data = "";

                    $za = new ZipArchive();

                    $za->open($filename);

                    for ($i = 0; $i < $za->numFiles; $i ++) {

                        $item = $za->statIndex($i);

                        $data .= $item["name"] . " - " . $utils->formatSize($item["size"]) . "<br>";
                        // print_r($za->statIndex($i));
                    }
                    // echo "numFile:" . $za->numFiles . "\n";
                } else {

                    $maxBytesFileLoadPart = Properties::getFileContentsMaxSize();

//                     $size = filesize($filename) /1024;
                    
//                     //                     $bparted = 20;
                    
//                     if($size > 3000)
//                     {
//                         $bparted = 1000;
//                     }
//                     else
//                     {
//                         if($size > 1000)
//                         {
//                             $bparted = 500;
//                         }
//                         else
//                         {
//                             $bparted = $maxBytesFileLoadPart;
//                         }
//                     }                    
                    
                    
                    $data1 = $utils->getContentFilePart($filename, ($maxBytesFileLoadPart * 1024));
//                     $script = "";
//                     $output = "";
                    
//                     if($utils->isMetadataFileScript($filename))
//                     {
//                         $script = $utils->getMetadataValueScript($filename, "script");
//                         $output = $utils->getMetadataValueScript($filename, "command-output");
                        
//                         if(strpos($data1["data"], "Accuracy:") ===  false)
//                         {
                            
//                         }
//                         else
//                         {
//                             $data1["data"] = substr($data1["data"], strpos($data1["data"], "Accuracy:"));
//                             $data1["data"] = substr($data1["data"], 0, strpos($data1["data"], "learning evaluation instances"));
//                         }
//                     }
//                     else 
//                     {
//                         if(strpos($data1["data"], "learning evaluation instances") === false)
//                         {
                            
                            
//                         }
//                         else 
//                         {
                            
//                             $script = substr($data1["data"], 0, strpos($data1["data"], "\n\n"));
                            
//                             $data1["data"] = substr($data1["data"], strpos($data1["data"], "Accuracy:"));
//                             $data1["data"] = substr($data1["data"], 0, strpos($data1["data"], "learning evaluation instances"));
                            
//                         }
                        
                        
//                     }
                        
                    
                    
                    $data = $data1["data"];
                    
                    $filesize = $utils->formatSize(filesize($filename));
                }
            }
        }
    }
}

?>

					<div class="page-header">
						<h1>
							<a href="<?php echo $_SERVER['REQUEST_URI']?>">Read file</a>
						</h1>
					</div>

							<form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>"
								name="saveform"
								class="ng-pristine ng-valid-email ng-invalid ng-invalid-required">
								<input type="hidden"
									value="<?php echo $application->getComponent()?>"
									name="component"> <input type="hidden" value="editfile"
									name="controller"> <input type="hidden" value="save"
									name="task" id="task"> <input type="hidden"
									value="<?php echo $application->getParameter("filename");?>"
									name="filename"> <input type="hidden"
									value="<?php echo $application->getParameter("folder");?>"
									name="folder">

								
									<div
										style="margin-left: 5px; display: table; width: 99%; height: 70px; background-color: #F3F781; border: 1px solid #000; text-align: center; vertical-align: middle;">
										Read-only <br>
											<?php

        echo "From Original: " . $utils->formatSize(filesize($filename)) . "<br>";

        $extension = substr($filename, strrpos($filename, ".") + 1);

        if ($extension == "zip") {} else {
            echo "Load: " . $utils->formatSize($data1["size"]);
        }
        ?>
											
											</div>


									<div
										style="float: left; padding-left: 5px; width: 100%; margin-top: 5px;">
												
												<?php

            if ($extension == "zip") {

                echo '<a href="' . PATH_WWW . '?component=resource&tmpl=tmpl&task=download&file=' . $application->getParameter("folder") . $application->getParameter("filename") . '">' . "<img width='16px' align='middle' src='" . $application->getPathTemplate() . "/images/icon_download.png' title='View contents'/></a> ";

                echo $application->getParameter("filename") . "<br><br>";
                echo $data;
            } else {
                ?>
													
												<input type="text" style="width: 100%" name="filenamenew"
											value="<?php echo $application->getParameter("filename");?>">
																						
										<textarea id="data" style="width: 100%; height: 400px;"
											name="data"><?php echo $data?></textarea>	
											
												
											<a target="_blank"
											href="<?php echo PATH_WWW ."?component=resource&tmpl=false&task=open&file=".$application->getParameter("folder").$application->getParameter("filename");?>">
											[Open]</a> <a
											href="<?php echo PATH_WWW."?component=resource&tmpl=false&task=download&file=".$application->getParameter("folder").$application->getParameter("filename");?>">
											[Download]</a> <?php echo $filesize?>		
													
													<?php
            }

            ?>
												
												
											</div>





									<div style="float: right; padding-left: 10px;">

										<input type="button" class="btn btn-default" value="Return" name="return"
										onclick="javascript: returnPage();" />
									</div>
									
									
							</form>




<script type="text/javascript">

function returnPage()
{

	window.location.href='?component=<?php echo $application->getParameter("component");?>'
			+'&controller=controller'
			+'&task=open'
			+'&folder=<?php echo $application->getParameter("folder");?>';
			
}

function downloadfile(){

	window.location.href='?component=<?php echo $application->getParameter("component");?>'
						+'&controller=files'
						+'&filename=<?php echo $application->getParameter("filename");?>'
						+'&folder=<?php echo $application->getParameter("folder");?>';
	
}

</script>

<script type="text/javascript">
	// initialisation
	editAreaLoader.init({
		id: "data"	// id of the textarea to transform	
			,start_highlight: false	
			,font_size: "8"
			,is_editable: false
			,word_wrap: true
			,font_family: "verdana, monospace"
			,allow_resize: "y"
			,allow_toggle: true
			,language: "en"
			,syntax: "html"	
			,toolbar: "go_to_line, |, undo, redo, |, select_font"
			//,load_callback: "my_load"
			//,save_callback: "my_save"
			,plugins: "charmap"
			,min_height: 300
			,charmap_default: "arrows"
	});


	function toogle_editable(id, id2)
	{
		if(id2.value == "Toggle to edit mode")
		{
			id2.value = "Toggle to read only mode";
		}
		else
		{
			id2.value = "Toggle to edit mode";
		}
		
		editAreaLoader.execCommand(id, 'set_editable', !editAreaLoader.execCommand(id, 'is_editable'));
	}

</script>




