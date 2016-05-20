<?php
namespace keeko\framework\normalizer;

use keeko\framework\events\NormalizerInterface;
use keeko\framework\service\ServiceContainer;

abstract class AbstractNormalizer implements NormalizerInterface {

	/** @var ServiceContainer */
	protected $service;
	
	public function setServiceContainer(ServiceContainer $service) {
		$this->service = $service;
	}
}