<?php
/**
 * @package    MOAM.Application
*
* @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/


namespace moam\components\extract;

defined('_EXEC') or die;

use moam\core\Framework;
use moam\core\Application;
use moam\core\Properties;
use moam\core\Template;
use moam\libraries\core\utils\Utils;
use moam\libraries\core\menu\Menu;
use moam\libraries\core\mining\Mining;


if (!class_exists('Application'))
{
    $application = Framework::getApplication();
}

if(!$application->is_authentication())
{
    $application->alert ( "Error: you do not have credentials." );
}

Framework::import("menu", "core/menu");

if (!class_exists('Menu'))
{
    $menu = new Menu();
    
}

Framework::import("Utils", "core/utils");
Framework::import("Mining", "core/mining");


Template::addHeader(array("tag"=>"link",
    "type"=>"text/css",
    "rel"=>"stylesheet",
    "href"=>""
    . $application->getPathTemplate()
    . "/css/table-excel.css"));

Template::addHeader(array("tag"=>"script",
    "type"=>"text/javascript",
    "src"=>""
    . $application->getPathTemplate()
    . "/javascript/base64.js"));


$utils = new Utils();


$csv = "";
$result_view = "";

//var_dump($_POST);exit();

$task = $application->getParameter("task");
$folder = $application->getParameter("folder");

if($folder != null){
    if(substr($folder, strlen($folder)-1)!="/"){
        $folder .= DIRECTORY_SEPARATOR;
    }
}

$scripts = "";

/*
 function htmlCSV($csv){
 
 $elements_classifieds_colors = array("#00FF00", "#00FFFF", "#FFFF00", "#FF7F24", "#FF00FF");
 $elements_classfieds_order = array("First", "second", "Third", "Fourth", "Fifth");
 
 
 
 $output="";
 $z=1;
 $table1 = "";
 $table2 = "";
 $itens = explode("\n",$csv);
 $avg_count=0;
 
 foreach($itens as $key=>$item){
 
 $cols = explode("\t", $item);
 
 if(count($cols)>0){
 
 if(empty($avg)){
 
 $bg_color="#ffffff";
 
 }else{
 
 if($avg==$avg_count){
 $bg_color="#cccccc";
 $avg_count=0;
 }else{
 $bg_color="#ffffff";
 $avg_count++;
 }
 
 }
 
 //echo "avg=".$avg.", avg_count=".$avg_count."<br>";
 
 $cols_v=false;
 foreach($cols as $key=>$item){
 if(trim($item)!=""){
 $cols_v = true;
 break;
 }
 }
 
 if($cols_v==true){
 
 $table1 .= "<tr>";
 $table1 .= "<th>".$z."</th>";
 
 $table2 .= "<tr style='background-color:$bg_color'>";
 // /classify_max
 
 foreach($cols as $key=>$item){
 if(trim($item)!=""){
 
 //if($classify_max==1){
 //	$table2 .= "<td bgcolor='{$elements_classifieds_colors[0]}'>".$item."</td>";
 //}else{
 $table2 .= "<td>".$item."</td>";
 //}
 
 
 }
 
 }
 
 $table2 .= "</tr>";
 $table1 .= "</tr>";
 $z++;
 
 }
 
 }
 
 
 }
 
 
 $result = 	"<table class=\"excel\" style=\"float:left;width:auto;display:table-cell;\">"
 .$table1."
 </table>
 <table class=\"excel\" style=\"float:left;width:auto;display:table-cell;\">
 <tbody>".$table2."
 </tbody>
 </table>";
 
 return $result;
 }
 */




$type_extract = $application->getParameter("type_extract");
$statisticaltest = $application->getParameter("statisticaltest");



