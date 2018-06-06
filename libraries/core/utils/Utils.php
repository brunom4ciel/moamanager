<?php
/**
 * @package    moam\libraries\core\utils
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\libraries\core\utils;

use Exception;

defined('_EXEC') or die();

class Utils
{

   
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

        $result = "<table class=\"excel\" style=\"float:left;width:auto;display:table-cell;\">" . $table1 . "
				    </table>
				    <table class=\"excel\" style=\"float:left;width:auto;display:table-cell;\">
				        <tbody>" . $table2 . "
				        </tbody>
				    </table>";

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

        $result = "<select name='" . $name . "' id='" . $id . "'" . $style . "" . $style . "" . $event . "><option value=\"\"></option>";

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
    public function finsert($handle, $string, $bufferSize = 16384)
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
	 * save data in file
	 * 
	 * @param	string	$filename	file name with real path.
	 * @param 	string	$data	data value
	 * 
	 * @return	void
	 */
    public static function setContentFile($filename, $data)
    {
        try {

            $handle = fopen($filename, "w") or die("Unable to open file!");

            fwrite($handle, $data);

            fclose($handle);
        } catch (Exception $e) {
            // chmod($base_directory_destine, 0777);
            exit("file nto permission");
        }

        // exit($filename);
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
        $handle = fopen($filename, "rb") or die("Unable to open file!");
        $result = "";

        while (! feof($handle))
            $result .= fread($handle, 1024);

        fclose($handle);

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

        return $result;
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
        $handle = fopen($filename, "rb") or die("Unable to open file!");
        $result = "";

        $filesize = filesize($filename);

        $i = 1;
        while (! feof($handle)) {

            if (($i * 1024) >= $maxbytes) {
                $i --;
                break;
            }

            $i ++;

            $result .= fread($handle, 1024);
        }

        fclose($handle);

        return array(
            "data" => $result,
            "size" => ($i * 1024)
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

            /* Esta é a forma correta de varrer o diretório */
            while (false !== ($file = readdir($handle))) {

                if (is_dir($base_directory_destine . $file))
                    $type = "dir";
                else
                    $type = "file";

                if ($file != "." && $file != "..")
                    if ($type == "dir") {

                        array_push($files_list, array(
                            "name" => $file,
                            "size" => $this->getDirSize($base_directory_destine . $file),
                            "type" => $type,
                            "datetime" => date("Y/m/d H:i:s", filemtime($base_directory_destine . $file))
                        ));
                    } else {

                        if (in_array(substr($file, strrpos($file, ".") + 1), $filter)) {
                            array_push($files_list, array(
                                "name" => $file,
                                "size" => $this->filesize_formatted($base_directory_destine . $file),
                                "type" => $type,
                                "datetime" => date("Y/m/d H:i:s", filemtime($base_directory_destine . $file))
                            ));
                        }
                    }
            }

            closedir($handle);
        }

        sort($files_list);

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
            if($is_dir) chmod_r($path);
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
        
        flush();
    }
    
    
    
    
    
    
}

?>
