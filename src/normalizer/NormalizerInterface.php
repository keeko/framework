<?php
namespace keeko\framework\events;

use keeko\framework\service\ServiceContainer;

interface NormalizerInterface {

	public function setServiceContainer(ServiceContainer $service);
	
	public function normalize($value);
}