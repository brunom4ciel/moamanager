<?php
/**
 * Application class
 * 
 * @package    moam\core
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * 
 */
namespace moam\core;

defined('_EXEC') or die();

/**
 * Base class for a MOAM application.
 * 
 */
class Application extends AbstractApplication
{

    private $outputview = "";

    private $session_id = null;

	/**
	 * new Application Class
	 *
	 * @return Application
	 * 
	 */
    public static function getInstance()
    {
        return new Application();
    }
	
	/**
	 * get templates path
	 * 
	 * @return string
	 *  
	 */
    public static function getPathTemplate()
    {
        return PATH_WWW . 'templates' . '/' . $_REQUEST['template'];
    }

	/**
	 * execute application
	 * 
	 * @return void
	 *  
	 */
    public function execute()
    { // $template = "default", $template_view = "default"){
        try {

            $template = "default";

            if (isset($_REQUEST['template'])) {
                $template = $_REQUEST['template'];
            } else {
                $_REQUEST['template'] = TEMPLATE_DEFAULT;
            }

            $template_view = "default";

            if (isset($_REQUEST['tmpl'])) {
                $template_view = $_REQUEST['tmpl'];
            }

            $this->session_constants();

            $this->language();

            $this->component();

            $this->template($template, $template_view);
        } catch (AppException $e) {

            // preciso resolver as mensagens de "erro"
            exit($e->getMessage());
        }
    }

	/**
	 * Import the language variables
	 *  
	 */
    private function language()
    {
        try {

            $language = $this->getParameter("language");

            if (empty($language)) {
                $language = LANGUAGE_DEFAULT;
            }

            $filename = PATH_LANGUAGE . DIRECTORY_SEPARATOR . $language . DIRECTORY_SEPARATOR . $language . '.ini';

            if (file_exists($filename)) {
                $this->loadAndDefineConstsFromINI($filename);
            } else {
                throw new AppException(get_class() . ' error: file not found - ' . $language);
            }

            $filename = PATH_LANGUAGE . DIRECTORY_SEPARATOR . $language . DIRECTORY_SEPARATOR . "com_" . $this->getComponent() . '.ini';

            if (file_exists($filename)) {
                $this->loadAndDefineConstsFromINI($filename);
            }
        } catch (AppException $e) {
            throw new AppException($e->getMessage());
        }
    }

	/**
	 * Interprets the language variables.
	 *  
	 */
    private function loadAndDefineConstsFromINI($filename = "")
    {
        try {

            if (file_exists($filename)) {

                $handle = fopen($filename, "rb") or die("Unable to open file language!");

                if ($handle) {
                    while (($buffer = fgets($handle, 1024)) !== false) {

                        if (substr($buffer, 0, 1) == ";" || trim(substr($buffer, 0, 1)) == "") {
                            // comment
                        } else {
                            $const_name = substr($buffer, 0, strpos($buffer, "="));
                            $const_name = strtoupper($const_name);

                            $const_value = substr($buffer, strpos($buffer, "=\"") + 2);
                            $const_value = substr($const_value, 0, strpos($const_value, "\""));

                            if (! defined($const_name)) {
                                define($const_name, $const_value);
                            }
                        }
                    }

                    if (! feof($handle)) {
                        throw new AppException(get_class() . ' error: ' . "unexpected fgets() fail.");
                    }

                    fclose($handle);
                }
            } else {

                throw new AppException(get_class() . ' error: file not found ');
            }
        } catch (AppException $e) {
            throw new AppException($e->getMessage());
        }
    }

	/**
	 * execute component
	 * 
	 * @return void
	 *  
	 */
    private function component()
    {
        try {

            $component = $this->getComponent();
            $controller = $this->getController();

            if (empty($component))
                $component = "index";

            if (empty($controller))
                $controller = "controller";

            $filename = PATH_COMPONENTS . DIRECTORY_SEPARATOR . $component . DIRECTORY_SEPARATOR . $controller . '.php';
            // exit($filename);
            if (file_exists($filename)) {

                ob_start();

                require_once ($filename);
                // exit("sim ".$filename);
                $this->outputview = ob_get_contents();

                // ob_end_flush();
                ob_end_clean();
                // require_once( $filename );
            } else {

                throw new AppException(get_class() . ' error: file not found component ' . $this->getComponent());
            }
        } catch (AppException $e) {
            throw new AppException($e->getMessage());
        }
    }

	/**
	 * Execute template
	 *
	 * @param string $template	define name
	 * @param string $template_view define name view
	 *
	 * @return void
	 */
    private function template($template = "default", $template_view = "default")
    {
        try {

            Template::setBody($this->outputview);

            $filename = PATH_TEMPLATES . DIRECTORY_SEPARATOR . $template . DIRECTORY_SEPARATOR . $template_view . '.php';
            // exit($filename);
            if (file_exists($filename)) {

                require_once ($filename);
            } else {

                throw new AppException(get_class() . ' error: file not found template ' . $this->getComponent());
            }
        } catch (AppException $e) {
            throw new AppException($e->getMessage());
        }
    }

