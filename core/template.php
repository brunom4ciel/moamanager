<?php
/**
 * @package     Joomla.Legacy
 * @subpackage  Application
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
namespace moam\core;

defined('_EXEC') or die();

/**
 * Base class for a MOAM application.
 *
 */
abstract class Template
{

    private static $body = "";

    private static $headers = array();

    private static $title = null;

    private static $disabledMenu = false;
    
    /**
     * sets the show menu.
     *
     * @param	boolean	$show enable or disabled menu
     *
     * @return	void
     */
    public static function setDisabledMenu($disabled = true)
    {
        try {
            self::$disabledMenu = $disabled;
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }
    /**
     * get the showmenu.
     *
     * @return	string
     */
    public static function getDisabledMenu()
    {
        try {
            
            return self::$disabledMenu;
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }
    
    
	/**
	 * sets the title.
	 * 
	 * @param	string	$title title
	 * 
	 * @return	void
	 */
    public static function setTitle($title = null)
    {
        try {
            self::$title = $title;
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }
	/**
	 * get the title.
	 *  
	 * @return	string
	 */
    public static function getTitle()
    {
        try {

            return self::$title;
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }
	/**
	 * sets the body.
	 * 
	 * @param	string	$body body
	 * 
	 * @return	void
	 */
    public static function setBody($body = "")
    {
        try {
            self::$body = $body;
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }
	/**
	 * get the body.
	 *  
	 * @return	string
	 */
    public static function getBody()
    {
        try {
            return self::$body;
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }
	/**
	 * insert new header in list
	 * 
	 * @param	mixed	$data header value
	 * 
	 * @return	void
	 */
    public static function addHeader($data = array())
    {
        try {
            self::$headers[] = $data;
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }
	/**
	 * clear the list of headers
	 * 
	 * @return	void
	 */
    public static function clearHeader()
    {
        try {
            self::$headers[] = array();
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }
	/**
	 * get one value in header list
	 * 
	 * @param	integer	$index position
	 * 
	 * @return	void
	 */
    public static function getHeader($index = 0)
    {
        try {
            return self::$headers[$index];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }
	/**
	 * get header list
	 * 
	 * @param	integer	$index position
	 * 
	 * @return	mixed	
	 */
    public static function getHeaders()
    {
        try {
            return self::$headers;
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }
	/**
	 * get header list in format html
	 * 
	 * @return	mixed
	 */
    public static function getHeadersHTML()
    {
        $result = "";

        try {
            foreach (self::getHeaders() as $item) {

                $tag = $item["tag"];
                unset($item["tag"]);

                $result .= "<" . $tag;

                foreach ($item as $key => $value) {
                    $result .= " " . $key . "=\"" . $value . "\"";
                }

                $result .= ">";

                if ($tag != "link")
                    $result .= "</" . $tag . ">";

                $result .= "\n";
            }
        } catch (\Exception $e) {
            exit($e->getMessage());
        }

        return $result;
    }
}

?>