if($task == "folder"){
    
    
    
}else{
    
    $filename = Properties::getBase_directory_destine($application)
    .$application->getUser()
    .DIRECTORY_SEPARATOR
    .$application->getParameter("folder")
    .$application->getParameter("filename");
    
    if($task == "preview"){
        
        $csv = $utils->getContentFile($filename);
        //echo $csv;
        
        //exit();
        
    }else if($task == "download"){
        
        
        //Header('Content-Disposition: attachment; filename=pedidos.csv');
        
        ob_end_clean();
        
        Header('Content-Description: File Transfer');
        
        
        $extension = $application->getParameter("filename");
        $extension = substr($extension, strrpos($extension, ".")+1);
        
        switch($extension){
            
            case 'tex':
                
                header( 'Content-Type: application/x-tex' );
                break;
            case 'csv':
                
                header( 'Content-Type: text/csv' );
                
                break;
            case 'html':
                
                header( 'Content-Type: text/html' );
                break;
                
        }
        
        
        //Header('Content-Type: application/force-download');
        header( 'Content-Disposition: attachment;filename='
            .$application->getParameter("filename"));
        
        echo $utils->getContentFile($filename);
        
        $contLength = ob_get_length();
        header( 'Content-Length: '.$contLength);
        
        exit();
        
    }else if($task == "view"){
        
        //Header('Content-Description: File Transfer');
        //Header('Content-Type: application/force-download');
        //Header('Content-Disposition: attachment; filename=pedidos.csv');
        
        //ob_end_clean();
        
        //echo "<pre>";
        $scripts = $utils->getContentFile($filename);
        //echo "</pre>";
        
        //$contLength = ob_get_length();
        //header( 'Content-Length: '.$contLength);
        
        //exit();
        
        
    }else if($task == "extract"){
        
        
        $element = $application->getParameter("element");
        
        
        $accuracy = $application->getParameter("accuracy");
        $timer = $application->getParameter("timer");
        $memory = $application->getParameter("memory");
        //$fp = $application->getParameter("fp");
        //	$fn = $application->getParameter("fn");
        $interval = $application->getParameter("interval");
        $breakline = $application->getParameter("breakline");
        $resume = $application->getParameter("resume");
        $detector = $application->getParameter("detector");
        $detectorsum = $application->getParameter("detectorsum");
        
        $dist = $application->getParameter("dist");
        $fn = $application->getParameter("fn");
        $fp = $application->getParameter("fp");
        $tn = $application->getParameter("tn");
        $tp = $application->getParameter("tp");
        $precision = $application->getParameter("precision");
        $recall = $application->getParameter("recall");
        $mcc = $application->getParameter("mcc");
        $f1 = $application->getParameter("f1");
        
        
        // 			$process_type = $application->getParameter("process_type");
        $decimalformat = $application->getParameter("decimalformat");
        
        if($dist == 1
            || $fn == 1
            || $fp == 1
            || $tn == 1
            || $tp == 1
            || $precision == 1
            || $recall == 1
            || $mcc == 1
            || $f1 == 1)
        {
            $resume = 1;
        }
        
        $parameters = array("accuracy"=>($accuracy==null?0:1),
            "type_extract"=>($type_extract==null?0:1),
            "timer"=>($timer==null?0:1),
            "memory"=>($memory==null?0:1),
            //"fp"=>($fp==null?0:1),
            //"fn"=>($fn==null?0:1),
            "column"=>(empty($_POST['column'])?0:1),
            "interval"=>($interval==null?0:1),
            "resume"=>($resume==null?0:1),
            "decimalformat"=>($decimalformat==null?".":$decimalformat),
            "detector"=>($detector==null?0:1),
            //  "mcc"=>($mcc==null?0:1),
        // "f1"=>($f1==null?0:1),
        "detectorsum"=>($detectorsum==null?0:1)
        );
        
        
        $dir = Properties::getBase_directory_destine($application)
        .$application->getUser()
        .DIRECTORY_SEPARATOR
        .$application->getParameter("folder");
        
        
        $mining = new Mining();
        
        $mining_store = array();
        
        
        
        foreach($element as $key=>$item){
            
            if(is_file($dir.$item)){
                
                
                $from_file = $dir.$item;//.DIRECTORY_SEPARATOR;
                
                //echo $from_file."<br>";
                
                
                
                
                if($type_extract==2){
                    
                    $scripts .= $mining->getScriptMOA($from_file);
                    $scripts .= "\n";
                    
                }else{
                    
                    
                    if($type_extract==1){
                        
                        $miningResult = $mining->extract_averages_in_file($from_file, $parameters);
                        
                        //var_dump($miningResult);exit("ok");
                        
                    }else{
                        //all
                        
                        if($detector == 1 || $detectorsum == 1)
                        {
                            $miningResult = $mining->extract_averages_detector_in_file($from_file, $parameters);
                            
                        }
                        else
                        {
                            $miningResult = $mining->miningFile($from_file, $parameters);
                        }
                        
                    }
                    
                    
                    array_push($mining_store, $miningResult);
                    
                    //echo "file - from: ".$from_file."<br>";
                    
                }
                
            }else{
                
                if(is_dir($dir.$item)){
                    
                    //exit("bruno");
                    
                    $from_dir = $dir.$item.DIRECTORY_SEPARATOR;
                    
                    //echo "dir - from: ".$from_dir."<br>";
                    
                    $files = $utils->getListElementsDirectory1($from_dir, array("txt"));
                    
                    
                    
                    foreach($files as $keyname=>$file){
                        
                        
                        if($type_extract==2){
                            
                            $scripts .= $mining->getScriptMOA($from_dir.$file["name"]);
                            $scripts .= "\n";
                            
                        }else{
                            if($type_extract==1){
                                
                                $miningResult = $mining->extract_averages_in_file($from_dir.$file["name"], $parameters);
                                
                            }else{
                                
                                
                                if($detector == 1)
                                {
                                    $miningResult = $mining->extract_averages_detector_in_file($from_dir.$file["name"], $parameters);
                                    
                                }
                                else if($detectorsum == 1)
                                {
                                    $miningResult = $mining->extract_averages_detector_in_file($from_dir.$file["name"], $parameters);
                                    
                                    $aux = array();
                                    
                                    foreach($miningResult as $item)
                                    {
                                        foreach($item as $key=>$value)
                                        {
                                            if(!isset($aux[$key]))
                                                $aux[$key] = 0;
                                                
                                                $aux[$key]=$aux[$key]+$value;
                                        }
                                        
                                    }
                                    
                                    $miningResult = $aux;
                                    
                                }
                                else
                                {
                                    $miningResult = $mining->miningFile($from_dir.$file["name"], $parameters);
                                }
                                
                                
                            }
                            
                            
                            
                            
                            
                            
                            
                            //$miningResult = $mining->miningFile($from_dir.$file["name"], $parameters);
                            
                            // echo $file["name"];
                            // var_dump($miningResult);
                            // exit();
                            array_push($mining_store, $miningResult);
                        }
                        
                    }
                    
                    
                }
            }
            
        }
        
        
        if($dist == 1
            || $fn == 1
            || $fp == 1
            || $tn == 1
            || $tp == 1
            || $precision == 1
            || $recall == 1
            || $mcc == 1
            || $f1 == 1)
        {
            
            $mining_store_aux = array();
            
            //$pos = array(0,1,2,3,4,5,6,7,8);
            
            if($dist == 1)
            {
                $pos  = 0;
            }
            
            if($fn == 1)
            {
                $pos  = 1;
            }
            
            if($fp == 1)
            {
                $pos  = 2;
            }
            
            if($tn == 1)
            {
                $pos  = 3;
            }
            
            if($tp == 1)
            {
                $pos  = 4;
            }
            
            if($precision == 1)
            {
                $pos  = 5;
            }
            
            if($recall == 1)
            {
                $pos  = 6;
            }
            
            if($mcc == 1)
            {
                $pos  = 7;
            }
            
            if($f1 == 1)
            {
                $pos  = 8;
            }
            
            foreach($mining_store as $item)
            {
                $a = array();
                
                foreach($item as $key=>$value)
                {
                    $itens = explode("\t", $value["resume"]);
                    
                    //0 = dist
                    //7 = mcc
                    //8 = f1
                    
                    $a[] = array("resume"=>$itens[$pos]);
                    
                }
                
                array_push($mining_store_aux, $a);
                
            }
            
            //
            $mining_store = $mining_store_aux;
        }
        
        
        
        
        
        if($type_extract==2){
            
            
        }else{
            
            $resume = $application->getParameter("resume");
            $detector = $application->getParameter("detector");
            
            if($resume == 1){
                
                $data_csv = $mining->convertCSV($mining_store, $parameters, 1);
                
            }else if($detector == 1 || $detectorsum == 1){
                
                if($detectorsum == 1)
                {
                    $data_csv = $utils->castArrayToCSV($mining_store, "\t", $decimalformat);
                }
                else
                {
                    $data_csv = $mining->convertCSV($mining_store, $parameters, 7);
                }
                
            }else{
                // 				    var_dump($mining_store);
                // 				    exit();
                $data_csv = $mining->convertCSV($mining_store, $parameters, $breakline);
                
            }
            
            
            $csv = $application->getParameter("csv");
            $tex = $application->getParameter("tex");
            $html = $application->getParameter("html");
            
            
            $save = $application->getParameter("save");
            
            if($save != null){
                
                
                //var_dump($_POST);
                
                $element = $application->getParameter("element");
                
                $dir = Properties::getBase_directory_destine($application)
                .$application->getUser()
                .DIRECTORY_SEPARATOR
                .$application->getParameter("folder");
                
                
                foreach($element as $key=>$item){
                    
                    if(is_file($dir.$item)){
                        
                        $filename_to_save = $dir.$item;;//.DIRECTORY_SEPARATOR;
                        
                    }else{
                        
                        $filename_to_save = $dir.$item;;//.DIRECTORY_SEPARATOR;
                        
                    }
                    
                }
                
                
                $overwrite = $application->getParameter("overwrite");
                $extensions = array();
                
                
                if($csv != null)
                    $extensions[] = "csv";
                    
                    if($tex != null)
                        $extensions[] = "tex";
                        
                        if($html != null)
                            $extensions[] = "html";
                            
                            
                            if($interval != null)
                                $ic = "(ic)";
                                else
                                    $ic = "";
                                    
                                    if($accuracy != null)
                                        $accu = "-accuracy";
                                        else
                                            $accu = "";
                                            
                                            if($timer != null)
                                                $tim = "-timer";
                                                else
                                                    $tim = "";
                                                    
                                                    if($memory != null)
                                                        $mem = "-memory";
                                                        else
                                                            $mem = "";
                                                            
                                                            
                                                            
                                                            foreach($extensions as $extension){
                                                                
                                                                
                                                                $filename = $filename_to_save.$accu.$tim.$mem.$ic.".".$extension;
                                                                
                                                                if(file_exists($filename))
                                                                    if($overwrite != null)
                                                                        unlink($filename);
                                                                        else
                                                                            break;
                                                                            
                                                                            switch($extension){
                                                                                
                                                                                case "csv":
                                                                                    
                                                                                    $data = $data_csv;
                                                                                    
                                                                                    break;
                                                                                case "html":
                                                                                    
                                                                                    $data = $utils->castToHTML($data_csv);
                                                                                    
                                                                                    break;
                                                                                case "tex":
                                                                                    
                                                                                    $data = $utils->castToTex($data_csv);
                                                                                    
                                                                                    $title = substr($filename_to_save,
                                                                                        strrpos($filename_to_save,
                                                                                            DIRECTORY_SEPARATOR)+1);
                                                                                    
                                                                                    $title = str_replace(" ", "-", $title);
                                                                                    
                                                                                    $data = str_replace("%title%", $title, $data);
                                                                                    
                                                                                    $data = str_replace("%label%",$title, $data);
                                                                                    
                                                                                    
                                                                                    break;
                                                                            }
                                                                            
                                                                            
                                                                            $utils->setContentFile($filename, $data);
                                                                            
                                                                            
                                                            }
                                                            
                                                            
            }
            
            
            
            $resume = $application->getParameter("resume");
            
            if($resume == 1){
                
                $labels = array("Dist.","FN","FP","TN","TP","Precision","Recall","MCC","F1");
                //-815.95 (+-1936.92)		467	13	599417		103	0.887931	0.000171804	0.400363313	0.000343542
                
                $result_view = implode("\t", $labels)."\n";
                
                $result_view .= $data_csv;
                
            }else if($detector == 1 || $detectorsum == 1){
                
                $labels = array("WFalse","WTrue","Drift","WcFalse","WcT","Dc", "Acc.");
                //-815.95 (+-1936.92)		467	13	599417		103	0.887931	0.000171804	0.400363313	0.000343542
                
                $result_view = implode("\t", $labels)."\n";
                
                $result_view .= $data_csv;
                
            }else{
                
                $result_view = $data_csv;
            }
            
            
            $viewdata = $application->getParameter("viewdata");
            
            
            switch($viewdata){
                
                case	"html":{
                    
                    header( 'Content-Type: text/html' );
                    
                    $result_view = $utils->createSheetHtml($result_view);
                    
                    break;
                }
                case	"txt":{
                    
                    ob_end_clean();
                    header( 'Content-Type: text/plain' );
                    
                    
                    
                    if($resume == 1){
                        
                        $labels = array("Dist.","FN","FP","TN","TP","Precision","Recall","\tMCC\t","F1");
                        //-815.95 (+-1936.92)		467	13	599417		103	0.887931	0.000171804	0.400363313	0.000343542
                        
                        $result_view = implode("\t", $labels)."\n";
                        
                        $result_view .= $data_csv;
                        
                    }else if($detector == 1 || $detectorsum == 1){
                        
                        $labels = array("WFalse","WTrue","Drift","WcFalse","WcT","Dc", "Acc.");
                        //-815.95 (+-1936.92)		467	13	599417		103	0.887931	0.000171804	0.400363313	0.000343542
                        
                        $result_view = implode("\t", $labels)."\n";
                        
                        $result_view .= $data_csv;
                        
                    }
                    
                    
                    //$a = $utils->castCsvToArray($result_view);
                    
                    //var_dump($a);exit("=====");
                    
                    //$result_view = $utils->createSheetHtml($result_view);
                    //var_dump($statisticaltest);exit("s");
                    
                    if($statisticaltest != 'no')
                    {
                        $filename = "AUTOLOAD" . time() . ".tmp";
                        $utils->setContentFile(PATH_USER_WORKSPACE_PROCESSING . $filename, $result_view);
                        
                        header("Location: index.php?component=statistical&controller=texteditor&filename=" . rawurlencode($filename) . "&task=".$statisticaltest);
                        
                    }else{
                        
                        echo $result_view;
                    }
                    
                    
                    //$contLength = ob_get_length();
                    //header( 'Content-Length: '.$contLength);
                    
                    exit();
                    
                    break;
                }
                case	"tex":{
                    
                    header( 'Content-Type: text/plain' );
                    ob_end_clean();
                    
                    echo  $utils->castToTex($result_view);
                    
                    
                    //$contLength = ob_get_length();
                    //header( 'Content-Length: '.$contLength);
                    
                    exit();
                    
                    break;
                }
                /*case	"csv":{
                
                header( 'Content-Type: text/csv' );
                ob_end_clean();
                
                $result_view = implode("\t", $labels)."\n";
                $result_view .= $data_csv;
                
                echo $result_view;
                
                $contLength = ob_get_length();
                header( 'Content-Length: '.$contLength);
                
                exit();
                
                break;
                }*/
                
            }
            
            
            
            
        }
    }
    
    
    
}

