<?php
namespace keeko\framework\events;

use Symfony\Component\EventDispatcher\Event;
use keeko\core\model\Module;

class ModuleEvent extends Event {
	
	const INSTALLED = 'core.module.installed';
	const UNINSTALLED = 'core.module.uninstalled';
	const UPDATED = 'core.module.updated';
	const ACTIVATED = 'core.module.activated';
	const DEACTIVATED = 'core.module.deactivated';

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