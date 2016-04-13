<?php
namespace keeko\framework\schema;

use phootwork\collection\Map;
use phootwork\collection\Set;

class ModuleSchema extends KeekoPackageSchema {
	
	/** @var Map<string, ActionSchema> */
	protected $actions;
	
	/** @var Map<string, CommandSchema> */
	protected $commands;
	
	/**
	 * @param array $contents
	 */
	protected function parse($contents = []) {
		$data = parent::parse($contents);
		
		$this->actions = new Map();
		foreach ($data->get('actions', []) as $name => $actionData) {
			$this->actions->set($name, new ActionSchema($name, $this->package, $actionData));
		}
		
		$this->commands = new Map();
		foreach ($data->get('commands', []) as $name => $commandData) {
			$this->commands->set($name, new CommandSchema($name, $this->package, $commandData));
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
		
		if ($this->commands->size() > 0) {
			$commands = [];
			foreach ($this->commands as $command) {
				$commands[$command->getName()] = $command->toArray();
			}
			$arr['commands'] = $commands;
		}

		return $arr;
	}

	/**
	 * Returns the slug for this module
	 * 
	 * @return string
	 */
	public function getSlug() {
		if ($this->package->getVendor() == 'keeko') {
			return $this->package->getName();
		}
		
		return str_replace('/', '.', $this->package->getFullName());
	}
	
	/**
	 * Checks whether an action with the given name exists
	 * 
	 * @param string $name
	 * @return boolean
	 */
	public function hasAction($name) {
		return $this->actions->has($name);
	}
	
	/**
	 * Adds an action
	 * 
	 * @param ActionSchema $action
	 * @return $this
	 */
	public function addAction(ActionSchema $action) {
		$action->setPackage($this->package);
		$this->actions->set($action->getName(), $action);
		return $this;
	}
	
	/**
	 * Gets the action with the given name
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

	/**
	 * Checks whether a command with the given name exists
	 * 
	 * @param string $name
	 * @return boolean
	 */
	public function hasCommand($name) {
		return $this->commands->has($name);
	}
	
	/**
	 * Adds a command
	 * 
	 * @param CommandSchema $command
	 * @return $this
	 */
	public function addCommand(CommandSchema $command) {
		$command->setPackage($this->package);
		$this->commands->set($command->getName(), $command);
		return $this;
	}
	
	/**
	 * Gets a command with the given name
	 * 
	 * @param string $name
	 * @return CommandSchema
	 */
	public function getCommand($name) {
		return $this->commands->get($name);
	}
	
	/**
	 * Returns all command names
	 * 
	 * @return string[]
	 */
	public function getCommandNames() {
		return $this->commands->keys();
	}
}

