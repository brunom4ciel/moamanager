<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\core;

defined('_EXEC') or die();

/**
 * Base class for a MOAM! application.
 */
abstract class Properties
{

    private static $acl_autoremove_account = true;

    public static $base_directory_destine = "/var/www/moamanagerdata/storage/";

    public static $base_directory_destine_exec = "/var/www/moamanagerdata/exec/";

    public static $base_directory_moa = "/opt/moamanager/moa/";

    public static $base_directory_moa_jar_default = "moa2014.jar";

    public static $app_title = "MOAManager";

    public static $passphrase = "298350yqb";

    public static $base_directory = "/moamanager/";

    public static $file_contents_max_size = 200; // kb
    
    public static $file_java_exec = "java";

    public static $plivo_AUTH_ID = "MANYBGKDHDFHBHGFJLKK5ZG"; // sms service

    public static $plivo_AUTH_TOKEN = "ZjFU5MGYIUHFHUTSS97UF6sfwG2JkkjlilMTQQW1"; // sms service

    private static $database_name = "moamanager";

    private static $database_host = "localhost";

    private static $database_user = "root";

    private static $database_pass = "123";

    private static $user_type_default = 2;
    
    private static $max_number_on_list_of_process="12";

    private static $output_directorys = array(
        "var/www/moamanagerdata/storage/"
    );   
    
    /**
     * Get the value of the maximum number on list of process.
     *
     * @return integer
     */
    public static function getMax_number_on_list_of_process() {
        return self::$max_number_on_list_of_process;
    }
    
    
    
    public static function setOutput_directorys($output) {
        self::$output_directorys = $output;
    }
    public static function getOutput_directorys() {
        return self::$output_directorys;
    }
    
    
	/**
	 * Set default type of user.
	 * 
	 * @param string 	$type	type user
	 * 
	 * @return void
	 */
    public static function setUser_type_default($type)
    {
        self::$user_type_default = $type;
    }

	/**
	 * Get the value of the type of user.
	 *  
	 * @return integer
	 */
    public static function getser_type_default()
    {
        return self::$user_type_default;
    }

	
	/**
	 * defines whether the user can delete his own account.
	 * 
	 * @param boolean	$acl_autoremove_account	true | false value
	 * 
	 * @return void
	 */
    public static function setAcl_autoremove_account($acl_autoremove_account)
    {
        self::$acl_autoremove_account = $acl_autoremove_account;
    }

	/**
	 * get parameter that defines whether user can delete his own account.
	 * 
	 * @return	boolean
	 */
    public static function getAcl_autoremove_account()
    {
        return self::$acl_autoremove_account;
    }

	/**
	 * sets list of directories to manage user accounts.
	 * 
	 * @param mixed	$base_directory_destine	directory list
	 * 
	 * @return void
	 */
    public static function setBase_directory_destine($base_directory_destine)
    {
        self::$base_directory_destine = $base_directory_destine;
    }

	/**
	 * Get list of directories to manage user accounts. When passed as 
	 * a parameter the application instance is retrieved directly from 
	 * the directory in which the user belongs.
	 * 
	 * @param	string	$application instance of Application
	 * 
	 * @return	mixed	directory list
	 */
    public static function getBase_directory_destine($application = null)
    {
        $dirp_ = "";

        try {

            if ($application->is_authentication()) {
                $dirp_ = $application->getUserWorkspace();
            } else {
                $dirp_ = self::$base_directory_destine;
            }
            
        } catch (AppException $e) {

            throw new AppException($e->getMessage());
        }

        return $dirp_;
    }
    
	/**
	 * sets the default directory for temporary files.
	 * 
	 * @param	string	$base_directory_destine_exec directory
	 * 
	 * @return	void
	 */
    public static function setBase_directory_destine_exec($base_directory_destine_exec)
    {
        self::$base_directory_destine_exec = $base_directory_destine_exec;
    }

	/**
	 * get the default directory for temporary files.
	 * 
	 * @param	string	$base_directory_destine_exec directory
	 * 
	 * @return	void
	 */
    public static function getBase_directory_destine_exec()
    {
        return self::$base_directory_destine_exec;
    }

	/**
	 * sets the directory for moa framework
	 * 
	 * @param	string	$base_directory_moa directory
	 * 
	 * @return	void
	 */
    public static function setBase_directory_moa($base_directory_moa)
    {
        self::$base_directory_moa = $base_directory_moa;
    }
    
	/**
	 * get the directory for moa framework
	 * 
	 * @return	string
	 */
    public static function getBase_directory_moa()
    {
        return self::$base_directory_moa;
    }

	/**
	 * defines the name of the moa executable file, to be used as 
	 * the default in the application.
	 * 
	 * @param	string	$base_directory_moa_jar_default file name
	 * 
	 * @return	void
	 */
    public static function setBase_directory_moa_jar_default($base_directory_moa_jar_default)
    {
        self::$base_directory_moa_jar_default = $base_directory_moa_jar_default;
    }

	/**
	 * get the name of the moa executable file, to be used as 
	 * the default in the application.
	 * 
	 * @param	string	$base_directory_moa_jar_default file name
	 * 
	 * @return	void
	 */
    public static function getBase_directory_moa_jar_default()
    {
        return self::$base_directory_moa_jar_default;
    }

	/**
	 * sets the application's default title.
	 * 
	 * @param	string	$app_title title
	 * 
	 * @return	void
	 */
    public static function setApp_title($app_title)
    {
        self::$app_title = $app_title;
    }
	/**
	 * get the application default title.
	 * 
	 * @param	string	$app_title title
	 * 
	 * @return	void
	 */
    public static function getApp_title()
    {
        return self::$app_title;
    }

    
    public static function setPassphrase($passphrase) {
        self::$passphrase = $passphrase;
    }
    public static function getPassphrase() {
        return self::$passphrase;
    }
    public static function setBase_directory($base_directory) {
        self::$base_directory = $base_directory;
    }
    public static function getBase_directory() {
        return self::$base_directory;
    }
    public static function getFileContentsMaxSize() {
        return self::$file_contents_max_size;
    }
    public static function setFileContentsMaxSize($file_contents_max_size) {
        self::$file_contents_max_size = $file_contents_max_size;
    }
    public static function getFileJavaExec() {
        return self::$file_java_exec;
    }
    public static function setFileJavaExec($file_java_exec) {
        self::$file_java_exec = $file_java_exec;
    }
    public static function getPlivoAuthId() {
        return self::$plivo_AUTH_ID;
    }
    public static function setPlivoAuthId($plivo_AUTH_ID) {
        self::$plivo_AUTH_ID = $plivo_AUTH_ID;
    }
    public static function getPlivoAuthToken() {
        return self::$plivo_AUTH_TOKEN;
    }
    public static function setPlivoAuthToken($plivo_AUTH_TOKEN) {
        self::$plivo_AUTH_TOKEN = $plivo_AUTH_TOKEN;
    }
    
    public static function getDatabaseName() {
        return self::$database_name;
    }
    public static function setDatabaseName($database_name) {
        self::$database_name = $database_name;
    }
    public static function getDatabaseHost() {
        return self::$database_host;
    }
    public static function setDatabaseHost($database_host) {
        self::$database_host = $database_host;
    }
    public static function getDatabaseUser() {
        return self::$database_user;
    }
    public static function setDatabaseUser($database_user) {
        self::$database_user = $database_user;
    }
    public static function getDatabasePass() {
        return self::$database_pass;
    }
    public static function setDatabasePass($database_pass) {
        self::$database_pass = $database_pass;
    }
    
    
}

?>