if($folder == null){
    
    $files_list = $utils->getListElementsDirectory1(
        Properties::getBase_directory_destine($application)
        .$application->getUser()
        .DIRECTORY_SEPARATOR, array("txt","report"));
    
}else{
    
    
    //$to_folder =// Properties::getBase_directory_destine($application)
    //.$application->getUser()
    //.DIRECTORY_SEPARATOR
    //$application->getParameter("folder")
    //.DIRECTORY_SEPARATOR
    //.$application->getParameter("rename");
    
    //exit("ddd - ".$folder);
    $files_list = $utils->getListElementsDirectory1(
        Properties::getBase_directory_destine($application)
        .$application->getUser()
        .DIRECTORY_SEPARATOR
        .$folder
        //.DIRECTORY_SEPARATOR
        , array("txt","report"));
}


foreach($files_list as $key=>$element){
    
    if($element["type"]=="dir"){
        if($element["name"]=="scripts"){
            unset($files_list[$key]);
        }
    }else{
        
        /*echo substr($element["name"],strrpos($element["name"],".")+1);
         if(substr($element["name"],strrpos($element["name"],".")+1)=="log"){exit("bruno");
         unset($files_list[$key]);
         }*/
    }
}


$dir_list = $utils->getListDirectory(
    Properties::getBase_directory_destine($application)
    .$application->getUser()
    .DIRECTORY_SEPARATOR
    .$folder);


