<?php
/**
 * Abstract class Application
 * 
 * @package    moam\core
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\core;

use moam\libraries\core\menu\Menu;

defined('_EXEC') or die();


/**
 * Abstract class AbstractApplication
 * 
 * @package moam\core
 */
abstract class AbstractApplication
{

    private static $menu = null;

    public static function menuClear()
    {
        self::$menu->clear();
    }

    public static function menuAdd($href = "", $label = "", $title = "", $target = "")
    {
        self::$menu->add($href, $label, $title, $target);
    }

    public static function showMenu($menu = null, $path_www = "")
    {
        self::$menu = $menu; // new Menu();

        if ($menu->count() > 0) {} else {

            $language = self::getParameter("language");

            if (! $language == null) {
                $language = "&language=" . $language;
            }

//             self::$menu->add($path_www . "?", MENU_HOME);

            
            self::$menu->add($path_www . "?component=files" . $language, MENU_FILE_MANAGER);
            self::$menu->add($path_www . "?component=scripts", MENU_SCRIPT_MANAGER);
            self::$menu->add($path_www . "?component=generator" . $language, MENU_SCRIPT_CREATOR);
            self::$menu->add($path_www . "?component=taskreport&controller=report" . $language, MENU_TASK_REPORT);
            self::$menu->add($path_www . "?component=extract" . $language, MENU_DATA_EXTRACTION);
            self::$menu->add($path_www . "?component=statistical&controller=texteditor" . $language, MENU_DATA_ANALYSIS);
           
            self::$menu->addTab();
            
            
            self::$menu->add($path_www . "?component=systemmonitor" . $language, MENU_SYSTEM_MONITOR);    
            
            
//             self::$menu->add($path_www . "?component=taskinitializer&controller=run" . $language, MENU_TASK_INITIALIZER);
            
            
            
            
            self::$menu->add($path_www . "?component=taskmanager" . $language, MENU_TASK_MANAGER);
            
            self::$menu->addTab();
            
            self::$menu->add($path_www . "?component=trash" . $language, MENU_TRASH);
            self::$menu->add($path_www . "?component=backup" . $language, MENU_BACKUP);
//             self::$menu->add($path_www . "?component=settings" . $language, MENU_SETTINGS);            
//             self::$menu->add($path_www . "?component=user&controller=login&logout" . $language, MENU_LOGOUT);
            
            
            


            
            
//             self::$menu->add($path_www . "?component=evaluation" . $language, MENU_EVALUATION);
            
            
            
            
            
            // self::$menu->add($path_www."?component=analyze", "Analyze");
        }

        return self::$menu->toHTML();
    }

	/**
	 * HTTP Request variables. This is a 'superglobal', or automatic 
	 * global, variable. This simply means that it is available in all 
	 * scopes throughout a script. An associative array that by default 
	 * contains the contents of $_GET, $_POST.
	 *
	 * @param string $keyname	key name array
	 *
	 * @return mixed
	 */
	public static function getParameter($keyname)
	{
		switch ($_SERVER['REQUEST_METHOD']) {
			case 'POST':

				if (strpos($_SERVER['CONTENT_TYPE'], "multipart/form-data") === false) {
					// possui upload
				} else {
					// nao possui docs

					if (isset($_POST[$keyname]))
						$_REQUEST[$keyname] = $_POST[$keyname];
				}

				break;
			case 'GET':

				break;
			case 'PUT':

				$post_vars = null;

				parse_str(file_get_contents("php://input"), $post_vars);
				$_REQUEST = $post_vars;

				break;
			case 'DELETE':

				parse_str(file_get_contents("php://input"), $post_vars);
				$_REQUEST = $post_vars;

				break;
		}

		if (isset($_REQUEST[$keyname]))
			return $_REQUEST[$keyname];
		else
			return null;
	}

	/**
	 * Set HTTP variable. 
	 * 
	 * @param string 	$keyname	key name array
	 * @param mixed		$value		value
	 * 
	 * @return void
	 */
	public static function setParameter($keyname, $value)
	{
		$_REQUEST[$keyname] = $value;
	}

	/**
	 * Get the value of the component name HTTP variable.
	 *  
	 * @return string
	 */
    public static function getComponent()
    {
        return self::getParameter('component');
    }

	/**
	 * Get the value of the controller name HTTP variable.
	 *  
	 * @return string
	 */
    public function getController()
    {
        return self::getParameter('controller');
    }
}

?>
