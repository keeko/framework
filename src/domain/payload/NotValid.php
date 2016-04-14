<?php
namespace keeko\framework\domain\payload;

use keeko\framework\domain\payload\AbstractPayload;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolation;

class NotValid extends AbstractPayload {

	/**
	 * 
	 * @return ConstraintViolationList
	 */
	public function getViolations() {
		$list = new ConstraintViolationList();
		$errors = $this->get('errors');
		if ($errors !== null) {
			if ($errors instanceof ConstraintViolationList) {
				return $errors;
			}
			
			foreach ($errors as $error) {
				$list->add(new ConstraintViolation($error, '', [], '', '', null));
			}
		}
		
		return $list;
	}
}