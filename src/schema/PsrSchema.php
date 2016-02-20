<?php
namespace keeko\framework\schema;

use phootwork\collection\Map;
use phootwork\lang\Arrayable;

class PsrSchema implements Arrayable {

	/** @var Map<string, string> */
	private $namespaces;
	
	public function __construct($contents = []) {
		$this->parse($contents);
	}

	private function parse($contents) {
		$this->namespaces = new Map($contents);
	}

	public function toArray() {
		return $this->namespaces->toArray();
	}
	
	public function setPath($namespace, $path) {
		$this->namespaces->set($namespace, $path);
	}

	/**
	 * Returns the path for the given namespace or null if the namespace doesn't exist
	 *
	 * @param string $namespace
	 * @return string|null
	 */
	public function getPath($namespace) {
		return $this->namespaces->get($namespace);
	}
	
	/**
	 * Returns whether the given path exists
	 *
	 * @param string $path
	 * @return boolean
	 */
	public function hasPath($path) {
		return $this->namespaces->contains($path);
	}
	
	/**
	 * Removes the given path
	 *
	 * @param string $path
	 */
	public function removePath($path) {
		$this->namespaces->remove($path);
		return $this;
	}
	
	/**
	 * Returns the namespace for the given path or false if the path cannot be found
	 *
	 * @param string $path
	 * @return stirng the namespace
	 */
	public function getNamespace($path) {
		return $this->namespaces->getKey($path);
	}
	
	public function hasNamespace($namespace) {
		return $this->namespaces->has($namespace);
	}
	
	public function isEmpty() {
		return $this->namespaces->isEmpty();
	}

}
