<?php
namespace keeko\framework\normalizer;

use keeko\framework\service\ServiceContainer;

interface NormalizerInterface {

	public function setServiceContainer(ServiceContainer $service);
	
	public function normalize($value);
}