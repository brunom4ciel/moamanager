<?php
/**
 * @package    moam\libraries\core\utils
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\libraries\core\utils;

use Exception;
use \RecursiveIteratorIterator;
use \RecursiveDirectoryIterator;
use moam\libraries\core\sys\CPULoad;

defined('_EXEC') or die();

class Utils
{
    
    function friedman_postos($data_values, $order=0)
    {
		$rank_avg = array();
    
		for($i = 1; $i <= count($data_values); $i++)
		{
			for($z = 0; $z < count($data_values[$i]); $z++)
			{
				if(isset($data_values[$i][$z]) && isset($data_values[$i]))
				{
					$rank_avg[$i][$z] = $this->rank_avg($data_values[$i][$z], $data_values[$i], $order);
				}
				
			}        
		}
		
		return $rank_avg;
	}
    
    function rank_avg($value, $array, $order = 0) {
        // sort
        if ($order) sort ($array); else rsort($array);
        // add item for counting from 1 but 0
        array_unshift($array, $value+1);
        // select all indexes vith the value
        $keys = array_keys($array, $value);
        if (count($keys) == 0) return NULL;
        // calculate the rank
        return array_sum($keys) / count($keys);
    }
    
    
    function avgColsArray($arr, $decimalprecision=2)
    {
        $result = array();
        $countRows = 0;
        
        foreach($arr as $key=>$item)
        {
            foreach($item as $key2=>$value)
            {
                $result[$key2] += $value;
            }
            $countRows++;
        }
        
        foreach($result as $key=>$value)
        {
            $result[$key] = floatval($result[$key] / $countRows);
            $result[$key] = number_format($result[$key], $decimalprecision);
        }
        
        return $result;
    }
    
    function sumColsArray($arr, $decimalprecision=2)
    {
        $result = array();
        foreach($arr as $key=>$item)
        {
            foreach($item as $key2=>$value)
            {
                $result[$key2] += $value;    
                $result[$key2] = number_format($result[$key2], $decimalprecision);   
            }            
        }
        
        return $result;
    }
    
    function winsColsArray($arr)
    {
		//var_dump($arr);
        $result = array();
        
        foreach($arr as $item)
        {
			foreach($item as $k=>$v)
			{
				$result[$k] = 0;
			}
			break;
		}
                
        foreach($arr as $key=>$item)
        {
			$aux = $item;        
			asort($aux);
			$best_value = 0;
			
			foreach($aux as $key1=>$val)
			{
				$best_value = $val;
				break;
			}
			
			$firstvalue = $best_value;
			$ties = 0;
			
			foreach($item as $key2=>$value)
			{
				if($firstvalue == $value)
				{
					$ties += 1;
				}					
			} 
				//echo $best_value . "=" . $losses."\n";
				
						 //var_dump($best_value);//var_dump($item);
						 //exit();       
			if($ties < 2)
			{
				foreach($item as $key2=>$value)
				{
					//echo $key2 . "=".$value ."==". $aux;//exit();
					//echo $best_value . "=" . $losses."\n";
					
					if($value == $best_value)
					{
						$result[$key2] += 1; 					
					}
				}  
			}
			
			
			
			
            //var_dump($item);var_dump($result);var_dump($aux[0]);exit();          
        }
        
        //var_dump($result);
        //exit();
        
        return $result;
    }
    
    function tiesColsArray($arr)
    {
		$result = array();
        
        foreach($arr as $item)
        {
			foreach($item as $k=>$v)
			{
				$result[$k] = 0;
			}
			break;
		}
                
        foreach($arr as $key=>$item)
        {
			$aux = $item;        
			asort($aux);
			$best_value = 0;
			
			foreach($aux as $key1=>$val)
			{
				$best_value = $val;
				break;				
			}
			
			$firstvalue = $best_value;
			$ties = 0;
			
			foreach($item as $key2=>$value)
			{
				if($firstvalue == $value)
				{
					$ties += 1;
				}					
			} 
			
			if($ties > 1)//is_float($best_value))
			{
				foreach($item as $key2=>$value)
				{
					//echo $key2 . "=".$value ."==". $aux;//exit();
					
					if($value == $best_value)
					{
						$result[$key2] += 1; 					
					}
				}
			}     
            
            
            //var_dump($item);var_dump($result);var_dump($aux[0]);exit();          
        }
        
        //var_dump($result);exit();
        
        return $result;
	}
    
    function lossesColsArray($arr)
    {
		$result = array();
        
        foreach($arr as $item)
        {
			foreach($item as $k=>$v)
			{
				$result[$k] = 0;
			}
			break;
		}
                
        foreach($arr as $key=>$item)
        {
			$aux = $item;        
			asort($aux);
			$best_value = 0;
			
			foreach($aux as $key1=>$val)
			{
				$best_value = $val;
				break;				
			}
						
			foreach($item as $key2=>$value)
			{
				//echo $key2 . "=".$value ."==". $aux;//exit();
				
				if($value != $best_value)
				{
					$result[$key2] += 1; 					
				}
			}  
			
            //var_dump($item);var_dump($result);var_dump($aux[0]);exit();          
        }
        
        //var_dump($result);exit();
        
        return $result;
	}
	
	
    function xCopy($source, $destination)
    {
        
        
        if(is_dir($source))
        {
            
            if (!file_exists($destination)) {
                mkdir($destination);
            }
            
            $splFileInfoArr = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
            
            foreach ($splFileInfoArr as $fullPath => $splFileinfo) {
                //skip . ..
                if (in_array($splFileinfo->getBasename(), [".", ".."])) {
                    continue;
                }
                //get relative path of source file or folder
                $path = str_replace($source, "", $splFileinfo->getPathname());
                
                if ($splFileinfo->isDir()) {
                    mkdir($destination . "/" . $path);
                } else {                    
                    copy($fullPath, $destination . "/" . $path);
                }
            }
        }
        else
        {
            
//             $dest = substr($destination, strrpos($destination, DIRECTORY_SEPARATOR)+1);
//             $dest = substr($dest, 0, strrpos($dest, "."));            
            
//             $dir = dirname($destination) . DIRECTORY_SEPARATOR;
            
//             $foldernew  = $dest;
//             $foldernew__ = $dest . "-copy";
//             $y=0;
            
//             while(is_dir($dir . $foldernew__)){
//                 $foldernew__ = $foldernew."-copy-(".$this->format_number($y++,2).")";
//             }
            
//             $foldernew = $foldernew__;
            
//             $this->create_dir($foldernew, $dir);
            
//             exit($dir . $foldernew);
//             echo "". $source . " -> " . $dir . $foldernew ;
            
//             exit();

            if(is_file($source))
            {
                if(!is_dir($destination))
                {
                    copy($source, $destination);                    
                }                
            }
            
        }
            
        
    }
    
    
    function killPID($pid)
    {
        if($this->checkPID($pid))
        {
            exec("kill $pid");
        }
    }
    
    
    function checkPID($pid)
    {
        $result = FALSE;
        //$pid=1039;
//         exec("ps aux | grep \"${pid}\" | grep -v grep | awk '{ print $2 }' | head -1", $out);
        exec("ps aux | grep \"${pid}\" | grep -v grep", $out);

        if(!empty($out[0]))
        {
            $str = explode(" ", $out[0]);
//             var_dump($str);exit();
            if(trim($str[1]) == "")
            {
                if($str[2] == $pid)
                {
                    $result = TRUE;
                }
            }else{
                if($str[1] == $pid)
                {
                    $result = TRUE;
                }
            }
            
//             var_dump($result);exit();            
//             $result = (int) $out[0];
            
        }
        
        return $result;        
    }
    
    
    public function removeDuplicateSpaces($str="")
    {
        while(strpos($str, "  "))
        {
            $str = str_replace("  "," ", $str);
        }
        
        return $str;
    }

    function compareVersion($versionLocal = "", $versionRemote)
    {
        
        $result = FALSE;
        
        $str_remote = explode(".", $versionRemote);
        
        $str_local = explode(".", $versionLocal);
        $button_show = false;
        
        for($i = 0; $i < count($str_local); $i++)
        {
            if($str_local[$i] < $str_remote[$i])
            {
                $result = TRUE;
            }else
            {
                
            }
        }
        
        return $result;
    }
    
    
    
    function getHardwareCpuName(){
       
        $file = file('/proc/cpuinfo');
        
        return trim($this->removeDuplicateSpaces(substr($file[4], strrpos($file[4], ":")+1)));
    }
    
    
    function getHardwareCpuUsage(){
        
        $cpuload = new CPULoad();
        $cpuload->get_load();
        // $cpuload->print_load();
        $cpu_du = 0;
        $cpu_df = 0;
        $cpu_dt = 0;
        
        // echo "CPU load is: ".$cpuload->load["cpu"]."%";
        
        return @round($cpuload->load["cpu"], 2);
    }
    
    
    function getHardwareMemoryRamUsage()
    {
        foreach (file('/proc/meminfo') as $ri)
            $m[strtok($ri, ':')] = strtok('');
            return 100 - @round(($m['MemFree'] + $m['Buffers'] + $m['Cached']) / $m['MemTotal'] * 100);
    }
    
    function getHardwareMemory(){
        
        $file = file('/proc/meminfo');
        
        $men = trim(substr($file[0], strrpos($file[0], ":")+1));
        $men = trim($men);
        $men = trim(substr($men, 0, strrpos($men, " ")));
        $men = (int) $men;
        
        $unit=array('kb','mb','gb','tb','pb');
        
        return @round($men/pow(1024,($i=floor(log($men,1024)))),2).' '.$unit[$i];
    }
    
    function getHardwareDisk(){
        
        $men = (int) disk_total_space('/');
        
        $unit=array('b','kb','mb','gb','tb','pb');
        
        return @round($men/pow(1024,($i=floor(log($men,1024)))),2).' '.$unit[$i];
    }
    
    function getHardwareDiskFree(){
        
        //$men = (int) disk_total_space('/');
        $men = (int) disk_free_space('/');
        
        $unit=array('b','kb','mb','gb','tb','pb');
        
        return @round($men/pow(1024,($i=floor(log($men,1024)))),2).' '.$unit[$i];
    }
    
    function getHardwareDiskUsage(){
        
        $men = (int) disk_total_space('/') - (int) disk_free_space('/');
        
        $unit=array('b','kb','mb','gb','tb','pb');
        
        return @round($men/pow(1024,($i=floor(log($men,1024)))),2).' '.$unit[$i];
    }
    
    function getHardwareUptime(){
        
        $str   = @file_get_contents('/proc/uptime');
        
        return $this->formatDatetime($str);
    }
    
    
    function formatDatetime($str)
    {
        $secs   = floatval($str);
        
        $bit = array(
            'y' => $secs / 31556926 % 12,
            'w' => $secs / 604800 % 52,
            'd' => $secs / 86400 % 7,
            'h' => $secs / 3600 % 24,
            'm' => $secs / 60 % 60,
            's' => $secs % 60
        );
        
        $ret = array();
        
        foreach($bit as $k => $v)
        {
            if($v > 0)
            {
                $ret[] = $v . $k;
            }
        }
        
        $s = join(' ', $ret);
        
        return trim($s);
    }
    
    function getHardwareKernelVersion() {
        
        $result = $this->runExternal("lsb_release -a");
        
        $str = explode("\n", $result["output"]);
        
        foreach($str as $line)
        {
//             if(trim(substr($line,0, strrpos($line, ":"))) == "Distributor ID") 
//             {
//                 $distributorId = trim(substr($line,strrpos($line, ":")+1));
//             }
            
            if(trim(substr($line,0, strrpos($line, ":"))) == "Description")
            {
                $description = trim(substr($line,strrpos($line, ":")+1));
            }
            
            if(trim(substr($line,0, strrpos($line, ":"))) == "Codename")
            {
                $codename = trim(substr($line,strrpos($line, ":")+1));
            }
                        
        }
        
        
        
//         $result = trim(substr($str[2], strrpos($str[2], ":")+1));
//         $result .= " " . trim(substr($str[4], strrpos($str[4], ":")+1));
        
//         var_dump($result);exit();
//         $file = file('/proc/version');
        //$kernel = explode(' ', file_get_contents('/proc/version'));
        //$result = $kernel[0] . " " . $kernel[2] . " " . $kernel[8];
                
//         var_dump($description);
        
        $result = $description . " " .  $codename;
        
        return $result;
        
    }
    
    
    function getHardwareInfo() {
        
        $sysinfo = $this->getHardwareCpuName();
        $sysinfo .= " / " . $this->getHardwareCpuUsage() . "%";        
        $sysinfo .= " RAM " . $this->getHardwareMemory();
        $sysinfo .= " / " . $this->getHardwareMemoryRamUsage() . "%";        
        $sysinfo .= " OS " . $this->getHardwareKernelVersion();
        $sysinfo .= " Uptime " . $this->getHardwareUptime();
        $sysinfo .= " Disk Total " . $this->getHardwareDisk();
        $sysinfo .= " Disk Free " . $this->getHardwareDiskFree();
        
        return $sysinfo;        
    }
    
    
	/*
	 * converts data in CSV notation to a table with HTML notation.
	 * 
	 * @param	string	$csv	data values
	 * 
	 * @return	string
	 */
    function createSheetHtml($csv)
    {
        $z = 1;
        $table1 = "";
        $table2 = "";
        $itens = explode("\n", $csv);
        $avg_count = 0;

        foreach ($itens as $key => $item) {

            $cols = explode("\t", $item);

            if (count($cols) > 0) {

                if (empty($avg)) {

                    $bg_color = "#ffffff";
                } else {

                    if ($avg == $avg_count) {
                        $bg_color = "#cccccc";
                        $avg_count = 0;
                    } else {
                        $bg_color = "#ffffff";
                        $avg_count ++;
                    }
                }

                // echo "avg=".$avg.", avg_count=".$avg_count."<br>";

                $cols_v = false;
                foreach ($cols as $key => $item) {
                    if (trim($item) != "") {
                        $cols_v = true;
                        break;
                    }
                }

                if ($cols_v == true) {

                    $table1 .= "<tr>";
                    $table1 .= "<th>" . $z . "</th>";

                    $table2 .= "<tr style='background-color:$bg_color'>";
                    // /classify_max

                    foreach ($cols as $key => $item) {
                        if (trim($item) != "") {

                            // if($classify_max==1){
                            // $table2 .= "<td bgcolor='{$elements_classifieds_colors[0]}'>".$item."</td>";
                            // }else{
                            $table2 .= "<td>" . $item . "</td>";
                            // }
                        }
                    }

                    $table2 .= "</tr>";
                    $table1 .= "</tr>";
                    $z ++;
                }
            }
        }

        $result = "<div style='width:100%;float:left'><table class=\"excel\" style=\"float:left;width:20px;display:block;\">" . $table1 . "
				    </table>
				    <table class=\"excel\" style=\"float:left;width:97%;display:block;\">
				        <tbody>" . $table2 . "
				        </tbody>
				    </table></div>";

        return $result;
    }

    

	/*
	 * converts data from a PHP array to a check box with HTML notation.
	 * 
	 * @param	string	$id	id attribute of html tag;
	 * @param	string	$name	name attribute of html tag;
	 * @param	string	$data	array data values
	 * @param	string	$default	value in the list
	 * @param	string	$style	style attribute of html tag;
	 * @param	string	$class	class attribute of html tag;
	 * @param	string	$event	event attribute of html tag;
	 * 
	 * @return	string
	 */
    public function createSelectList($id, $name, $data, $default = '', $style = '', $class = '', $event = '')
    {
        if (! empty($style))
            $style = " style=\"" . $style . "\" ";

        if (! empty($class))
            $class = " class=\"" . $class . "\" ";

        if (! empty($event))
            $event = " " . $event . " ";

        $result = "<select name='" . $name . "' id='" . $id . "'"  . $class . $style . "" . $style . "" . $event . "><option value=\"\"></option>";

        foreach ($data as $element) {

            if ($element["id"] == $default) {

                $result .= "<option value=\"" . $element["id"] . "\" selected>" . $element["name"] . "</option>";
            } else {

                $result .= "<option value=\"" . $element["id"] . "\">" . $element["name"] . "</option>";
            }
        }

        return $result . "</select>";
    }

 
    
	/*
	 * format file size notation.
	 * 
	 * @param	integer	$bytes	size in bytes
	 * 
	 * @return	string
	 */
    function formatSize($bytes)
    {
        $types = array(
            'Byte(s)',
            'KB',
            'MB',
            'GB',
            'TB'
        );

        for ($i = 0; $bytes >= 1024 && $i < (count($types) - 1); $bytes /= 1024, $i ++);
        return (round($bytes, 2) . " " . $types[$i]);
    }

	/*
	 * get the amount of RAM used.
	 * 
	 * @param	integer	$format_type	format file size notation.
	 * 
	 * @return	string
	 */
    function getRAM($format_type)
    {
        $result = 0;

        $fh = fopen('/proc/meminfo', 'r');
        $mem = 0;
        while ($line = fgets($fh)) {
            $pieces = array();
            if (preg_match('/^MemTotal:\s+(\d+)\skB$/', $line, $pieces)) {
                $mem = $pieces[1];
                break;
            }
        }

        fclose($fh);

        switch ($format_type) {
            case 'kb':

                $result = $mem;
                break;
            case 'mb':

                $result = $mem / 1024;

                break;
            case 'gb':

                $result = $mem / 1024;
                $result = $result / 1024;

                break;
            default:

                $result = $mem * 1024;
        }

        return $result;
    }

	/*
	 * get available byte size on disk.
	 * 
	 * @param	string	$directory	path
	 * 
	 * @return	integer
	 */
    public function getFreeSpace($directory = '/')
    {
        $result = 0;

        try {
            if (is_dir($directory))
                $result = disk_free_space($directory);
        } catch (Exception $e) {}

        return $result;
    }

	/*
	 * insert data at the beginning of a file.
	 * 
	 * @param	string	$handle	point resource
	 * @param	string	$string	data
	 * @param	integer	$bufferSize	size for reading the data
	 * 
	 * @return	void
	 */
    public function finsert($handle, $string, $bufferSize = 1024)
    {
        $insertionPoint = ftell($handle);

        // Create a temp file to stream into
        $tempPath = tempnam(sys_get_temp_dir(), "file-chainer");
        $lastPartHandle = fopen($tempPath, "w+");

        // Read in everything from the insertion point and forward
        while (! feof($handle)) {
            fwrite($lastPartHandle, fread($handle, $bufferSize), $bufferSize);
        }

        // Rewind to the insertion point
        fseek($handle, $insertionPoint);

        // Rewind the temporary stream
        rewind($lastPartHandle);

        // Write back everything starting with the string to insert
        fwrite($handle, $string);
        
        while (! feof($lastPartHandle)) {
            fwrite($handle, fread($lastPartHandle, $bufferSize), $bufferSize);
        }

        // Close the last part handle and delete it
        fclose($lastPartHandle);
        unlink($tempPath);

        // Re-set pointer
        fseek($handle, $insertionPoint + strlen($string));
    }

	/*
	 * for zero-padding with a length of n
	 *  
	 * @param	mixed	$number
	 * @param	string	$format
	 * 
	 * @return	void
	 */
    public function format_number($number, $format)
    {
        $result = "";

        $n = floor(strlen($number) / 10);
        $s = $format - strlen($number);

        while ($n < $s) {

            $result .= "0";
            $n ++;
        }

        $result .= $number;

        return $result;
    }

	/*
	 * removes files and directories in a recursive way.
	 * 
	 * @param	string	$dir	real path
	 * 
	 * @return	void
	 */
    public function delTree($dir)
    {
        if (is_dir($dir)) {
            $files = array_diff(scandir($dir), array(
                '.',
                '..'
            ));

            foreach ($files as $file) {
                (is_dir("$dir/$file")) ? $this->delTree("$dir/$file") : unlink("$dir/$file");
            }

            return rmdir($dir);
        }
    }

	/*
	 * cast from csv to html table
	 * 
	 * @param	string	$data	data values
	 * 
	 * @return	string
	 */
    public function castToHTML($data)
    {
        $rows = "";

        $lines = explode("\n", $data);

        foreach ($lines as $line) {

            $values = explode("\t", $line);

            $rows .= "\t<tr>\n";

            foreach ($values as $value) {

                $rows .= "\t\t<td>" . $value . "</td>\n";
            }

            $rows .= "\t</tr>\n";
        }

        return "<table>\n" . $rows . "</table>";
    }


	/*
	 * cast from csv to html latex
	 * 
	 * @param	string	$data	data values
	 * 
	 * @return	string
	 */
    public function castToTex($data)
    {
        $result = "";
        $rows = "";
        $cols = "";
        $qtd_cols = 0;

        $lines = explode("\n", $data);

        foreach ($lines as $line) {

            $values = explode("\t", $line);

            if ($cols == "") {

                foreach ($values as $value) {
                    $cols .= "c";
                    $qtd_cols ++;
                }
            }

            $i = 0;
            foreach ($values as $value) {

                if ($i == 0)
                    $rows .= $value;
                else
                    $rows .= " & " . $value;

                $i ++;
            }

            $rows .= "\\\\ ";

            if ($i < $qtd_cols - 1) {
                $rows .= "\n";
            } else {
                $rows .= "\hline\n";
            }
        }

        $result = "
\\begin{table}[ht!]
\\centering
\\caption{%title%}
\\label{%label%}
	\begin{tabular}{" . $cols . "}
        \\toprule\n" . "\t\t" . $rows . "		
        \\bottomrule
    \\end{tabular}
\\end{table}";

        return $result;
    }

	/*
	 * cast from csv to array PHP
	 * 
	 * @param	string	$data	data values
	 * 
	 * @return	mixed
	 */
    public function castCsvToArray($data)
    {
        $result = array();

        $lines = explode("\n", $data);

        foreach ($lines as $line) {

            $values = explode("\t", $line);

            $data = array();
            foreach ($values as $value) {
                $data[] = $value;
            }
            $result[] = $data;
        }

        return $result;
    }

	/*
	 * cast from array PHP to csv
	 * 
	 * @param	string	$data	data values
	 * @param 	string	$separated	separated value
	 * @param 	string	$separated_decimal	separated decimal value
	 * 
	 * @return	string
	 */
    public function castArrayToCSV($data, $separated = "\t", $separated_decimal = ".")
    {
        $result = "";

        foreach ($data as $item) {
            $line = "";

            foreach ($item as $key => $value) {
                $value = str_replace(".", $separated_decimal, $value);

                if ($line == "") {
                    $line = $value;
                } else {
                    $line .= $separated . $value;
                }
            }

            $result .= $line . "\n";
        }

        return $result;
    }

    
    
    /*
     * is metadata in file script
     *
     * @param	string	$filename	file name with real path.
     *
     * @return	boolean
     */
    public static function isMetadataFileScript($filename)
    {
        
        $result =  false;        
        
        if(is_readable($filename))
        {
            
            $handle_r = fopen($filename, "rb") or die("Unable to open file!");
            
            $metadata = false;
            
            while (!feof($handle_r))
            {
                $data = fread($handle_r, 512);
                
                if(strpos($data, "<meta-data") === false)
                {
                    
                }
                else
                {
                    $metadata = true;
                }      

                break;
            }

            fclose($handle_r);
            
            $result = $metadata;
        }
        
        return $result;
    }
    
    
    
    
    /*
     * checksum file script
     *
     * @param	string	$filename	file name with real path.
     * @param   string  $keyname    key name
     *
     * @return	string
     */
    public static function getMetadataValueScript($filename, $keyname)
    {
        
        $keyvalue = "";
        
        
        if(is_readable($filename))
        {
            
            $handle_r = fopen($filename, "rb") or die("Unable to open file!");

            $eof_metadata = false;
            $end_tag = false;
            $open_tag = false;
            $tagvalue = "";
            
            while (!feof($handle_r))
            {
                $data = fread($handle_r, 8024);
                
                $data_list = explode("\n", $data);
                
                $line_seek = 0;
                
                foreach($data_list as $item)
                {
                    
                    if(strpos($item, $keyname) === false && $open_tag == false)
                    {
                        
                    }
                    else
                    {
                        if($open_tag == false)
                        {
                            $tagvalue = substr($item, strpos($item, $keyname)+strlen($keyname));
                            $aux = "moamanager:value=\"";
                            $tagvalue = substr($tagvalue, strpos($tagvalue, $aux)+strlen($aux));
                            $open_tag = true;
                        }
                        else
                        {
                            $tagvalue .= $item;
                        }
                       
                        
                    }
                    
                }
                        
                        
                if(strpos($tagvalue, "\"/>"))
                {
                    $tagvalue = substr($tagvalue,0, strpos($tagvalue, "\"/>"));
                    $keyvalue = $tagvalue;
                    $eof_metadata = true;
                }
                        

                
                if($eof_metadata == true)
                {
                    break;
                }
                
            }                
                
            fclose($handle_r);
            
        }
        
        return $keyvalue;
    }
    
    
    
    /*
     * calc checksum in file script
     *
     * @param	string	$filename	file name with real path.
     * @param   string  $folder_tmp  folder tmp
     * @param   string  $script script data
     *
     * @return	string
     */
    public static function checksumFileScriptMOA($filename, $folder_tmp, $script)
    {
        
        $hash_file = "";
                
        
        if(is_readable($filename))
        {
            $ok = true;
            
            while($ok == true)
            {
                $filename_aux = $folder_tmp . basename($filename);
                $filename_aux_ext = substr($filename_aux, strrpos($filename_aux, "."));
                $filename_aux = substr($filename_aux, 0, strrpos($filename_aux, "."));
                $filename_aux .= "_TMP" . time() . $filename_aux_ext;
                
                if(!is_readable($filename_aux))
                {
                    $ok = false;
                }
            }
            
            if(self::setContentFile($filename_aux, ""))
            {
                
                $detect = "learning evaluation instances";
                $size = self::getContentFileSizeDetectPart($filename, $detect);
                
                if($size > 0)
                {
                    $data1 = self::getContentFilePart($filename, $size);
                    
                    $data = explode(PHP_EOL, $data1["data"]);
                    
                    $result = "";
                    
                    foreach($data as $item)
                    {
                        if(substr($item, 0, strlen("<meta-data")) != "<meta-data")
                        {
                            break;
                        }
                        else
                        {
                            $result .= $item . "\n";
                        }
                    }
                    
                    $line_seek = strlen($result)+1;//$data1["data"]);
                    
                    $handle_r = fopen($filename, "rb") or die("Unable to open file!");
                    $handle_w = fopen($filename_aux, "w");// or die("Unable to open file!");
                    
                    fseek($handle_r, 0, SEEK_SET);                    
                    fseek($handle_r, $line_seek);
                    
                    while (! feof($handle_r))
                    {
                        $data = fread($handle_r, 1024);
                        fwrite($handle_w, $data);
                    }
                    
                    fclose($handle_r);
                    fclose($handle_w);
                    
                    if(is_readable($filename_aux))
                    {
                        $hash_file = hash_hmac_file('md5', $filename_aux, $script);
                        unlink($filename_aux);
                    }
                    
                }

                /*$handle_r = fopen($filename, "rb") or die("Unable to open file!");
                $handle_w = fopen($filename_aux, "w");// or die("Unable to open file!");
                
                $eof_metadata = false;
                
                while (! feof($handle_r))
                {
                    $data = fread($handle_r, 8024);
                    
                    $data_list = explode("\n", $data);

                    $line_seek = 0;
                    
                    foreach($data_list as $item)
                    {   
                        $line_seek += strlen($item)+1;
                        
                        if(strpos($item, "security-hash-hmac-file") === false)
                        {
                            
                        }
                        else
                        {
                            $eof_metadata = true;
                            break;
                        }
                    }
                          
                    if($eof_metadata == true)
                    {
                        $line_seek++;
                        break;
                    }
                   
                }
                
                // = rewind
                fseek($handle_r, 0, SEEK_SET);
                
                fseek($handle_r, $line_seek);
                
                while (! feof($handle_r))
                {
                    $data = fread($handle_r, 1024);
                    fwrite($handle_w, $data);
                }
                
                fclose($handle_r);
                fclose($handle_w); 
                
                if(is_readable($filename_aux))
                {
                    $hash_file = hash_hmac_file('md5', $filename_aux, $script);
                    unlink($filename_aux);
                }*/
                                
            }             
                
        }
            
        return $hash_file;
    }
    
    
	/*
	 * save data in file
	 * 
	 * @param	string	$filename	file name with real path.
	 * @param 	string	$data	data value
	 * 
	 * @return	void
	 */
    public static function setContentFile($filename, $data)
    {
        $result = FALSE;
        try {

            if(file_exists($filename))
            {
                if(is_writable($filename))
                {
                    $handle = fopen($filename, "w");// or die("Unable to open file!");
                    fwrite($handle, $data);        
                    fclose($handle);                    
                    $result = TRUE;
                }
                else 
                {                    
                    //exit("file not permission");
                }
            }
            else 
            {
                $handle = fopen($filename, "w");// or die("Unable to open file!");                
                fwrite($handle, $data);                
                fclose($handle);                
                $result = TRUE;
            }
            
        } catch (Exception $e) {
            // chmod($base_directory_destine, 0777);
            exit("file not permission");
        }

        return $result;
    }

	/*
	 * get data in file
	 * 
	 * @param	string	$filename	file name with real path.
	 * 
	 * @return	string
	 */
    public static function getContentFile($filename)
    {
        $result = false;
        
        if(is_readable($filename))
        {
            $handle = fopen($filename, "rb");// or die("Unable to open file: " . $filename);
            $result = "";
    
            while (!feof($handle))
            {
                $result .= fread($handle, 1024);
            }
            
            fclose($handle);
        }

        return $result;
    }

	/*
	 * get number of scripts within a file.
	 * 
	 * @param	string	$filename	file name with real path.
	 * 
	 * @return	integer
	 */
    public static function getScriptsNumber($filename)
    {
        
        if(is_readable($filename))
        {
            $handle = fopen($filename, "rb") or die("Unable to open file!");
            $result = 0;
            
            while (! feof($handle)) {
                // $str =fread($handle, 9024);
                $str = fgets($handle, 4096);
                
                if (!empty(trim($str))) {
                    // echo "-----";
                    // var_dump($str);
                    $result ++;
                }
            }
            
            fclose($handle);
        }

        return $result;
    }
    
    
    
    
    /*
     * get str size partial data in file
     *
     * @param	string	$filename	file name with real path.
     * @param	string	$maxbytes	size bytes
     *
     * @return	string
     */
    public static function getContentFileSizeDetectPart($filename, $detect="")
    {
        $result = "";
        $findDetect = FALSE;
        
        if(is_readable($filename))
        {
            $handle = fopen($filename, "rb") or die("Unable to open file!");
            $filesize = filesize($filename);
            
            while (!feof($handle))
            {               
                $data = fread($handle, 512);
                $result .= $data;
                
                if(strpos($result, $detect) !== FALSE)
                {
                    $result = substr($result, 0, strpos($result, $detect));
                    $findDetect = TRUE;
                    break;
                }
            }
            
            fclose($handle);
        }        
        
        if($findDetect)
        {
            return (int) mb_strlen($result, '8bit');
        }
        else 
        {
            return -1;
        }
        
    }
    
    

	/*
	 * get partial data in file
	 * 
	 * @param	string	$filename	file name with real path.
	 * @param	string	$maxbytes	size bytes
	 * 
	 * @return	string
	 */
    public static function getContentFilePart($filename, $maxbytes)
    {
        $result = "";
        
        if(is_readable($filename))
        {
            $handle = fopen($filename, "rb") or die("Unable to open file!");
            $filesize = filesize($filename);            
            
            while (!feof($handle)) 
            {
                $data = fread($handle, 1024);
                $result .= $data; 
                
                if (mb_strlen($result, '8bit') > $maxbytes) 
                { 
                    $result = substr($result, 0, $maxbytes);
                    break;
                }
                
            }
            
            fclose($handle);
        }
        

        return array(
            "data" => $result,
            "size" => mb_strlen($result, '8bit')
        );
    }

	/*
	 * get bytes size of directory.
	 * 
	 * @param	string	$file_directory	real path.
	 * 
	 * @return	string
	 */
    public function getDirSize($file_directory)
    {

        // $file_directory = dirname(__FILE__);
        $output = exec('du -sk ' . "\"" . $file_directory . "\"");
        $size = trim(str_replace($file_directory, '', $output)) * 1024;

        $units = array(
            'B',
            'KB',
            'MB',
            'GB',
            'TB',
            'PB',
            'EB',
            'ZB',
            'YB'
        );
        $power = $size > 0 ? floor(log($size, 1024)) : 0;
        return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];

        // return $filesize;
    }

	/*
	 * set bytes size notation.
	 * 
	 * @param	string	$path	file name with real path.
	 * 
	 * @return	string
	 */
    public function filesize_formatted($path)
    {
        $size = filesize($path);
        $units = array(
            'B',
            'KB',
            'MB',
            'GB',
            'TB',
            'PB',
            'EB',
            'ZB',
            'YB'
        );
        $power = $size > 0 ? floor(log($size, 1024)) : 0;
        return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
    }

	/*
	 * get list of files and directories
	 * 
	 * @param	string	$base_directory_destine	real path.
	 * @param 	string	$filter	array with values
	 * 
	 * @return	mixed
	 */
    public function getListElementsDirectory1($base_directory_destine, $filter = array("txt"))
    {
        $files_list = array();

        if ($handle = opendir($base_directory_destine)) {

            $elements_dir_list = array();
            $elements_file_list = array();
            
            /* Esta é a forma correta de varrer o diretório */
            while (false !== ($file = readdir($handle))) {

                if (is_dir($base_directory_destine . $file))
                    $type = "dir";
                else
                    $type = "file";

                if ($file != "." && $file != "..")
                    if ($type == "dir") {
                        
                        $elements_dir_list[] = $file;
//                         array_push($files_list, array(
//                             "name" => $file,
//                             "size" => $this->getDirSize($base_directory_destine . $file),
//                             "type" => $type,
//                             "datetime" => date("Y/m/d H:i:s", filemtime($base_directory_destine . $file))
//                         ));
                    } else {

                        if (in_array(substr($file, strrpos($file, ".") + 1), $filter)) 
                        {
                            $elements_file_list[] = $file;
//                             array_push($files_list, array(
//                                 "name" => $file,
//                                 "size" => $this->filesize_formatted($base_directory_destine . $file),
//                                 "type" => $type,
//                                 "datetime" => date("Y/m/d H:i:s", filemtime($base_directory_destine . $file))
//                             ));
                        }
                    }
            }

            closedir($handle);
            
            
            sort($elements_dir_list);
            
            foreach($elements_dir_list as $item) {
                
                $type = "dir"; 
                    
                if ($item != "." && $item != "..")
                {   if ($type == "dir") {
                        
                        array_push($files_list, array(
                            "name" => $item,
                            "size" => $this->getDirSize($base_directory_destine . $item),
                            "type" => $type,
                            "datetime" => date("Y/m/d H:i:s", filemtime($base_directory_destine . $item))
                        ));
                    }                         
                }
            
            }
            
            sort($elements_file_list);
            
            foreach($elements_file_list as $item) {
                
                $type = "file";
                
                if ($item != "." && $item != "..")
                {                           
                    if (in_array(substr($item, strrpos($item, ".") + 1), $filter)) {
                        array_push($files_list, array(
                            "name" => $item,
                            "size" => $this->filesize_formatted($base_directory_destine . $item),
                            "type" => $type,
                            "datetime" => date("Y/m/d H:i:s", filemtime($base_directory_destine . $item))
                        ));
                    }
                
                }                
            }

        }       

        return $files_list;
    }

	/*
	 * get list of files and directories
	 * 
	 * @param	string	$base_directory_destine	real path.
	 * @param 	string	$filter	array with values
	 * 
	 * @return	mixed
	 */
    public function getListElementsDirectory($base_directory_destine, $filter = array("data"))
    {
        $files_list = array();

        if ($handle = opendir($base_directory_destine)) {

            /* Esta é a forma correta de varrer o diretório */
            while (false !== ($file = readdir($handle))) {

                if (is_dir($base_directory_destine . $file))
                    $type = "dir";
                else
                    $type = "file";

                if ($file != "." && $file != "..")
                    if ($type == "dir") {

                        array_push($files_list, $file);
                    } else {

                        if (in_array(substr($file, strrpos($file, ".") + 1), $filter)) {

                            array_push($files_list, $file);
                        }
                    }
            }

            closedir($handle);
        }

        sort($files_list);

        return $files_list;
    }

	/*
	 * get list of directories
	 * 
	 * @param	string	$base_directory_destine	real path.
	 * 
	 * @return	mixed
	 */
    public function getListDirectory($base_directory_destine)
    {
        $files_list = array();

        if ($handle = opendir($base_directory_destine)) {

            /* Esta é a forma correta de varrer o diretório */
            while (false !== ($file = readdir($handle))) {

                if (is_dir($base_directory_destine . $file))
                    $type = "dir";
                else
                    $type = "file";

                if ($file != "." && $file != "..")
                    if ($type == "dir") {

                        array_push($files_list, $file);
                    }
            }

            closedir($handle);
        }

        sort($files_list);

        return $files_list;
    }
    
    /*
	 * get list of files
	 * 
	 * @param	string	$base_directory_destine	real path.
	 * @param 	string	$filter	array with values
	 * 
	 * @return	mixed
	 */
    public function getListFilesFromDirectory($base_directory_destine, $filter = array("data"))
    {
        $files_list = array();

        if ($handle = opendir($base_directory_destine)) {

            /* Esta é a forma correta de varrer o diretório */
            while (false !== ($file = readdir($handle))) {

                if (is_dir($base_directory_destine . $file))
                    $type = "dir";
                else
                    $type = "file";

                if ($file != "." && $file != "..")
                    if ($type == "dir") {

                        //array_push($files_list, $file);
                    } else {

                        if (in_array(substr($file, strrpos($file, ".") + 1), $filter)) {

                            array_push($files_list, $file);
                        }
                    }
            }

            closedir($handle);
        }

        sort($files_list);

        return $files_list;
    }

	/*
	 * get process status
	 * 
	 * @param	integer	$pid	id of process
	 * 
	 * @return	string
	 */
    public function proc_get_status($pid)
    {
        $command = "ps -p " . $pid;

        $status = $this->runExternal($command);
        $result = explode("\n", $status["output"]);

        if (count($result) > 2) {
            // $r = explode(" ", $result[1]);

            $result = "running"; // . $r[10];
        } else {
            $result = "closed";
        }

        return $result;
    }

	/*
	 * execute external process
	 * 
	 * @param	string	$cmd	line command
	 * 
	 * @return	mixed
	 */
    function runExternal($cmd)
    {
        $descriptorspec = array(
            0 => array(
                "pipe",
                "r"
            ), // stdin is a pipe that the child will read from
            1 => array(
                "pipe",
                "w"
            ), // stdout is a pipe that the child will write to
            2 => array(
                "pipe",
                "w"
            ) // stderr is a file to write to
        );

        $pipes = array();
        $process = proc_open($cmd, $descriptorspec, $pipes);

        $output = "";

        if (! is_resource($process))
            return false;

        // close child's input imidiately
        fclose($pipes[0]);

        stream_set_blocking($pipes[1], false);
        stream_set_blocking($pipes[2], false);

        $todo = array(
            $pipes[1],
            $pipes[2]
        );

        while (true) {
            $read = array();

            if (! feof($pipes[1]))
                $read[] = $pipes[1];
            if (! feof($pipes[2]))
                $read[] = $pipes[2];

            if (! $read)
                break;

            $ready = @stream_select($read, $write = NULL, $ex = NULL, 2);

            if ($ready === false) {
                break; // should never happen - something died
            }

            foreach ($read as $r) {
                $s = fread($r, 1024);
                $output .= $s;
            }
        }

        fclose($pipes[1]);
        fclose($pipes[2]);

        $code = proc_close($process);

        return array(
            "output" => $output,
            "code" => $code
        );
    }


    /**
     * Checks if a folder exist and return canonicalized absolute pathname (long version)
     *
     * @param string $folder
     *            the path being checked.
     * @return mixed returns the canonicalized absolute pathname on success otherwise FALSE is returned
     */
    function folder_exist($folder)
    {
        // Get canonicalized absolute pathname
        $path = realpath($folder);

        // If it exist, check if it's a directory
        if ($path !== false and is_dir($path)) {
            // Return canonicalized absolute pathname
            return true; // $path;
        }

        // Path/folder does not exist
        return false;
    }
    
    
    
    
    
    
    
    
    
    
    /**
     * Changes permissions on files and directories within $dir and dives recursively
     * into found subdirectories.
     */
    function chmod_r($dir)
    {
        $dp = opendir($dir);
        while($file = readdir($dp))
        {
            if (($file == ".") || ($file == "..")) continue;
            
            $path = $dir . "/" . $file;
            $is_dir = is_dir($path);
            
            $this->set_perms($path, $is_dir);
            if($is_dir) $this->chmod_r($path);
        }
        closedir($dp);
    }
    
    function set_perms($file, $is_dir)
    {
        $perm = substr(sprintf("%o", fileperms($file)), -4);
        $dirPermissions = "0750";
        $filePermissions = "0644";
        
        if($is_dir && $perm != $dirPermissions)
        {
//             echo("Dir: " . $file . "\n");
            chmod($file, octdec($dirPermissions));
        }
        else if(!$is_dir && $perm != $filePermissions)
        {
//             echo("File: " . $file . "\n");
            chmod($file, octdec($filePermissions));
        }
        
//         flush();
    }
    
    
    
    function create_dir($dirname, $realpath, $chmod="0777"){
        
        $realpath = trim($realpath);
        
        if(substr($realpath, strlen($realpath)-1) != DIRECTORY_SEPARATOR){
            $realpath .= DIRECTORY_SEPARATOR;
        }
        
        $dirname = trim($dirname);
        
        if(substr($dirname, strlen($dirname)-1) == DIRECTORY_SEPARATOR){
            $dirname = substr($dirname, 0, strlen($dirname)-1);     
        }
        
        //define o local do diretorio base
        $new_path = $realpath . $dirname;
        
        if(!is_dir($new_path)){
                      
            //criar o diretorio
            if(mkdir($new_path, octdec($chmod), true)){
                
                //modifica as permissoes do diretorio
                chmod($new_path, octdec($chmod));
                
                //define o local do diretorio base
                $new_path .= DIRECTORY_SEPARATOR;
                
            }else{
                //define o local do diretorio base
                $new_path = -1;
            }
              
        }else{
            //define o local do diretorio base
            $new_path .= DIRECTORY_SEPARATOR;
        }
                
        return $new_path;
        
    }
    
    
    
    function getDirContents($dir, &$results = array())
    {
    
		$files = scandir($dir);
		
		foreach($files as $key => $value){
			$path = realpath($dir.DIRECTORY_SEPARATOR.$value);
			if(is_dir($path) == false) {
				$results[] = $path;
			}
			else if($value != "." && $value != "..") {
				getDirContents($path, $results);
				if(is_dir($path) == false) {
					$results[] = $path;
				}
			}
		}
		return $results;
		
	}
    
    
}

?>
