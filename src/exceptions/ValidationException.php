<?php
namespace keeko\framework\exceptions;

use Symfony\Component\Validator\ConstraintViolationList;

class ValidationException {

	private $violations;
	
	/*
	 * (non-PHPdoc) @see Exception::__construct()
	 */
	public function __construct(ConstraintViolationList $violations) {
		$this->violations = $violations;
		parent::__construct('Validation Failed', 422);
	}

	/**
	 * Returns a violations list
	 *
	 * @return ConstraintViolationList
	 */
	public function getViolations() {
		return $this->violations;
	}
}