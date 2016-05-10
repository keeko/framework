<?php
namespace keeko\framework\events;

use Symfony\Component\EventDispatcher\Event;
use keeko\core\model\Module;

class ModuleEvent extends Event {
	
	const INSTALLED = 'framework.module.installed';
	const UNINSTALLED = 'framework.module.uninstalled';
	const UPDATED = 'framework.module.updated';
	const ACTIVATED = 'framework.module.activated';
	const DEACTIVATED = 'framework.module.deactivated';

	/** @var Module */
	private $module;
	
	public function __construct(Module $module) {
		$this->module = $module;
	}
	
	/**
	 * @return Module
	 */
	public function getModule() {
		return $this->module;
	}
}