<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\libraries\core\json;

use Exception;
defined('_EXEC') or die();

class JsonFile
{

    private $json = "";

    private $data = array();

    private $filename = "";

    public function __construct($filename = "")
    {
        $this->filename = $filename;
    }

    public function jsonToArray()
    {
        return json_decode($this->data);
    }

    public function getContents()
    {
        return $this->data;
    }

    public function open($filename = "")
    {
        if (! empty($filename))
            $this->filename = $filename;

        $this->load();
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function SerializingData($data)
    {
        return json_encode($data);
    }

    public function DeserializingData($json)
    {
        $db = array();

        if (! empty($json)) {

            $this->json = json_decode($json);
            
            if (is_array($this->json) || is_object($this->json) ) {
//                 var_dump($this->json);exit();
                foreach ($this->json as $key => $item) {

                    if (is_object($item)) {

                        $item = (array) $item;
                        $db[] = $item;
                    } else {

                        $db[] = $item;
                    }
                }
            }
        }
        
        return $db;
    }

    public function load()
    {
        $handle = fopen($this->filename, "rb") or die("Unable to open file!");
        $data = "";

        while (! feof($handle))
            $data .= fread($handle, 1024);

        fclose($handle);

        if (! empty($data))
            $this->data = $this->DeserializingData($data);
        
    }

    public function save()
    {
        try {

            $json_data = $this->SerializingData($this->data);
            
            $filename = $this->filename;
            
            if(file_exists($filename))
            {
                if(is_writable($filename))
                {
                    $handle = fopen($filename, "w") or die("Unable to open file " . $filename);
                    
                    fwrite($handle, $json_data);
                    
                    fclose($handle);
                    
                    //                 var_dump($json_data);exit("---------------");
                }
                else
                {
                    exit("Unable to write on file.");
                }
                
            }else 
            {
                $handle = fopen($filename, "w") or die("Unable to open file " . $filename);
                
                fwrite($handle, $json_data);
                
                fclose($handle);
            }
            
            
        } catch (Exception $e) {
            exit("Error: " . $e->getMessage());
        }
    }
    
    
    
    public function issetKeyValue($keyname)
    {
        $result = false;
        
        if (! empty($this->data)) 
        {
            foreach ($this->data as $key => $item) 
            {                
                if (is_array($item)) 
                {                    
                    foreach ($item as $key2 => $item2) 
                    {      
                        
                        if ($key2 == $keyname) 
                        {                            
                            $result = true;
                            break 2;
                        }
                    }
                }
            }
        }
        
        return $result;
    }
    
    
    public function findDataKeyValue($keyname, $findValue)
    {
        $result = false;
        
        if (! empty($this->data)) {
            // var_dump($this->data);
            foreach ($this->data as $key => $item) {
                
                if (is_array($item)) {
                    
                    foreach ($item as $key2 => $item2) {
                        
                        if ($key2 == $keyname) {
                            
                            if (strpos($item2, $findValue) === FALSE ) 
                            {
                                
                            }
                            else 
                            {
                                $result = $item;
                                break 2;
                            }
                        }
                    }
                }
            }
        }
        
        return $result;
    }
    

    public function getDataKeyValue($keyname, $findValue)
    {
        $result = false;

        if (! empty($this->data)) {
            // var_dump($this->data);
            foreach ($this->data as $key => $item) {

                if (is_array($item)) {

                    foreach ($item as $key2 => $item2) {

                        if ($key2 == $keyname) {

                            if ($findValue == $item2) {
                                $result = $item;
                                break 2;
                            }
                        }
                    }
                }
            }
        }

        return $result;
    }

    public function setDataKeyValue($keyname, $findValue, $value)
    {
        $result = false;

        if (empty($this->data)) {} else {

            $db = array();

            foreach ($this->data as $key => $item) {

                if (is_array($item)) {

                    $db_data = array();

                    foreach ($item as $key2 => $item2) {

//                         var_dump($keyname);var_dump($findValue);var_dump($value);
//                         exit();
                        
                        if ($key2 == $keyname) {

                            if ($findValue == $item2) {
                                // echo $keyname."=".$item3."\n";
                                // $db_data[$key2] = $value;
                                $db_data = $value;

                                // var_dump($item);
//                                 exit("ppp");
                                break;
                            } else {
                                // $db_data[$key2] = $item2;
                            }
                        } else {
                            // echo $key3."=".$item3."\n\n";
                            // $db_data[$key2] = $item2;
                        }
                    }

                    if (count($db_data) == 0)
                        $db_data = $item;

                    $db[] = $db_data;
                    $this->setData($db);
                }
            }
        }

        return $result;
    }

    public function removeDataKeyValue($keyname, $findValue)
    {
        $result = false;

        if (empty($this->data)) {} else {

            $db = array();

            foreach ($this->data as $key => $item) {

                if (is_array($item)) {

                    $db_data = array();

                    foreach ($item as $key2 => $item2) {

                        if ($key2 == $keyname) {

                            if ($findValue == $item2) {

                                unset($this->data[$key]);
                                $result = true;
                                
                                break 2;
                            } else {
                                // $db_data[$key2] = $item2;
                            }
                        } else {
                            // echo $key3."=".$item3."\n\n";
                            // $db_data[$key2] = $item2;
                        }
                    }
                }
            }
        }

        return $result;
    }

    public function setValue($keyname, $findValue, $value)
    {
        $result = false;

        if (empty($this->data)) {} else {

            $db = array();

            foreach ($this->data as $key => $item) {

                if (is_array($item)) {

                    $db_data = array();

                    foreach ($item as $key2 => $item2) {

                        if ($key2 == $keyname) {

                            if ($findValue == $item2) {
                                // echo $keyname."=".$item3."\n";
                                $db_data[$key2] = $value;
                            } else {
                                $db_data[$key2] = $item2;
                            }
                        } else {
                            // echo $key3."=".$item3."\n\n";
                            $db_data[$key2] = $item2;
                        }
                    }

                    $db[] = $db_data;
                    $this->setData($db);
                }
            }
        }

        return $result;
    }
}