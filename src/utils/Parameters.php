<?php
namespace keeko\framework\utils;

use Tobscure\JsonApi\Parameters as BaseParameters;

class Parameters extends BaseParameters {
	
	public function getPage($key, $default = null) {
		$page = $this->getInput('page', []);
		
		return isset($page[$key]) ? $page[$key] : $default;
	}
}