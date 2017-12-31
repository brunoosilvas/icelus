<?php

/**
 *
 * @author Bruno Silva
 * @license MIT Licence
 * @link https://github.com/brunoosilvas/icelus
 *
 */

namespace icelus\orm\type;

use icelus\orm\type\Generic;

class IString extends Generic 
{
		
	public function __construct($value = null) 
	{
		$this->value = null;
		if ($this->isValid($value))
			$this->value = $value;
	}
	
	public function value() 
	{
		return $this->value;
	}
	
	public function isValid($value) 
	{
		return is_string($value) ? true : false;
	}
	
	public function compare(Type $type) 
	{
		$this->compareIsValid($type);
		
		$compare = false;
		if (strcmp($this->value(), $type->value()) == 0)
			$compare = true;
		
		return $compare;
	}
	
	public function length() 
	{
		return strlen($this->value());
	}
}