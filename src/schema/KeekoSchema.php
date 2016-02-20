<?php
namespace keeko\framework\schema;

use phootwork\collection\Map;

class KeekoSchema extends SubSchema {
	
	/** @var AppSchema */
	private $app;
	
	/** @var ModuleSchema */
	private $module;
	
	protected function parse($contents) {
		$data = new Map($contents);
	
		if ($data->has('app')) {
			$this->app = new AppSchema($this->package, $data->get('app'));
		}
		
		if ($data->has('module')) {
			$this->module = new ModuleSchema($this->package, $data->get('module'));
		}
	}
	
	public function toArray() {
		if ($this->app !== null) {
			return ['app' => $this->app->toArray()];
		}
		
		if ($this->module !== null) {
			return ['module' => $this->module->toArray()];
		}
		
		return [];
	}
	
	/**
	 * @return boolean
	 */
	public function isApp() {
		return $this->app !== null;
	}
	
	/**
	 * @return boolean
	 */
	public function isModule() {
		return $this->module !== null;
	}
	
	/**
	 *
	 * @return ModuleSchema
	 */
	public function getModule() {
		return $this->module;
	}
	
	/**
	 *
	 * @return AppSchema
	 */
	public function getApp() {
		return $this->app;
	}
	
	
	public function getKeekoPackage($type) {
		switch ($type) {
			case 'app':
				if ($this->app === null) {
					$this->app = new AppSchema($this->package);
				}
				return $this->app;
				
			case 'module':
				if ($this->module === null) {
					$this->module = new ModuleSchema($this->package);
				}
				return $this->module;
		}
	}
}

