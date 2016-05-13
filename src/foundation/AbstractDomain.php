<?php
namespace keeko\framework\foundation;

use keeko\framework\service\ServiceContainer;
use keeko\framework\validator\ValidatorInterface;

abstract class AbstractDomain {
		
	/** @var ServiceContainer */
	protected $service;

	public function __construct(ServiceContainer $service) {
		$this->service = $service;
	}
	
	/**
	 * Returns the service container
	 *
	 * @return ServiceContainer
	 */
	protected function getServiceContainer() {
		return $this->service;
	}

	/**
	 * Returns a validator
	 * 
	 * @return ValidatorInterface
	 */
	protected function getValidator() {
		return null;
	}
}
