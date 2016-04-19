<?php
namespace keeko\framework\utils;

use Tobscure\JsonApi\Parameters;

class Parameters extends Parameters {
	
	public function getPage($key, $default = null) {
		$page = $this->getInput('page', []);
		
		return isset($page[$key]) ? $page[$key] : $default;
	}
}