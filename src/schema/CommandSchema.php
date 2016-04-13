<?php
namespace keeko\framework\schema;

use phootwork\collection\Map;

class CommandSchema extends SubSchema {

	/** @var string */
	private $name;
	
	/** @var string */
	private $class;
	
	/** @var string */
	private $handler;
		
	public function __construct($name, PackageSchema $package = null, $contents = []) {
		$this->name = $name;
		parent::__construct($package, $contents);
	}
	
	/**
	 * @param array $contents
	 */
	protected function parse($contents) {
		$data = new Map($contents);
	
		$this->class = $data->get('class', '');
		$this->handler = $data->get('handler', '');
	}
	
	public function toArray() {
		return [
			'class' => $this->class,
			'handler' => $this->handler
		];
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getClass() {
		return $this->class;
	}
	
	public function setClass($class) {
		$this->class = $class;
		return $this;
	}
	
	public function getHandler() {
		return $this->handler;
	}
	
	public function setHandler($handler) {
		$this->handler = $handler;
		return $this;
	}
}
