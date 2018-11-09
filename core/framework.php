<?php
/**
 * @package    moam\core
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\core;

defined('_EXEC') or die();

use moam\core\Application;
use moam\core\AbstractApplication;
use Exception;

/**
 * MOAM Framework class.
 *
 */
abstract class Framework extends AbstractApplication
{

    public static $application = null;

	/**
	 * get instance Application class
	 *  
	 * @return Application
	 */ 
    public static function getApplication()
    {
        try {
            if (! self::$application) {
                self::$application = Application::getInstance();
            }
        } catch (Exception $e) {

            throw new AppException($e->getMessage());
        }

        return self::$application;
    }

	/**
	 * get instance class name
	 *  
	 * @return mixed
	 */ 
    public static function getInstance($classname = null)
    {
        try {

            if (empty($classname)) {
                throw new AppException(get_class() . ' error: not defined class name ');
            } else {

                if (! class_exists($classname)) {
                    throw new AppException(get_class() . ' error: class name instance not exists ');
                }
            }
        } catch (Exception $e) {

            throw new AppException($e->getMessage());
        }

        return new $classname();
    }

	/**
	 * import library
	 * 
	 * @param $library string name lib
	 * @param $package string name package
	 *  
	 * @return void
	 */ 
    public static function import($library = "", $package = "core")
    {
        $filename = PATH_LIBRARIES . DIRECTORY_SEPARATOR . $package . DIRECTORY_SEPARATOR . $library . ".php";

        try {

            if (! is_file($filename))
                throw new AppException(get_class() . ' error: import library not found ' . $library);
        } catch (AppException $e) {

            throw new AppException($e->getMessage());
        }

        require_once ($filename);
    }

}

?>