	/**
	 * Verifies that there is an authenticated user.
	 * 
	 * @return boolean
	 * 
	 */
    public function is_authentication()
    {
        $result = false;

        try {
            $this->session_open();

            if (isset($_SESSION['user']['email'])) {
                $result = true;
            }

            $this->session_close();
        } catch (AppException $e) {
            throw new AppException($e->getMessage());
        }

        return $result;
    }

    
        
    
    /**
     * Set Software version
     *
     * @param string $version	software version
     *
     * @return void
     */
    public function setSoftwareRemoteVersion($version)
    {
        try {
            $this->session_open();
            
            $_SESSION['software']['version'] = $version;
            
            $this->session_close();
        } catch (AppException $e) {
            throw new AppException($e->getMessage());
        }
    }
    
    
    /**
     * Get Sotware Version
     *
     * @return string
     */
    public function getSoftwareRemoteVersion()
    {
        try {
            $this->session_open();
            
            if (self::is_authentication()) {
                return $_SESSION['software']['version'];
            } else {
                return "";
            }
            
            $this->session_close();
        } catch (AppException $e) {
            throw new AppException($e->getMessage());
        }
    }
    
    
	/**
	 * Read username
	 * 
	 * @return string
	 */
    public function getUser()
    {
        try {
            $this->session_open();

            if (self::is_authentication()) {
                return $_SESSION['user']['email'];
            } else {
                return "";
            }

            $this->session_close();
        } catch (AppException $e) {
            throw new AppException($e->getMessage());
        }
    }

	/**
	 * Read user type 
	 * 
	 * @return string
	 */
    public function getUserType()
    {
        try {
            $this->session_open();

            if (self::is_authentication()) {
                return $_SESSION['user']['type'];
            } else {
                return "";
            }

            $this->session_close();
        } catch (AppException $e) {
            throw new AppException($e->getMessage());
        }
    }
	
	/**
	 * Read user id
	 * 
	 * @return string
	 */
    public function getUserId()
    {
        try {
            $this->session_open();

            if (self::is_authentication())
                return $_SESSION['user']['id'];
            else
                return "";

            $this->session_close();
        } catch (AppException $e) {
            throw new AppException($e->getMessage());
        }
    }
	
	/**
	 * Log out if there is any one authenticated.
	 * 
	 * @param string http_referer URI to redirect
	 * 
	 * @return void
	 */
    public function logout($http_referer = null)
    {
        try {

            $this->session_open();

            $this->session_end();

            if (is_null($http_referer) || empty($http_referer)) {

                $this->alert("logout Successfully.");
            } else {

                $this->alert("logout Successfully.", $http_referer);
            }
        } catch (AppException $e) {
            throw new AppException($e->getMessage());
        }
    }

	/**
	 * authenticate user in application 
	 * 
	 * @param string $email	define name
	 * @param string $type define name view
	 * @param string $user_id define name view
	 * @param string $workspace defines workspace of user
	 * 
	 * @return string
	 */
    public function authentication($email, $type, $user_id, $workspace)
    {
        try {
            $this->session_open();

            $_SESSION['user']['email'] = $email;
            $_SESSION['user']['type'] = $type;
            $_SESSION['user']['id'] = $user_id;
            $_SESSION['user']['workspace'] = $workspace;

            $this->session_close();
        } catch (AppException $e) {
            throw new AppException($e->getMessage());
        }
    }

	/**
	 * Read user workspace
	 * 
	 * @return string
	 */
    public function getUserWorkspace()
    {
        try {
            $this->session_open();

            if ($this->is_authentication()) {
                // $workspace = $_SESSION['user']['workspace'];

                // if(substr($workspace, strlen($workspace)-1) == "/")
                // {
                // $workspace .= $_SESSION['user']['email']
                // . DIRECTORY_SEPARATOR;
                // }else
                // {
                // $workspace = DIRECTORY_SEPARATOR
                // . $_SESSION['user']['email']
                // . DIRECTORY_SEPARATOR;
                // }

                return $_SESSION['user']['workspace'];
            } else {
                return - 1;
            }

            $this->session_close();
        } catch (AppException $e) {
            throw new AppException($e->getMessage());
        }
    }

	/**
	 * An alert box is often used if you want to make sure information 
	 * comes through to the user.
	 * 
	 * @param string $msg message
	 * @param string $http_referer URL redirect
	 * 
	 * @return void
	 */
    public function alert($msg = "", $http_referer = null)
    {

        // $logout = $this->getParameter ( "logout" );
        // $component = $this->getParameter( "component" );//App::getParameter ( "component" );
        // $controller = $this->getParameter( "controller" );//App::getParameter ( "controller" );

        // if(is_null($logout)){
        if (is_null($http_referer) || empty($http_referer))
		{
			$vars = "&http_referer=";/// . urlencode(base64_encode($_SERVER["REQUEST_URI"]));
        }
        else
        {
			$vars = "&http_referer=" . urlencode($http_referer);
		}

        if ($this->is_authentication()) 
        {
            $_GET['alert'] = true;
            $_GET['msg'] = $msg;
        } 
        else 
        {
            $this->redirect(PATH_WWW . "?component=user&controller=login&msg=" . urlencode($msg) . $vars);
        }
    }
    
