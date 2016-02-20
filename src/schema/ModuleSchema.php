<?php
namespace keeko\framework\schema;

use phootwork\collection\Map;
use phootwork\collection\Set;

class ModuleSchema extends KeekoPackageSchema {
	
	/** @var Map<string, ActionSchema> */
	protected $actions;
	
	/**
	 * @param array $contents
	 */
	protected function parse($contents = []) {
		$data = parent::parse($contents);
		
		$this->actions = new Map();
		foreach ($data->get('actions', []) as $name => $actionData) {
			$this->actions->set($name, new ActionSchema($name, $this->package, $actionData));
		}
	}
	
	public function toArray() {
		$arr = parent::toArray();
		
		if ($this->actions->size() > 0) {
			$actions = [];
			foreach ($this->actions as $action) {
				$actions[$action->getName()] = $action->toArray();
			}
			$arr['actions'] = $actions;
		}

		return $arr;
	}
	
	public function getSlug() {
		if ($this->package->getVendor() == 'keeko') {
			return $this->package->getName();
		}
		
		return str_replace('/', '.', $this->package->getFullName());
	}
	
	public function hasAction($name) {
		return $this->actions->has($name);
	}
	
	/**
	 * @param ActionSchema $action
	 * @return $this
	 */
	public function addAction(ActionSchema $action) {
		$action->setPackage($this->package);
		$this->actions->set($action->getName(), $action);
		return $this;
	}
	
	/**
	 *
	 * @param string $name
	 * @return ActionSchema
	 */
	public function getAction($name) {
		return $this->actions->get($name);
	}
	
	/**
	 * Returns all action names
	 *
	 * @return Set
	 */
	public function getActionNames() {
		return $this->actions->keys();
	}

}

