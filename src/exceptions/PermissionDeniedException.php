<?php
namespace keeko\framework\exceptions;

class PermissionDeniedException extends \Exception {
	
	public function __construct($message = null, $code = 403) {
		parent::__construct($message, $code);
	}
}
