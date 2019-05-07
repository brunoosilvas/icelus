<?php

/**
 *
 * @author Bruno Silva
 * @license MIT Licence
 * @link https://github.com/brunoosilvas/icelus
 *
 */

namespace icelus\controller;

use icelus\util\Files;
use icelus\util\Utils;

class FactoryController
{
	private $uri;
	private $controller;
	
	public function __construct($uri) 
	{
		$this->uri = $uri;
	}
	
	public function instantiate() 
	{		
		if (!Files::exists($this->uri, NULL))
		{
			throw new \ErrorException(sprintf("Controller not found in path '%s'", $this->uri));
		}

		$this->controller = Utils::convertToNamespace($this->uri);
		$this->controller = new $this->controller;

		return $this->controller;
	} 
	
	public function execute($method, $param) 
	{		
		$reflectionClass = new \ReflectionClass($this->controller);
		if (!$reflectionClass->hasMethod($method))
		{
			throw new \ErrorException(sprintf("Method '%s' in controller '%s' is missing.", $method, $this->uri));
		}
		
		$reflectionClass->getMethod($method)->invoke($this->controller, $param);
	}	
	
}