	/**
	 * To redirect URL.
	 * 
	 * @param string $url_to URL destine
	 * 
	 * @return void
	 */
    public function redirect($url_to = "")
    {
        try 
        {
            
            if(is_array($url_to))
            {
                $url = "";
                $queryurl = "";
                
                foreach($url_to as $key=>$value)
                {
                    if($key == 'url')
                    {
                        $url = $value;
                    }
                    else 
                    {
                        if(!empty($queryurl))
                        {
                            $queryurl .= "&";
                        }
                        
                        $queryurl .= $key . "=" . $value;
                    }
                }
                
                $url_to = $url . $queryurl;
                
            }

            header("Location: " . $url_to);
            exit();
            
        } 
        catch (AppException $e) 
        {
            throw new AppException($e->getMessage());
        }
    }

	/**
	 * Session start
	 * 
	 * @return void
	 */	 
    public function session_open()
    {
        try {
            
            $status = session_status();

            if ($status == PHP_SESSION_NONE) {
                // There is no active session
                session_name(SESSION_NAME);
                session_start();
            } else if ($status == PHP_SESSION_DISABLED) {
                // Sessions are not available
            } else if ($status == PHP_SESSION_ACTIVE) {
                // Destroy current and start new one
                // session_destroy();
                session_write_close();
                session_start();
            }
        } catch (AppException $e) {
            throw new AppException($e->getMessage());
        }
    }

	/**
	 * Session destroy
	 * 
	 * @return void
	 */	 
    public function session_end()
    {
        try {
            
            $status = session_status();

            if ($status == PHP_SESSION_ACTIVE) {
                
                session_name(SESSION_NAME);
                // Destroy current and start new one
                session_destroy();
            }
        } catch (AppException $e) {
            throw new AppException($e->getMessage());
        }
    }

	/**
	 * Session close
	 * 
	 * @return void
	 */	 
    public function session_close()
    {
        try {
            // Release the session lock: behold, fast ajax calls!
            // (just don't try to write to the session after this, changes will be lost)
            session_write_close();
        } catch (AppException $e) {
            throw new AppException($e->getMessage());
        }
    }

	/**
	 * Defines session constants
	 * 
	 * @return void
	 */	 
    public function session_constants()
    {
        try {
            $this->session_open();

            if ($this->is_authentication()) {
                $workspace = $_SESSION['user']['workspace'];

                if (substr($workspace, strlen($workspace) - 1) == "/") {
                    $workspace .= $_SESSION['user']['email'] . DIRECTORY_SEPARATOR;
                } else {
                    $workspace = DIRECTORY_SEPARATOR . $_SESSION['user']['email'] . DIRECTORY_SEPARATOR;
                }

                define("USERNAME", $_SESSION['user']['email']);
                define("USER_TYPE", $_SESSION['user']['type']);
                define("USER_ID", $_SESSION['user']['id']);
                define("PATH_USER_WORKSPACE_STORAGE", $workspace);
                define("PATH_USER_WORKSPACE_PROCESSING", Properties::getbase_directory_destine_exec() . USERNAME . DIRECTORY_SEPARATOR);

                define("PATH_MOA", Properties::getBase_directory_moa());

                if (substr(PATH_MOA, strlen(PATH_MOA) - 1) == "/") {
                    $path_moa = PATH_MOA;
                } else {
                    $path_moa = PATH_MOA . DIRECTORY_SEPARATOR;
                }

                define("PATH_MOA_BIN", $path_moa . "bin" . DIRECTORY_SEPARATOR);

                define("DEFAULT_MOA_BIN_USER", PATH_MOA_BIN . USERNAME . ".jar");
            } else {
                define("USERNAME", '');
                define("USER_TYPE", '');
                define("USER_ID", '');
                define("PATH_USER_WORKSPACE_STORAGE", '');
                define("PATH_USER_WORKSPACE_PROCESSING", '');
                define("PATH_MOA", '');
                define("PATH_MOA_BIN", '');
                define("DEFAULT_MOA_BIN_USER", '');
            }

            $this->session_close();
        } catch (AppException $e) {
            throw new AppException($e->getMessage());
        }
    }

	/**
	 * Force download file
	 * 
	 * @param string $data content
	 * @param string $type mime type
	 * @param string $filename defines file name
	 * 
	 * @return void
	 */ 
    public function force_download($data, $type, $filename = "")
    {
        try {
            // force download
            @ob_end_clean();
            ob_start();

            header('Content-type: ' . $type);
            Header('Content-Description: File Transfer');
            // header("Content-Transfer-Encoding: binary");
            header('Accept-Ranges: bytes');
            header('Content-Disposition: attachment;filename=' . "\"" . $filename . "\"");

            echo $data;

            header('Content-Length: ' . ob_get_length());

            ob_flush();
            ob_end_flush();
        } catch (AppException $e) {
            throw new AppException($e->getMessage());
        }
    }
}

?>
