<?php
namespace keeko\framework\events;

use keeko\framework\service\ServiceContainer;

interface KeekoEventListenerInterface {

	public function setServiceContainer(ServiceContainer $service);
}