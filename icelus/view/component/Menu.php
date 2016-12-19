<?php

/**
 * @namespace icelus\view\component
 *
 * @author Bruno Silva
 * @license MIT Licence
 * @link https://github.com/brunoosilvas/icelus
 *
 */

namespace icelus\view\component;

class Menu {
	
	private $itens;

	public function __construct() {
		$this->itens = array();	
	}
	
	public function add(MenuItem $item) {
		array_push($this->itens, $item);		
	}
	
	public function itens() {
		return $this->itens;
	}

	
}