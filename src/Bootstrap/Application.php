<?php

/**
 *
 * @author Bruno Silva
 * @license MIT Licence
 * @link https://github.com/brunoosilvas/icelus-php
 *
 */

namespace Icelus\Bootstrap;

use Icelus\Http\Request;
use Icelus\Http\Response;

class Application 
{
    private static $instance;
    
    private $config;
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
    public function init($config) 
    {	
        $this->config = $config;		
        $this->registerTimeStartScript();
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
        //echo var_dump(ob_get_status());
        //flush();
        if (ob_get_status())
        {
            ob_end_clean();
        }
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
    private function registerTimeStartScript() 
    {
        list($usec, $sec) = explode(' ', microtime());
        $this->timeScriptStart = ((float) $sec + (float) $usec);		
    }

    /**
     * Time elipsed script execution
     * 
     * @return double
     */
    public function registerTimeEndScript() 
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
     * @param string $namespace 
     * @return void
     */    
    private function loadClass($namespace) 
    {
        $class = $this->rootDir();

        if (preg_match(Application::ICELUS, $namespace))
        {
            $class .= Application::FOLDER_VENDOR . DIRECTORY_SEPARATOR;
        }

        $class .= $namespace . Application::EXTENSION_DEFAULT;
        $class = str_replace("\\", "/", $class);

        require_once($class);
    }
    
    /**
     * Register notification error application
     * 
     * @return void
     */
    private function notifyError() 
    {	
        register_shutdown_function(array($this, "errorHandler"));
    }

    private function clearError()
    {
        error_clear_last();
    }

    public function errorHandler()
    {
        $error = error_get_last();
        
        if ($error)
        {
            $this->restoreError($error);
        }        
    }
    
    public function restoreError($error) 
    {
        $this->clearError();
        $this->bufferPageEnd();

        if (Request::isAjax()) 
        {
            Response::fromJson($error);
        }
        else
        {
            echo "<pre>";
            echo var_dump($error);
        }
    }
    
}