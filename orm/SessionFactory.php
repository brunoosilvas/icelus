<?php

/**
 *
 * @author Bruno Silva
 * @license MIT Licence
 * @link https://github.com/brunoosilvas/icelus
 *
 */

namespace icelus\orm;

use icelus\util\Files;
use icelus\orm\Session;

class SessionFactory 
{	
	private static $instance;
	private $config;
	private $session;
		
	public static function instance() 
	{
        if (self::$instance == NULL)
        {
            self::$instance = new self();
        }
	
		return self::$instance;
	}
	
	public function configure($uri) 
	{
		$this->config = Files::xmlLoad($uri);
		return $this;
	}

	public function getConfig()
	{
		return $this->config;
	}
	
	public function build() 
	{		
        try 
        {
            if ($this->session == NULL) 
            {
                $persistence = $this->config->persistence;

                $url = $persistence->url->__toString();
                $username = $persistence->username->__toString();
                $password = $persistence->password->__toString();

                $dbc = new \PDO($url, $username, $password);
                $dbc->setAttribute(\PDO::ATTR_PERSISTENT, false);
                $dbc->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

                $dialect = $this->config->dialect->__toString();
                $dialect = new $dialect;

                $this->session = new Session($dbc, $dialect);
            }
        } 
        catch(\PDOException $e) 
        {
            throw new \ErrorException($e->getMessage(), $e->getCode(), 0, $e->getFile(), $e->getLine());
        }

		return $this;
	}

	public function getSession()
	{
		return $this->session;
	}
}