?>


<style>

div#table_id table tr td{
	border:1px solid #cccccc;
	border-collapse: collapse;
	padding:1px;
}

</style>	
	
		<div class="content content-alt">
			<div class="container" style="width:90%">
				<div class="row">
					<div class="" >
					
						<div class="card" style="width:100%">
							<div class="page-header">
								<h1><a href="<?php echo $_SERVER['REQUEST_URI']?>">Extract</a></h1>
							</div>
							
							<div style="width:100%;padding-bottom:15px;display:table">
						
								
								<div style="float:left;width:100%;border:1px solid #fff;display:table;">
									
									
	<!-- 	<input type="button" value="Return" name="return" onclick="javascript: returnPage();" /> 	<br><br>	 -->	
<?php 

	if($task == "preview"){
		
		$filename = Properties::getBase_directory_destine($application)
						.$application->getUser()
						.DIRECTORY_SEPARATOR
						.$application->getParameter("folder")
						.$application->getParameter("filename");
		
		$extension_file = $filename;
		$extension_file = substr($extension_file, strrpos($extension_file,".")+1);
		
		if($extension_file == "html"){
			
			echo "<div id='table_id'>".$csv."</div>";
			
		}else if($extension_file == "csv"){
			
			echo $utils->createSheetHtml($csv);//htmlCSV($csv);
		}
		
		
		
	}else{

	
		
		if($type_extract==2){
	?>	
			Content:<br>
			<textarea id="data"	style="width:100%;height:400px;" name="data"><?php echo $scripts?></textarea><br>
													
	<?php 														
		}else{
				
			if($statisticaltest != 'no')
			{
				
				header("Location: index?component=statistical&controller=texteditor&data=" . rawurlencode($result_view));
				exit();
				
			}else{		
				
				//if(strlen($csv)>1){
					
					//exit("ooo");
					//$aa = $utils->castToCSV($csv);
					
					//var_dump($aa);exit();
					//App::setParameter('tmpl',true);
					echo $result_view;//echo $utils->createSheetHtml($csv);//htmlCSV($csv);
				
				//}
				
			}
	
		}
		
		
	}
?>	
									
<script>


function returnPage(){
	//window.history.go(-1);

	//http://localhost/iea/?component=moa&controller=reportview&filename=maciel.log&folder=New%20Folder/

		window.location.href='?component=<?php echo $application->getParameter("component");?>'
			+'&controller=extract-values'
			+'&folder=<?php echo $application->getParameter("folder");?>'
			+'&task=open';
		
}


</script>									
								
								</div>
							
							</div>
							
						</div>
					</div>
				</div>
			</div>
		</div>