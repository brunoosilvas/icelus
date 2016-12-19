<?php

/**
 * @namespace icelus\controller
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
		if (! Files::exists($this->uri, null))
			throw new \ErrorException(sprintf("Controller not found in path '%s'", $this->uri));

		$controller = Utils::convertToNamespace($this->uri);
		$this->controller = new $controller;
		
		return $this->controller;
	} 
	
	public function execute($method, $param) 
	{		
		if (!method_exists($this->controller, $method))
			throw new \ErrorException(sprintf("Method '%s' in controller '%s' is missing.", $method, $this->uri));
		
		call_user_func(array($this->controller, $method), $param);
	}	
	
}