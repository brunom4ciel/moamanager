<?php
/**
 * @package    moam\libraries\core\sys
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\libraries\core\sys;


defined('_EXEC') or die();

class SoftwareUpdate
{

    var $url = "https://raw.githubusercontent.com/brunom4ciel/moamanager/master/index.php";
    var $version = "";
    
    /* gets the data from a URL */
    private function http_GET($url) 
    {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
        
    function getVersion()
    {       
        $result = "";
        
        if(empty($this->version))
        {
            $str = $this->http_GET($this->url);
            
            $tag = "define('MOAMANAGER_VERSION'";
            
            if(strrpos($str, $tag) === FALSE)
            {
                //$moamanager_remote_version = "Error while querying remote version. Try again later.";
            }
            else
            {
                $str = substr($str, strrpos($str, $tag)+strlen($tag));
                $str = substr($str, strpos($str, "'")+1);
                $moamanager_remote_version = substr($str, 0, strpos($str, "'"));
                
                $result = $moamanager_remote_version;
            }
            
            $this->version = $result;
        }
        else 
        {
            $result = $this->version;
        }
        
        return $result;
    }
    
    
    function isUpdate($versionLocal = "")
    {
                
        $result = FALSE;
        
        $str_remote = explode(".", $this->getVersion());
        
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
    
    
}

?>
