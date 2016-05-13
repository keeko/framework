<?php
namespace keeko\framework\validator;

use Symfony\Component\Validator\ConstraintViolationListInterface;

interface ValidatorInterface {
	
	/**
	 * Validates something
	 * 
	 * @param mixed $value
	 * @return boolean
	 */
	public function validate($value);
	
	/**
	 * Returns the failures from latest validation
	 * 
	 * @return ConstraintViolationListInterface
	 */
	public function getValidationFailures();
}