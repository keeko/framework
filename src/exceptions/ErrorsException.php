<?php
namespace keeko\framework\exceptions;

class ErrorsException extends \Exception {

	private $errors;

	/*
	 * (non-PHPdoc) @see Exception::__construct()
	 */
	public function __construct(array $errors) {
		$this->errors = $errors;
		parent::__construct('Errors');
	}

	public function getErrors() {
		return $this->errors;
	}
}