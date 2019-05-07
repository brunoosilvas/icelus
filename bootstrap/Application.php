<?php

/**
 *
 * @author Bruno Silva
 * @license MIT Licence
 * @link https://github.com/brunoosilvas/icelus
 *
 */

namespace icelus\bootstrap;

use icelus\http\Request;
use icelus\http\Response;

class Application 
{
    private CONST ICELUS = "/icelus/i";
    private CONST EXTENSION_PHP = ".php";
    
    private CONST FOLDER_VENDOR = "vendor";
    private CONST FOLDER_ERROR = "error";


    private static $instance;
    
    private $conf;
    private $timeScriptStart;
    private $timeScriptEnd;
    private $timeScriptElapsed;	
    
    /**
     * @return Instance of class Application
     */
    public static function environment() 
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }
        
        return self::$instance;
    }

    /**
     * Start initial process application
     * 
     * @return void
     */
    public function init($conf) 
    {	
        $this->conf = $conf;		
        $this->registerTimeStartExecutionOfScript();
        $this->registerAutoloadClass();
        $this->notifyError();
        
        $this->bufferPageStart();
    }

    /**
     * Control de flush page
     * 
     * @return void	 
     */
    public function bufferPageStart()
    {
        ob_start();
    }

    /**
     * Control de flush page
     * 
     * @return void	 
     */
    public function bufferPageEnd()
    {
        ob_end_clean();
    }
    
    /**
     * @return string
     */
    public static function rootDir() 
    {
        return $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR;
    }
    
    /**
     * Register time execution of script
     * 
     * @return void
     */
    private function registerTimeStartExecutionOfScript() 
    {
        list($usec, $sec) = explode(' ', microtime());
        $this->timeScriptStart = ((float) $sec + (float) $usec);		
    }

    /**
     * Time elipsed script execution
     * 
     * @return double
     */
    public function timeEndScriptExecution() 
    {
        list($usec, $sec) = explode(' ', microtime());
        $this->timeScriptEnd = ((float) $sec + (float) $usec);
        $this->timeScriptElapsed = round($this->timeScriptEnd - $this->timeScriptStart, 4);
        
        return $this->timeScriptElapsed;
    }
    
    /**
     * Register autoload class of application
     * 
     * @return void
     */
    private function registerAutoloadClass() 
    {
        spl_autoload_register(array($this, "autoloadClass"));		
    }
    
    /**
     * Handler autoload class of application
     * 
     * @param string $namespace
     * @return void
     */
    public function autoloadClass($namespace) 
    {
        if (!class_exists($namespace) && !interface_exists($namespace))
        {
            $this->loadClass($namespace);
        }
    }	
    
    /**
     * Include class of application
     * 
     * @param string $path 
     * @param string $className 
     * @return void
     */
    
    private function loadClass($namespace) 
    {

        $pathClass = $this->rootDir();

        if (preg_match(Application::ICELUS, $namespace))
        {
            $pathClass .= Application::FOLDER_VENDOR . DIRECTORY_SEPARATOR;
        }

        $pathClass .= $namespace . Application::EXTENSION_PHP;

        require_once(str_replace("\\", "/", $pathClass));
    }
    
    /**
     * Register notification error application
     * 
     * @return void
     */
    private function notifyError() 
    {	
        set_error_handler(array($this, "errorHandler"));
    }

    /**
     * Handler default send error applicaiton
     * 
     * @param integer $error
     * @param string $message
     * @param string $file
     * @param string $errLine
     * @throws \ErrorException
     */
    public function errorHandler($errorType, $errorMessage, $errorFile, $errorLine, $errorContext = null)
    {
        throw new \ErrorException($errorMessage, 0, $errorType, $errorFile, $errorLine);
    }
    
    public function restoreError(\ErrorException $exception) 
    {
        if (Request::isAjax()) 
        {
            Response::fromJson($exception);
        }
        else
        {
            $this->bufferPageEnd();

            echo var_dump($exception);
        }
    }
    
    public function restoreFatalError(\Error $error)
    {
        if (Request::isAjax()) 
        {
            Response::fromJson($error);
        }
        else
        {
            $this->bufferPageEnd();

            echo var_dump($error);
        }
    }
    
}